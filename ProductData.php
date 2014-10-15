<?php

class ProductData
{
    protected $connection;

    public function __construct()
    {
        include('./config.php');
        $this->connect();
    }

    public function connect()
    {
        $this->connection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_DB, DB_USER, DB_PASSWORD);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllProducts($start, $size)
    {
        $query = $this->connection->prepare("select * from product limit :start, :size");
        $query->bindValue(':start', (int) $start, PDO::PARAM_INT);
        $query->bindValue(':size', (int) $size, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function addProduct($product)
    {
        $query = $this->connection->prepare("insert into product (name) VALUES (:name)");
        $insertData = [':name' => $product];
        $query->execute($insertData);
    }

    public function getAttributesForProducts($productIds)
    {
        $inQuery = implode(',', array_fill(0, count($productIds), '?'));
        $query = $this->connection->prepare("select * from product_attributes_values where pid in (" . $inQuery . ")");
        foreach ($productIds as $key => $value) {
            $query->bindValue(($key + 1), $value);
        }
        $query->execute();
        $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
        return $attributes;
    }

    public function getProduct($name)
    {
        $query = $this->connection->prepare("select * from product where name = :name");
        $selectData = [':name' => $name];
        $query->execute($selectData);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }

    public function addAttributesForProduct($insertedProductId, $productAttributesList)
    {
        foreach ($productAttributesList as $productAttributes) {
            $query = $this->connection->prepare("insert into product_attributes_values (pid, name, value) values (:pid, :name, :value)");
            $insertData = [':pid' => $insertedProductId, ':name' => $productAttributes["name"], ':value' => $productAttributes["value"]];
            $query->execute($insertData);
        }

    }

    public function deleteAttributesForProduct($id)
    {
        $query = $this->connection->prepare("delete from product_attributes_values where pid = :pid");
        $deleteData = [':pid' => $id];
        $query->execute($deleteData);
    }

    public function deleteProduct($id)
    {
        $query = $this->connection->prepare("delete from product where id = :id");
        $deleteData = [":id" => $id];
        $query->execute($deleteData);
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }


}
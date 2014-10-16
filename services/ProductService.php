<?php
require __DIR__ . '/../data/ProductData.php';


class ProductService
{
    protected $data;

    public function __construct()
    {
        $this->data = new ProductData();
    }

    public function getAllProducts()
    {
        list($start, $size) = $this->setupPaginationParams();
        $products = $this->data->getAllProducts($start, $size);
        $products = $this->addAttributes($products);
        return $products;
    }

    public function addProduct($product)
    {
        $this->data->beginTransaction();
        $productName = $product["name"];
        $this->data->addProduct($productName);
        if (isset($product["attributes"])) {
            $this->addAttributesForProduct($product);
        }
        $this->data->commit();
    }

    private function getProductIdArrayFromProductObjects($products, $actualKey)
    {
        $productIdArray = array();
        foreach ($products as $product) {
            $productIdArray[] = $product[$actualKey];
        }
        return $productIdArray;
    }

    /**
     * @param $productAttributes
     * @param $products
     * @param $key
     * @return mixed
     */
    private function setProductAttributes($productAttributes, $products, $key)
    {
        unset($productAttributes["pid"]);
        unset($productAttributes["id"]);
        $products[$key]["attributes"][] = $productAttributes;
        return $products;
    }

    public function getProduct($name)
    {
        $product = $this->data->getProduct($name);
        if (!empty($product)) {
            $products = array();
            $products[] = $product;
            $products = $this->addAttributes($products);
            return $products[0];
        }
    }

    /**
     * @param $products
     * @return mixed
     */
    public function addAttributes($products)
    {
        if ($this->shouldIncludeAttributes()) {
            $productIdArray = $this->getProductIdArrayFromProductObjects($products, "id");
            $productAttributesList = $this->data->getAttributesForProducts($productIdArray);
            foreach ($products as $key => $value) {
                $products[$key]["attributes"] = array();
                foreach ($productAttributesList as $productAttributes) {
                    if ($products[$key]["id"] == $productAttributes["pid"]) {
                        $products = $this->setProductAttributes($productAttributes, $products, $key);
                    }
                }
            }
            return $products;
        }
        return $products;
    }

    /**
     * @param $product
     */
    protected function addAttributesForProduct($product)
    {
        $productName = $product["name"];
        $insertedProduct = $this->data->getProduct($productName);
        $insertedProductId = $insertedProduct["id"];
        $productAttributesList = $product["attributes"];
        $this->data->addAttributesForProduct($insertedProductId, $productAttributesList);
    }

    public function deleteProduct($productName)
    {
        $this->data->beginTransaction();
        $product = $this->getProduct($productName);
        if (!empty($product)) {
            $this->data->deleteAttributesForProduct($product['id']);
            $this->data->deleteProduct($product['id']);
        }
        $this->data->commit();
    }

    public function queryProducts($start, $size, $shouldIncludeAttributes)
    {
        list($attributeKey, $attributeValue) = $this->extractAttributeKeyValue();
        $productIds = $this->data->queryProductByAttribute($start, $size, $attributeKey, $attributeValue);
        $productIdArray = $this->getProductIdArrayFromProductObjects($productIds, "pid");
        $products = $this->data->getProductsByProductIds($productIdArray);
        if($shouldIncludeAttributes) {
            $_GET["includeAttributes"] = "true";
        }
        $products = $this->addAttributes($products);
        return $products;
    }

    /**
     * @return array
     */
    public function setupPaginationParams()
    {
        $start = 0;
        $size = 10;
        if (isset($_GET['start'])) {
            $start = $_GET['start'];
        }
        if (isset($_GET['size'])) {
            $size = $_GET['size'];
        }
        return array($start, $size);
    }

    private function extractAttributeKeyValue()
    {
        foreach ($_GET as $key => $value) {
            return array($key, $value);
        }
    }


    /**
     * @return bool
     */
    private function shouldIncludeAttributes()
    {
        return isset($_GET["includeAttributes"]) && $_GET["includeAttributes"] == "true";
    }

    public function updateProduct($name, $product, $productBody)
    {
        $this->data->beginTransaction();
        $productId = $product["id"];
        foreach ($productBody["attributes"] as $key => $value) {
            $this->data->updateProductAttribute($productId, $value['name'], $value['value']);
        }
        $this->data->commit();
    }

}

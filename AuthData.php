<?php

class AuthData
{

    protected $connection;

    public function __construct()
    {
        include('./config.php');
        $this->connect();
    }

    public function connect()
    {
        $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DB, DB_USER, DB_PASSWORD);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getUser($username)
    {
        $query = $this->connection->prepare("select * from auth where username = :name");
        $selectData = [':name' => $username];
        $query->execute($selectData);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }

} 
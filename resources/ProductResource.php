<?php

require(__DIR__ . '/../services/ProductService.php');

class ProductResource
{
    protected $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function getAllProducts()
    {
        $products = $this->productService->getAllProducts();
        header('Content-type: application/json');
        $result = array();
        $result['data'] = $products;
        $result['start'] = $_GET['start'] ? (int)$_GET['start'] : 0;
        $result['size'] = sizeof($products);
        echo json_encode($result);
    }

    public function addProduct()
    {
        $product = $entityBody = file_get_contents('php://input');
        $product = json_decode($product, true);
        if (!isset($product['name'])) {
            header('HTTP/1.1 400 Bad Request');
            echo "'name' is a required field";
        } elseif ($this->productService->getProduct($product['name'])) {
            header('HTTP/1.1 409 Conflict');
            echo "already exists";
        } else {
            $this->productService->addProduct($product);
            $this->redirect("/products/" . $product['name'] . '?includeAttributes=true', 302);
        }
    }

    public function getProduct($name)
    {
        $product = $this->productService->getProduct($name);
        if (empty($product)) {
            header('HTTP/1.1 404 Not Found');
            echo "Not found";
        } else {
            header('Content-type: application/json');
            echo json_encode($product);
        }
    }

    private function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    public function deleteProduct($name)
    {
        if (!$this->productService->getProduct($name)) {
            header('HTTP/1.1 404 Not Found');
            echo "Not found";
        } else {
            $this->productService->deleteProduct($name);
            header('HTTP/1.1 204 No Content');
        }
    }

    public function queryProducts()
    {
        $products = $this->productService->queryProducts();
    }


}
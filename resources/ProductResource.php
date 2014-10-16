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
            $this->redirect("/products/" . $product['name'] . '?includeAttributes=true', 303);
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
        list($start, $size) = $this->setupPaginationParams();
        $shouldIncludeAttributes = $this->shouldIncludeAttributes();
        if (!$this->checkValidQueryRequest()) {
            header('HTTP/1.1 400 Bad Request');
            echo "Wrong number of keys provided";
        } else {
            $products = $this->productService->queryProducts($start, $size, $shouldIncludeAttributes);
            $_GET['start'] = $start;
            $_GET['size'] = $size;
            header('Content-type: application/json');
            $result = array();
            $result['data'] = $products;
            $result['start'] = $_GET['start'] ? (int)$_GET['start'] : 0;
            $result['size'] = sizeof($products);
            echo json_encode($result);
        }
    }

    private function checkValidQueryRequest()
    {
        $this->unsetPaginationParams();
        $this->unsetIncludeAttributes();
        if (sizeof($_GET) != 1) {
            return false;
        } else {
            return true;
        }
    }

    private function setupPaginationParams()
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

    private function unsetPaginationParams()
    {
        if (isset($_GET['start'])) {
            unset($_GET['start']);
        }
        if (isset($_GET['size'])) {
            unset($_GET['size']);
        }
    }

    private function shouldIncludeAttributes()
    {
        return isset($_GET["includeAttributes"]) && $_GET["includeAttributes"] == "true";
    }

    private function unsetIncludeAttributes()
    {
        if (isset($_GET["includeAttributes"])) {
            unset($_GET["includeAttributes"]);
        }
    }

    public function updateProduct($name)
    {
        $product = $this->productService->getProduct($name);
        if (empty($product)) {
            header('HTTP/1.1 404 Not Found');
            echo "Not found";
        } else {
            $productBody = $entityBody = file_get_contents('php://input');
            $productBody = json_decode($productBody, true);
            if (!isset($productBody['attributes'])) {
                header('HTTP/1.1 400 Bad Request');
                echo "'attributes' is a required field";
            } else {
                $this->productService->updateProduct($name, $product, $productBody);
                $this->redirect("/products/" . $name . '?includeAttributes=true', 303);
            }
        }
    }
}
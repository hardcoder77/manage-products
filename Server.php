<?php
require './ProductResource.php';
require './AuthResource.php';
require './AuthService.php';

class Server
{

    protected $productResource;

    protected $authResource;

    protected $authService;

    public function __construct()
    {
        $this->productResource = new ProductResource();
        $this->authResource = new AuthResource();
        $this->authService = new AuthService();
    }

    public function serve()
    {

        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $paths = explode('/', $this->paths($uri));
        array_shift($paths);
        $resource = array_shift($paths);

        if ($resource == 'products') {
            $name = array_shift($paths);

            if (empty($name)) {
                $this->handle_base($method);
            } else {
                $this->handle_name($method, $name);
            }

        } elseif ($resource == 'query' && $method == 'GET') {
            $this->handle_query();
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }

    private function handle_base($method)
    {
        switch ($method) {
            case 'GET':
                $this->productResource->getAllProducts();
                break;

            case 'POST':
                $this->productResource->addProduct();
                break;

            default:
                header('HTTP/1.1 405 Method Not Allowed');
                header('Allow: GET');
                break;
        }
    }

    private function handle_name($method, $name)
    {
        switch ($method) {
            case 'PUT':
                $this->create_contact($name);
                break;

            case 'DELETE':
                $this->productResource->deleteProduct($name);
                break;

            case 'GET':
                $this->productResource->getProduct($name);
                break;

            default:
                header('HTTP/1.1 405 Method Not Allowed');
                header('Allow: GET, PUT, DELETE');
                break;
        }
    }


    private function paths($url)
    {
        $uri = parse_url($url);
        return $uri['path'];
    }


    private function handle_query()
    {
        $this->productResource->queryProducts();
    }

    public function checkAuth()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Not authorized';
            return false;
        } else {
            return $this->authService->authenticate();
        }
    }
}

$server = new Server();
if ($server->checkAuth()) {
    $server->serve();
}

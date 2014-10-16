<?php
require __DIR__ . '/../data/AuthData.php';

class AuthService {

    protected $data;

    public function __construct() {
        $this->data = new AuthData();
    }

    public function authenticate()
    {
        $username = $_SERVER['PHP_AUTH_USER'];
        $user = $this->getUser($username);
        $password = base64_encode($_SERVER['PHP_AUTH_PW']);
        if($password == $user['password']) {
            return true;
        }
    }

    public function getUser($username) {
        $user = $this->data->getUser($username);
        return $user;
    }

}
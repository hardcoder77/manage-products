<?php

class AuthResource {

    protected $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function authenticate()
    {
        return $this->authService->authenticate();
    }
}
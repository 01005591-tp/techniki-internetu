<?php

namespace p1\view\login;

require_once "view/login/sign-up-controller.php";
require_once "view/login/sign-up-request-validator.php";

class LoginConfiguration
{
    private static LoginConfiguration $instance;
    private SignUpRequestValidator $signUpRequestValidator;
    private SignUpController $signUpController;

    private function __construct()
    {
        $this->signUpRequestValidator = new SignUpRequestValidator();
        $this->signUpController = new SignUpController($this->signUpRequestValidator());
    }

    public function signUpRequestValidator(): SignUpRequestValidator
    {
        return $this->signUpRequestValidator;
    }

    public function signUpController(): SignUpController
    {
        return $this->signUpController;
    }

    // SINGLETON SPECIFIC FUNCTIONS

    /**
     * Singleton cloning is forbidden.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton deserialization is forbidden.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot deserialize singleton");
    }

    public static function instance(): LoginConfiguration
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
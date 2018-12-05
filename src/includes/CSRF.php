<?php

require_once 'Utils.php';

class CSRF
{
    const SESSION_FIELD_NAME = 'csrf';
    const FORM_FIELD_NAME = '_csrf';

    /**
     * Renew the CSRF token
     */
    public static function renewToken()
    {
        $_SESSION[self::SESSION_FIELD_NAME] = Utils::generateRandomToken();
    }

    /**
     * Get the current CSRF token from the session or renew it
     *
     * @return string
     */
    public static function getToken()
    {
        if (!isset($_SESSION[self::SESSION_FIELD_NAME])) {
            self::renewToken();
        }

        return $_SESSION[self::SESSION_FIELD_NAME];
    }

    /**
     * Validate the CSRF token against the one in the session
     *
     * @param string $token
     * @return bool
     */
    public static function validateToken($token)
    {
        return hash_equals($token, self::getToken());
    }

    /**
     * Get a form field that contains the CSRF token
     *
     * @return string
     */
    public static function getFormField()
    {
        return sprintf('<input type="hidden" name="%s" value="%s" />', self::FORM_FIELD_NAME, self::getToken());
    }

    /**
     * Validates the csrf token from the given form data against the current one
     *
     * @param array $formData Form data as array like $_POST or $_GET
     * @return bool
     */
    public static function validateForm(array $formData)
    {
        return isset($formData[self::FORM_FIELD_NAME]) && self::validateToken($formData[self::FORM_FIELD_NAME]);
    }

    /**
     * Expects a valid CSRF token in GET or POST form data and throws an exception if none is present or invalid
     *
     * @throws Exception
     */
    public static function expectValidTokenInRequest()
    {
        if (!self::validateForm($_GET) && !self::validateForm($_POST)) {
            throw new Exception('Invalid CSRF token!');
        }
    }
}
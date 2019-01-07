<?php

class Checkout
{
    const STEP_ADDRESS = 0;
    const STEP_PAYMENT = 1;
    const STEP_CONFIRM = 2;

    const SESSION_FIELD = 'checkout';

    static function reset()
    {
        $_SESSION[self::SESSION_FIELD] = array('step' => self::STEP_ADDRESS);
    }

    static function getData()
    {
        return $_SESSION[self::SESSION_FIELD];
    }

    static function setData($data)
    {
        $_SESSION[self::SESSION_FIELD] = $data;
    }
}
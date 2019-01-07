<?php
class Utils
{
    public static function generateRandomToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    public static function formatPrice($price)
    {
        return number_format($price / 100, 2) . " €";
    }

    public static function escapeHtml($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }
}
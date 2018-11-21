<?php
    class Utils {
        public static function generateRandomToken($length = 32) {
            return bin2hex(random_bytes($length));
        }
    }
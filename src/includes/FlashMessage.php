<?php

class FlashMessage
{
    const SEVERITY_NEUTRAL = -1;
    const SEVERITY_SUCCESS = 0;
    const SEVERITY_WARNING = 1;
    const SEVERITY_ERROR = 2;

    /**
     * Reset all flash messages
     */
    private static function reset()
    {
        $_SESSION['flash_messages'] = [
            self::SEVERITY_NEUTRAL => [],
            self::SEVERITY_SUCCESS => [],
            self::SEVERITY_WARNING => [],
            self::SEVERITY_ERROR => []
        ];
    }

    /**
     * Add a flash message to the session
     *
     * @param string $message
     * @param int $severity
     */
    public static function addMessage($message, $severity = self::SEVERITY_NEUTRAL)
    {
        if (!isset($_SESSION['flash_messages'])) {
            self::reset();
        }
        $_SESSION['flash_messages'][$severity][] = $message;
    }

    /**
     * Indicate if any messages are in the session
     *
     * @return bool
     */
    public static function hasMessages()
    {
        if (!isset($_SESSION['flash_messages'])) {
            return false;
        }
        foreach ($_SESSION['flash_messages'] as $messages) {
            if (count($messages)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return all messages in the container and flush it
     *
     * @return array
     */
    public static function flush()
    {
        if (!isset($_SESSION['flash_messages'])) {
            return [];
        }
        $messages = $_SESSION['flash_messages'];
        self::reset();
        return $messages;
    }
}
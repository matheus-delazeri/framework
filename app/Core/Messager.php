<?php

namespace App\Core;

/** Singleton class */
class Messager extends Singleton {

    /**
     * Add new message to session
     *
     * @param string $message
     * @return void
     */
    public static function add(string $message): void {
        if (!isset($_SESSION)) session_start();

        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = [];
        }

        $_SESSION['messages'][] = $message;
    }

    /**
     * Return all session messages
     *
     * @return array
     */
    public static function getAll(): array {
        return $_SESSION['messages'] ?? [];
    }

    /**
     * Return all session messages and clear the messages
     * from session
     *
     * @return array
     */
    public static function extractAll(): array {
        $messages = self::getAll();
        self::clear();

        return $messages;
    }

    /**
     * Clear all current messages in session
     *
     * @return void
     */
    public static function clear(): void {
        $_SESSION['messages'] = [];
    }
}
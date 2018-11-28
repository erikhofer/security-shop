<?php
require_once 'DatabaseConnection.php';

class User
{
    public static function findUserById($id)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $success = $stmt->execute();
        if ($success && $user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $user;
        }
        return null;
    }

    public static function createUser(array $data)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('INSERT INTO users(firstname, lastname, address, email, password) VALUES (:firstname, :lastname, :address, :email, :password)');
        $success = $stmt->execute([
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':email' => $data['email'],
            ':password' => self::createPasswordHash($data['password']),
            ':address' => $data['address'],
        ]);
        return $success && $stmt->rowCount() === 1;
    }

    public static function createPasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function comparePassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function login($email, $password)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $success = $stmt->execute([
            ':email' => $email
        ]);
        if ($success && $user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (self::comparePassword($password, $user['password'])) {
                self::rehashPasswordIfNeeded($user, $password);
                self::attachBasketItemsToUser($user);
                return $user;
            }
        }
        return null;
    }

    private static function rehashPasswordIfNeeded($user, $password)
    {
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $db = DatabaseConnection::getInstance();
            $stmt = $db->prepare('UPDATE users SET password = :password WHERE id = :id');
            $success = $stmt->execute([
                ':id' => $user['id'],
                ':password' => self::createPasswordHash($password)
            ]);
            return $success && $stmt->rowCount() === 1;
        }
        return false;
    }

    public static function startSession()
    {
        session_start();
        if (self::isLoggedIn()) {
            $user = self::findUserById($_SESSION['user_id']);
            if ($user) {
                self::startUserSession($user);
                return;
            }
        }
        self::startAnonymousSession();
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function startAnonymousSession()
    {
        unset($_SESSION['user_id']);
    }

    public static function startUserSession($userData)
    {
        if (!self::isLoggedIn()) {
            $_SESSION['user_id'] = $userData['id'];
        }
    }

    public static function logout()
    {
        unset($_SESSION['user_id']);
    }

    public static function attachBasketItemsToUser($user) {
        if(isset($_COOKIE['basket_id'])) {
            $user_id = $user['id'];
            $db = DatabaseConnection::getInstance();

            $stmt = $db->prepare('SELECT * FROM basket_positions WHERE user_id = :user_id');
            $success = $stmt->execute([
                'user_id' => $user_id
            ]);
            if($success && $stmt->rowCount() === 0) {
                $basket_id = $_COOKIE['basket_id'];

                $stmt = $db->prepare('UPDATE basket_positions SET user_id = :user_id, basket_id = NULL WHERE basket_id = :basket_id');
                $success = $stmt->execute([
                    'user_id' => $user_id,
                    'basket_id' => $basket_id
                ]);
            }
        }
    }
}
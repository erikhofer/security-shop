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
        if ($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row;
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
        return password_hash($password, PASSWORD_BCRYPT);
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
        if ($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (self::comparePassword($password, $row['password'])) {
                return $row;
            }
        }
        return null;
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
}
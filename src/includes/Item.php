<?php
    require_once 'DatabaseConnection.php';

    class Item
    {
        public static function getItems() {
            $db = DatabaseConnection::getInstance();
            $stmt = $db->query('SELECT * FROM products');
            $success = $stmt->execute();
            
            if($success)
                return $stmt->fetchall();
            return null;
        }

        public static function getItemById($id) {
            $db = DatabaseConnection::getInstance();
            $stmt = $db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
            $success = $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row;
            }
            return null;
        }

        public static function reduceStock($id) {
            $db = DatabaseConnection::getInstance();
            $stmt = $db->prepare('UPDATE products SET stock = (stock - 1) WHERE id = :id');
            $success = $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if($success) {
                $success = $stmt->execute();
                return $success;
            }
            return false;
        }
    }
?>
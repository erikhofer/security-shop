<?php
require_once 'DatabaseConnection.php';

class Order
{
    public static function getOrders() {
        $db = DatabaseConnection::getInstance();

        if (isset($_SESSION["user_id"])) {
            $stmt = $db->prepare('SELECT id, address, payment_method, date FROM orders WHERE user_id = :user_id');
            $success = $stmt->execute([
                ':user_id' => $_SESSION["user_id"]
            ]);
            if($success) {
                return $stmt->fetchall();
            }
        }
        return null;
    }

    public static function getOrderData($id) {
        $db = DatabaseConnection::getInstance();

        if(isset($_SESSION["user_id"])) {
            $stmt = $db->prepare('SELECT product_id, quantity, price FROM order_positions WHERE order_id = :order_id');
            $success = $stmt->execute([
                ':order_id' => $id
            ]);
            if($success) {
                return $stmt->fetchall();
            }
        }
    }
}
?>
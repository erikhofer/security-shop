<?php
require_once 'DatabaseConnection.php';
require_once 'Utils.php';

class Item
{
    public static function getItems()
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->query('SELECT * FROM products');
        $success = $stmt->execute();

        if ($success)
            return $stmt->fetchall();
        return null;
    }

    public static function getItemById($id)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $success = $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row;
        }
        return null;
    }

    public static function getBasketItemsByUser($user_id)
    {
        $db = DatabaseConnection::getInstance();

        if (isset($_SESSION["user_id"])) {
            $stmt = $db->prepare('SELECT products.id as id, products.name as name, basket_positions.quantity as quantity FROM products, basket_positions WHERE products.id = basket_positions.product_id AND basket_positions.user_id = :user_id');
            $success = $stmt->execute([
                ':user_id' => $_SESSION["user_id"]
            ]);
            if ($success) {
                return $stmt->fetchall();
            }
        }
        return false;
    }

    public static function getBasketItemsByBasket($basket_id)
    {
        $db = DatabaseConnection::getInstance();

        $stmt = $db->prepare('SELECT products.id as id, products.name as name, basket_positions.quantity as quantity FROM products, basket_positions WHERE products.id = basket_positions.product_id AND basket_positions.basket_id = :basket_id');
        $success = $stmt->execute([
            ':basket_id' => $basket_id
        ]);
        if ($success) {
            return $stmt->fetchall();
        }
        return false;
    }

    public static function getBasketItemsForCurrentSession()
    {
        if (isset($_SESSION["user_id"])) {
            return Item::getBasketItemsByUser($_SESSION["user_id"]);
        } else if (isset($_COOKIE['basket_id'])) {
            return Item::getBasketItemsByBasket($_COOKIE["basket_id"]);
        }
        return array();
    }

    public static function putIntoBasket($id, $amount)
    {
        $db = DatabaseConnection::getInstance();
        $userContext = self::getUserContext();

        $stmt = $db->prepare(self::createQuery("SELECT * FROM basket_positions WHERE product_id = :id AND %userContext% = :fieldValue"));
        $success = $stmt->execute([
            ':id' => $id,
            ':fieldValue' => $userContext
        ]);
        if(!$success) {
            return false;
        } else {
            if($stmt->rowCount() > 0) {
                $stmt = $db->prepare(self::createQuery('UPDATE basket_positions SET quantity = (quantity + :amount) WHERE product_id = :id AND %userContext% = :fieldValue'));
                $success = $stmt->execute([
                    ':amount' => $amount,
                    ':id' => $id,
                    ':fieldValue' => $userContext
                ]);
                if(!$success) {
                    return false;
                }
            } else {
                $stmt = $db->prepare(self::createQuery('INSERT INTO basket_positions (product_id, quantity, %userContext%) VALUES (:id, :amount, :fieldValue)'));
                $success = $stmt->execute([
                    ':id' => $id,
                    ':amount' => $amount,
                    ':fieldValue' => $userContext
                ]);
                if(!$success) {
                    return false;
                }
            }
        }
    }

    private static function createQuery($query) {
        return str_replace("%userContext%", isset($_SESSION["user_id"]) ? "user_id" : "basket_id", $query);
    }

    public static function updateBasket($id, $amount)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare(self::createQuery("UPDATE basket_positions SET quantity = :quantity WHERE product_id = :id AND %userContext% = :fieldValue"));

        $success = $stmt->execute([
            ':quantity' => $amount,
            ':id' => $id,
            ':fieldValue' => self::getUserContext()
        ]);
        return $success;
    }

    public static function getUserContext() {
        if(isset($_SESSION["user_id"])) {
            return $_SESSION["user_id"];
        } else {
            if(isset($_COOKIE["basket_id"])) {
                return $_COOKIE["basket_id"];
            } else {
                $id = Utils::generateRandomToken();
                setcookie("basket_id", $id);
                return $id;
            }
        }
    }

    public static function removeFromBasket($id)
    {
        $db = DatabaseConnection::getInstance();

        $stmt = $db->prepare(self::createQuery('DELETE FROM basket_positions WHERE product_id = :id AND %userContext% = :fieldValue'));
        $success = $stmt->execute([
            ':id' => $id,
            ':fieldValue' => self::getUserContext()
        ]);
        return $success;
    }

    public static function reduceStock($id, $amount)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('UPDATE products SET stock = (stock - :amount) WHERE id = :id');
        $success = $stmt->execute([
            ':amount' => $amount,
            ':id' => $id
        ]);
        return $success;
    }

    public static function increaseStock($id, $amount)
    {
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare('UPDATE products SET stock = (stock + :amount) WHERE id = :id');
        $success = $stmt->execute([
            ':amount' => $amount,
            ':id' => $id
        ]);
        return $success;

    }
}
?>
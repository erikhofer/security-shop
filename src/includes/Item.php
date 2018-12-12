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

        if (isset($_SESSION["user_id"])) {
            $stmt = $db->prepare('SELECT * FROM basket_positions WHERE product_id = :id AND user_id = :user_id');
            $success = $stmt->execute([
                ':id' => $id,
                ':user_id' => $_SESSION["user_id"]
            ]);
            if (!$success) {
                return false;
            } else {
                if ($stmt->rowCount() > 0) {
                    $stmt = $db->prepare('UPDATE basket_positions SET quantity = (quantity + :amount) WHERE product_id = :id AND user_id = :user_id');
                    $success = $stmt->execute([
                        ':amount' => $amount,
                        ':id' => $id,
                        ':user_id' => $_SESSION["user_id"]
                    ]);
                    if (!$success) {
                        return false;
                    }
                } else {
                    $stmt = $db->prepare('INSERT INTO basket_positions (product_id, quantity, user_id) VALUES (:id, :amount, :user_id)');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':amount' => $amount,
                        ':user_id' => $_SESSION["user_id"]
                    ]);
                    if (!$success) {
                        return false;
                    }
                }
            }
            return $success;
        } else {
            $basket_id = isset($_COOKIE["basket_id"]) ? $_COOKIE["basket_id"] : Utils::generateRandomToken();
            if (!isset($_COOKIE["basket_id"])) {
                $basket_id = Utils::generateRandomToken();
                setcookie("basket_id", $basket_id, time() + (60 * 60 * 24 * 30));
            } else {
                $basket_id = $_COOKIE["basket_id"];
            }

            $stmt = $db->prepare('SELECT * FROM basket_positions WHERE product_id = :id AND basket_id = :basket_id');
            $success = $stmt->execute([
                ':id' => $id,
                ':basket_id' => $basket_id
            ]);
            if (!$success) {
                return false;
            } else {
                if ($stmt->rowCount() > 0) {
                    $stmt = $db->prepare('UPDATE basket_positions SET quantity = (quantity + :amount) WHERE product_id = :id AND basket_id = :basket_id');
                    $success = $stmt->execute([
                        ':amount' => $amount,
                        ':id' => $id,
                        ':basket_id' => $basket_id
                    ]);
                    if (!$success) {
                        return false;
                    }
                } else {
                    $stmt = $db->prepare('INSERT INTO basket_positions (product_id, quantity, basket_id) VALUES (:id, :amount, :basket_id)');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':amount' => $amount,
                        ':basket_id' => $basket_id
                    ]);
                    if (!$success) {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    public static function removeFromBasket($id)
    {
        $db = DatabaseConnection::getInstance();

        if (isset($_SESSION["user_id"])) {
            $stmt = $db->prepare('SELECT * FROM basket_positions WHERE product_id = :id AND user_id = :user_id');
            $success = $stmt->execute([
                ':id' => $id,
                ':user_id' => $_SESSION["user_id"]
            ]);
            if ($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["quantity"] > 1) {
                    $stmt = $db->prepare('UPDATE basket_positions SET quantity = (quantity - 1) WHERE product_id = :id AND user_id = :user_id');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':user_id' => $_SESSION["user_id"]
                    ]);
                    return $success;
                } else {
                    $stmt = $db->prepare('DELETE * FROM basket_positions WHERE product_id = :id AND user_id = :user_id');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':user_id' => $_SESSION["user_id"]
                    ]);
                    return $success;
                }
            }
        } else if (isset($_COOKIE['basket_id'])) {
            $basket_id = $_COOKIE['basket_id'];
            $stmt = $db->prepare('SELECT * FROM basket_positions WHERE product_id = :id AND basket_id = :basket_id');
            $success = $stmt->execute([
                ':id' => $id,
                ':basket_id' => $basket_id
            ]);
            if ($success && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["quantity"] > 1) {
                    $stmt = $db->prepare('UPDATE basket_positions SET quantity = (quantity - 1) WHERE product_id = :id AND basket_id = :basket_id');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':basket_id' => $basket_id
                    ]);
                    return $success;
                } else {
                    $stmt = $db->prepare('DELETE FROM basket_positions WHERE product_id = :id AND basket_id = :basket_id');
                    $success = $stmt->execute([
                        ':id' => $id,
                        ':basket_id' => $basket_id
                    ]);
                    return $success;
                }
            }
        }
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
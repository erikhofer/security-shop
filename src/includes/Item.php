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

        public static function putIntoBasket($id) {
            if(!static::reduceStock($id)) {
                return false;
            }

            $db = DatabaseConnection::getInstance();

            if(isset($_SESSION["user_id"])) {
                $stmt = $db->prepare('SELECT * FROM basket_positions WHERE product_id = :id AND user_id = :user_id');
                $success = $stmt->execute([
                    ':id' => $id,
                    ':user_id' => $_SESSION["user_id"]
                ]);
                if(!$success) {
                    static::increaseStock($id);
                } else {
                    if($stmt->rowCount() > 0) {
                        $stmt = $db->prepare('UPDATE basket_positions SET quantity = (quantity + 1) WHERE product_id = :id AND user_id = :user_id');
                        $success = $stmt->execute([
                            ':id' => $id,
                            ':user_id' => $_SESSION["user_id"]
                        ]);
                        if(!$success) {
                            static::increaseStock($id);
                            return false;
                        }
                    } else {
                        $stmt = $db->prepare('INSERT INTO basket_positions (product_id, quantity, user_id) VALUES (:id, 1, :user_id)');
                        $success = $stmt->execute([
                            ':id' => $id,
                            ':user_id' => $_SESSION["user_id"]
                        ]);
                        if(!$success) {
                            static::increaseStock($id);
                            return false;
                        }
                    }
                }
                return $success;
            } else {
                echo "NYI: adding items to basket without logged in user";
                //TODO: implement adding items to basket without logged in user
            }
            
            return false;
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

        public static function increaseStock($id) {
            $db = DatabaseConnection::getInstance();
            $stmt = $db->prepare('UPDATE products SET stock = (stock + 1) WHERE id = :id');
            $success = $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if($success) {
                $success = $stmt->execute();
                return $success;
            }
            return false;
        }
    }
?>
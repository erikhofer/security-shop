<?php

class Checkout
{
    const STEP_ADDRESS = 0;
    const STEP_PAYMENT = 1;
    const STEP_CONFIRM = 2;

    const SESSION_FIELD = 'checkout';

    static function reset()
    {
        $_SESSION[self::SESSION_FIELD] = array('step' => self::STEP_ADDRESS);
    }

    static function getData()
    {
        return $_SESSION[self::SESSION_FIELD];
    }

    static function setData($data)
    {
        $_SESSION[self::SESSION_FIELD] = $data;
    }

    static function placeOrder($payment_method = null) {
        try {
            $db = DatabaseConnection::getInstance();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->beginTransaction();

            $data = self::getData();
            $user_id = User::getUserData()['id'];
            $items = Item::getBasketItemsForCurrentSession();

            foreach($items as $item) {
                $stock = Item::getStock($item['id']);
                if($stock < $item['quantity']) {
                    throw new Exception("Insufficient stock for article: " . $item['name']);
                }
            }

            if($payment_method == null && isset($data['creditCardInstitute'])) {
                $institute = $data['creditCardInstitute'];
                $number = $data['cardnumber'];
                $payment = $institute . " *" . substr($number, -3);
            } else {
                $payment = $payment_method;
                // additional payment methods could be handled here
            }


            $stmt = $db->prepare('INSERT INTO orders (user_id, address, payment_method, date) VALUES (:user_id, :address, :payment, :date)');
            $stmt->execute([
                ':user_id' => $user_id,
                ':address' => $data['address'],
                ':payment' => $payment,
                ':date' => date('y-m-d H:i:s')
            ]);
            $order_id = $db->lastInsertId();

            foreach($items as $item) {
                $stmt = $db->prepare('INSERT INTO order_positions VALUES (:product_id, :quantity, :price, :order_id)');
                $stmt->execute([
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                    ':price' => Item::getPrice($item['id']),
                    ':order_id' => $order_id
                ]);
                Item::reduceStock($item['id'], $item['quantity']);
                Item::removeFromBasket($item['id']);

                $stmt = $db->prepare('DELETE FROM basket_positions WHERE user_id = :user_id AND product_id = :product_id');
                $stmt->execute([
                    ':user_id' => $_SESSION['user_id'],
                    ':product_id' => $item['id']
                ]);
            }
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
        return null;
    }
}
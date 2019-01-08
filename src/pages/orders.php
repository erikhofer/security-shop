<?php
    require_once 'includes/Order.php';
    require_once 'includes/Item.php';
?>

<h1>My Orders</h1>
<a href="javascript:window.print();">Print</a>
<hr>

<?php 
    $user = User::getUserData();

    if ($user === null) {
        FlashMessage::addMessage('You have to be logged in to see any orders!', FlashMessage::SEVERITY_ERROR);
        Routing::redirect('home');
    }

    $orders = Order::getOrders();
    if($orders === null) {
        echo("Orders could not be loaded");
    } else {
        foreach($orders as $order) {
            $orderData = Order::getOrderData($order['id']);
            ?>
                <table>
                    <tr><td>Date: </td><td><?= $order['date'] ?></td></tr>
                    <tr><td>Address: </td><td><?= $order['address'] ?></td></tr>
                    <tr><td>Payment Method: </td><td><?= $order['payment_method'] ?></td></tr>
                </table>
                <br>
            <?php
            foreach($orderData as $data) {
                ?>
                <table>
                    <tr><td>Product Name: </td><td><?= Item::getName($data['product_id']) ?></td></tr>
                    <tr><td>Quantity: </td><td><?= $data['quantity'] ?></td></tr>
                    <tr><td>Price: </td><td><?= $data['price'] ?></td></tr>
                </table>
                <br>
                <?php
            }
            ?>
            <hr>
            <?php
        }
    }
?>
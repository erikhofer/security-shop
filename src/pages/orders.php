<?php
    require_once 'includes/Order.php';
    require_once 'includes/Item.php';
?>

<h1>My Orders</h1>
<div class="text-right">
    <a class="btn btn-primary" href="javascript:window.print();">Print</a>
</div>
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
            $totalPrice = 0;
            ?>
            <dl>
                <dt>Date</dt>
                <dd><?= $order['date'] ?></dd>
                <dt>Address</dt>
                <dd><?= Utils::escapeHtml($order['address']) ?></dd>
                <dt>Payment Method</dt>
                <dd><?= $order['payment_method'] ?></dd>
            </dl>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>qty</th>
                    <th>price</th>
                    <th>total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($orderData as $item):
                    ?>
                    <tr>
                        <td><?= $item['product_id']; ?></td>
                        <td><?= Item::getName($item['product_id']) ?></td>
                        <td><?= $item['quantity']; ?></td>
                        <td><?= Utils::formatPrice($item['price']); ?></td>
                        <td><?= Utils::formatPrice($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php
                    $totalPrice += ($item['quantity'] * $item['price']);
                endforeach;
                ?>
                <tr>
                    <td colspan="4"></td>
                    <td class="font-weight-bold"><?= Utils::formatPrice($totalPrice) ?></td>
                </tr>
                </tbody>
            </table>
            <hr>
            <?php
        }
    }
?>
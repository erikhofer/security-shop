<?php
require_once 'includes/Item.php';
?>

<h1>Your basket</h1>

<?php
if ($_POST && isset($_POST["remove"]) && isset($_POST["id"])) {
    Item::removeFromBasket($_POST["id"]);
}

if ($_POST && isset($_POST["update"]) && isset($_POST["id"])) {
    Item::updateBasket($_POST["id"], $_POST["amount"]);
}

$basketItems = Item::getBasketItemsForCurrentSession();
if (count($basketItems) > 0) :
?>
    <table class="table table-striped">
        <colgroup>
            <col width="10%"/>
            <col width="60%"/>
            <col width="30%"/>
        </colgroup>
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>qty</th>
    </tr>
    </thead>
    <tbody>
<?php
    foreach ($basketItems as $item) :
        ?>
    <tr>
        <td><?= $item['id']; ?></td>
        <td><?= $item["name"]; ?></td>
        <td>
            <form action="<?= Routing::getUrlToSite('basket'); ?>" method="post">
                <div class="input-group">
                    <input type="number" class="form-control" id="amount" value="<?php echo $item["quantity"] ?>"
                           min="1" name="amount"/>
                    <div class="input-group-append">
                        <button type="submit" name="update" class="btn btn-primary">Update amount</button>
                        <button type="submit" name="remove" class="btn btn-warning"><i class="far fa-trash-alt"></i></button>
                        <input type="hidden" name="id" value="<?php echo $item["id"]; ?>">
                    </div>
                </div>
            </form>
        </td>
    </tr>
    <?php
    endforeach;
    ?>
    </tbody>
    </table>
<p class="text-right"><a class="btn btn-primary btn-lg" href="<?= Routing::getUrlToSite("checkout") ?>">Checkout</a></p>
<?php
else :
?>
    <p class="alert alert-info">There are no items in your basket.</p>
    <p class="text-center"><a class="btn btn-primary btn-lg" href="<?= Routing::getUrlToSite("home") ?>">Browse products</a></p>
<?php
endif;
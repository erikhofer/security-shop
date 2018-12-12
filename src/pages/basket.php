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
    foreach ($basketItems as $item) :
    echo $item["id"] . ": " . $item["name"] . " (" . $item["quantity"] . ")";
?>
    <br/>
    <form action="<?= Routing::getUrlToSite('basket'); ?>" method="post">
        <input type="number" class="form-control mr-sm-2" id="amount" value="<?php echo $item["quantity"] ?>" min="1" name="amount"/>
        <button type="submit" name="remove" class="btn btn-primary"><i class="far fa-trash-alt"></i></button> <button type="submit" name="update" class="btn btn-primary">Update amount</button>
        <input type="hidden" name="id" value="<?php echo $item["id"]; ?>">
    </form>
<?php
endforeach;
?>
<p class="text-center"><a class="btn btn-primary btn-lg" href="<?= Routing::getUrlToSite("checkout") ?>">Checkout</a></p>
<?php
else :
?>
    <p class="alert alert-info">There are no items in your basket.</p>
    <p class="text-center"><a class="btn btn-primary btn-lg" href="<?= Routing::getUrlToSite("home") ?>">Browse products</a></p>
<?php 
endif;
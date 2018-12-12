<?php

require_once 'includes/Item.php';

if ($_POST && isset($_POST["add"]) && isset($_POST["id"]) && isset($_POST["amount"])) {
    Item::putIntoBasket($_POST["id"], $_POST["amount"]);
    Routing::redirect("home");
    exit;
}

$items = Item::getItems();

?>
<h1>Welcome to the Security Shop!</h1>

<?php for ($i = 0; $i <= (int)(count($items) / 3); $i++) : ?>
<div class="card-deck">
    <?php if ($i == (int)(count($items) / 3)) {
        $max = count($items) % 3;
    } else {
        $max = 3;
    }
    for ($j = 0; $j < $max; $j++) : $item = $items[$i * 3 + $j]; ?>
    <div class="card" style="width: 18rem;">
        <img class="card-img-top" src="assets/img/default.jpg" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title"><?php echo $item['name'] ?></h5>
            <p class="card-text"><?php echo $item['description'] ?></p>
            <h3><span class="badge badge-info"><?php echo Utils::formatPrice($item['price']); ?></span></h3>
        </div>
        <div class="card-footer">
            <form class="form-inline add-to-basket" action="<?= Routing::getUrlToSite('home'); ?>" method="post">
                <input type="number" class="form-control mr-sm-2" id="amount" value="1" min="1" name="amount"/>
                <button type="submit" name="add" class="btn btn-primary"><i class="fas fa-cart-plus"></i> Add to basket</button>
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            </form>
        </div>
    </div>
    <?php endfor; ?>
</div>
<?php endfor; ?>
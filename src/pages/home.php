<?php

require_once 'includes/Item.php';

if ($_POST && isset($_POST["add"]) && isset($_POST["id"])) {
    Item::putIntoBasket($_POST["id"]);
}

$items = Item::getItems();

?>
<h1>Welcome to the Security Shop!</h1>

<?php for ($i = 0; $i < 3; $i++) : ?>
<div class="card-deck">
    <?php for ($j = 0; $j < 3; $j++) : ?>
    <div class="card" style="width: 18rem;">
        <img class="card-img-top" src="assets/img/default.jpg" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <h3><span class="badge badge-info">1337 €</span></h3>
        </div>
        <div class="card-footer">
            <form class="form-inline add-to-basket">
                <input type="number" class="form-control mr-sm-2" id="amount" value="1" min="1" />
                <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus"></i> Add to basket</button>
            </form>
        </div>
    </div>
    <?php endfor; ?>
</div>
<?php endfor; ?>

<?php
foreach (Item::getItems() as $item) {
    ?>
            <div>
                <b>
                    <?php
                    if ($item["stock"] > 0) {
                        echo $item["id"] . ": " . $item["name"] . " (" . $item["stock"] . " on stock)";
                        ?>
                                <br/>
                                <form action="<?= Routing::getUrlToSite('home'); ?>" method="post">
                                    <button type="submit" name="add" class="btn btn-primary">+</button>  
                                    <input type="hidden" name="id" value="<?php echo $item["id"]; ?>">
                                </form>
                            <?php

                        } else {
                            echo $item["id"] . ": " . $item["name"] . " (currently not available)";
                        }
                        ?>
                </b>
                
            </div>
        <?php
        ?><br><br><?php

                }

                ?>
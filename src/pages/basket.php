<?php
    require_once 'includes/Item.php';
?>

<h1>Your basket</h1>

<?php
    if($_POST && isset($_POST["remove"]) && isset($_POST["id"])){
        Item::removeFromBasket($_POST["id"]);
    }

    if(isset($_SESSION["user_id"])) {
        foreach(Item::getBasketItemsByUser($_SESSION["user_id"]) as $item) {
            echo $item["id"] . ": " . $item["name"] . " (" . $item["quantity"] . ")";
            ?>
            <br/>
            <form action="<?= Routing::getUrlToSite('basket'); ?>" method="post">
                <button type="submit" name="remove" class="btn btn-primary">-</button>  
                <input type="hidden" name="id" value="<?php echo $item["id"]; ?>">
            </form>
            <?php
        }
    }
?>
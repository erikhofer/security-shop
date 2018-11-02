<?php

require_once 'includes/Item.php';
?>
<h1>Welcome to Security Shop!</h1>

<?php
    if($_POST && isset($_POST["add"]) && isset($_POST["id"])){
        Item::reduceStock($_POST["id"]);
    }

    foreach(Item::getItems() as $item){
        ?>
            <div>
                <b>
                    <?php
                        if($item["stock"] > 0) {
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
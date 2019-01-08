<?php
require_once 'includes/Checkout.php';
require_once 'includes/Utils.php';
require_once 'includes/User.php';
require_once 'includes/CSRF.php';
require_once 'includes/FlashMessage.php';

function getSubmitButtons($step)
{
    ?>
<button type="submit" name="submit" value="back" class="btn btn-default"><?= $step === 0 ? 'Cancel' : 'Back' ?></button>
<button type="submit" name="submit" value="continue" class="btn btn-primary"><?= $step === 2 ? 'Place Order' : 'Continue' ?></button>
<?php

}

?>

<h1>Checkout</h1>

<?php

$data = Checkout::getData();
$user = User::getUserData();

if ($user === null) {
    FlashMessage::addMessage('You have to be logged in to proceed the checkout!', FlashMessage::SEVERITY_ERROR);
    Routing::redirect('basket');
}

if (isset($_POST['submit'])) {
    CSRF::expectValidTokenInRequest();
    if ($_POST['submit'] === 'continue') {
        if ($data['step'] === Checkout::STEP_ADDRESS) {
            if (isset($_POST['address']) && !empty(trim($_POST['address']))) {
                $data['address'] = trim($_POST['address']);
                $data['step'] = Checkout::STEP_PAYMENT;
                Checkout::setData($data);
            } else {
                FlashMessage::addMessage('Shipping address is required!', FlashMessage::SEVERITY_ERROR);
                Routing::redirect('checkout');
            }
        } elseif ($data['step'] === Checkout::STEP_PAYMENT) {
            if (isset($_POST['creditCardInstitute']) && !empty(trim($_POST['creditCardInstitute']))) {
                $data['creditCardInstitute'] = trim($_POST['creditCardInstitute']);
            } else {
                FlashMessage::addMessage('Credit card institute is required!', FlashMessage::SEVERITY_ERROR);
                $data['step'] = Checkout::STEP_PAYMENT;
                Routing::redirect('checkout');
            }
            if (isset($_POST['cardname']) && !empty(trim($_POST['cardname']))) {
                $data['cardname'] = trim($_POST['cardname']);
            } else {
                $data['step'] = Checkout::STEP_PAYMENT;
                FlashMessage::addMessage('Name on Card is required!', FlashMessage::SEVERITY_ERROR);
                Routing::redirect('checkout');
            }
            if (isset($_POST['cardnumber']) && !empty(trim($_POST['cardnumber']))) {
                $data['cardnumber'] = trim($_POST['cardnumber']);
            } else {
                FlashMessage::addMessage('Credit card number is required!', FlashMessage::SEVERITY_ERROR);
                $data['step'] = Checkout::STEP_PAYMENT;
                Routing::redirect('checkout');
            }
            if (isset($_POST['expmonth']) && !empty(trim($_POST['expmonth']))) {
                $data['expmonth'] = trim($_POST['expmonth']);
            } else {
                FlashMessage::addMessage('Exp Month is required!', FlashMessage::SEVERITY_ERROR);
                $data['step'] = Checkout::STEP_PAYMENT;
                Routing::redirect('checkout');
            }
            if (isset($_POST['expyear']) && !empty(trim($_POST['expyear']))) {
                $data['expyear'] = trim($_POST['expyear']);

            } else {
                FlashMessage::addMessage('Exp Year is required!', FlashMessage::SEVERITY_ERROR);
                Routing::redirect('checkout');
            }
            if (isset($_POST['cvv']) && !empty(trim($_POST['cvv']))) {
                $data['cvv'] = trim($_POST['cvv']);
            } else {
                FlashMessage::addMessage('CVV is required!', FlashMessage::SEVERITY_ERROR);
                Routing::redirect('checkout');
            }
            if (FlashMessage::hasMessages()) {
                Routing::redirect('checkout');
            } else {
                $data['step'] = Checkout::STEP_CONFIRM;
                Checkout::setData($data);
            }

        } elseif ($data['step'] === Checkout::STEP_CONFIRM) {
            $placeOrder = Checkout::placeOrder();
            if ($placeOrder == null) {
                FlashMessage::addMessage('Your order has successfully been placed!', FlashMessage::SEVERITY_SUCCESS);
            } else {
                FlashMessage::addMessage('Your order could not be placed. Error: ' . $placeOrder, FlashMessage::SEVERITY_ERROR);
            }
            Checkout::reset();
            Routing::redirect('home');
        }
    } elseif ($_POST['submit'] === 'back') {
        if ($data['step'] === 0) {
            Checkout::reset();
            Routing::redirect('basket');
        } else {
            $data['step']--;
            Checkout::setData($data);
        }
    }
}
if ($data['step'] === Checkout::STEP_ADDRESS) :
?>

<form action="<?= Routing::getUrlToSite('checkout'); ?>" method="post">
    <h2>Shipping address</h2>
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="differentAddress" />
        <label class="form-check-label" for="differentAddress">Deliver to a different address</label>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <input required type="text" name="address" id="address" class="form-control" value="<?= Utils::escapeHtml($user['address']); ?>" readonly>
    </div>
    <?= CSRF::getFormField();
    getSubmitButtons($data['step']); ?>
</form>
<script>
$(() => {
    $('#differentAddress').click(function() {
        $("#address").prop('readonly', !this.checked);
    });
})
</script>

<?php elseif ($data['step'] === Checkout::STEP_PAYMENT) : ?>

<form action="<?= Routing::getUrlToSite('checkout'); ?>" method="post">
    <h2>Credit card</h2>
    <div class="form-group">
        <label><input type="radio" name="creditCardInstitute" value="Mastercard" class="form-control"> Mastercard</Label><br>
        <label><input type="radio" name="creditCardInstitute" value="Visa"class="form-control"> Visa card    </Label><br>
        <label for="cname">Name on Card</label>
        <input type="text" id="cname" name="cardname" placeholder="John More Doe" class="form-control">
        <label for="ccnum">Credit card number</label>
        <input type="text" id="ccnum" name="cardnumber" placeholder="1111-2222-3333-4444" class="form-control">
        <label for="expmonth">Exp Month</label>
        <input type="text" id="expmonth" name="expmonth" placeholder="September" class="form-control">
        <label for="expyear">Exp Year</label>
        <input type="text" id="expyear" name="expyear" placeholder="2018" class="form-control">
        <label for="cvv">CVV</label>
        <input type="text" id="cvv" name="cvv" placeholder="352" class="form-control">
    </div>
    <?= CSRF::getFormField();
    getSubmitButtons($data['step']); ?>
</form>

<?php elseif ($data['step'] === Checkout::STEP_CONFIRM) :

    $basketItems = Item::getBasketItemsForCurrentSession();
?>

<form action="<?= Routing::getUrlToSite('checkout'); ?>" method="post">
<table>
<h2>Shipping Adress<h2>
<tr><td></td><td><?= $data['address'] ?></td></tr>
</table>

<h2>Credit card information</h2>
<table>
<tr><td>Credit card institute: </td><td><?= $data['creditCardInstitute'] ?></td></tr>
<tr><td>Name on Card: </td><td><?= $data['cardname'] ?></td></tr>
<tr><td>Credit card number: </td><td><?= $data['cardnumber'] ?></td></tr>
<tr><td>Exp Month: </td><td><?= $data['expmonth'] ?></td></tr>
<tr><td>Exp Year: </td><td><?= $data['expyear'] ?></td></tr>
<tr><td>CVV: </td><td><?= $data['cvv'] ?></td></tr>
</table>

<h2>Order</h2>
<table>
<tr><th>Product</th><th>Amount</th><th>Price</th></tr>
<?php

$basketItems = Item::getBasketItemsForCurrentSession();

if (count($basketItems) > 0) :
    foreach ($basketItems as $item) :
    echo $item["id"] . ": " . $item["name"] . " (" . $item["quantity"] . ")";
?>
    <br/>
    <form action="<?= Routing::getUrlToSite('basket'); ?>" method="post">
        <input type="number" class="form-control mr-sm-2" id="amount" value="<?php echo $item["quantity"] ?>" min="1" name="amount" readonly/>
    </form>
<?php
endforeach;
?>
<?php 
endif;
?>
</table>
<?= CSRF::getFormField();
getSubmitButtons($data['step']); ?>
</form>

<?php endif; ?>
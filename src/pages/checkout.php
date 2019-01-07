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
<button type="submit" name="submit" value="continue" class="btn btn-primary">Continue</button>
<?php

}

?>

<h1>Checkout</h1>

<?php

$data = Checkout::getData();
$user = User::getUserData();

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
        // todo 
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
        <label for="email">Address</label>
        <input required type="text" name="address" class="form-control" value="<?= Utils::escapeHtml($user['address']); ?>" readonly>
    </div>
    <?= CSRF::getFormField();
    getSubmitButtons($data['step']); ?>
</form>

<?php elseif ($data['step'] === Checkout::STEP_PAYMENT) : ?>

Radio Mastercard / Visa
Nummer und so

<form action="<?= Routing::getUrlToSite('checkout'); ?>" method="post">
    <h2>Credit card</h2>
    <div class="form-group">
        <label for="email">Moin</label>
        <input required type="text" name="address" class="form-control" readonly>
    </div>
    <?= CSRF::getFormField();
    getSubmitButtons($data['step']); ?>
</form>

<?php elseif ($data['step'] === Checkout::STEP_CONFIRM) : ?>

<table>
<tr><td>Lieferadresse auf Englisch</td><td><?php $data['address'] ?></td></tr>
</table>

<h2>Credit card information</h2>
<table>
<tr><td>Type</td><td><?php $data['cc']['type'] ?></td></tr>
<tr><td>Number</td><td><?php $data['cc']['number'] ?></td></tr>
</table>

<h2>Order</h2>
<table>
<tr><th>Product</th><th>Amount</th><th>Price</th></tr>
</table>

<?php endif; ?>
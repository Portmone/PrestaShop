<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
$portmone = Module::getInstanceByName('portmone');
$context = Context::getContext();

if (isset($_REQUEST['RESULT']) && isset($_REQUEST['SHOPORDERNUMBER'])) {

    $shopnumbercount = strpos($_REQUEST['SHOPORDERNUMBER'], "_");
    $cartId = substr($_REQUEST['SHOPORDERNUMBER'], 0, $shopnumbercount);
    $cart = new Cart((int)$cartId);
    $context->cart = $cart;

    if ($cart->id_customer == 0 or $cart->id_address_delivery == 0 or $cart->id_address_invoice == 0 or !$portmone->active) {
        Tools::redirect('index.php');
    }

    $customer = new Customer((int)$cart->id_customer);
    if (!Validate::isLoadedObject($customer)) {
        Tools::redirect('index.php');
    }

    $paymentInfo = $portmone->isPaymentValid();
    $orderId = Db::getInstance()->getValue(
        'SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int)$cartId
    );

    if ($orderId) {
        $order = new Order($orderId);
        $order->setCurrentState(Configuration::get($paymentInfo['status']));
        $order->save();
    } else {
        $portmone->validateOrder($cart->id, Configuration::get($paymentInfo['status']), (float)$_REQUEST['BILL_AMOUNT'], $portmone->displayName, NULL, array(), null, false, $customer->secure_key);
        $order = new Order($portmone->currentOrder);
    }

    if ($paymentInfo['status'] == 'PORTMONE_ERROR') {
        Tools::redirectLink(__PS_BASE_URI__ . 'history');
    } else {
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $order->id_cart . '&id_module=' . $portmone->id . '&id_order=' . $portmone->currentOrder . '&key=' . $customer->secure_key);
    }

} else {
    Tools::redirect('index.php');
}

?>
<?php
    include(dirname(__FILE__).'/../../config/config.inc.php');
    include(dirname(__FILE__).'/../../init.php');
    $context    = Context::getContext();
    $cart       = $context->cart;
    $portmone   = Module::getInstanceByName('portmone');

    if($cart->id_customer==0 OR $cart->id_address_delivery==0 OR $cart->id_address_invoice==0 OR !$portmone->active)Tools::redirect('index.php?controller=order&step=1');

    $authorized = false;
    foreach(Module::getPaymentModules() as $module)if($module['name']=='portmone'){$authorized=true;break;}
    if(!$authorized)die($portmone->getTranslator()->trans('This payment method is not available.', array(), 'Modules.Portmone.Shop'));

    $customer = new Customer((int)$cart->id_customer);
    if(!Validate::isLoadedObject($customer))Tools::redirect('index.php?controller=order&step=1');
    if (isset($_REQUEST['RESULT']) && isset($_REQUEST['SHOPORDERNUMBER'])) {
        $shopnumbercount = strpos($_REQUEST['SHOPORDERNUMBER'], "_");
        $orderId         = substr($_REQUEST['SHOPORDERNUMBER'], 0, $shopnumbercount);
        $paymentInfo     = $portmone->isPaymentValid();

        if ($cart->id != NULL) {
            $portmone->validateOrder($cart->id,Configuration::get($paymentInfo['status']),$_REQUEST['BILL_AMOUNT'],$portmone->displayName,NULL,array(),(int)$currency->id,false,$customer->secure_key);
            $order = new Order($portmone->currentOrder);
        } else {
            $order = new Order(intval($orderId));
            $order->setCurrentState(Configuration::get($paymentInfo['status']));
            $order->save();
        }
        if ($paymentInfo['status'] == 'PORTMONE_ERROR') {
            Tools::redirectLink(__PS_BASE_URI__ . 'history');
        } else {
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$order->id_cart.'&id_module='.$portmone->id.'&id_order='.$portmone->currentOrder.'&key='.$customer->secure_key) ;
        }
    } else {
        Tools::redirect('index.php?controller=order&step=4');
    }
?>
<?php
    include(dirname(__FILE__).'/../../config/config.inc.php');
    include(dirname(__FILE__).'/portmone.php');
    $portmone = new Portmone();

    if (isset($_REQUEST['RESULT']) && isset($_REQUEST['SHOPORDERNUMBER'])) {
        $shopnumbercount = strpos($_REQUEST['SHOPORDERNUMBER'], "_");
        $orderId         = substr($_REQUEST['SHOPORDERNUMBER'], 0, $shopnumbercount);
        $paymentInfo     = $portmone->isPaymentValid();
        $oOrder          = new Order(intval($orderId));
        $oOrder->setCurrentState(Configuration::get($paymentInfo['status']));
        $portmone->massage = $paymentInfo['message'] . ' <br />' . $portmone->lang['number_pay']  . ': ' . $orderId ;
        $oOrder->save();
    } else {
        Tools::redirectLink(__PS_BASE_URI__.'order.php');
    }
?>
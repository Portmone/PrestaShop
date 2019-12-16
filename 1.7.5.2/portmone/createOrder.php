<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/portmone.php');
echo (new Portmone())->createOrder($_POST);
<?php
    include_once(dirname(__FILE__).'/validation.php');
    include_once(dirname(__FILE__).'/../../header.php');
        if(isset($_REQUEST['RESULT']) && $_REQUEST['RESULT'] == '0'){
        ?>
            <h3><?php echo $portmone->massage; ?></h3>
            <br/>
            <p class="cart_navigation">
                <a href="<?= _PS_BASE_URL_.__PS_BASE_URI__ ?>" class="exclusive_large"><?php echo  $portmone->lang['next']; ?></a>
            </p>
        <?php
        } else {
        ?>
            <h3><?php echo $portmone->massage; ?></h3>
            <br/>
            <p class="cart_navigation">
                <a href="<?= _PS_BASE_URL_.__PS_BASE_URI__.'/order?step=3' ?>" class="exclusive_large"><?php echo $portmone->lang['re_payment']; ?></a>
            </p>
            <p class="cart_navigation">
                <a href="<?= _PS_BASE_URL_.__PS_BASE_URI__ ?>" class="exclusive_large"><?php echo $portmone->lang['next']; ?></a>
            </p>
        <?php
        }
    include(dirname(__FILE__).'/../../footer.php');
?>
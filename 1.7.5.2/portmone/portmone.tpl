<style>
    #page-preloader {
        position: fixed;
        display: none;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.95);
        z-index: 100500;
    }

    #page-preloader .spinner {
        display: none;
        width: 300px;
        height: 35px;
        position: absolute;
        left: 41%;
        top: 51%;
        background: url('{$spinner_url}') no-repeat 50% 50%;
        background-size: cover;
        margin: -16px 0 0 -16px;
    }
    .payment-option img {
        max-width: 200px;
    }
</style>
<div id="page-preloader"><span class="spinner"></span></div>
    <div class="row">
        <div class="col-xs-12">
            <script>
                function createOrder() {
                    var $preloader = $('#page-preloader'),
                        $spinner = $preloader.find('.spinner');
                    $.ajax({
                        type: "post",
                        url: '{$createOrder_url}',
                        data: {
                            cart_id:        '{$cart_id}',
                            status_order:   '{$status_order}',
                            bill_amount:    '{$bill_amount}',
                            display_name:   '{$display_name}',
                        },
                        beforeSend: function (data) {
                            $spinner.show();
                            $preloader.show();
                        },
                        success: function(data){
                            data = JSON.parse(data);
                            if(data.shop_order_number !== undefined && data.description !== undefined ) {
                                $('#portmone > [name=shop_order_number]').val(data.shop_order_number);
                                $('#portmone > [name=description]').val(data.description);
                                $('#portmone').submit();
                            } else {
                                $spinner.hide();
                                $preloader.hide();
                            }
                        },
                        error: function (data) {
                            $spinner.hide();
                            $preloader.hide();
                        }
                    });
                }
            </script>
            <p class="payment_module">
                {if $description_user !== '' && $description_user != false}
                <span style="font-size: 14px;">{$description_user}</span>
                <hr><br>
                {/if}
                {if $show_pay_button == 1}
                    <button type="button" class="btn btn-danger btn-block" onclick="createOrder();">{$description}</button>
                {/if}
            </p>
            <form id="portmone" accept-charset="utf-8" method="POST" action="{$action_url}">
                <input type="hidden" name="payee_id" value="{$payee_id}">
                <input type="hidden" name="shop_order_number" value="">
                <input type="hidden" name="bill_amount" value="{$bill_amount}">
                <input type="hidden" name="description" value="{$description}">
                <input type="hidden" name="success_url" value="{$success_url}">
                <input type="hidden" name="failure_url" value="{$failure_url}">
                <input type="hidden" name="preauth_flag" value="{$preauth_flag}">
                <input type="hidden" name="exp_time" value="{$exp_time}">
                <input type="hidden" name="lang" value="{$lang}">
                <input type="hidden" name="encoding" value="UTF-8">
            </form>
        </div>
    </div>
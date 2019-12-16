<?php

/**
 * @name Portmone 1.0.5
 * @description Модуль разработан в компании Portmone предназначен для CMS Prestashop 1.6.1.14
 * @author glib.yuriiev@portmone.me
 * @email support@portmone.com
 * @version 1.0.5
 */

    class Portmone extends PaymentModule {

        const ORDER_PAYED                       = 'PAYED';
        const ORDER_CREATED                     = 'CREATED';
        const ORDER_REJECTED                    = 'REJECTED';
        const ORDER_PREAUTH                     = 'PREAUTH';
        private $_html                          = '';
        private $_postErrors                    = array();
        private $text_data                      = array();
        public $massage                         = '';

        public function __construct(){
            $this->name                         = 'portmone';
            $this->tab                          = 'payments_gateways';
            $this->version                      = '1.0.5';
            $this->ps_versions_compliancy = [
                'min' => '1.6',
                'max' => '1.6'
            ];
            $this->author                       = 'glib.yuriiev@portmone.me';
            $this->logo_img                     = 'img/logo_settings.svg';
            $this->portmone_url                 = 'https://www.portmone.com.ua/';
            $this->portmone_url_gataway         = 'https://www.portmone.com.ua/gateway/';
            $this->currencies_mode              = 'checkbox';
            $this->lang                         = [];
            $this->currencies                   = true;

            $this->text_data = array(
            'display_name'                      => $this->l('Portmone'),
            'description'                       => $this->l('Плати через Portmone'),
            'confirmUninstall'                  => $this->l('Вы уверены, что хотите удалить?'),
            'paid_with_ru'                      => $this->l('Оплачено с помощью Portmone'),
            'paidButNotVerified_with_ru'        => $this->l('Оплачено с помощью Portmone (но не проверено)'),
            'preauth_with_ru'                   => $this->l('Оплачено с помощью Portmone (блокировка средств)'),
            'title_payee'                       => $this->l('Оплата заказа через Portmone'),
            'paid_with_all'                     => $this->l('Paid with Portmone'),
            'paidButNotVerified_with_all'       => $this->l('Paid with Portmone (but not verified)'),
            'preauth_with_all'                  => $this->l('Paid with Portmone (blocking of funds)'),
            'payee_id_error'                    => $this->l('ID Интернет-магазина не заполнен'),
            'login_error'                       => $this->l('Логин Интернет-магазина не заполнен'),
            'password_error'                    => $this->l('Пароль Интернет-магазина не заполнен'),
            'configuration_succ'                => $this->l('Настройки успешно изменены!'),
            'payee_id_title'                    => $this->l('Идентификатор магазина в системе Portmone(Payee ID)'),
            'payee_id_description'              => $this->l('ID Интернет-магазина, предоставленный менеджером Portmone'),
            'login_title'                       => $this->l('Логин Интернет-магазина в системе Portmone'),
            'login_description'                 => $this->l('Логин Интернет-магазина, предоставленный менеджером Portmone'),
            'password_title'                    => $this->l('Пароль Интернет-магазина в системе Portmone'),
            'password_description'              => $this->l('Пароль для Интернет-магазина, предоставленный менеджером Portmone'),
            'save'                              => $this->l('Сохранить'),
            'configuration'                     => $this->l('Настройки'),
            'error_auth'                        => $this->l('Ошибка авторизации. Введен не верный логин или пароль'),
            'error_merchant'                    => $this->l('При совершении оплаты возникла ошибка. Данные Интернет-магазина некорректны'),
            'error_order_in_portmone'           => $this->l('В системе Portmone данного платежа нет, он возвращен или создан некорректно'),
            'order_rejected'                    => $this->l('При совершении оплаты возникла ошибка. Проверьте данные вашей карты и попробуйте провести оплату еще раз!'),
            'thankyou_text'                     => $this->l('Спасибо за покупку!'),
            'number_pay'                        => $this->l('Номер вашего заказа'),
            'next'                              => $this->l('Продолжить'),
            're_payment'                        => $this->l('Попробовать оплатить еще раз'),
            'description_title'                 => $this->l('Комментарий для клиента'),
            'description_modul1'                => $this->l('Сервис проведения платежей обеспечивается системой Portmone.com с использованием современного и безопасного механизма авторизации платежных карт.'),
            'description_modul2'                => $this->l('Служба поддержки Portmone.com:'),
            'description_modul3'                => $this->l('телефон:'),
            'description_modul4'                => $this->l('электронная почта:'),
            'description_description'           => $this->l('Описание для клиента на странице оплаты заказа'),
            'showlogo_title'                    => $this->l('Показать логотип на странице оплаты'),
            'exp_time_title'                    => $this->l('Устанавливает интервал, в течение которого заказ может быть оплачен'),
            'showlogo_description'              => $this->l('Отметьте, чтобы показать логотип Portmone'),
            'exp_time_description'              => $this->l('Если значение параметра указано в секундах, то с момента вызова платежной страницы показывается обратный отсчет, который виден Клиенту на странице оплаты. По истечению времени на оплату счет переходит в статус "REJECTED" и оплатить его невозможно'),
            'preauth_flag_title'                => $this->l('Режим преавторизации'),
            'preauth_flag_description'          => $this->l('Средства только блокируются на карте клиента, но финансового списания со счета клиента не происходит'),
            'proxy_title'                       => $this->l('Использовать прокси'),
            'proxy_error'                       => $this->l('Настройки прокси не заполнены'),
            'proxy_description'                 => $this->l('Обращения к серверу Portmone для проверки оплаты покупок если вы используете прокси'),
            'proxy_settings_title'              => $this->l('Настройки прокси'),
            'proxy_settings_port_description'   => $this->l('Порт'),
            'proxy_settings_adress_description' => $this->l('Адрес'),
            'yes'                               => $this->l('Да'),
            'no'                                => $this->l('Нет'),
            );

            foreach ($this->text_data as  $key => $value) {
                $this->lang[$key] = $value;
            }

            $config = Configuration::getMultiple(array(
                    'payee_id',
                    'login',
                    'pass',
                    'description_user',
                    'showlogo',
                    'exp_time',
                    'preauth_flag',
                    'proxy',
                    'proxy_settings_port',
                    'proxy_settings_adress'
                    ));

            if (isset($config['payee_id']))             { $this->payee_id               = $config['payee_id']; }
            if (isset($config['login']))                { $this->login                  = $config['login']; }
            if (isset($config['pass']))                 { $this->pass                   = $config['pass']; }
            if (isset($config['description_user']))     { $this->description_user       = $config['description_user']; }
            if (isset($config['showlogo']))             { $this->showlogo               = $config['showlogo']; }
            if (isset($config['exp_time']))             { $this->exp_time               = $config['exp_time']; }
            if (isset($config['preauth_flag']))         { $this->preauth_flag           = $config['preauth_flag']; }
            if (isset($config['proxy']))                { $this->proxy                  = $config['proxy']; }
            if (isset($config['proxy_settings_port']))  { $this->proxy_settings_port    = $config['proxy_settings_port']; }
            if (isset($config['proxy_settings_adress'])){ $this->proxy_settings_adress  = $config['proxy_settings_adress']; }

            parent::__construct();
            $this->displayName      = $this->lang['display_name'];
            $this->description      = $this->lang['description'];
            $this->confirmUninstall = $this->lang['confirmUninstall'];
        }

        public function addOrderState($state, $color){
            $orderState = new OrderState();
            foreach (Language::getLanguages() AS $language){
                $orderState->name[$language['id_lang']] = (strtolower($language['iso_code']) == 'ru')? $this->lang[$state.'_with_ru'] : $this->lang[$state.'_with_all'];
            }

            $orderState -> send_mail   = 1;
            $orderState -> template    = "portmone";
            $orderState -> invoice     = 1;
            $orderState -> color       = $color;
            $orderState -> unremovable = false;
            $orderState -> logable     = 1;
            $orderState -> paid        = 1;

            if ($orderState->add()) {
                $source         = dirname(__FILE__).'/../../modules/portmone/img/portmone32.gif';
                $destination    = dirname(__FILE__).'/../../img/os/'.(int) $orderState->id.'.gif';
                copy($source, $destination);
            }
            return $orderState->id;
        }

        /**
         *  Initializing the configuration form in the admin panel
         **/
        public function install(){
            $portmonePaidId                 = $this->addOrderState('paid', '#109b00');
            $portmonePaidButNotVerifiedId   = $this->addOrderState('paidButNotVerified', '#0a4e03');
            $portmonePreauthId              = $this->addOrderState('preauth', '#ffe000');

            if (!parent::install()
                OR !$this->registerHook('payment')
                OR !$this->registerHook('paymentReturn')
                OR !Configuration::updateValue('PORTMONE_PAID', $portmonePaidId)
                OR !Configuration::updateValue('PORTMONE_PAID_BUT_NOT_VERIFIED', $portmonePaidButNotVerifiedId)
                OR !Configuration::updateValue('PORTMONE_PREAUTH', $portmonePreauthId)
            )
            return false;
        }

        /**
         * Де инсталация плагина
         **/
        public function uninstall(){
            if (   !Configuration::deleteByName('payee_id')
                OR !Configuration::deleteByName('login')
                OR !Configuration::deleteByName('pass')
                OR !Configuration::deleteByName('description_user')
                OR !Configuration::deleteByName('showlogo')
                OR !Configuration::deleteByName('exp_time')
                OR !Configuration::deleteByName('preauth_flag')
                OR !Configuration::deleteByName('proxy')
                OR !Configuration::deleteByName('proxy_settings_port')
                OR !Configuration::deleteByName('proxy_settings_adress')
                OR !Configuration::deleteByName('PORTMONE_PAID')
                OR !Configuration::deleteByName('PORTMONE_PAID_BUT_NOT_VERIFIED')
                OR !Configuration::deleteByName('PORTMONE_PREAUTH')
                OR !parent::uninstall()
            )
            return false;
        }

        /**
         * Валидируем или в админке введены обязательные поля
         **/
        private function _postValidation(){
            if (isset($_POST['portmone_submit'])) {
                if (empty($_POST['payee_id'])){
                    $this->_postErrors[] = $this->lang['payee_id_error'];
                }
                if (empty($_POST['login'])){
                    $this->_postErrors[] = $this->lang['login_error'];
                }
                if (empty($_POST['pass'])){
                    $this->_postErrors[] = $this->lang['password_error'];
                }
                if ($_POST['proxy'] == 1){
                    if (empty($_POST['proxy_settings_port']) || empty($_POST['proxy_settings_adress'])){
                        $this->_postErrors[] = $this->lang['proxy_error'];
                    }
                }
            }
        }

        /**
         * Записываем в базу введенные значения в админ панеле
         **/
        private function _postProcess() {
            if (isset($_POST['portmone_submit'])) {
                Configuration::updateValue('payee_id', $_POST['payee_id']);
                Configuration::updateValue('login', $_POST['login']);
                Configuration::updateValue('pass', $_POST['pass']);
                Configuration::updateValue('description_user', $_POST['description_user']);
                Configuration::updateValue('showlogo', $_POST['showlogo']);
                Configuration::updateValue('exp_time', $_POST['exp_time']);
                Configuration::updateValue('proxy', $_POST['proxy']);
                Configuration::updateValue('proxy_settings_port', $_POST['proxy_settings_port']);
                Configuration::updateValue('proxy_settings_adress', $_POST['proxy_settings_adress']);
                Configuration::updateValue('preauth_flag', $_POST['preauth_flag']);
            }
            $this->_html .= '<div class="bootstrap">
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>
                ' . $this->lang['configuration_succ'] . '
            </div>
        </div>';
        }

        /**
         * Логотип в админ панеле
         **/
        private function _displayPortmone() {
            $this->_html .= '
            <a target="_blank" href="'. $this->portmone_url .'r3/'. $this->_getLanguage() .'"><img src="../modules/portmone/img/logo_settings.svg" style="margin-right:15px; height: 50px;" /></a><br /><br />';
        }

        /**
         * Форма в админ панеле
         **/
        private function _displayForm() {
            $this->_html .=
            '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" class="defaultForm form-horizontal bootstrap">
                <fieldset>
                    <legend>
                        <img src="../img/admin/contact.gif" />' . $this->lang['configuration'] . '
                    </legend>
                    <label>' . $this->lang['payee_id_title'] . ' <span  style="color:red;">*</span>' .'</label>
                        <div class="margin-form">
                            <input type="text" size="33" maxlength="36" name="payee_id" value="' . htmlentities(Tools::getValue('payee_id', $this->payee_id), ENT_COMPAT, 'UTF-8') . '" />
                            <p>' . $this->lang['payee_id_description'] . '</p>
                        </div>
                    <label>' . $this->lang['login_title'] . ' <span  style="color:red;">*</span>' . '</label>
                        <div class="margin-form">
                            <input type="text" size="33" maxlength="30" name="login" value="' . htmlentities(Tools::getValue('login', $this->login), ENT_COMPAT, 'UTF-8') . '" />
                            <p>'. $this->lang['login_description'] .'</p>
                        </div>
                    <label>' . $this->lang['password_title'] . ' <span style="color:red;">*<span/>' . '</label>
                        <div class="margin-form">
                            <input type="password" size="33" maxlength="30" name="pass" value="' . htmlentities(Tools::getValue('pass', $this->pass), ENT_COMPAT, 'UTF-8') . '" />
                            <p>'. $this->lang['password_description'] .'</p>
                        </div>
                    <label>' . $this->lang['description_title'] . '</label>
                        <div class="margin-form">
                            <input type="text" size="33" name="description_user" value="' . htmlentities(Tools::getValue('description_user', $this->description_user), ENT_COMPAT, 'UTF-8') . '" />
                            <p>'. $this->lang['description_description'] .'</p>
                        </div>
                    <label>' . $this->lang['exp_time_title'] . '</label>
                        <div class="margin-form">
                            <input type="text" size="33" name="exp_time" value="' . htmlentities(Tools::getValue('exp_time', $this->exp_time), ENT_COMPAT, 'UTF-8') . '" />
                            <p>'. $this->lang['exp_time_description'] .'</p>
                        </div>';

                        $this->_radioInForm('preauth_flag');
                        $this->_radioInForm('showlogo');
                        $this->_radioInForm('proxy');

                    $this->_html .= '<label>' . $this->lang['proxy_settings_title'] . '</label>
                        <div class="margin-form">
                            <div class="col-lg-2">
                                <input type="text" size="33" maxlength="30" name="proxy_settings_port" value="' . htmlentities(Tools::getValue('proxy_settings_port', $this->proxy_settings_port), ENT_COMPAT, 'UTF-8') . '" />
                                <p>'. $this->lang['proxy_settings_port_description'] .'</p>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" size="33" maxlength="30" name="proxy_settings_adress" value="' . htmlentities(Tools::getValue('proxy_settings_adress', $this->proxy_settings_adress), ENT_COMPAT, 'UTF-8') . '" />
                                <p>'. $this->lang['proxy_settings_adress_description'] .'</p>
                            </div>
                        </div>';
                    $this->_html .= '<button type="submit" value="1" name="portmone_submit" style="width:100%" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> '.  $this->lang['save'] .'
                    </button>
                </fieldset>
            </form>
                <h3> '.
                $this->lang['description_modul1']
                . '<br /> '.
                $this->lang['description_modul2']
                . '<br /> '.
                $this->lang['description_modul3'] . ' <a href="tel:+380442000902">+380 (44) 200 09 02</a>'
                . '<br /> '.
                $this->lang['description_modul4'] . ' <a href="mailto:support@portmone.com">support@portmone.com</a>'
                . '<h3>';
        }


        private function _radioInForm($radio_e) {
            $this->_html .= '<label>' . $this->lang[$radio_e.'_title'] . '</label>
                <div class="margin-form">
                    <div class="col-lg-12">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="'.$radio_e.'" id="PORTMONE_'.$radio_e.'_ON" value="1" ';
                                if ( isset($_POST[$radio_e]) && $_POST[$radio_e] == '1' ) {
                                        $this->_html .= 'checked="checked"';
                                } else {
                                    if (  $this->$radio_e == '1' && !isset($_POST[$radio_e])) {
                                        $this->_html .= 'checked="checked"';
                                    }
                                }
                                $this->_html .= '>
                            <label for="PORTMONE_'.$radio_e.'_ON">'. $this->lang['yes'] .'</label>
                                <input type="radio" name="'.$radio_e.'" id="PORTMONE_'.$radio_e.'_OFF" value="0"';
                                if ( isset($_POST[$radio_e]) && $_POST[$radio_e] == '0' ) {
                                    $this->_html .= 'checked="checked"';
                                } else {
                                    if (  $this->$radio_e == '0' && !isset($_POST[$radio_e])) {
                                        $this->_html .= 'checked="checked"';
                                    }
                                }
                                $this->_html .= '>
                            <label for="PORTMONE_'.$radio_e.'_OFF">'. $this->lang['no'] .'</label>
                            <a class="slide-button btn"></a>
                        </span>
                        <p>'. $this->lang[$radio_e.'_description'] .'</p>
                    </div>
                </div>';
        }

        /**
         * Формируем контект в админ панеле модуля
         **/
        public function getContent(){
            $this->_displayPortmone();
            if (!empty($_POST['portmone_submit'])) {
                $this->_postValidation();
                if (!sizeof($this->_postErrors)) {
                    $this->_postProcess();
                } else {
                    $this->_html .= '
                            <div class="bootstrap">
                                <div class="module_confirmation conf confirm alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">×</button>';
                                    foreach ($this->_postErrors AS $err) {
                                        $this->_html .=  $err . '<br />' ;
                                    }
                    $this->_html .= '
                                </div>
                            </div>';
                }
            } else {
                $this->_html .= '<br />';
            }
            $this->_displayForm();
            return $this->_html;
        }

        /**
         * Инициализируем переменные формы в клиентской части для отправки в Portmone
         **/
        public function hookPayment($params){
            global $smarty;

            if (!$this->active)
                return;

            $id_currency    = intval($params['cart']->id_currency);
            $currency       = new Currency(intval($id_currency));
            $payee_id       = Configuration::get('payee_id');
            $bill_amount    = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', '');
            $preauth_flag   = (Configuration::get('preauth_flag') == 1)? 'Y' : 'N' ;
            $showlogo       = Configuration::get('showlogo');
                if ($showlogo == '1') {
                   $showlogo_img = '<img src="'. Tools::getHttpHost(true) .$this->_path.$this->logo_img.'" style="height: 40px;" title="'.$this->displayName .'" alt="'.$this->displayName .'"/>';
                } else { $showlogo_img = ''; }

            $parameters = array(
                'action_url'        => $this->portmone_url_gataway,
                'payee_id'          => $payee_id,
                'cart_id'           => $params['cart']->id,
                'status_order'      => Configuration::get('PS_OS_CHEQUE'),
                'display_name'      => $this->displayName,
                'bill_amount'       => $bill_amount,
                'success_url'       => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/success.php',
                'failure_url'       => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/fail.php',
                'createOrder_url'   => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/createOrder.php',
                'lang'              => $this->_getLanguage(),
                'title_payee'       => $this->lang['title_payee'],
                'description_user'  => Configuration::get('description_user'),
                'exp_time'          => Configuration::get('exp_time'),
                'showlogo'          => $showlogo_img,
                'preauth_flag'      => $preauth_flag,
                'spinner_url'       => Tools::getHttpHost(true) .$this->_path.$this->logo_img,
            );

            $smarty->assign($parameters);
            return $this->display(__FILE__, 'portmone.tpl');
        }

        /**
         * Create order
         **/
        public function createOrder($data) {
            $this->validateOrder(
                $data['cart_id'],
                $data['status_order'],
                $data['bill_amount'],
                $data['display_name'],
                'success',
                NULL
            );

            return json_encode([
                'shop_order_number' => $this->currentOrder .'_'. time(),
                'description'       => '#' . $this->currentOrder,
            ]);
        }

        /**
         * Get Language
         **/
        private function _getLanguage() {
            global $cookie;
            $language       = Language::getIsoById(intval($cookie->id_lang));
            $language       = (!in_array($language, array('ua', 'en', 'ru'))) ? 'ru' : $language;
            return $language == 'ua' ? 'uk' : $language;
        }

        /**
         * Handling a payment response from Portmone
         **/
        public function isPaymentValid() {
            $data = array(
                "method"            => "result",
                "payee_id"          => $this->payee_id ,
                "login"             => $this->login,
                "password"          => $this->pass ,
                "shop_order_number" => $_REQUEST['SHOPORDERNUMBER'],
            );

            $result_portmone = $this->curlRequest($this->portmone_url_gataway, $data);
            $parseXml = $this->parseXml($result_portmone);

            if ($parseXml === false) {
                if ($_REQUEST['RESULT'] == '0') {
                    return [
                        'status' => 'PORTMONE_PAID_BUT_NOT_VERIFIED',
                        'message' => $this->lang['thankyou_text']
                    ];
                } else {
                    return [
                        'status' => 'PS_OS_ERROR',
                        'message' => $this->lang['error_auth']
                    ];
                }
            }

            $payee_id_return    = (array)$parseXml->request->payee_id;
            $order_data         = (array)$parseXml->orders->order;

            if ($_REQUEST['RESULT'] !== '0') {
                return [
                    'status' => 'PS_OS_ERROR',
                    'message' => $_REQUEST['RESULT']
                ];
            }

            if ($payee_id_return[0] !=  $this->payee_id) {
                return [
                    'status' => 'PS_OS_ERROR',
                    'message' => $this->lang['error_merchant']
                ];
            }
            if (count($parseXml->orders->order) == 0) {
                return [
                    'status' => 'PS_OS_ERROR',
                    'message' => $this->lang['error_order_in_portmone']
                ];
            } elseif (count($parseXml->orders->order) > 1){
                $no_pay = false;
                foreach($parseXml->orders->order as $order ){
                    $status = (array)$order->status;
                    if ($status[0] == self::ORDER_PAYED){
                        $no_pay = true;
                        break;
                    }
                }

                if ($no_pay == false) {
                    return [
                        'status' => 'PS_OS_ERROR',
                        'message' => $this->lang['error_order_in_portmone']
                    ];
                } else {
                    return [
                        'status' => 'PORTMONE_PAID',
                        'message' => $this->lang['thankyou_text']
                    ];
                }
            }

            if ($order_data['status'] == self::ORDER_REJECTED) {
                return [
                    'status' => 'PS_OS_ERROR',
                    'message' => $this->lang['order_rejected']
                ];
            }

            if ($order_data['status'] == self::ORDER_CREATED) {
                return [
                    'status' => 'PS_OS_ERROR',
                    'message' => $this->lang['order_rejected']
                ];
            }

            if ($order_data['status'] == self::ORDER_PAYED) {
                return [
                    'status' => 'PORTMONE_PAID',
                    'message' => $this->lang['thankyou_text']
                ];
            }

            if ($order_data['status'] == self::ORDER_PREAUTH) {
                return [
                    'status' => 'PORTMONE_PREAUTH',
                    'message' => $this->lang['thankyou_text']
                ];
            }
            return true;
        }

        /**
         * A request to verify the validity of payment in Portmone
         **/
        private function curlRequest($url, $data) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            if(Configuration::get('proxy') == 1) {
                curl_setopt($ch, CURLOPT_PROXY, Configuration::get('proxy_settings_adress'));
                curl_setopt($ch, CURLOPT_PROXYPORT, Configuration::get('proxy_settings_port'));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (200 !== intval($httpCode)) {
                return false;
            }
            return $response;
        }

        /**
         * Parsing XML response from Portmone
         **/
        private function parseXml($string) {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (false !== $xml) {
                return $xml;
            } else {
                return false;
            }
        }
    }
?>
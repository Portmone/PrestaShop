<?php

/**
 * @name Portmone 2.0.3
 * @description Модуль разработан в компании Portmone предназначен для CMS Prestashop 1.7.5.2
 * @author glib.yuriiev@portmone.me
 * @email support@portmone.com
 * @version 2.0.3
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
if(!defined('_PS_VERSION_'))exit;

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
            $this->version                      = '2.0.3';
            $this->ps_versions_compliancy = [
                'min' => '1.7',
                'max' => _PS_VERSION_
            ];
            $this->author                       = 'glib.yuriiev@portmone.me';
            $this->logo_img                     = 'img/logo_settings.svg';
            $this->portmone_url                 = 'https://www.portmone.com.ua/';
            $this->portmone_url_gataway         = 'https://www.portmone.com.ua/gateway/';
            $this->currencies_mode              = 'checkbox';
            $this->lang                         = [];
            $this->currencies                   = true;

            $this->text_data = [
            'display_name'                                  => $this->l('Portmone'),
            'description'                                   => $this->l('Плати через Portmone'),
            'confirmUninstall'                              => $this->l('Вы уверены, что хотите удалить?'),
            'paid_with_ru'                                  => $this->l('Оплачено с помощью Portmone'),
            'paidButNotVerified_with_ru'                    => $this->l('Оплачено с помощью Portmone (но не проверено)'),
            'preauth_with_ru'                               => $this->l('Оплачено с помощью Portmone (блокировка средств)'),
            'error_with_ru'                                 => $this->l('Оплата заказа через Portmone НЕ удалась'),
            'title_payee'                                   => $this->l('Оплата заказа через Portmone'),
            'paid_with_all'                                 => $this->l('Paid with Portmone'),
            'paidButNotVerified_with_all'                   => $this->l('Paid with Portmone (but not verified)'),
            'preauth_with_all'                              => $this->l('Paid with Portmone (blocking of funds)'),
            'error_with_all'                                => $this->l('Payment for order through Portmone is NOT successful'),
            'payee_id_error'                                => $this->l('ID Интернет-магазина не заполнен'),
            'login_error'                                   => $this->l('Логин Интернет-магазина не заполнен'),
            'password_error'                                => $this->l('Пароль Интернет-магазина не заполнен'),
            'configuration_succ'                            => $this->l('Настройки успешно изменены!'),
            'payee_id_title'                                => $this->l('Идентификатор магазина в системе Portmone(Payee ID)'),
            'payee_id_description'                          => $this->l('ID Интернет-магазина, предоставленный менеджером Portmone'),
            'login_title'                                   => $this->l('Логин Интернет-магазина в системе Portmone'),
            'login_description'                             => $this->l('Логин Интернет-магазина, предоставленный менеджером Portmone'),
            'password_title'                                => $this->l('Пароль Интернет-магазина в системе Portmone'),
            'password_description'                          => $this->l('Пароль для Интернет-магазина, предоставленный менеджером Portmone'),
            'company_key_title'                             => $this->l('Ключ компании в системе Portmone'),
            'company_key_description'                       => $this->l('Ключ компании, предоставленный менеджером Portmone'),
            'company_key_error'                             => $this->l('Ключ компании не заполнен'),
            'save'                                          => $this->l('Сохранить'),
            'configuration'                                 => $this->l('Настройки'),
            'error_auth'                                    => $this->l('Ошибка авторизации. Введен не верный логин или пароль'),
            'error_merchant'                                => $this->l('При совершении оплаты возникла ошибка. Данные Интернет-магазина некорректны'),
            'error_order_in_portmone'                       => $this->l('В системе Portmone данного платежа нет, он возвращен или создан некорректно'),
            'order_rejected'                                => $this->l('При совершении оплаты возникла ошибка. Проверьте данные вашей карты и попробуйте провести оплату еще раз!'),
            'thankyou_text'                                 => $this->l('Спасибо за покупку!'),
            'number_pay'                                    => $this->l('Номер вашего заказа'),
            'next'                                          => $this->l('Продолжить'),
            're_payment'                                    => $this->l('Попробовать оплатить еще раз'),
            'description_title'                             => $this->l('Комментарий для клиента'),
            'description_modul1'                            => $this->l('Сервис проведения платежей обеспечивается системой Portmone.com с использованием современного и безопасного механизма авторизации платежных карт.'),
            'description_modul2'                            => $this->l('Служба поддержки Portmone.com:'),
            'description_modul3'                            => $this->l('телефон:'),
            'description_modul4'                            => $this->l('электронная почта:'),
            'description_modul5'                            => $this->l('Portmone.com, принимает только Гривны (UAH)'),
            'description_modul6'                            => $this->l('Сумма платежа не конверируется в валюту Гривны(UAH) автоматически. В магазине по умолчанию должна быть валюта Гривны (UAH)'),
            'description_description'                       => $this->l('Описание для клиента на странице оплаты заказа'),
            'showlogo_title'                                => $this->l('Показать логотип на странице оплаты'),
            'exp_time_title'                                => $this->l('Устанавливает интервал, в течение которого заказ может быть оплачен'),
            'showlogo_description'                          => $this->l('Отметьте, чтобы показать логотип Portmone'),
            'exp_time_description'                          => $this->l('Если значение параметра указано в секундах, то с момента вызова платежной страницы показывается обратный отсчет, который виден Клиенту на странице оплаты. По истечению времени на оплату счет переходит в статус "REJECTED" и оплатить его невозможно'),
            'save_client_first_last_name_flag_title'        => $this->l('Сохранить имя и фамилию клиента'),
            'save_client_first_last_name_flag_description'  => $this->l('Имя и фамилия клиента берется из адреса указаного в заказе. Согласовывается с менеджером Portmone'),
            'save_client_phone_number_flag_title'           => $this->l('Сохранить телефон клиента'),
            'save_client_phone_number_flag_description'     => $this->l('Телефон клиента берется из адреса указаного в заказе. Согласовывается с менеджером Portmone'),
            'preauth_flag_title'                            => $this->l('Режим преавторизации'),
            'preauth_flag_description'                      => $this->l('Средства только блокируются на карте клиента, но финансового списания со счета клиента не происходит'),
            'proxy_title'                                   => $this->l('Использовать прокси'),
            'proxy_error'                                   => $this->l('Настройки прокси не заполнены'),
            'proxy_description'                             => $this->l('Обращения к серверу Portmone для проверки оплаты покупок если вы используете прокси'),
            'proxy_settings_title'                          => $this->l('Настройки прокси'),
            'proxy_settings_port_description'               => $this->l('Порт'),
            'proxy_settings_adress_description'             => $this->l('Адрес'),
            // 'show_pay_button_title'             => $this->l('Показать кнопку оплаты Portmone.com'),
            // 'show_pay_button_description'       => $this->l('При выборе Portmone.com из списка оплат, показывать кнопку оплаты. При ее использовании заказ в Админ панеле создается перед переходом пользователя на страницу Portmone.com для оплаты. Если проводить оплату стандартной кнопкой, заказ в админ панеле появляется уже после оплаты'),
            'yes'                                           => $this->l('Да'),
            'no'                                            => $this->l('Нет'),
            ];

            foreach ($this->text_data as  $key => $value) {
                $this->lang[$key] = $value;
            }

            $config = Configuration::getMultiple(array(
                    'payee_id',
                    'login',
                    'pass',
                    'company_key',
                    'description_user',
                    'showlogo',
                    'exp_time',
                    'save_client_first_last_name_flag',
                    'save_client_phone_number_flag',
                    'preauth_flag',
                    'proxy',
                    'proxy_settings_port',
                    'proxy_settings_adress',
                    // 'show_pay_button'
                ));

            if (isset($config['payee_id']))                         { $this->payee_id                         = $config['payee_id']; }
            if (isset($config['login']))                            { $this->login                            = $config['login']; }
            if (isset($config['pass']))                             { $this->pass                             = $config['pass']; }
            if (isset($config['company_key']))                      { $this->company_key                      = $config['company_key']; }
            if (isset($config['description_user']))                 { $this->description_user                 = $config['description_user']; }
            if (isset($config['showlogo']))                         { $this->showlogo                         = $config['showlogo']; }
            if (isset($config['exp_time']))                         { $this->exp_time                         = $config['exp_time']; }
            if (isset($config['save_client_first_last_name_flag'])) { $this->save_client_first_last_name_flag = $config['save_client_first_last_name_flag']; }
            if (isset($config['save_client_phone_number_flag']))    { $this->save_client_phone_number_flag    = $config['save_client_phone_number_flag']; }
            if (isset($config['preauth_flag']))                     { $this->preauth_flag                     = $config['preauth_flag']; }
            if (isset($config['proxy']))                            { $this->proxy                            = $config['proxy']; }
            if (isset($config['proxy_settings_port']))              { $this->proxy_settings_port              = $config['proxy_settings_port']; }
            if (isset($config['proxy_settings_adress']))            { $this->proxy_settings_adress            = $config['proxy_settings_adress']; }
            // if (isset($config['show_pay_button']))      { $this->show_pay_button        = $config['show_pay_button']; }

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

            $orderState->send_mail   = 1;
            $orderState->template    = "portmone";
            $orderState->invoice     = 1;
            $orderState->color       = $color;
            $orderState->unremovable = false;
            $orderState->logable     = 1;
            $orderState->paid        = 1;

            if ($orderState->add()) {
                $source         = dirname(__FILE__).'/../../modules/portmone/img/portmone32.gif';
                $destination    = dirname(__FILE__).'/../../img/os/'.(int) $orderState->id.'.gif';
                copy($source, $destination);
            }
            return $orderState->id;
        }

        /**
         * Initializing the configuration form in the admin panel
         **/
        public function install(){
            $portmonePaidId                 = $this->addOrderState('paid', '#109b00');
            $portmonePaidButNotVerifiedId   = $this->addOrderState('paidButNotVerified', '#0a4e03');
            $portmonePreauthId              = $this->addOrderState('preauth', '#ffe000');
            $portmoneErrorId                = $this->addOrderState('error', '#bb0f0f');

            if (!parent::install()
                OR !$this->registerHook('paymentOptions')
                OR !Configuration::updateValue('PORTMONE_PAID', $portmonePaidId)
                OR !Configuration::updateValue('PORTMONE_PAID_BUT_NOT_VERIFIED', $portmonePaidButNotVerifiedId)
                OR !Configuration::updateValue('PORTMONE_PREAUTH', $portmonePreauthId)
                OR !Configuration::updateValue('PORTMONE_ERROR', $portmoneErrorId)
            ) {
                return false;
            }
            return true;
        }

        /**
         * Де инсталация плагина
         **/
        public function uninstall() {
            return (Configuration::deleteByName('payee_id')
                && Configuration::deleteByName('login')
                && Configuration::deleteByName('pass')
                && Configuration::deleteByName('company_key')
                && Configuration::deleteByName('description_user')
                && Configuration::deleteByName('showlogo')
                && Configuration::deleteByName('exp_time')
                && Configuration::deleteByName('save_client_first_last_name_flag')
                && Configuration::deleteByName('save_client_phone_number_flag')
                && Configuration::deleteByName('preauth_flag')
                && Configuration::deleteByName('proxy')
                && Configuration::deleteByName('proxy_settings_port')
                && Configuration::deleteByName('proxy_settings_adress')
                // && Configuration::deleteByName('show_pay_button')
                && Configuration::deleteByName('PORTMONE_PAID')
                && Configuration::deleteByName('PORTMONE_PAID_BUT_NOT_VERIFIED')
                && Configuration::deleteByName('PORTMONE_PREAUTH')
                && Configuration::deleteByName('PORTMONE_ERROR')
                && parent::uninstall()
            );
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
                if (empty($_POST['company_key'])){
                    $this->_postErrors[] = $this->lang['company_key_error'];
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
                Configuration::updateValue('company_key', $_POST['company_key']);
                Configuration::updateValue('description_user', $_POST['description_user']);
                Configuration::updateValue('showlogo', $_POST['showlogo']);
                Configuration::updateValue('exp_time', $_POST['exp_time']);
                Configuration::updateValue('proxy', $_POST['proxy']);
                Configuration::updateValue('proxy_settings_port', $_POST['proxy_settings_port']);
                Configuration::updateValue('proxy_settings_adress', $_POST['proxy_settings_adress']);
                Configuration::updateValue('save_client_first_last_name_flag', $_POST['save_client_first_last_name_flag']);
                Configuration::updateValue('save_client_phone_number_flag', $_POST['save_client_phone_number_flag']);
                Configuration::updateValue('preauth_flag', $_POST['preauth_flag']);
                // Configuration::updateValue('show_pay_button', $_POST['show_pay_button']);
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
                      <label>' . $this->lang['company_key_title'] . ' <span style="color:red;">*<span/>' . '</label>
                        <div class="margin-form">
                            <input type="text" size="33" maxlength="60" name="company_key" value="' . htmlentities(Tools::getValue('company_key', $this->company_key), ENT_COMPAT, 'UTF-8') . '" />
                            <p>'. $this->lang['company_key_description'] .'</p>
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

                        $this->_radioInForm('save_client_first_last_name_flag');
                        $this->_radioInForm('save_client_phone_number_flag');
                        $this->_radioInForm('preauth_flag');
                        $this->_radioInForm('showlogo');
                        // $this->_radioInForm('show_pay_button');
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
                    'License: Payment Card Industry Data Security Standard (PCI DSS) '. '<a target="_blank" href="https://www.portmone.com.ua/company#security">License</a>'
                . '<br /> '.
                    $this->lang['description_modul5']
                . '<br /> '.
                    $this->lang['description_modul6']
                . '<br /> '.
                    $this->lang['description_modul2']
                . '<br /> '.
                    $this->lang['description_modul3'] . ' <a href="tel:+380442000922">+380 (44) 200 09 22</a>'
                . '<br /> '.
                    $this->lang['description_modul4'] . ' <a href="mailto:b2bsupport@portmone.me">b2bsupport@portmone.me</a>'
                . '<h3>';
        }

        /**
         * Добавление ползунков выбора в форму
         **/
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
        public function hookPaymentOptions(){
            if (!$this->active)
                return;

            $payment_options = [
                $this->getCardPaymentOption()
            ];
            return $payment_options;
        }

        /*
         * Формирование формы
         * Сбор темплейта платежа для пользователя
         */
        public function getCardPaymentOption() {
            global $cart;

            $total          = $cart->getOrderTotal(true, 3);
            $payee_id       = Configuration::get('payee_id');
            $bill_amount    = number_format(Tools::convertPrice($total, $cart->id_currency), 2, '.', '');
            $save_client_first_last_name_flag   = (Configuration::get('save_client_first_last_name_flag') == 1)? 'Y' : 'N' ;
            $save_client_phone_number_flag   = (Configuration::get('save_client_phone_number_flag') == 1)? 'Y' : 'N' ;
            $preauth_flag   = (Configuration::get('preauth_flag') == 1)? 'Y' : 'N' ;

            if (Configuration::get('showlogo') == '1') {
                $showlogo_img = Tools::getHttpHost(true) .$this->_path.$this->logo_img;
            } else {
                $showlogo_img = '';
            }

            $cms_module_name = json_encode(['name' => 'PrestaShop', 'v' => _PS_VERSION_]);
            $dt = date("YmdHis");

            $shop_order_number = $cart->id .'_'. time();
            $strToSignature = $payee_id . $dt . bin2hex($shop_order_number) . $bill_amount;
            $strToSignature = strtoupper($strToSignature).strtoupper(bin2hex(Configuration::get('login')));
            $signature = strtoupper(hash_hmac('sha256', $strToSignature,  Configuration::get('company_key')));

            $address = $this->context->customer->getSimpleAddress($cart->id_address_delivery);
            $attribute1 = $save_client_first_last_name_flag == 'Y' ? $address['firstname'] . ' ' . $address['lastname'] : '';
            $attribute2 = $save_client_phone_number_flag == 'Y' ? $address['phone'] : '';

            $form = [
                'payee_id' => ['name' => 'payee_id',
                    'type' => 'hidden',
                    'value' => $payee_id,
                ],

                'shop_order_number' => ['name' => 'shop_order_number',
                    'type' => 'hidden',
                    'value' => $shop_order_number,
                ],

                'bill_amount' => ['name' => 'bill_amount',
                    'type' => 'hidden',
                    'value' => $bill_amount,
                ],

                'description' => ['name' => 'description',
                    'type' => 'hidden',
                    'value' => '#' . $cart->id,
                ],

                'success_url' => ['name' => 'success_url',
                    'type' => 'hidden',
                    'value' => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/validation.php',
                ],

                'failure_url' => ['name' => 'failure_url',
                    'type' => 'hidden',
                    'value' => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/validation.php',
                ],

                'preauth_flag' => ['name' => 'preauth_flag',
                    'type' => 'hidden',
                    'value' => $preauth_flag,
                ],

                'exp_time' => ['name' => 'exp_time',
                    'type' => 'hidden',
                    'value' => Configuration::get('exp_time'),
                ],

                'attribute1' => ['name' => 'attribute1',
                    'type' => 'hidden',
                    'value' =>  $attribute1,
                ],

                'attribute2' => ['name' => 'attribute2',
                    'type' => 'hidden',
                    'value' => $attribute2,
                ],

                'lang' => ['name' => 'lang',
                    'type' => 'hidden',
                    'value' => $this->_getLanguage(),
                ],
                'encoding' => ['name' => 'encoding',
                    'type' => 'hidden',
                    'value' => 'UTF-8',
                ],
                'cms_module_name' => ['name' => 'cms_module_name',
                    'type' => 'hidden',
                    'value' => $cms_module_name,
                ],
                'dt' => ['name' => 'dt',
                    'type' => 'hidden',
                    'value' => $dt,
                ],
                'signature' => ['name' => 'signature',
                    'type' => 'hidden',
                    'value' => $signature,
                ],
            ];

            $externalOption = new PaymentOption();
            if ($showlogo_img !== '' ) {
                $externalOption->setLogo($showlogo_img);
            } else {
                $externalOption->setCallToActionText($this->text_data['title_payee']);
            }

            $externalOption->setAction($this->portmone_url_gataway)
                ->setInputs($form)
                ->setAdditionalInformation($this->context->smarty->assign(array(
                    'action_url'        => $this->portmone_url_gataway,
                    'payee_id'          => $payee_id,
                    'cart_id'           => $cart->id,
                    'status_order'      => Configuration::get('PS_OS_CHEQUE'),
                    'display_name'      => $this->displayName,
                    'description'       => $this->text_data['description'],
                    'bill_amount'       => $bill_amount,
                    'success_url'       => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/validation.php',
                    'failure_url'       => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/validation.php',
                    'createOrder_url'   => Tools::getHttpHost(true).__PS_BASE_URI__ . 'modules/portmone/createOrder.php',
                    'lang'              => $this->_getLanguage(),
                    'title_payee'       => $this->lang['title_payee'],
                    'description_user'  => Configuration::get('description_user'),
                    'exp_time'          => Configuration::get('exp_time'),
                    'showlogo'          => $showlogo_img,
                    'show_pay_button'   => 0, // Configuration::get('show_pay_button'),
                    'save_client_first_last_name_flag' => $save_client_first_last_name_flag,
                    'save_client_phone_number_flag'    => $save_client_phone_number_flag,
                    'attribute1'        => $attribute1,
                    'attribute2'        => $attribute2,
                    'preauth_flag'      => $preauth_flag,
                    'spinner_url'       => Tools::getHttpHost(true) .$this->_path.$this->logo_img,
                    'cms_module_name'   => $cms_module_name,
                    'dt'   => $dt,
                    'signature'   => $signature,
                ))->fetch('module:portmone/portmone.tpl'));
            return $externalOption;
        }

        /**
         * Создание платежа в базе перед отправкой его в Portmone
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
         * Получение языка в PrestaShop
         **/
        private function _getLanguage() {
            global $cookie;
            $language       = Language::getIsoById(intval($cookie->id_lang));
            $language       = (!in_array($language, array('ua', 'uk', 'en', 'ru'))) ? 'ru' : $language;
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
                        'status' => 'PORTMONE_ERROR',
                        'message' => $_REQUEST['RESULT'] . ' ' .$this->lang['error_auth']
                    ];
                }
            }

            $payee_id_return    = (array)$parseXml->request->payee_id;
            $order_data         = (array)$parseXml->orders->order;

            if ($_REQUEST['RESULT'] !== '0') {
                return [
                    'status' => 'PORTMONE_ERROR',
                    'message' => $_REQUEST['RESULT']
                ];
            }

            if ($payee_id_return[0] != $this->payee_id) {
                return [
                    'status' => 'PORTMONE_ERROR',
                    'message' => $this->lang['error_merchant']
                ];
            }
            if (count($parseXml->orders->order) == 0) {
                return [
                    'status' => 'PORTMONE_ERROR',
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
                        'status' => 'PORTMONE_ERROR',
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
                    'status' => 'PORTMONE_ERROR',
                    'message' => $this->lang['order_rejected']
                ];
            }

            if ($order_data['status'] == self::ORDER_CREATED) {
                return [
                    'status' => 'PORTMONE_ERROR',
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
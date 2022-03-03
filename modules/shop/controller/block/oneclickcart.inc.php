<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Block;
use \RS\Orm\Type;

/**
* Блок-контроллер Покупка в один клик корзины
*/
class OneClickCart extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Покупка в один клик корзины',
        $controller_description = 'Позволяет оформить заказ в один клик в корзине';

    protected
        $default_params = [
            'indexTemplate' => 'blocks/oneclickcart/oneclickcart.tpl',
            'use_captcha' => 0
    ];

    /**
     * @var \Catalog\Model\OneClickApi $api
     */
    public $api;

    /**
     * Инициализация блока
     */
    function init()
    {
        parent::init();
        $this->api = new \Catalog\Model\OneClickApi();
    }

    /**
     * Получение параметров блока
     *
     * @return \RS\Orm\AbstractObject
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'use_captcha' => new Type\Integer([
                'description' => t('Использовать каптчу?'),
                'checkboxview' => [1,0]
            ])
        ]);
    }

    /**
     * Обработка основного метода
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
        $click_fields_manager->setErrorPrefix('clickfield_');
        $click_fields_manager->setArrayWrapper('clickfields');
        
        //Предварительные данные
        $errors  = [];
        if ($this->isMyPost()){
            $phone = $this->request('phone', TYPE_STRING, false);      //Телефон
            $name  = $this->request('name', TYPE_STRING, false);      //Имя пользователя
            
            if ($this->api->checkFieldsFromPostToSend($click_fields_manager, false)) { //OK
                $this->api->send($this->api->getPreparedProductsFromCart()); //Отправим данные
                //Очистим корзину
                $cart = \Shop\Model\Cart::currentCart();
                $cart->clean();
                $this->result->setSuccess(true);
            }else{
                $errors = $this->api->getErrors();
                $this->result->setSuccess(false);
            }

            $this->view->assign([
                'phone' => $phone,
                'name' => $name,
                'open' => true,
            ]);
        }
        
        $this->view->assign([
            'success' => $this->result->isSuccess(),
            'errors' => $errors,
            'use_captcha' => $this->getParam('use_captcha'),
            'oneclick_userfields' => $click_fields_manager,
        ]);
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}

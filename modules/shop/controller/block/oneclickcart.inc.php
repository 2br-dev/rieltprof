<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Block;
use Catalog\Model\Orm\OneClickItem;
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
        $this->api = new \Catalog\Model\OneClickItemApi();
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
        /**
         * @var $click OneClickItem
         */
        $click = $this->api->getElement();

        if ($this->user->id <= 0) {
            $click->__kaptcha->setEnable($this->getParam('use_captcha'));
        }
        
        //Предварительные данные
        if ($this->isMyPost()) {

            //@deprecated Переменные для совместимости со старыми шаблонами, до версии ReadyScript 6
            $old_values = [];
            if ($name = $this->url->post('name', TYPE_STRING)) {
                $old_values['user_fio'] = $name;
            }
            if ($phone = $this->url->post('phone', TYPE_STRING)) {
                $old_values['user_phone'] = $phone;
            }
            //---

            $click->products = $this->api->getPreparedProductsFromCart();

            $this->result->setSuccess($this->api->save(null, $old_values));
            if ($this->result->isSuccess()) {
                $this->view->assign([
                    'success' => t('Спасибо, в ближайшее время с Вами свяжется наш менеджер.')
                ]);

                //Очистим корзину
                $cart = \Shop\Model\Cart::currentCart();
                $cart->clean();
            }

            $this->view->assign([
                'open' => true,
            ]);
        } else {
            $click['user_fio']   = $this->user['id'] ? $this->user->getFio() : "";
            $click['user_phone'] = $this->user['phone'];
        }
        
        $this->view->assign([
            'click' => $click,
        ]);
        // @deprecated Для совместимости со старыми шаблонами, до ReadyScript 6
        $this->view->assign([
            'success' => $this->result->isSuccess(),
            'errors' => $click->getErrors(),
            'use_captcha' => $this->getParam('use_captcha'),
            'oneclick_userfields' => $click->getFieldsManager(),
            'phone' => $click['user_phone'],
            'name' => $click['user_fio'],
        ]);
        //---
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}

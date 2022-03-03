<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;
use RS\Application\Application;

/**
 * Контроллер возвратов заказов
 */
class MyProductsReturn extends \RS\Controller\AuthorizedFront
{
    /**
     * @var \Shop\Model\ProductsReturnApi $api
     */
    public $api;
    /**
     * @var \Shop\Model\OrderApi $order_api
     */
    public $order_api;

    /**
     * Инициализация контроллера
     */
    function init()
    {
        $this->api       = new \Shop\Model\ProductsReturnApi();
        $this->order_api = new \Shop\Model\OrderApi();
        
        if (!$this->getModuleConfig()->return_enable) {
            $this->e404();
        }
    }

    /**
     * Окно со списком всех возвратов
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Orm\Exception
     */
    function actionIndex()
    {
        $this->app->title->addSection(t('Мои возвраты'));
        $this->app->breadcrumbs->addBreadCrumb(t('Мои возвраты'));

        $orders       = $this->getOrders();
        $returns      = $this->api->getReturnsByUserId($this->user['id']);
        $returns_list = $this->api->getReturnItemsByUserId($this->user['id']);

        $this->view->assign([
            'order_list' => $orders,
            'returns_list' => $returns,
            'return_items' => $returns_list,
            'currency_api' => new \Catalog\Model\CurrencyApi()
        ]);

        return $this->result->setTemplate('myproducts_return.tpl');
    }

    /**
     * Возвращает список заказов пользователя
     *
     * @return array
     */
    function getOrders()
    {
        $this->order_api->setFilter('user_id', $this->user['id']);
        return $this->order_api->getList();
    }

    /**
     * Окно со сгенерированным PDF документом
     *
     * @return string
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionPrint()
    {
        $return_num = $this->url->get('return_id',TYPE_STRING, false);
        /** @var \Shop\Model\Orm\ProductsReturn $return */
        $return = $this->api->getById($return_num);

        if ($return['id']) {
            $this->wrapOutput(false);

            //Разрешаем пачатать заявление
            if ($this->user->isAdmin() || $return['user_id'] == $this->user->id) {

                $this->app->headers->addHeader('content-type', 'application/pdf');
                return $return->getPdfForm();
            }
        }

        $this->e404();
    }

    /**
     * Создание заявления на возврат
     *
     * @param null|integer $return_id
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Behavior\Exception
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionAdd($return_id = null)
    {
        $this->app->title->addSection(t('Заявление на возврат товаров'));
        $this->app->breadcrumbs->addBreadCrumb(t('Мои возвраты'), $this->router->getUrl('shop-front-myproductsreturn'));        
        
        if (!$return_id){ //Если нужно создать новый возврат
            $order_id = $this->url->request('order_id', TYPE_STRING,false); //id заказа            
            $this->app->breadcrumbs->addBreadCrumb(t('Создать заявление на возврат товаров'));

            if ($order_id) {
                $order    = $this->order_api->getById($order_id);
                $return = new \Shop\Model\Orm\ProductsReturn();
                $return['order_id'] = $order['id'];
                $return->preFillFields();
            } else if (!$order_id) {
                $this->e404();
            }
        } else {
            if ($return = $this->api->getById($return_id)) {
                
                $this->app->breadcrumbs->addBreadCrumb(t('Заявление на возврат товаров №%0', [$return['return_num']]));
                $return->fillReturnItems();
            } else {
                $this->e404();
            }
        }

        $order = $return->getOrder();

        // проверяем принадлежит ли заказ пользователю
        if ($order['user_id'] == $this->user['id']) {
            $this->app->title->addSection(t('Форма оформления возврата'));
            $this->view->assign([
                'return' => $return,
            ]);

            $return->excludePostKeys(['user_id']);

            if ($this->isMyPost() && $this->url->checkCsrf() && $return->save($return['id'])) {// если пришел POST
                Application::getInstance()->redirect($this->router->getUrl('shop-front-myproductsreturn'));
            }
            return $this->result->setTemplate('myproducts_return_add.tpl');
        }
        Application::getInstance()->redirect($this->router->getUrl('shop-front-myproductsreturn'));
    }

    /**
     * Редактирование возврата
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionEdit()
    {
        $return_num = $this->url->request('return_id', TYPE_STRING, false); //id заказа
        /** @var \Shop\Model\Orm\ProductsReturn $return */
        $return = $this->api->getById($return_num);
        if (!$return['id']){ //Если такой возврат не найден
            $this->e404();
        }
        return $this->actionAdd($return['id']);
    }

    /**
     * Удаление возврата
     *
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Db\Exception
     */
    function actionDelete()
    {
        $return_num = $this->url->request('return_id', TYPE_STRING, false); //id заказа
        /** @var \Shop\Model\Orm\ProductsReturn $return */
        $return = $this->api->getById($return_num);
        if (!$return['id'] || ($return['user_id'] != $this->user['id'])){ //Если такой возврат не найден
            $this->e404();
        }
        $return->delete();
        Application::getInstance()->redirect($this->router->getUrl('shop-front-myproductsreturn'));
    }

    /**
     * Показывает страницу с правилами для возврата.
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRules()
    {
        // окно правил возврата
        return $this->result->setTemplate('%shop%/return/return_rules.tpl');
    }
}

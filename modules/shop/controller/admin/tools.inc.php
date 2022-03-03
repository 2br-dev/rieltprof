<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Toolbar\Button\SaveForm;
use RS\Html\Toolbar\Element;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Cdek;
use Shop\Model\DeliveryType\Cdek\CdekApi;
use RS\Orm\Type;
use Shop\Model\DeliveryType\Cdek2;
use Shop\Model\Exception as ShopException;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Region;
use Shop\Model\ReservationApi;

class Tools extends \RS\Controller\Admin\Front
{
    /**
     * Пересчитывает доходность заказов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxCalcProfit()
    {
        $position = $this->url->request('pos', TYPE_INTEGER);
        
        $order_api = new \Shop\Model\OrderApi();
        $result = $order_api->calculateOrdersProfit($position);
        
        if ($result === true) {
            return $this->result->addMessage(t('Пересчет успешно завершен'));
        } 
        elseif ($result === false) {
            return $this->result->addEMessage($order_api->getErrorsStr());
        }
        else {
            return $this->result
                            ->addSection('repeat', true)
                            ->addSection('queryParams', [
                                'data' => [
                                    'pos' => $result
                                ]]
                            );
        }
    }
    
    /**
     * Выводит содержимое лог файла обмена с кассами
     * 
     * @return string
     */
    function actionShowCashRegisterLog()
    {
        $this->wrapOutput(false);
        $log_file = \Shop\Model\CashRegisterType\AbstractType::getLogFilename();
        if (file_exists($log_file)) {
            echo '<pre>';
            readfile($log_file);
            echo '</pre>';
        } else {
            return t('Лог файл не найден');
        }
    }

    /**
     * Удаляет лог файл запросов к кассе
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDeleteCashRegisterLog()
    {
        $log_file = \Shop\Model\CashRegisterType\AbstractType::getLogFilename();
        if (file_exists($log_file)) {
            unlink($log_file);
            return $this->result->setSuccess(true)->addMessage(t('Лог-файл успешно удален'));
        } else {
            return $this->result->setSuccess(true)->addEMessage(t('Лог-файл отсутствует'));
        }
    }

    /**
     * Выполняет поиск ID заказа по строке
     *
     * @return string
     */
    function actionAjaxSearchOrder()
    {
        $api = new OrderApi();

        $term = $this->url->request('term', TYPE_STRING);
        $cross_multisite = $this->url->request('cross_multisite', TYPE_INTEGER);

        if ($cross_multisite) {
            //Устанавливаем поиск по всем мультисайтам
            $api->setMultisite(false);
        }

        $list = $api->search($term, ['id', 'order_num', 'user_fio', 'user'], 8);

        $json = [];
        foreach ($list as $order) {

            $user = $order->getUser();
            $json[] = [
                'label' => t('Заказ №%num от %date', [
                    'num' => $order['order_num'],
                    'date' => date('d.m.Y', strtotime($order['dateof']))
                ]),
                'id' => $order['id'],
                'desc' => t('Покупатель').':'.$user->getFio().
                    ($user['id'] ? "({$user['id']})" : '').
                    ($user['is_company'] ? t(" ; {$user['company']}(ИНН:{$user['company_inn']})") : '')
            ];
        }

        return json_encode($json);
    }
    /**
     * Выполняет поиск ID заказа по строке
     *
     * @return string
     */
    function actionAjaxSearchReservation()
    {
        $api = new ReservationApi();

        $term = $this->url->request('term', TYPE_STRING);
        $cross_multisite = $this->url->request('cross_multisite', TYPE_INTEGER);

        if ($cross_multisite) {
            //Устанавливаем поиск по всем мультисайтам
            $api->setMultisite(false);
        }

        $list = $api->search($term, ['id', 'phone', 'email', 'user'], 8);

        $json = [];
        foreach ($list as $reservation) {

            $user = $reservation->getUser();
            $json[] = [
                'label' => t('Предзаказ №%num от %date', [
                    'num' => $reservation['id'],
                    'date' => date('d.m.Y', strtotime($reservation['dateof']))
                ]),
                'id' => $reservation['id'],
                'desc' => t('Покупатель').':'.$user->getFio().
                    ($user['id'] ? "({$user['id']})" : '').
                    ($user['is_company'] ? t(" ; {$user['company']}(ИНН:{$user['company_inn']})") : '')
            ];
        }

        return json_encode($json);
    }

    public function actionUpdateCdekRegions()
    {
        try {
            $delivery = $this->getCdekDelivery();
            $page = $this->url->request('page', TYPE_INTEGER, 0);

            /** @var Cdek2 $delivery_type */
            $delivery_type = $delivery->getTypeObject();
            $new_page = $delivery_type->api->updateCdekRegionsStep($page);

            if ($new_page === null) {
                $this->result->setSuccess(true)->addMessage(t('База регионов СДЭК успешно обновлена'));
            } else {
                $this->result->addSection('repeat', true)->addSection('queryParams', [
                    'url' => $this->url->getSelfUrl(),
                    'data' => ['page' => $new_page],
                ]);
            }

            return $this->result;
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage());
        }
    }

    public function actionCdekSubscribeWebHooks()
    {
        try {
            $delivery = $this->getCdekDelivery();

            /** @var Cdek2 $delivery_type */
            $delivery_type = $delivery->getTypeObject();
            $delivery_type->api->webHooksSubscribe();

            return $this->result->setSuccess(true)->addMessage(t('Сайт успешно подписан на получение веб-хуков'));
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage());
        }
    }

    public function actionCdekInfoWebHooks()
    {
        try {
            $delivery = $this->getCdekDelivery();

            /** @var Cdek2 $delivery_type */
            $delivery_type = $delivery->getTypeObject();
            $response = $delivery_type->api->webHooksInfo();

            if (empty($response['entity']['url'])) {
                throw new ShopException(t('Произошла неизвестная ошибка'));
            }

            $message = t('Подписка активирована на адрес "%0"', [$response['entity']['url']]);

            return $this->result->setSuccess(true)->addMessage($message);
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage());
        }
    }

    public function actionCdekUnsubscribeWebHooks()
    {
        try {
            $delivery = $this->getCdekDelivery();

            /** @var Cdek2 $delivery_type */
            $delivery_type = $delivery->getTypeObject();
            $delivery_type->api->webHooksUnsubscribe();;

            return $this->result->setSuccess(true)->addMessage(t('Сайт успешно отписан от получения веб-хуков'));
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage());
        }
    }

    protected function getCdekDelivery()
    {
        $shop_config = ConfigLoader::byModule('shop');

        $delivery = new Delivery($shop_config['cdek_default_delivery']);
        if (!$delivery['id']) {
            $delivery_api = new DeliveryApi();
            foreach ($delivery_api->getList() as $item) {
                /** @var Delivery $item */
                if ($item->getTypeObject() instanceof Cdek2) {
                    $delivery = $item;
                    break;
                }
            }
        }

        if (!$delivery['id']) {
            throw new ShopException(t('Не создано ни одной доставки СДЭК'));
        }
        return $delivery;
    }

    /**
     * @return FormObject
     */
    function getCityFormObject()
    {
        $form_object = new FormObject(new PropertyIterator([
                    'city_id' => new Type\Integer([
                        'description' => t('Город'),
                        'tree' => ['\Shop\Model\RegionApi::staticTreeList'],
                        'attr' => [[
                            AbstractTreeListIterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES => true
                        ]]
                    ])
                ]));

        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('CityForm');

        return $form_object;
    }

    /**
     * @deprecated (21.03) - связан с устаревшим СДЭКом
     * Событие создание актуального файла страны по СДЕК
     *
     * @return string
     */
    function actionRebaseCdekFile()
    {
        if($this->url->isPost()){
            $country = $this->url->post('country', TYPE_STRING);
            $page = $this->url->post('page',TYPE_INTEGER);
            $flag = $this->url->post('flag',TYPE_INTEGER);

            if(!empty($country)){
                // Удаляем уже созданный файл
                $file_path = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/cdek_'.$country.'.csv';
                if (file_exists($file_path)) {
                    unlink($file_path);
                }


                // Новая строка-заголовок
                $file = fopen($file_path,'a');
                fputcsv($file, ['city_name','cdek_id','region_name','regionCode','subRegion','kladr','paymentLimit'],';');
                fclose($file);

                $data = \Shop\Model\DeliveryType\Cdek::getCitiesByCountryId($country, $page);

                if(!isset($data['close']))
                {
                    $this->result
                        ->addSection('repeat',true)
                        ->addSection('queryParams', [
                            'data'=> [
                                'country' => $country,
                                'page' => $data['page'],
                            ],
                            'url'=>\RS\Router\Manager::obj()->getAdminUrl('CountryCdekFile', []),
                        ]);
                }else
                    $this->result->addMessage(t('Данные по стране актуализированы'));
            }
            else{
                $this->result->addEMessage(t('Укажите страну'));
            }
        }

        $this->result->setSuccess(true);
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Актуализация базы городов СДЕК'));
        $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'items' => [
                new \RS\Html\Toolbar\Button\SaveForm(null, t('Обновить'))
            ]
        ]));

        $this->view->assign([
            'countries' => \Shop\Model\DeliveryType\Cdek::getCountries()
        ]);

        $helper['form'] = $this->view->fetch('%shop%/cdek_rebase.tpl');
        $helper->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * @deprecated (21.03) - связан с устаревшим СДЭКом
     * Обход выполения скрипта по 30 секунд, делает запрос на каждые 1000 строк из API CDEK
     * @return \RS\Controller\Result\Standard
     * @throws \SmartyException
     */
    function actionCountryCdekFile()
    {
        if($this->url->isPost()) {
            $country = $this->url->post('country', TYPE_STRING);
            $page = $this->url->post('page', TYPE_INTEGER);

            $data = \Shop\Model\DeliveryType\Cdek::getCitiesByCountryId($country, $page);

            if(!isset($data['close']))
            {
                $this->result
                    ->addSection('repeat',true)
                    ->addSection('queryParams', [
                        'data'=> [
                            'country' => $country,
                            'page' => $data['page'],
                        ],
                        'url'=>\RS\Router\Manager::obj()->getAdminUrl('CountryCdekFile', []),
                    ]);
            }else
                $this->result->addMessage(t('Данные по стране актуализированы'));
        }
        $this->result->setSuccess(true);
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Актуализация базы городов СДЕК'));
        $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'items' => [
                new \RS\Html\Toolbar\Button\SaveForm(null, t('Обновить'))
            ]
        ]));

        $this->view->assign([
            'countries' => \Shop\Model\DeliveryType\Cdek::getCountries()
        ]);

        $helper['form'] = $this->view->fetch('%shop%/cdek_rebase.tpl');
        $helper->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * @deprecated (21.03) - связан с устаревшим СДЭКом
     * Метод позволяет проверить, удается ли по названию города, региона найти ID в СДЕК
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCdekCityChecker()
    {
        $form_object = $this->getCityFormObject();

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Найти ID города в справочниках СДЕК'));
        $helper->viewAsForm();
        $helper->setFormObject($form_object);
        $helper->setBottomToolbar(new Element([
            'Items' => [
                new SaveForm(null, t('Проверить'))
            ]
        ]));

        if ($this->url->isPost() && $form_object->checkData()) {

            $city = new Region($form_object['city_id']);
            $region = $city->getParent();
            $country = $region->getParent();

            $address = new Address();
            $address->country_id = $country->id;
            $address->region_id = $region->id;
            $address->city_id = $city->id;

            $cdek = new Cdek();
            $cdek_city_id = $cdek->getCityIdByName($address);
            $this->view->assign([
                'cdek_city_id' => $cdek_city_id,
                'post' => true
            ]);
            $this->result->setSuccess(true);
        }

        $helper->setHeaderHtml($this->view->fetch('%shop%/delivery/cdek/city_check.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }
}

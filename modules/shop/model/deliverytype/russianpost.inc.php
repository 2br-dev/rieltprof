<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use \RS\Orm\Type;
use Shop\Model\Orm\Order;

class RussianPost extends Universal implements InterfaceIonicMobile
{
    
    function __construct()
    {
        $this->loadDefalutOptions();
    } 
    
    /**
    * Устанавливает узначения по умолчанию
    * 
    */
    function loadDefalutOptions()
    {
        $zoneApi = new \Shop\Model\ZoneApi();
        $this->setOption(['max_weight' => '20000']);
        $this->setOption([
            'rules' => [
              1 => [
                'zone' => $zoneApi->getZoneByTitle('Магистральный пояс 1')->id,
                'ruletype' => 'weight',
                'from' => '0',
                'to' => '20000',
                'actiontype' => 'fixed',
                'value' => '138.80 + floor(($W-1)/500)*12',
              ],
              2 => [
                'zone' => $zoneApi->getZoneByTitle('Магистральный пояс 2')->id,
                'ruletype' => 'weight',
                'from' => '0',
                'to' => '20000',
                'actiontype' => 'fixed',
                'value' => '140.70 + floor(($W-1)/500)*13.90',
              ],
              3 => [
                'zone' => $zoneApi->getZoneByTitle('Магистральный пояс 3')->id,
                'ruletype' => 'weight',
                'from' => '0',
                'to' => '20000',
                'actiontype' => 'fixed',
                'value' => '146.40 + floor(($W-1)/500)*20.30',
              ],
              4 => [
                'zone' => $zoneApi->getZoneByTitle('Магистральный пояс 4')->id,
                'ruletype' => 'weight',
                'from' => '0',
                'to' => '20000',
                'actiontype' => 'fixed',
                'value' => '178.30 + floor(($W-1)/500)*29.20',
              ],
              5 => [
                'zone' => $zoneApi->getZoneByTitle('Магистральный пояс 5')->id,
                'ruletype' => 'weight',
                'from' => '0',
                'to' => '20000',
                'actiontype' => 'fixed',
                'value' => '199 + floor(($W-1)/500)*33.70',
              ],
              6 => [
                'zone' => 'all',
                'ruletype' => 'weight',
                'from' => '10001',
                'to' => '20000',
                'actiontype' => 'delivery_percent',
                'value' => '30',
              ],
            ],
        ]);
    } 
    

    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Почта России');
    }
    
    /**
    * Возвращает описание типа доставки
    * 
    * @return string
    */
    function getDescription()
    {
        return t("Рассчет доставки Почтой России");
    }
    
    /**
    * Возвращает идентификатор данного типа доставки. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'russianpost';
    }


    /**
     * Рассчитывает структурированную информацию по сроку, который требуется для доставки товара по заданному адресу
     *
     * @param \Shop\Model\Orm\Order $order объект заказа
     * @param \Shop\Model\Orm\Address $address объект адреса
     * @param \Shop\Model\Orm\Delivery $delivery объект доставки
     * @return Helper\DeliveryPeriod | null
     */
    protected function calcDeliveryPeriod(\Shop\Model\Orm\Order $order,
                                          \Shop\Model\Orm\Address $address = null,
                                          \Shop\Model\Orm\Delivery $delivery = null)
    {
        //Получим город доставки
        if (!$address){
            $address = $order->getAddress();
        }

        $city = $address->getCity();

        if (isset($city['id'])){

            //Возвращаем текст указанный у города доставки
            $period = new Helper\DeliveryPeriod($city['russianpost_arrive_min'],
                $city['russianpost_arrive_max'],
                $city['russianpost_arriveinfo']);
        } 
        
        if (isset($period) && $period->hasPeriod()) {
            return $period;
        } else {
            return parent::calcDeliveryPeriod($order, $address, $delivery);
        }
    }

    /**
    * Возвращает HTML для приложения на Ionic
    * 
    * @param \Shop\Model\Orm\Order $order - объект заказа
    * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
    */
    function getIonicMobileAdditionalHTML(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Delivery $delivery)
    {
        return "";    
    }

    /**
     * Возвращает трек номер для отслеживания
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return boolean
     */
    public function getTrackNumber(Order $order)
    {
        return !empty($order['track_number']) ? $order['track_number'] : false;
    }

    /**
     * Возвращает ссылку на отслеживание заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     *
     * @return string
     */
    public function getTrackNumberUrl(Order $order)
    {
        $track_number = $this->getTrackNumber($order);
        if ($track_number){
            return "https://www.pochta.ru/tracking#".$track_number;
        }
        return false;
    }
}
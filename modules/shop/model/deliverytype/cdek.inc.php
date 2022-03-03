<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType;

use Catalog\Model\Orm\Product;
use Catalog\Model\ProductDimensions;
use Main\Model\Requester\ExternalRequest;
use RS\Orm\FormObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\DeliveryType\Cdek\Api;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Region;

/**
 * @deprecated (20.10) - устарел, будет удалён
 */
class Cdek extends AbstractType implements interfaceIonicMobile
{
    const API_URL = "https://integration.cdek.ru/"; //Основной URL
    const API_URL_CALCULATE = "http://api.cdek.ru/calculator/calculate_price_by_json.php"; //URL для калькуляции доставки
    const API_CALCULATE_VERSION = "1.0"; //Версия API для подсчёта стоимости доставки
    const DEVELOPER_KEY ="522d9ea0ad70744c58fd8d9ffae01fc1";// СДЭК попросил добавить дополнительный атрибут к запросу  28.09.2017

    protected $tariffId = [];  //Идентификатор тарифа по которому будет произведена доставка
    protected $delivery_cost_info = []; //Стоимость доставки по данному расчётному классу
    protected $cache_pochtomates; // Кэшированный список ПВЗ
    protected $cache_city_id = []; // Кэшированные id городов
    protected $api;

    public $log;

    protected static $cache_api_requests = [];

    function __construct()
    {
        $this->api = new Api($this->getOption('write_log'),$this->getOption('timeout'));
    }

    /**
     * Возвращает название расчетного модуля (типа доставки)
     *
     * @return string
     */
    function getTitle()
    {
        return t('СДЭК (устаревший)');
    }

    /**
     * Возвращает описание типа доставки
     *
     * @return string
     */
    function getDescription()
    {
        $link = RouterManager::obj()->getAdminUrl('edit', [
            'mod' => 'catalog',
        ], 'modcontrol-control');
        $link .= '#tab-7';

        $description = t('Доставка СДЭК <br/><br/>
        <div class="notice-box no-padd">
            <div class="notice-bg">
                Для работы доставки необходимо у товаров указаать <b>вес</b> и <b>габариты</b>.<br/> 
                Значения веса и габаритов по умолчанию можно указать в <u><a href="%link" target="_blank">настройках модуля "Каталог"</a></u>
            </div>
        </div>', ['link' => $link]);

        $description .= t('
            <div class="notice-box no-padd">
                <div class="notice-bg">
                    Данный расчётный класс больше не поддерживается обновлениями и в дальнейшем будет удалён из системы.<br>
                    Рекомендуем вам сменить используемый расчётный клас на "СДЭК".
                </div>
            </div>
        ', ['link' => $link]);

        return $description;
    }

    /**
     * Возвращает идентификатор данного типа доставки. (только англ. буквы)
     *
     * @return string
     */
    function getShortName()
    {
        return t('cdek');
    }

    /**
     * Возвращает какие поля адреса необходимы данной доставке
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        $fields = ['country', 'region', 'city'];
        if (!$this->hasPvz()) {
            $fields[] = 'address';
        }
        return $fields;
    }

    /**
     * Возвращает true если стоимость доставки можно расчитать на основе адреса доставки
     *
     * @param Address $address - адрес
     * @return bool
     */
    public function canCalculateCostByDeliveryAddress(Address $address): bool
    {
        return !empty($address['city_id']);
    }

    /**
     * Возвращает ORM объект для генерации формы или null
     *
     * @return \RS\Orm\FormObject | null
     */
    function getFormObject()
    {
        $properties = new \RS\Orm\PropertyIterator([
            'default_cash_on_delivery' => new Type\Integer([
                'maxLength' => 1,
                'default' => 0,
                'CheckboxView' => [1,0],
                'description' => t('Наложенный платёж? (значение по умолчанию)'),
                'hint' => t('Платёж при получении товаров. Если стоит галочка, и при создании заказа не выбрана оплата, то будет применена опция НП. В противном случае опция зависит 
                от типа оплаты, выбранного при оформлении заказа ( если тип оплаты Счет\Квитанция ПД4\Серсис Онлайн оплаты - опция НП не будет активна, в иных случаях - будет подразумеваться НП))')
            ]),
            'secret_login' => new Type\Varchar([
                'maxLength' => 150,
                'hint' => t('Используется дополнительно для расчёта стоимости<br/>Выдаётся СДЭК'),
                'description' => t('Логин для доступа к серверу расчётов'),
            ]),
            'secret_pass' => new Type\Varchar([
                'maxLength' => 150,
                'hint' => t('Используется дополнительно для расчёта стоимости<br/>Выдаётся СДЭК'),
                'description' => t('Пароль для доступа к серверу расчётов'),
            ]),
            'day_apply_delivery_to_block' => new Type\Integer([
                'description' => t('Прибавлять время на подготовку заказа к максимальному времени доставки'),
                'maxLength' => 1,
                'default' => 0,
                'CheckboxView' => [1,0],
                'hint' => t('Будет отображаено в блоке расчета стоимости доставки и на странице оформления заказа'),
            ]),
            'day_apply_delivery' => new Type\Integer([
                'maxLength' => 11,
                'default' => 1,
                'description' => t('Количество дней, через сколько будет произведена планируемая отправка заказа'),
            ]),
            'city_from_name' => new Type\Varchar([
                'maxLength' => 150,
                'description' => t('Название города отправления<br/>Например: Краснодар'),
            ]),
            'city_from_zipcode' => new Type\Varchar([
                'maxLength' => 11,
                'description' => t('Почтовый индекс города отправителя<br/>Например: 350000'),
            ]),
            'city_from' => new Type\Integer([
                'description' => t('Город отправитель'),
                'tree' => [['\Shop\Model\RegionApi', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]]
            ]),
            'tariffTypeCode' => new Type\Integer([
                'description' => t('Тариф'),
                'maxLength' => 11,
                'visible' => false,
                'List' => [['\Shop\Model\DeliveryType\Cdek\CdekInfo','getAllTariffs'], false],
                'ChangeSizeForList' => false,
                'attr' => [[
                    'size' => 16
                ]]
            ]),
            'tariffTypeList' => new Type\ArrayList([
                'description' => t('Список тарифов по приоритету'),
                'maxLength' => 1000,
                'runtime' => false,
                'attr' => [[
                    'multiple' => true
                ]],
                'template' => '%shop%/form/delivery/cdek/tariff_list.tpl',
                'hint' => t('При расчёте стоимости, если указаный тариф будет не доступен для отправления, то расчёт будет вестись по нижеследующему тарифу указанному в списке'),
                'listFromArray' => [[]]
            ]),
            'additional_services' => new Type\ArrayList([
                'maxLength' => 1000,
                'default' => 0,
                'attr' => [[
                    'size' => 7,
                    'multiple' => true
                ]],
                'list' => [['\Shop\Model\DeliveryType\Cdek','getAdditionalServices']],
                'description' => t('Добавлять дополнительные услуги к заказу:<br/>(Необязательно)'),
                'hint' => t('Дополнительные услуги зависят от Ваших условий договора. Обратитесь к менеджеру, за дальнейшими разъяснениями по использованию доп. услуг.')
            ]),
            'add_barcode_uniq' => new Type\Integer([
                'description' => t('Добавлять уникальное окончание к артикулу?'),
                'default' => 0,
                'maxLength' => 1,
                'CheckboxView' => [1,0],
                'hint' => t('Нужно только если у Вас артикулы совпадают у всех комплектаций товара, т.к. для СДЭКа это кретичный момент.'),
            ]),
            'write_log' => new Type\Integer([
                'description' => t('Вести лог запросов?'),
                'maxLength' => 1,
                'default' => 0,
                'CheckboxView' => [1,0],
            ]),
            'decrease_declared_cost' => new Type\Integer([
                'description' => t('Снижать объявленную стоимость товаров до 0.1 копейки'),
                'maxLength' => 1,
                'default' => 0,
                'CheckboxView' => [1,0],
                'hint' => t("Влияет на размер страховки"),
            ]),
            'delivery_recipient_vat_rate' => new Type\Varchar([
                'description' => t('Ставка НДС за доставку'),
                'default' => '',
                'listFromArray' => [[
                    '' => t('- Не указано -'),
                    'VATX' => 'Без НДС',
                    'VAT0' => '0%',
                    'VAT10' => '10%',
                    'VAT18' => '18%',
                    'VAT20' => '20%',
                ]],
            ]),
            'min_product_weight' => new Type\Integer([
                'description' => t('Минимальный вес одного товара (г)'),
                'hint' => t("Если указан - вес товаров с меньшим весом будет автоматически увеличен"),
                'default' => 0,
            ]),
            'auto_create_order' => new Type\Integer([
                'description' => t('Не создавать автоматически заказ в СДЭК при оформлении заказа на сайте'),
                'maxLength' => 1,
                'checkboxView' => [1,0],
                'default' => 0,
            ]),
            'timeout' => new Type\Integer([
                'description' => t('Максимальное время ожидания ответа на запрос (сек)'),
                'hint' => t("Если в течение указанного времени от сервера СДЭК  не будет ответа, то процесс ожидания прервется"),
                'default' => 20,
            ]),
            'unit_weight' => new Type\Integer([
                'description' => t('Запретить доставку, если <a target="_blank" href="https://global.cdek.ru/faq/vse-o-dostavke?faq_search%5Bsearch%5D=%D0%BE%D0%B1%D1%8A%D0%B5%D0%BC%D0%BD%D1%8B%D0%B9+%D0%B2%D0%B5%D1%81">объемный вес</a> превышает реальный у товаров в заказе'),
                'hint' => t("Если (ширина*длина*высота)/ 5000 > вес"),
                'checkboxView' => [1,0],
                'default' => 0,
            ]),
            'contact_person_to_print_document' => new Type\Integer([
                'description' => t('Передавать данные из поля "Контактное лицо" в особые отметки в "Печатная форма квитанции к заказу"'),
                'maxLength' => 1,
                'checkboxView' => [1,0],
                'default' => 0,
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Получение кода защиты для СДЭК запросов
     *
     * @param string $format - формат даты отправления
     * @return false|string
     */
    protected function getDateExecute( $format = "Y-m-d" )
    {
        $time = time() + ((int)$this->getOption("day_apply_delivery", 0) * 60 * 60 * 24);
        return date($format,$time);
    }

    /**
     * Получает секретный код основанный на MD5 и текущей дате
     *
     * @param string $date_execute - дата для ключа
     * @return string
     */
    protected function getSecure($date_execute)
    {
        return md5($date_execute."&".trim($this->getOption('secret_pass', null)));
    }

    /**
     * Возвращает список ПВЗ на основе адреса
     *
     * @param Address $address - адрес получателя
     * @return Pvz[]
     */
    public function getPvzByAddress(Address $address)
    {
        // Попробуем найти город в базе СДЭК
        $city_id = $this->getCityIdByName($address);

        $response = $this->api->getPvzList();

        $pvz_list = [];
        if ($response){
            $xml = @simplexml_load_string($response);
            if (isset($xml->Pvz)){
                foreach($xml->Pvz as $item){
                    if (($city_id == (string)$item['CityCode']) || (!$city_id && in_array($address->getCity()['title'], explode(', ', (string)$item['City'])) )) {
                        $pvz_list[] = $item;
                    }
                }
            }
        }
        // удаляем из адреса ПВЗ ковычки чтобы избежать проблем с json
        foreach ($pvz_list as $key => $item) {
            $pvz_list[$key]['Address'] = str_replace('"', ' ', $item['Address']);
            $pvz_list[$key]['Note'] = str_replace('"', ' ', $item['Note']);
        }

        $result = [];

        foreach ($pvz_list as $pochtomat) {
            $attr = (array) $pochtomat->attributes();
            $attr = $attr['@attributes'];
            $pvz = new \Shop\Model\DeliveryType\Cdek\Pvz();
            // Записываем свойства ПВЗ
            $pvz->setCode($attr['Code']);
            $pvz->setTitle($attr['Name']);
            $pvz->setCountry($attr['CountryName']);
            $pvz->setRegion($attr['RegionName']);
            $pvz->setCity($attr['City']);
            $pvz->setAddress($attr['Address']);
            $pvz->setWorktime($attr['WorkTime']);
            $pvz->setCoordX($attr['coordX']);
            $pvz->setCoordY($attr['coordY']);
            $pvz->setPhone($attr['Phone']);
            $pvz->setPaymentByCards(((string)$attr['HaveCashless'] == 'Да') ? 1 : 0);
            $pvz->setCashOnDelivery(isset($attr['cashOnDelivery']) ? $attr['cashOnDelivery'] : 0);
            $pvz->setNote($attr['Note']);
            // Удаляем записанные поля из extra
            $remove_key = ['Code', 'Name', 'CountryName', 'RegionName', 'City', 'coordX', 'coordY', 'Phone', 'cashOnDelivery', 'Note'];
            foreach ($remove_key as $key) {
                unset($attr[$key]);
            }
            // преобразуем массив графика работы
            $worktime_y = [];
            foreach ($pochtomat->WorkTimeY as $item) {
                $worktime_y[(string) $item['day']] = (string) $item['periods'];
            }
            $attr['WorkTimeY'] = $worktime_y;
            $pvz->setExtra($attr);

            $result[] = $pvz;
        }

        return $result;
    }


    /**
     * Возвращает, поддерживает ли данный способ доставки ПВЗ
     *
     * @return bool
     */
    public function hasPvz(): bool
    {
        if ($tariff_list = $this->getOption('tariffTypeList')) {
            $tariff_id = reset($tariff_list);
            $cdek_info = new \Shop\Model\DeliveryType\Cdek\CdekInfo();
            $tariffs = $cdek_info->getAllTariffsWithInfo();
            if (!empty($tariffs[$tariff_id]['regim_id']) && in_array($tariffs[$tariff_id]['regim_id'], [2, 4])) {
                return true;
            }
        }
        return false;
    }


    /**
     * Отправляет запрос на получение почтоматов для забора товара пользователем
     *
     * @param Order $order - объект заказа
     * @param Address $address - объект адреса
     * @param array $tariff - массив сведений по тарифу
     * @return array
     */
    protected function requestPochtomat(\Shop\Model\Orm\Order $order, $tariff, \Shop\Model\Orm\Address $address = null)
    {
        if (!$address){
            $address = $order->getAddress();
        }

        if ($this->cache_pochtomates === null) {
            $this->cache_pochtomates = [];
            // Попробуем найти город в базе СДЭК
            $city_id = $this->getCityIdByName($address);

            $response = $this->api->getPvzList();

            $pochtomates = [];
            if ($response){
                $xml = @simplexml_load_string($response);
                if (isset($xml->Pvz)) {
                    foreach($xml->Pvz as $item){
                        if (($city_id == (string)$item['CityCode']) || (!$city_id && strcasecmp((string)$item['ownerCode'], $tariff['ownerCode']) == 0 && in_array($address->getCity()['title'], explode(', ', (string)$item['City'])) )) {
                            $pochtomates[] = $item;
                        }
                    }
                }
            }

            // удаляем из адреса ПВЗ ковычки чтобы избежать проблем с json
            foreach ($pochtomates as $key=>$item) {
                $pochtomates[$key]['Address'] = str_replace('"', ' ', $item['Address']);
                $pochtomates[$key]['Note'] = str_replace('"', ' ', $item['Note']);
            }
            $this->cache_pochtomates = $pochtomates;
        }
        return $this->cache_pochtomates;
    }

    /**
     * Запрос на информацию по заказу
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return \SimpleXMLElement
     * @throws \RS\Exception
     */
    protected function requestGetInfo(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        if (!isset($extra_info['cdek_order_id']['data']['orderId'])){
            throw new \RS\Exception(t('[Ошибка] Не удалось получить сведения о заказе СДЭК'));
        }

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><InfoRequest/>");

        $data_create = date('Y-m-d');
        $sxml['Date']       = $data_create;
        $sxml['Account']    = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']     = $this->getSecure($data_create); //Генерируемый секретный ключ

        $sxml->ChangePeriod['DateBeg'] = date('Y-m-d', strtotime($order['dateof'])); //Дата начала запрашиваемого периода

        //Номер отправления
        $sxml->Order['DispatchNumber'] = $extra_info['cdek_order_id']['data']['orderId'];


        $xml = $sxml->asXML(); //XML заказа

        try{
            return @simplexml_load_string($this->api->getOrderInfoRequest($xml));
        }catch(\Exception $ex){
            throw new \RS\Exception($ex->getMessage().t("<br/> Файл:%0<br/> Строка:%1", [$ex->getFile(), $ex->getLine()]),$ex->getCode());
        }
    }


    /**
     * Запрос вызова курьера
     *
     * @param array $call - массив со сведениями об отправке
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return \SimpleXMLElement
     * @throws \RS\Exception
     */
    protected function requestGetCallCourier($call, \Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        if (!isset($extra_info['cdek_order_id']['data']['orderId'])){
            throw new \RS\Exception(t('[Ошибка] Не удалось получить сведения о заказе СДЭК'));
        }

        $adress = $order->getAddress();

        //Настройки текущего сайта
        $site_config = \RS\Config\Loader::getSiteConfig();

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><CallCourier/>");
        $data_create       = date('Y-m-d');
        $sxml['Date']      = $data_create;
        $sxml['Account']   = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']    = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['CallCount'] = 1; //Общее количество заявок для вызова курьера в документе

        //Сведения об узнаваемом заказе
        $sxml->Call['Date']    = date('Y-m-d',strtotime($call['Date']));
        $sxml->Call['TimeBeg'] = $call['TimeBeg'].":00";
        $sxml->Call['TimeEnd'] = $call['TimeEnd'].":00";

        //Если указано время обеда
        if (isset($call['use_lunch']) && $call['use_lunch']){
            $sxml->Call['LunchBeg'] = $call['LunchBeg'].":00";
            $sxml->Call['LunchEnd'] = $call['LunchEnd'].":00";
        }

        $sxml->Call['SendCityCode'] = $call['SendCityCode'];

        //Укажем телефон для вызова
        if (isset($call['use_admin']) && !$call['use_admin']){ //Если свой телефон
            $sxml->Call['SendPhone'] = $call['SendPhone'];
        }else{ //Если телефон администратора
            $sxml->Call['SendPhone'] = $site_config['admin_phone'];
        }

        $products = $order->getCart()->getProductItems();
        $order_weight = 0;
        $min_product_weight = $this->getOption('min_product_weight') / 1000;
        foreach ($products as $n=>$item) {
            $product_weight = $item['product']->getWeight($item['cartitem']['offer'], \Catalog\Model\Api::WEIGHT_UNIT_G);
            $correct_weight = ($product_weight < $min_product_weight) ? $min_product_weight : $product_weight;
            $order_weight += $correct_weight * $item['cartitem']['amount'];
        }
        $sxml->Call['SenderName']   = $call['SenderName'] ? $call['SenderName'] : $site_config['firm_name'];
        $sxml->Call['Weight']       = $order_weight;
        $sxml->Call['Comment']      = $call['Comment'];

        //Адрес
        $sxml->Call->Address['Street'] = $adress['address'];
        $sxml->Call->Address['House']  = "-";
        $sxml->Call->Address['Flat']   = "-";

        $xml = $sxml->asXML(); //XML заказа

        try{
            return @simplexml_load_string($this->api->createOrderCallCourier($xml));
        }catch(\Exception $ex){
            throw new \RS\Exception($ex->getMessage().t("<br/> Файл:%0<br/> Строка:%1", [$ex->getFile(), $ex->getLine()]),$ex->getCode());
        }
    }

    /**
     * Запрос статусов заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return \SimpleXMLElement
     * @throws \RS\Exception
     */
    protected function requestOrderStatus(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        if (!isset($extra_info['cdek_order_id']['data']['orderId'])){
            throw new \RS\Exception(t('[Ошибка] Не удалось получить сведения о заказе СДЭК'));
        }

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><StatusReport/>");
        $data_create = date('Y-m-d');
        $sxml['Date']        = $data_create;
        $sxml['Account']     = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']      = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['ShowHistory'] = 1; //Показ истории заказа

        //Сведения об узнаваемом заказе
        $sxml->Order['DispatchNumber'] = $extra_info['cdek_order_id']['data']['orderId'];

        $xml = $sxml->asXML(); //XML заказа

        try{
            return @simplexml_load_string($this->api->getOrderStatusReport($xml));
        }catch(\Exception $ex){
            throw new \RS\Exception($ex->getMessage().t("<br/> Файл:%0<br/> Строка:%1", [$ex->getFile(), $ex->getLine()]),$ex->getCode());
        }
    }

    /**
     * Запрос на удаление заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return \SimpleXMLElement
     * @throws \RS\Exception
     */
    protected function requestDeleteOrder(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><DeleteRequest/>");

        $data_create        = date('Y-m-d');
        $sxml['Date']       = $data_create;
        $sxml['Account']    = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Number']     = (isset($extra_info['cdek_act_number']['data']['actNumber']) && !empty($extra_info['cdek_act_number']['data']['actNumber'])) ? $extra_info['cdek_act_number']['data']['actNumber'] : 1; //Номер Акта
        $sxml['Secure']     = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['OrderCount'] = 1; //Общее количество заказов в xml


        //Номер отправления
        $sxml->Order['Number'] = $order['order_num'];

        $xml = $sxml->asXML(); //XML заказа

        try{
            return @simplexml_load_string($this->api->deleteOrder($xml));
        }catch(\Exception $ex){
            throw new \RS\Exception($ex->getMessage().t("<br/> Файл:%0<br/> Строка:%1", [$ex->getFile(), $ex->getLine()]),$ex->getCode());
        }
    }

    /**
     * Отправляет запрос на создание заказа в системе СДЭК
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param \Shop\Model\Orm\Address $address - объект адреса
     * @return \SimpleXMLElement
     * @throws \RS\Exception
     */
    protected function requestCreateOrder(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null)
    {
        if (!$address){
            $address = $order->getAddress();
        }

        //Настройки текущего сайта
        $site_config = \RS\Config\Loader::getSiteConfig();
        //Настройки выбранного способа оплаты
        $payment_config = new Payment($order['payment']);

        //Доп. данные указанные в доставке
        $extra_info = $order->getExtraKeyPair('delivery_extra');
        $extra_ti = $order->getExtraKeyPair('tariffId');
        if (isset($extra_info['pvz_data'])) {
            $extra_info = array_merge($extra_info, json_decode(htmlspecialchars_decode($extra_info['pvz_data']), true));
        } elseif (isset($extra_info['value'])) {
            $extra_info = array_merge($extra_info, json_decode(htmlspecialchars_decode($extra_info['value']), true));
        }

        $cash_on_delivery = 0;
        if (!empty($order['payment'])) {
            $payment = $order->getPayment()->getTypeObject();
            if ($payment->cashOnDelivery()) {
                $cash_on_delivery = 1;
            }
        } else {
            $cash_on_delivery = $this->getOption('default_cash_on_delivery');
        }
        $decrease_declared_cost = $this->getOption('decrease_declared_cost');

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><DeliveryRequest/>");
        $sxml['Number'] = $order['order_num']; //Номер заказа

        if ($this->getOption('day_apply_delivery')){
            $data_create        = date('Y-m-d',strtotime("+".$this->getOption('day_apply_delivery')." days"));
        }
        else{
            $data_create        = date('Y-m-d');
        }

        $sxml['Date']       = $data_create; //Дата создания заказа
        $sxml['Account']    = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']     = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['OrderCount'] = 1; //Сколько заказов будет отправлено
        $sxml['DeveloperKey'] = self::DEVELOPER_KEY;

        $delivery = $order->getDelivery(); //Сведения о доставке заказа
        $user     = $order->getUser(); //Получим пользователя

        $sxml->Order['Number'] = $order['order_num'];    //Номер заказа
        if (isset($extra_info['cityCode']) || isset($extra_info['CityCode'])) { //код города получателя если выбран какой-то пункт выдачи или если город найдётся в базе СДЭК
            $sxml->Order['RecCityCode'] = isset($extra_info['cityCode']) ? $extra_info['cityCode'] : $extra_info['CityCode']; //Для совместимости со старой версией проверяем
        } elseif ($recCityCode = $this->getCityIdByName($address)) {
            $sxml->Order['RecCityCode'] = $recCityCode;
        } else {
            $sxml->Order['RecCityPostCode'] = $address['zipcode'] ?: $address->getCity()['zipcode']; // иначе - почтовый индекс города получателя
        }

        $sender_city_id = false;
        if ($this->getOption('city_from') && $address_from = $this->getAddressFromCityId($this->getOption('city_from'))) {
            $sender_city_id = $this->getCityIdByName($address_from);
        }
        if ($sender_city_id) {
            $sxml->Order['SendCityCode'] = $sender_city_id;
        } else {
            $sxml->Order['SendCityPostCode'] = $this->getOption('city_from_zipcode');
        }

        $sxml->Order['RecipientName']         = !empty($address['contact_person']) ? $address['contact_person'] : $user->getFio(); //ФИО получателя
        $sxml->Order['RecipientEmail']        = $user['e_mail']; //E-mail получателя
        $sxml->Order['Phone']                 = $user['phone'];  //Телефон получателя
        $sxml->Order['TariffTypeCode']        = (!empty($extra_info['tariffId']) && in_array($extra_info['tariffId'], $this->getOption('tariffTypeList'))) ? $extra_info['tariffId'] : $extra_ti; //Тип тарифа, по которому будет доставка
        $delivery_recipient_cost = $cash_on_delivery ? $delivery->getDeliveryCost($order, null, false) : 0; //Цена за доставку, если наложенный платёж
        $sxml->Order['DeliveryRecipientCost'] = $delivery_recipient_cost;
        // При наложенном платеже - добавляем сведения о налоге
        if (!empty($delivery_recipient_cost) && $this->getOption('delivery_recipient_vat_rate')) {
            $sxml->Order['DeliveryRecipientVATRate'] = $this->getOption('delivery_recipient_vat_rate');
            $sxml->Order['DeliveryRecipientVATSum']  = round($delivery_recipient_cost * $this->getTaxRateById($this->getOption('delivery_recipient_vat_rate')), 2);
            $sxml->Order['RecipientCurrency']        = $order['currency']; //Валюта доставки
        }
        $sxml->Order['SellerName'] = $site_config['firm_name']; //Имя фирмы отправителя
        /**
         * @var \Catalog\Model\Orm\Currency
         */
        $default_currency = \Catalog\Model\CurrencyApi::getBaseCurrency();
        $sxml->Order['ItemsCurrency']    = $default_currency['title']; //Код валюты в которой был составлен заказ

        if ($this->getOption('contact_person_to_print_document')) {
            $sxml->Order['Comment'] = PHP_EOL . 'получатель: ' . $order->contact_person;
        }

        //Адрес куда будет доставлено
        $sxml->Order->Address['Street'] = $address['address'];
        if (!empty($address['house'])){
            $house =$address['house'];
        }else{
            $house = '-';
        }
        $sxml->Order->Address['House']  = $house;

        if (!empty($address['apartment'])){
            $flat =$address['apartment'];
        }else{
            $flat = '-';
        }
        $sxml->Order->Address['Flat']   = $flat;
        $cdek_info = new \Shop\Model\DeliveryType\Cdek\CdekInfo();
        $tariffs = $cdek_info->getAllTariffsWithInfo();
        $extra_ti = $order->getExtraKeyPair('tariffId');
        if (isset($tariffs[$extra_ti])) {
            $tariff = $tariffs[$extra_ti];
            if ($tariff && in_array($tariff['regim_id'], [2, 4])) { //Если нужны почтоматы
                if ($selected_pvz = $order->getSelectedPvz()) {
                    $sxml->Order->Address['PvzCode'] = $selected_pvz->getCode();
                    $order->addExtraInfoLine(t('Выбран пункт забора'), $selected_pvz->getAddress(), null, 'pvz', Order::EXTRAINFOLINE_TYPE_DELIVERY);
                }
            }
        }

        //Упаковка с товарами
        $products = $order->getCart()->getProductItems();
        $cartdata = $order->getCart()->getPriceItemsData();
        $sxml->Order->Package['Number']  = $order['order_num'];
        $sxml->Order->Package['BarCode'] = $order['order_num'];

        $order_weight = 0;
        $min_product_weight = $this->getOption('min_product_weight') / 1000;

        $i=0;
        $max_sizes = [0, 0, 0];
        $secondary_max_sizes = [0, 0, 0];
        $volume = 0;
        $many_items = count($products) > 1;
        foreach ($products as $n=>$item) {
            /**
             * @var \Catalog\Model\Orm\Product $product
             */
            $product     = $item['product'];
            $barcode     = $product->getBarCode($item['cartitem']['offer']);
            $offer_title = $product->getOfferTitle($item['cartitem']['offer']);

            if ($this->getOption('add_barcode_uniq', 0) && $product->isOffersUse() && $item['cartitem']['offer']){ //Если нужно уникализировать артикул
                $barcode = $barcode."-".$product['offers']['items'][(int) $item['cartitem']['offer']]['id'];
            }

            $sxml->Order->Package->Item[$i]['WareKey'] = $barcode; //Артикул

            $cartdata_item = $cartdata['items'][$n]['single_cost'] - ($cartdata['items'][$n]['discount'] / $item['cartitem']['amount']);
            $cartdata_item_commission = $cartdata_item * $payment_config['commission'] / 100;
            if (!empty($payment_config['commission']) && $payment_config['commission_as_product_discount']){ //Учет опции "Присваивать комиссию в качестве скидки или наценки к товарам"
                $cartdata_item += $cartdata_item_commission;
            }
            $sxml->Order->Package->Item[$i]['Cost']    = $decrease_declared_cost ? '0.001' : $cartdata_item; //Цена товара
            $item_payment = $cash_on_delivery ? $cartdata_item : 0; //Оплата при получении, только есть указано в настройках иначе 0
            $sxml->Order->Package->Item[$i]['Payment'] = $item_payment;
            if (!empty($item_payment)) { // При наложенном платеже - добавляем сведения о налоге
                $tax_id = $this->getRightTaxForProduct($order, $product);
                $sxml->Order->Package->Item[$i]['PaymentVATRate'] = $tax_id;
                $sxml->Order->Package->Item[$i]['PaymentVATSum'] = round($item_payment * $this->getTaxRateById($tax_id), 2);
            }

            $product_weight = $product->getWeight($item['cartitem']['offer'], \Catalog\Model\Api::WEIGHT_UNIT_G);
            $sxml->Order->Package->Item[$i]['Weight'] = ($product_weight < $min_product_weight) ? $min_product_weight : $product_weight;
            $sxml->Order->Package->Item[$i]['Amount'] = $item['cartitem']['amount'];
            $order_weight += $sxml->Order->Package->Item[$i]['Weight'] * $sxml->Order->Package->Item[$i]['Amount'];

            if ($product['title'] == $offer_title){ //Если наименования комплектаций совпадает, то покажем только название товара
                $sxml->Order->Package->Item[$i]['Comment'] = $product['title'];
            }else{
                $sxml->Order->Package->Item[$i]['Comment'] = $offer_title ? $product['title']." [".$offer_title."]" : $product['title'];
            }

            $dimensions_object = $product->getDimensionsObject();
            $length = $dimensions_object->getLength(ProductDimensions::DIMENSION_UNIT_SM);
            $width = $dimensions_object->getWidth(ProductDimensions::DIMENSION_UNIT_SM);
            $height = $dimensions_object->getHeight(ProductDimensions::DIMENSION_UNIT_SM);
            $sizes = [$length, $width, $height];
            rsort($sizes);
            foreach ($max_sizes as $key => $value) {
                if ($sizes[$key] > $value) {
                    $secondary_max_sizes[$key] = ($item['cartitem']['amount'] > 1) ? $sizes[$key] : $max_sizes[$key];
                    $max_sizes[$key] = $sizes[$key];
                }
            }
            $volume += $dimensions_object->getVolume(ProductDimensions::DIMENSION_UNIT_SM) * $item['cartitem']['amount'];
            if ($item['cartitem']['amount'] > 1) {
                $many_items = true;
            }

            $i++;
        }
        $sxml->Order->Package['Weight']  = $order_weight;
        if ($many_items) {
            $max_sizes[2] = $max_sizes[2] + $secondary_max_sizes[2];
            rsort($max_sizes);
            $root3 = pow($volume, 1 / 3);
            if ($max_sizes[0] > $root3) {
                $size_a = $max_sizes[0];
                $volume_left = $volume / $size_a;
                $root2 = sqrt($volume_left);
                if ($max_sizes[1] > $root2) {
                    $size_b = $max_sizes[1];
                    $size_c = $volume_left / $size_b;
                } else {
                    $size_b = $root2;
                    $size_c = $root2;
                }
            } else {
                $size_a = $root3;
                $size_b = $root3;
                $size_c = $root3;
            }
        } else {
            $size_a = $max_sizes[0];
            $size_b = $max_sizes[1];
            $size_c = $max_sizes[2];
        }

        $sxml->Order->Package['SizeA'] = $size_a;
        $sxml->Order->Package['SizeB'] = $size_b;
        $sxml->Order->Package['SizeC'] = $size_c;

        //Добавление дополнительных услуг
        $additional_services = $this->getOption('additional_services',null);
        if (!empty($additional_services)){
            $i=0;
            foreach($additional_services as $service){
                $sxml->Order->AddService[$i]['ServiceCode'] = $service;
                $i++;
            }
        }

        $xml = $this->toFormatedXML($sxml->asXML()); //XML заказа

        try{
            return @simplexml_load_string($this->api->createNewOrder($xml));
        }catch(\Exception $ex){
            throw new \RS\Exception($ex->getMessage().t("<br/> Файл:%0<br/> Строка:%1", [$ex->getFile(), $ex->getLine()]),$ex->getCode());
        }

    }

    /**
     * Запрашивает информацию о доставке
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param \Shop\Model\Orm\Address $address - объект адреса доставки
     * @return mixed
     * @throws \RS\Exception
     */
    protected function requestDeliveryInfo(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null)
    {
        if (!$address){
            $address = $order->getAddress();
        }

        //Параметры
        $query_info = [
            'version' => self::API_CALCULATE_VERSION,
        ];
        $sender_city_id = false;
        if ($this->getOption('city_from') && $address_from = $this->getAddressFromCityId($this->getOption('city_from'))) {
            $sender_city_id = $this->getCityIdByName($address_from);
        }
        if ($sender_city_id) {
            $query_info['senderCityId'] = $sender_city_id;
        } else {
            $query_info['senderCityPostCode'] = $this->getOption('city_from_zipcode');
        }
        if ($receiverCityId = $this->getCityIdByName($address)) {
            $query_info['receiverCityId'] = $receiverCityId;
        } else {
            $query_info['receiverCityPostCode'] = $address['zipcode'] ?: $address->getCity()['zipcode'];
        }

        //Код тарифа
        $tariffsList = $this->getOption('tariffTypeList');
        $query_info['tariffId']   = null;
        $query_info['tariffList'] = [];
        if (!empty($tariffsList)){
//            $query_info['tariffId'] = $tariffsList[0];  // id тарифа, не нужен, если есть tariffsList
            foreach($tariffsList as $priority=>$tariff){
                $arr[] = [
                    'priority' => $priority,
                    'id' => $tariff,
                ];
            }
            $query_info['tariffList'] = $arr;
        }else{
            $this->addError(t('Не заданы тарифы для доставок.'));
        }



        //Секретный логин и пароль, если они указаны
        $secret_login = trim($this->getOption('secret_login',false));
        $secret_pass  = trim($this->getOption('secret_pass',false));
        if ($secret_login && $secret_pass){
            $query_info['authLogin']   = $secret_login;
            $date                      = $this->getDateExecute();
            $query_info['secure']      = $this->getSecure($date);
            $query_info['dateExecute'] = $date;
        }

        //Переберём товары
        $items = $order->getCart()->getProductItems();
        $product_arr = [];
        $min_product_weight = $this->getOption('min_product_weight') / 1000;
        foreach ($items as $key=>$item){
            /** @var Product $product */
            $product  = $item['product'];
            $cartitem = $item['cartitem'];
            $product->fillProperty();

            //Вес
            $product_weight = $product->getWeight($item['cartitem']['offer'], \Catalog\Model\Api::WEIGHT_UNIT_KG);
            $correct_weight = ($product_weight < $min_product_weight) ? $min_product_weight : $product_weight;
            $product_arr['weight'] = $correct_weight;

            //Длинна
            $length = $product->getDimensionsObject()->getLength(ProductDimensions::DIMENSION_UNIT_SM);
            if ($length) {
                $product_arr['length'] = $length;
            } else {
                $this->addError(t('У товара с артикулом %0 не указана характеристика длинны.', [$product['barcode']]));
            }

            //Ширина
            $width = $product->getDimensionsObject()->getWidth(ProductDimensions::DIMENSION_UNIT_SM);
            if ($width) {
                $product_arr['width'] = $width;
            } else {
                $this->addError(t('У товара с артикулом %0 не указана ширина.', [$product['barcode']]));
            }

            //Высота
            $height = $product->getDimensionsObject()->getHeight(ProductDimensions::DIMENSION_UNIT_SM);
            if ($height) {
                $product_arr['height'] = $height;
            } else {
                $this->addError(t('У товара с артикулом %0 не указана высота.', [$product['barcode']]));
            }

            // Объемный вес
            if ($this->getOption('unit_weight', false)) {
                $unit_weight = ($length * $width * $height) / 5000;
                if ($unit_weight > $product_arr['weight']) {
                    $this->addError(t('Габариты товара с артикулом %0 слишком велики для данной доставки', [$product['barcode']]));
                }
            }

            for ($i = 0; $i < $cartitem['amount']; $i++) {
                $query_info['goods'][] = $product_arr;
            }
        }

        $external_response = (new ExternalRequest('delivery_cdek', self::API_URL_CALCULATE))
            ->setMethod(ExternalRequest::METHOD_POST)
            ->setContentType(ExternalRequest::CONTENT_TYPE_JSON)
            ->setTimeout($this->getOption('timeout') ?: 20)
            ->setParams($query_info)
            ->setEnableLog((bool)$this->getOption('write_log'))
            ->executeRequest();

        $response = $external_response->getResponseJson();

        if ( empty($response) ){
            $this->addError(t('Повторите попытку позже.'));
        }

        if ( isset($response['error']) ){
            foreach ($response['error'] as $error){
                $this->addError($error['code']." ".$error['text']);
            }
        } else {
            $this->setTariffId($response['result']['tariffId']);
        }

        $cdek_info = new \Shop\Model\DeliveryType\Cdek\CdekInfo();
        $tariffs = $cdek_info->getAllTariffsWithInfo();
        if ( isset($response['result']) ){
            $tariff = $tariffs[$response['result']['tariffId']];
            if ( $tariff && in_array($tariff['regim_id'], [2, 4]) ) { //Если нужны почтоматы
                $pochtomates = $this->requestPochtomat($order, $tariff);
                if ( empty($pochtomates) ) {
                    $this->addError(t('В указанном населённом пункте нет пунктов самовывоза'));
                }
            }
        }

        return $response;
    }

    /**
     * Получает валюту по имени этой волюты пришедшей из СДЭК
     *
     * @param string $name - сокращённое название валюты из СДЭК
     * @return mixed
     * @throws \RS\Orm\Exception
     */
    protected function getCurrencyByName($name)
    {
        //Подгружим валюты системы
        $currencies = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Currency())
            ->where([
                'public' => 1
            ])
            ->orderby('`default` DESC')
            ->objects(null,'title');

        if (isset($currencies[$name])){
            return $currencies[$name];
        }else{
            foreach($currencies as $currency){
                if ($currency['default']){
                    return $currency;
                }
            }
        }
    }

    /**
     * Добавляет ошибки в комментарий админа в заказе через ORM запрос
     *
     * @param string $action - действие русскими словами в родительном падеже
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param array $errors - массив ошибок из ответного XML
     * @throws \RS\Db\Exception
     */
    protected function addErrorsToOrderAdminComment($action, $order, $errors)
    {
        if($action === NULL){
            $action = t("создание заказа");
        }
        $text = "";

        foreach ($errors as $error){
            $str = t("СДЭК ошибка ").$action.": ";
            if (isset($error['ErrorCode'])){
                $str .= "[".$error['ErrorCode']."]";
            }
            $text .= $str." ".$error['Msg']."\n";
        }
        $this->addToOrderAdminComment($order,$text);
    }


    /**
     * Возвращает HTML виджет временем прозвона покупателя и отправляет запрос на вызов
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    protected function cdekGetCallCourierHTML(\Shop\Model\Orm\Order $order)
    {
        $view = new \RS\View\Engine();
        $request = new \RS\Http\Request();

        $cdekInfo = new Cdek\CDEKInfo();

        if ($request->isPost()){
            $call = $request->request('call',TYPE_ARRAY,false); //Массив со информациями о вызове
            $responce = $this->requestGetCallCourier($call, $order);
            if (isset($responce->Call['ErrorCode'])){
                $this->addError(t('Не удалось отправить запрос на вызов курьера.<br/> ').$responce->Call['Msg']);
            }else{
                $view->assign([
                    'success' => t('Сделан вызов курьера на %0.<br/> 
                Ожидание курьера с %1:00 по %2:00',
                        [$call['Date'], $call['TimeBeg'], $call['TimeEnd']])
                ]);
            }
        }

        $view->assign([
            'title' => t('Вызов курьера для забора товара СДЭКом'),
            'current_date' => date('Y-m-d',time()+ 24 * 60 * 60),
            'time_range' => range(1,24),
            'time_default_start' => 10,
            'time_default_end' => 13,
            'order' => $order,
            'delivery_type' => $this,
            'current_city' => $this->getOption('city_from_name',''),
            'regions' => $cdekInfo->getAllCities(),
            'errors' => $this->getErrors()
        ]);
        return $view->fetch('%shop%/form/delivery/cdek/cdek_call_courier.tpl');
    }

    /**
     * Пересоздаёт заказ в СДЭК
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    protected function cdekReCreateOrder(\Shop\Model\Orm\Order $order)
    {
        $this->cdekDeleteOrder($order);

        if (isset($order['extra']['extrainfo']['cdek_order_id'])){
            $order_extra = $order['extra'];
            unset($order_extra['extrainfo']['cdek_order_id']);
            $order['extra'] = $order_extra;
            //Запишем данные в таблицу, чтобы не вызывать повторного сохранения
            \RS\Orm\Request::make()
                ->update()
                ->from(new \Shop\Model\Orm\Order())
                ->set([
                    '_serialized' => serialize($order['extra'])
                ])
                ->where([
                    'id' => $order['id']
                ])->exec();
        }
        $isorderquerry = true;
        //Пересоздадим заказ
        $this->onOrderCreate($order, $order->getAddress(),$isorderquerry);

        return t("Заказ успешно пересоздан.");
    }

    /**
     * Возвращает HTML виджет с печатной формой
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    protected function cdekDeleteOrder(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        //Отправим запрос
        $result = $this->requestDeleteOrder($order);

        //Если старая версия и номер акта небыл указан, добавим его из сообщения
        if (empty($extra_info['cdek_act_number']['data']['actNumber'])) {
            $act_number = (string)$result->Order[0]['Number'];
        } else {
            $act_number = $extra_info['cdek_act_number']['data']['actNumber'];
        }

        $order->removeExtraInfoLine('cdek_act_number');
        $order->removeExtraInfoLine('cdek_order_id');
        //Запишем данные в таблицу, чтобы не вызывать повторного сохранения
        \RS\Orm\Request::make()
            ->update()
            ->from(new \Shop\Model\Orm\Order())
            ->set([
                '_serialized' => serialize($order['extra'])
            ])
            ->where([
                'id' => $order['id']
            ])->exec();

        if (empty($this->is_cdekDeleteOrder_action)) {
            $this->is_cdekDeleteOrder_action = true;
            return $this->cdekDeleteOrder($order);
        }

        $status = [];
        if (isset($result->Order)){
            foreach ($result->Order as $deleteRequest){
                $status[] = (string)$deleteRequest['Msg'];
            }
        }

        $view = new \RS\View\Engine();
        $view->assign([
            'status' => $status
        ]);

        return $view->fetch('%shop%/form/delivery/cdek/cdek_delete_order.tpl');
    }


    /**
     * Возвращает HTML виджет с печатной формой
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    protected function cdekGetPrintDocument(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        if (!isset($extra_info['cdek_order_id']['data']['orderId'])){
            return t('[Ошибка] Не удалось получить сведения о заказе СДЭК');
        }


        $view = new \RS\View\Engine();

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><OrdersPrint/>");

        $data_create        = date('Y-m-d');
        $sxml['Date']       = $data_create;
        $sxml['Account']    = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']     = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['OrderCount'] = 1; //Общее количество заказов в xml
        $sxml['CopyCount']  = 2; //Количество копий печатной формы в одном документе

        //Номер отправления
        $sxml->Order['DispatchNumber'] = $extra_info['cdek_order_id']['data']['orderId'];

        $xml = $sxml->asXML(); //XML заказа

        $view->assign([
            'xml' => $xml,
            'api_url' => self::API_URL
        ]);

        return $view->fetch('%shop%/form/delivery/cdek/cdek_print_form.tpl');
    }


    /**
     * Возвращает печать ШК-места
     *
     * @param Order $order
     * @return string
     * @throws \SmartyException
     */
    protected function cdekGetPrintLabelForm(\Shop\Model\Orm\Order $order)
    {
        $extra_info = $order->getExtraInfo();

        if (!isset($extra_info['cdek_order_id']['data']['orderId'])){
            return t('[Ошибка] Не удалось получить сведения о заказе СДЭК');
        }


        $view = new \RS\View\Engine();

        //Подготовим XML
        $sxml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><OrdersPackagesPrint/>");

        $data_create        = date('Y-m-d');
        $sxml['Date']       = $data_create;
        $sxml['Account']    = trim($this->getOption('secret_login',null)); //Id аккаунта
        $sxml['Secure']     = $this->getSecure($data_create); //Генерируемый секретный ключ
        $sxml['OrderCount'] = 1; //Общее количество заказов в xml
        $sxml['CopyCount']  = 2; //Количество копий печатной формы в одном документе

        //Номер отправления
        $sxml->Order['DispatchNumber'] = $extra_info['cdek_order_id']['data']['orderId'];

        $xml = $sxml->asXML(); //XML заказа

        $view->assign([
            'xml' => $xml,
            'api_url' => self::API_URL
        ]);

        return $view->fetch('%shop%/form/delivery/cdek/cdek_print_label_form.tpl');
    }

    /**
     * Возвращает HTML виджет с информацией о заказе
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    protected function cdekGetInfo(\Shop\Model\Orm\Order $order)
    {
        try{
            $response = $this->requestGetInfo($order);
        } catch (\Exception $ex){
            return $ex->getMessage();
        }

        $cdekInfo = new \Shop\Model\DeliveryType\Cdek\CDEKInfo();
        $tariffs = $cdekInfo->getRusTariffs() + $cdekInfo->getInternationTariffs();

        $view = new \RS\View\Engine();
        //Если есть доп услуга

        if (isset($response->Order->AddedService) && !empty($response->Order->AddedService['ServiceCode'])){
            $addServices  = $cdekInfo->getAllAdditionalServices();
            $service_code = (integer)$response->Order->AddedService['ServiceCode'];
            if (isset($addServices[$service_code])){
                $view->assign([
                    'addTariffCode' => $addServices[$service_code]
                ]);
            }

        }


        $view->assign([
            'order_info' => $response->Order,
            'title' => t('Информация о заказе СДЭК'),
            'tariffCode' => $tariffs[(integer)$response->Order['TariffTypeCode']],
            'tariffs' => $tariffs,
            'order' => $order,
            'delivery_type' => $this
        ]);
        return $view->fetch('%shop%/form/delivery/cdek/cdek_order_info.tpl');
    }


    /**
     * Возвращает HTML виджет со статусом заказа для админки
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    protected function cdekGetHtmlStatus(\Shop\Model\Orm\Order $order)
    {
        try{
            $response = $this->requestOrderStatus($order);
        } catch (\Exception $ex){
            return $ex->getMessage();
        }


        $view = new \RS\View\Engine();
        $view->assign([
            'order_info' => $response->Order,
            'title' => t('Статус заказа'),
            'order' => $order,
            'delivery_type' => $this
        ]);
        return $view->fetch('%shop%/form/delivery/cdek/cdek_get_status.tpl');
    }

    /**
     * Возвращает текст, в случае если доставка невозможна. false - в случае если доставка возможна
     *
     * @param \Shop\Model\Orm\Order $order
     * @param \Shop\Model\Orm\Address $address - Адрес доставки
     * @return bool|mixed|string
     * @throws \RS\Exception
     */
    function somethingWrong(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null)
    {
        if (!$address){
            $address = $order->getAddress();
        }

        $this->requestDeliveryInfo($order, $address);

        if ($this->hasErrors()){ //Если есть ошибки
            return $this->getErrorsStr();
        }

        return false;
    }

    /**
     * Действие с запросами к заказу для получения дополнительной информации от доставки
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return string|void
     * @throws \Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function actionOrderQuery(\Shop\Model\Orm\Order $order)
    {
        $url = new \RS\Http\Request();
        $method = $url->request('method',TYPE_STRING,false);
        switch ($method){
            case "getPrintLabelForm": //Получение
                return $this->cdekGetPrintLabelForm($order);
                break;
            case "getCallCourierHTML": //Получение статуса заказа
                return $this->cdekGetCallCourierHTML($order);
                break;
            case "getInfo": //Получение статуса заказа
                return $this->cdekGetInfo($order);
                break;
            case "getPrintDocument": //Получение статуса заказа
                return $this->cdekGetPrintDocument($order);
                break;
            case "deleteOrder": //Получение статуса заказа
                return $this->cdekDeleteOrder($order);
                break;
            case "recreateOrder": //Получение статуса заказа
                return $this->cdekReCreateOrder($order);
                break;
            case "getStatus": //Получение статуса заказа
            default:
                return $this->cdekGetHtmlStatus($order);
                break;
        }
    }

    /**
     * Возвращает дополнительный HTML для админ части в заказе
     *
     * @param \Shop\Model\Orm\Order $order - заказ доставки
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getAdminHTML(\Shop\Model\Orm\Order $order)
    {
        $view = new \RS\View\Engine();

        $view->assign([
            'order' => $order,
        ]);

        return $view->fetch("%shop%/form/delivery/cdek/cdek_additional_html.tpl");
    }

    /**
     * Получает выбранные тарифы для отправки доставки
     *
     * @return array
     */
    protected function getSelectedTariffs()
    {
        return $this->getOption('tariffTypeList');
    }

    /**
     * Возвращет первый выбранный пользователем тариф
     *
     * @return integer
     */
    protected function getSelectedFirstTariffId()
    {
        $tariffs    = $this->getSelectedTariffs();
        return !empty($tariffs) ? (int)current($tariffs) : false;
    }

    /**
     * Возвращает информацию по первому выбранному тарифу пользователем
     *
     * @return false|array
     */
    protected function getSelectedFirstTariffInfo()
    {
        $tariff_id = $this->getSelectedFirstTariffId();

        if (!$tariff_id){
            return false;
        }

        $cdek_info = new \Shop\Model\DeliveryType\Cdek\CdekInfo();
        $tariffs = $cdek_info->getAllTariffsWithInfo();

        return $tariffs[$tariff_id];
    }


    /**
     * Возвращает дополнительный HTML для публичной части с выбором в заказе
     *
     * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
     * @param \Shop\Model\Orm\Order $order - заказ доставки
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getAddittionalHtml(\Shop\Model\Orm\Delivery $delivery, \Shop\Model\Orm\Order $order = null)
    {
        $view = new \RS\View\Engine();
        if (!$order){
            $order = \Shop\Model\Orm\Order::currentOrder();
        }
        $this->getDeliveryCostText($order, null, $delivery);

        $pochtomates = $this->getPvzList($order);
        if (!empty($pochtomates)){ //Если нужны почтоматы
            return $this->getAdditionalHtmlForPickPoints($delivery, $order, $pochtomates);
        }

        $view->assign([
                'errors'       => $this->getErrors(),
                'order'        => $order,
                'extra_info'   => $order->getExtraKeyPair(),
                'delivery'     => $delivery,
                'deliverytype' => $this,
            ] + \RS\Module\Item::getResourceFolders($this));

        return $this->wrapByWidjet($delivery, $order, $view->fetch("%shop%/delivery/cdek/additional_html.tpl"));
    }


    /**
     * Возвращает дополнительный HTML для административной части с выбором опций доставки в заказе
     *
     * @param \Shop\Model\Orm\Order $order - заказ доставки
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getAdminAddittionalHtml(\Shop\Model\Orm\Order $order = null)
    {
        return '';
        /*$view = new \RS\View\Engine();

        //Получим данные потоварам
        $products = $order->getCart()->getProductItems();
        if (empty($products)){
            $this->addError(t('В заказ не добавлено ни одного товара'));
        }

        $pickpoints = $this->getPvzList($order); //Получим почтоматы

        //Получим цену с параметрами по умолчанию
        $cost = $this->getDeliveryCostText($order, null, $order->getDelivery());


        $view->assign([
                'errors'     => $this->getErrors(),
                'order'      => $order,
                'cost'       => $cost,
                'extra_info' => $order->getExtraKeyPair(),
                'cdek'       => $this,
                'pickpoints' => $pickpoints,
            ] + \RS\Module\Item::getResourceFolders($this));

        return $view->fetch("%shop%/form/delivery/cdek/admin_pochtomates.tpl");*/
    }

    /**
     * Функция срабатывает после создания заказа
     *
     * @param \Shop\Model\Orm\Order $order     - объект заказа
     * @param \Shop\Model\Orm\Address $address - Объект адреса
     * @return void
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    function onOrderCreate(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null, $isorderquerry = false)
    {

        if ($this->getOption('auto_create_order') && $isorderquerry == false) {

            return ;

        }else{
            $extra = $order->getExtraInfo();
            if (!isset($extra['cdek_order_id'])) { //Если заказ в СДЭК ещё не создан
                $created_order = $this->requestCreateOrder($order, $address);
                //Итак смотрим, если в ответе если у первого элемента ответа, есть ErrorCode,
                //то добавим ошибки в админ поле заказа.
                //Иначе запишем все данные и добавим в коммент сведения об успешном создании заказа
                if ($created_order) {//Если дождались ответ от СДЭК
                    if (isset($created_order->Order[0]['ErrorCode'])) { //Если есть ошибки, добавим в комметарий
                        $this->addErrorsToOrderAdminComment("создание заказа", $order, $created_order->Order);
                    } else {//Если ошибок нет
                        $cdek_order_id = (string)$created_order->Order[0]['DispatchNumber'];
                        $cdek_act_number = (string)$created_order->Order[0]['Number'];
                        $this->removeFromOrderDeliveryErrorInfoLine($order);
                        $order->addExtraInfoLine(
                            t('id заказа СДЭК'),
                            '<a href="http://lk.cdek.ru/" target="_blank">' . t('Перейти к заказу №%0', [$cdek_order_id]) . '</a>',
                            [
                                'orderId' => $cdek_order_id
                            ],
                            'cdek_order_id',
                            Order::EXTRAINFOLINE_TYPE_DELIVERY
                        );
                        $order->addExtraInfoLine(
                            t('Номер акта СДЭК'),
                            '',
                            [
                                'actNumber' => $cdek_act_number
                            ],
                            'cdek_act_number',
                            Order::EXTRAINFOLINE_TYPE_DELIVERY
                        );
                    }
                } else {
                    $this->addToOrderAdminComment($order, t("Не удалось связаться с сервером с СДЭК при создании заказа."));
                }
            }
            //Запишем данные в таблицу, чтобы не вызывать повторного сохранения
            \RS\Orm\Request::make()
                ->update()
                ->from(new \Shop\Model\Orm\Order())
                ->set([
                    '_serialized' => serialize($order['extra'])
                ])
                ->where([
                    'id' => $order['id']
                ])->exec();
        }
    }


    /**
     * Возвращает стоимость доставки для заданного заказа. Только число.
     *
     * @param Order $order - объект заказа
     * @param Address $address - адрес доставки
     * @param Delivery $delivery - объект доставки
     * @param boolean $use_currency - использовать валюту?
     * @return double
     * @throws \RS\Event\Exception
     */
    function getDeliveryCost(Order $order, Address $address = null, Delivery $delivery, $use_currency = true)
    {
        $order_delivery = $order->getDelivery();
        $cache_key = md5($order['order_num'].$order_delivery['id']);
        if (!isset(self::$cache_api_requests[$cache_key])){
            $sxml = $this->requestDeliveryInfo($order, $address);
        } else {
            $sxml = self::$cache_api_requests[$cache_key];
        }

        $cost = false;
        if (isset($sxml['result'])){

            $this->delivery_cost_info = $sxml['result'];
            // Если в ответе есть валюта, используем стоимость в валюте, иначе используем стоимость в рублях
            $cost = (isset($sxml['result']['currency'])) ? $sxml['result']['priceByCurrency'] : $sxml['result']['price'];

            $currency_name = (isset($sxml['result']['currency'])) ? $sxml['result']['currency'] : 'RUB';
            $currency = \Catalog\Model\Orm\Currency::loadByWhere(['title' => $currency_name]);
            // переводим стоимость в базовую валюту
            if ($currency['id'] && !$currency['is_base']) {
                $cost *= $currency['ratio'];
            }

            //Добавим тариф по которому будет осуществленна доставка
            $order->addExtraKeyPair('tariffId', $sxml['result']['tariffId']);
            $order->addExtraKeyPair('deliveryPeriodMin', $sxml['result']['deliveryPeriodMin']);
            $order->addExtraKeyPair('deliveryPeriodMax', $sxml['result']['deliveryPeriodMax']);
            $order->addExtraKeyPair('deliveryDateMin', $sxml['result']['deliveryDateMin']);
            $order->addExtraKeyPair('deliveryDateMax', $sxml['result']['deliveryDateMax']);
            (new OrmRequest())
                ->update(new Order())
                ->set([
                    '_serialized' => serialize($order['extra']),
                ])
                ->where([
                    'id' => $order['id'],
                ])
                ->exec();
        }

        return $cost;
    }

    /**
     * Возвращает указанный в заказе ПВЗ
     *
     * @param Order $order - заказ
     * @return Pvz|null
     */
    public function getSelectedPvz(Order $order): ?Pvz
    {
        if ($this->hasPvz()) {
            $delivery_extra = $order->getExtraKeyPair(Order::EXTRAKEYPAIR_DELIVERY_EXTRA);
            if (!empty($delivery_extra[self::EXTRA_KEY_PVZ_DATA])) {
                $pvz_data = json_decode(htmlspecialchars_decode($delivery_extra[self::EXTRA_KEY_PVZ_DATA]), true);
                if ($pvz_data) {
                    return Pvz::loadFromArray($pvz_data);
                }
            } elseif (!empty($delivery_extra['value'])) {
                $pvz_data = json_decode(htmlspecialchars_decode($delivery_extra['value']), true);
                if ($pvz_data) {
                    return Pvz::loadFromArray($pvz_data);
                }
            }
        }

        return null;
    }

    /**
     * Получает массив доп. услуг
     *
     * @return array
     */
    public static function getAdditionalServices()
    {
        $list = \Shop\Model\DeliveryType\Cdek\CDEKInfo::getAdditionalServices();

        $arr = [];
        foreach($list as $k=>$item){
            $arr[$k] = $item['title'];
        }

        return $arr;
    }



    /**
     * Устанавливает тариф по которому будет произведена доставка после подсчёта стоимости
     *
     * @param integer $id
     */
    function setTariffId($id)
    {
        $this->tariffId = $id;
    }

    /**
     * Получает id тарифа по которому будет произведена доставка после подсчёта стоимости
     *
     */
    function getTariffId()
    {
        return $this->tariffId;
    }

    /**
     * Возвращает трек номер для отслеживания
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return bool
     */
    function getTrackNumber(\Shop\Model\Orm\Order $order)
    {
        $extra = $order->getExtraInfo();
        if (isset($extra['cdek_order_id']['data']['orderId'])){
            return $extra['cdek_order_id']['data']['orderId'];
        }
        return false;
    }

    /**
     * Возвращает ссылку на отслеживание заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     *
     * @return string
     */
    function getTrackNumberUrl(\Shop\Model\Orm\Order $order)
    {
        $track_number = $this->getTrackNumber($order);
        if ($track_number){
            return "https://www.cdek.ru/track.html?order_id=".$track_number;
        }
        return "";
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
        if (($info = $this->requestDeliveryInfo($order, $address)) && !$this->hasErrors()) {
            return new Helper\DeliveryPeriod($info['result']['deliveryPeriodMin'], $info['result']['deliveryPeriodMax']);
        }

        return null;
    }

    /**
     * Возвращает HTML для приложения на Ionic
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
     * @return string
     * @throws \Exception
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function getIonicMobileAdditionalHTML(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Delivery $delivery)
    {
        $view = new \RS\View\Engine();
        if (!$order){
            $order = \Shop\Model\Orm\Order::currentOrder();
        }

        $tariff = $this->getSelectedFirstTariffInfo();

        $pochtomates = [];
        if ($tariff && in_array($tariff['regim_id'], [2, 4])){ //Если нужны почтоматы
            $pochtomates = $this->requestPochtomat($order, $tariff);
        }

        $this->getDeliveryCostText($order, null, $delivery);

        $view->assign([
                'errors'      => $this->getErrors(),
                'order'       => $order,
                'extra_info'  => $order->getExtraKeyPair(),
                'delivery'    => $delivery,
                'cdek'        => $this,
                'pochtomates' => $pochtomates,
            ] + \RS\Module\Item::getResourceFolders($this));

        return $view->fetch("%shop%/delivery/cdek/mobilesiteapp/pochtomates.tpl");
    }

    /**
     * Возвращает список доступных ПВЗ для переданного заказа
     *
     * @param \Shop\Model\Orm\Order $order
     * @return array|boolean
     */
    function getPvzList(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null)
    {
        $this->getDeliveryFinalCost($order, null, $this->getDelivery());
        if (!$address) {
            $address = $order->getAddress();
        }
        $cdek_info = new \Shop\Model\DeliveryType\Cdek\CdekInfo();
        $tariffs = $cdek_info->getAllTariffsWithInfo();
        $extra_ti = $order->getExtraKeyPair('tariffId');
        if (!empty($extra_ti) && isset($tariffs[$extra_ti])){
            $tariff = $tariffs[$extra_ti];
            if ($tariff && in_array($tariff['regim_id'], [2, 4])){ //Если нужны почтоматы
                $pochtomates = $this->requestPochtomat($order, $tariff, $address);
                $result = [];

                foreach ($pochtomates as $pochtomat) {
                    $attr = (array) $pochtomat->attributes();
                    $attr = $attr['@attributes'];
                    $pvz = new \Shop\Model\DeliveryType\Cdek\Pvz();
                    // Записываем свойства ПВЗ
                    $pvz->setCode($attr['Code']);
                    $pvz->setTitle($attr['Name']);
                    $pvz->setCountry($attr['CountryName']);
                    $pvz->setRegion($attr['RegionName']);
                    $pvz->setCity($attr['City']);
                    $pvz->setAddress($attr['Address']);
                    $pvz->setWorktime($attr['WorkTime']);
                    $pvz->setCoordX($attr['coordX']);
                    $pvz->setCoordY($attr['coordY']);
                    $pvz->setPhone($attr['Phone']);
                    $pvz->setPaymentByCards(((string)$attr['HaveCashless'] == 'Да') ? 1 : 0);
                    $pvz->setCashOnDelivery(isset($attr['cashOnDelivery']) ? $attr['cashOnDelivery'] : 0);
                    $pvz->setNote($attr['Note']);
                    // Удаляем записанные поля из extra
                    $remove_key = ['Code', 'Name', 'CountryName', 'RegionName', 'City', 'coordX', 'coordY', 'Phone', 'cashOnDelivery', 'Note'];
                    foreach ($remove_key as $key) {
                        unset($attr[$key]);
                    }
                    // преобразуем массив графика работы
                    $worktime_y = [];
                    foreach ($pochtomat->WorkTimeY as $item) {
                        $worktime_y[(string) $item['day']] = (string) $item['periods'];
                    }
                    $attr['WorkTimeY'] = $worktime_y;
                    $pvz->setExtra($attr);

                    $result[] = $pvz;
                }
                return $result;
            }
        }


        return false;
    }

    /**
     * Возвращает правильный идентификатор налога
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param \Catalog\Model\Orm\Product $product - объект товара
     * @return string
     */
    protected function getRightTaxForProduct(\Shop\Model\Orm\Order $order, \Catalog\Model\Orm\Product $product)
    {
        $address = $order->getAddress();
        $tax_api = new \Shop\Model\TaxApi();
        $taxes   = $tax_api->getProductTaxes($product, $order->getUser(), $address);
        $tax     = new \Shop\Model\Orm\Tax();
        foreach ($taxes as $item){
            if ($item['is_nds']){
                $tax = $item;
                break;
            }
        }

        //Получим ставку
        $tax_rate = $tax->getRate($address);
        if (!empty($tax_rate)) {
            switch((int)$tax_rate){
                case 10:
                    $tax_id = 'VAT10';
                    break;
                case 18:
                    $tax_id = 'VAT18';
                    break;
                case 20:
                    $tax_id = 'VAT20';
                    break;
                case 0:
                    $tax_id = 'VAT0';
                    break;
                default:
                    $tax_id = 'VATX';
            }
        } else {
            $tax_id = 'VATX';
        }

        return $tax_id;
    }

    /**
     * Возвращает ставку налога по идентификатору
     *
     * @param string $tax_id
     * @return float
     */
    protected function getTaxRateById($tax_id)
    {
        $tax_rate = [
            'VATX' => 0,
            'VAT0' => 0,
            'VAT10' => 10/110,
            'VAT18' => 18/118,
            'VAT20' => 20/120,

        ];

        return $tax_rate[$tax_id] ?? 0;
    }

    /**
     * Возвращает id города в базе СДЭК, или false
     *
     * @param Address $address
     * @return string|false
     */
    public function getCityIdByName(Address $address)
    {
        static $cache_city_id = [];
        $address->updateAddressTitles();
        $cdekCity = null;
        $cdekRegion = null;
        $cdekKladr = null;
        $city = $address->getCity();

        if (!empty($city['cdek_city_id'])) {
            return $city['cdek_city_id'];
        }

        if(empty($city['title'])) {
            $city['title'] = $address->city;
        }

        $region = clone $address->getRegion();
        $cache_key = $city['title'].'_'.$region['title'].'_'.$region['country'];
        if (!isset($cache_city_id[$cache_key])) {
            $file_path =\Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/';

            $region['title'] = str_replace('область','',$region['title']);
            $region['title'] = str_replace('республика','',$region['title']);
            $region['title'] = str_replace('автономный округ','',$region['title']);
            $region['title'] = str_replace('автономная область','',$region['title']);
            $region['title'] = str_replace('край','',$region['title']);
            $region['title'] = str_replace('АО','',$region['title']);
            $region['title'] = str_replace('Тыва (Тува)','Тыва',$region['title']);
            $region['title'] = str_replace('Саха (Якутия)','Саха /Якутия/',$region['title']);
            $region['title'] = str_replace('Чечня','Чеченская',$region['title']);

            $result = false;
            // По названию страны определяем в каком файле искать город
            $country = $address->getCountry();

            //Берем список городов из СДЕК
            $countries = $this->getCountriesFromFile();
            $country_id = array_search($country['title'], $countries);

            // Если есть город в базе
            if ($country_id && file_exists($file_path . 'cdek_' . $country_id . '.csv')) {
                $file_name = 'cdek_' . $country_id . '.csv';
            } else {
                switch ($country['title']) {
                    case t('Россия'):
                        $file_name = 'cdek_rus.csv';
                        break;
                    case t('Казахстан'):
                        $file_name = 'cdek_kaz.csv';
                        break;
                    default:
                        $file_name = '';
                }
            }

            $time = 0;
            if (!empty($file_name)) {
                $file =  fopen($file_path.$file_name,'r');
                $f = array_flip(fgetcsv($file, null, ';', '"'));

                (!empty($city['kladr_id']))? $kladr_id = $city['kladr_id'] : $kladr_id = false;// Заполнено ли поле кладр в базе сайта

                $start = microtime(true);
                while($cdek_city = fgetcsv($file, null, ';', '"')) { // Город найден при совпадении названия города и его области

                    if (($kladr_id === false) || ($country['title'] != 'Россия')) {
                        if (!empty($cdek_city) && mb_strtolower($city['title']) == mb_strtolower($cdek_city[$f['city_name']])
                            && stripos($cdek_city[$f['region_name']], trim($region['title'])) !== false) {
                            $cdekCity = $cdek_city[$f['city_name']];
                            $cdekRegion = $cdek_city[$f['region_name']];
                            $result = $cdek_city[$f['cdek_id']];
                            $cdekKladr = false;
                            break;
                        }
                    } else {
                        if (!empty($cdek_city) && $kladr_id === $cdek_city[$f['kladr']]) {
                            $cdekCity = $cdek_city[$f['city_name']];
                            $cdekRegion = $cdek_city[$f['region_name']];
                            $cdekKladr = $cdek_city[$f['kladr']];
                            $result = $cdek_city[$f['cdek_id']];
                            break;
                        }
                    }
                }
                $end = microtime(true);
                fclose($file);
                $time = $end-$start;
            }
            $cache_city_id[$cache_key] = $result;
            $this->writeToLog('Поиск в файле: '.$file_name.' Город из базы: '.$city['title'].' Регион из базы: '.$region['title'].' Найденый город по СДЕК: '.$cdekCity.' Найденый регион по СДЕК: '.$cdekRegion.' Найден КЛАДР по СДЕК:'.$cdekKladr.' Время на поиск '.$time.' Результат работы: ', $cache_city_id[$cache_key]);
        }
        return $cache_city_id[$cache_key];
    }

    /**
     * Функция актуализации городов СДЕК по API
     * @param $id
     * @param int $page
     * @return mixed
     */
    public static function getCitiesByCountryId($id, $page=0)
    {
        $params = [
            'countryCode' => $id,
            'page' => $page
        ];
        //Получаем 1000 записей о городах
        $request = Api::getInstance()->getLocationsCities($params); //file_get_contents('http://integration.cdek.ru/v1/location/cities/json?countryCode='.$id.'&page='.$page);
        $page++;

        //сохраняем их в файл
        self::createCountryFile($id,$request);

        $data['page']= $page;
        if(empty($request)) $data['close'] = true;

        return $data;
    }

    /**
     * Получение стран доступных в СДЕК API
     *
     * @return string[]
     */
    public static function getCountries()
    {
        $countries = [];
        $file_path = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/cdek_countries.csv';

        $f = fopen($file_path, 'r');
        while ($item = fgetcsv($f, null, ';', '"')) {
            $countries[$item[0]] = $item[1];
        }
        fclose($f);

        return $countries;
    }

    /**
     * Получение закэшированных город сдек
     * @return mixed
     */
    protected function getCountriesFromFile(){
        $file_path = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/cdek_countries.csv';
        $data = [];
        $file = fopen($file_path,'r');

        while($row = fgetcsv($file,null,';','"')){
            $data[$row[0]] = $row[1];
        }

        return  $data;
    }

    /**
     * Создает файл страны с городами СДЕК
     * @param $countryId
     * @param array $data
     * @param bool $flag
     */
    public static function createCountryFile($countryId, $data = [], $flag = false)
    {
        $file_path = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/cdek_'.$countryId.'.csv';
        $file = fopen($file_path,'a');
        foreach ($data as $key => $city)
        {
            unset($city->cityUuid);
            unset($city->regionCodeExt);
            unset($city->latitude);
            unset($city->longitude);
            unset($city->country);
            unset($city->countryCode);
            unset($city->regionFiasGuid);
            unset($city->fiasGuid);
            $data[$key] = (array)$data[$key];
            fputcsv($file, $data[$key],';','"');
        }
        fclose($file);
    }

    protected function getAddressFromCityId(int $city_id)
    {
        $city = new Region($city_id);
        if ($city['is_city']) {
            $region = $city->getParent();
            $address = new Address();
            $address['city_id'] = $city['id'];
            $address['region_id'] = $region['id'];
            $address['country_id'] = $region['parent_id'];
            return $address;
        }
        return null;
    }

    public function loadOptions(array $opt = null)
    {
        parent::loadOptions($opt);
        $this->api->setWriteLog($this->getOption('write_log'));
        $this->api->setTimeout($this->getOption('timeout'));
    }
}

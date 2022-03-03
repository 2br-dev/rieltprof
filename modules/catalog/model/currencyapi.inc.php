<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

class CurrencyApi extends \RS\Module\AbstractModel\EntityList
{
    const
        COOKIE_CURRENCY_KEY = 'currency',
        CBR_LINK = "http://www.cbr.ru/scripts/XML_daily.asp"; //ссылка получения курса ЦБ РФ
        
    protected static
        $current_currency,
        $default_currency,
        $base_currency;
    
    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\Currency,
        [
            'multisite' => true,
            'nameField' => 'title'
        ]);
    }    
    
    /**
    * Возращает объект текущей валюты
    * 
    * @return Orm\Currency
    */
    public static function getCurrentCurrency()
    {
        if (self::$current_currency === null) {
            //Приоритет: self, cookie, Default
            $cookie = \RS\Http\Request::commonInstance()->cookie(self::COOKIE_CURRENCY_KEY, TYPE_STRING);
            if (!empty($cookie)) {
               self::$current_currency = \RS\Orm\Request::make()
                ->from(new Orm\Currency())
                ->where(['site_id' => \RS\Site\Manager::getSiteId(), 'title' => $cookie])
                ->object(); 
            }
                        
            if (self::$current_currency === null) {
                //Возвращаем валюту по-умолчанию
                self::$current_currency = self::getDefaultCurrency();
            }
        }
        return self::$current_currency;
    }
    
    /**
    * Возвращает валюту по-умолчанию для текущего сайта
    * 
    * @return Orm\Currency
    */
    public static function getDefaultCurrency()
    {
        if (!self::$default_currency) {
            self::$default_currency = Orm\Currency::loadByWhere(['site_id' => \RS\Site\Manager::getSiteId(), 'default' => 1]);
            if (!self::$default_currency) {
                self::$default_currency = Orm\Currency::loadByWhere(['site_id' => \RS\Site\Manager::getSiteId()]);
            }
        }
        return self::$default_currency;
    }
    
    /**
    * Возвращает объект с базовой валютой. (базовой считается валюта, в которой цены в системе) 
    * 
    * @return Orm\Currency
    */
    public static function getBaseCurrency()
    {
        if (!self::$base_currency) {
            self::$base_currency = Orm\Currency::loadByWhere(['site_id' => \RS\Site\Manager::getSiteId(), 'is_base' => 1]);
        }
        return self::$base_currency;        
    }
    
    
    /**
    * Устанавливает текущую валюту в системе
    * 
    * @param string $alias - трехсимвольный идентификатор валюты
    * @return bool Возвращает true, если установка валюты прошла успешно
    */
    public static function setCurrentCurrency($alias, $send_header = true)
    {
        $currency = \Catalog\Model\Orm\Currency::loadByWhere([
            'site_id' => \RS\Site\Manager::getSiteId(),
            'title' => $alias
        ]);
        
        if ($currency['id']) {
            if ($send_header) {
                \RS\Application\Application::getInstance()->headers->addCookie(self::COOKIE_CURRENCY_KEY, $alias, time() + 60 * 60 * 24 * 720, '/');
            }
            self::$current_currency = $currency;
            return true;
        }
        return false;
    }
    
    /**
    * Возвращает код текущей валюты
    * 
    * @return string
    */
    public static function getCurrecyCode()
    {
        $currency = self::getCurrentCurrency();
        return $currency['title'];
    }
    
    /**
    * Возвращает символ текущей валюты
    * 
    * @return string
    */
    public static function getCurrecyLiter()
    {
        $currency = self::getCurrentCurrency();
        return $currency['stitle'];
    }
    
    /**
    * Корректирует цену с учетом курса валюты относительно базовой валюты
    * 
    * @param float $cost - Цена в базовой валюте
    * @param $currency - объект валюты
    * @return float
    */
    public static function applyCurrency($cost, Orm\Currency $currency = null)
    {
        if (!$currency) {
            $currency = self::getCurrentCurrency();
        }
        
        $ratio = $currency['ratio'] >0 ? 1/$currency['ratio'] : 0;
        return $currency['is_base'] ? $cost : round($cost * $ratio, 2);
    }
    
    /**
    * Конвертирует цену в текущей валюте к базовой валюте
    * 
    * @param float $cost - цена
    * @param Orm\Currency $cost_currency - валюта, в которой указана $cost. Если null, то текущая валюта
    * @return float
    */
    public static function convertToBase($cost, Orm\Currency $cost_currency = null)
    {
        $base = self::getBaseCurrency();
        $cost_currency = $cost_currency ?: self::getCurrentCurrency();
        if ($base['id'] == $cost_currency['id']) {
            return $cost;
        } else {
            return \Catalog\Model\CostApi::roundCost($cost * $cost_currency['ratio']);
        }
    }
    
    /**
    * Возвращает объект валюты по трехсимвольному идентификатору
    * 
    * @return Orm\Currency
    */
    public static function getByUid($uid)
    {
        return \RS\Orm\Request::make()->from(new Orm\Currency())->where([
            'title' => $uid
        ])->object();
    }
    
    /**
    * Получает курсы валют с сайта ЦБ РФ
    * 
    * @param boolean|string $date - дата за которую нужно получить данные
    * @return boolean|\SimpleXMLElement
    */
    function getCBRFCourse($date = false){
       //Получим xml от ЦБ c валютами
       $opts = [
          'http'=> [
            'method'=>"GET",
          ]
       ];
       $config  = \RS\Config\Loader::byModule($this);
       $date    = $date ? $date : date("d/m/Y");
       $context = stream_context_create($opts);
       $url     = $config['cbr_link'] ? $config['cbr_link'] : self::CBR_LINK;

       if (!($cbr_xml =  @simplexml_load_string(file_get_contents($url."?date_req=".rawurlencode($date),false,$context)))){
          return $this->addError(t('Невозможно получить данные по курсам по адресу ').$url."?date_req=".$date); 
       } 
       
       return $cbr_xml; 
    }
    
    /**
    * Получает курсы ЦБ РФ всех валют за исключением базовой (Рублей)
    * И обновляет у них не только значение, но и у товаров обновляются цены, если они указаны 
    * в этих валютах 
    * Если удалось обновить возвращается true
    * 
    * @param $cur_site - по умолчанию true;
    * если true        - то делать для текщего сайта с обновлением, 
    * если integer     - Если задано значение то для конкретного сайта
    * если false|null  - Обновить для всех валют всех сайтов 
    * 
    * @return boolean
    */
    function getCBRFCourseWithUpdate($cur_site = true){
       $config = \RS\Config\Loader::byModule($this); 
        
       //Получим наши валюты за исключением базовой.
       $q = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Currency())
                ->where([
                      'is_base' => 0
                ]);
       if ($cur_site === true){ //Если для текущего сайта
          $q->where([
             'site_id' => \RS\Site\Manager::getSiteId()
          ]);
       }
       if ($cur_site>0 && !is_bool($cur_site)){ //Если для конкретного сайта
          $q->where([
             'site_id' => $cur_site
          ]);
       }
                    
       $currencies = $q->objects(null,'title');
       $curr_keys = array_keys($currencies); //Имена валют
        
       //Получим xml от ЦБ c валютами
       $cbr_xml =  $this->getCBRFCourse();
       if ($cbr_xml){
          foreach($cbr_xml->Valute as $currency){
              $code = (string)$currency->CharCode;
              if (in_array($code, $curr_keys)) { //Если интересущая валюта у нас есть, по получим значение и обновим
                 $old_ratio = $currencies[$code]['ratio'];//Старый коэфициент
                 $currencies[$code]['ratio']     = str_replace(",",".",$currency->Value); //Значение
                 $currencies[$code]['ratio'] = (double)$currencies[$code]['ratio']/(double)$currency->Nominal;
                 $currencies[$code]['reconvert'] = 1;                                     //Переконвертировать цены с учётом новой цены
                 //Увеличим или уменьшим на определнённый процент
                 $percent = (str_replace(",",".",$currencies[$code]['percent'])/100)*$currencies[$code]['ratio'];
                 $currencies[$code]['ratio'] = $currencies[$code]['ratio']+$percent;
                 
                 if ($config['cbr_percent_update']){ //Если нужно проверить на разницу c прошлым значением коэфициента
                    $delta = abs($currencies[$code]['ratio']-$old_ratio); 
                    $delta_percent = floor(($delta/$currencies[$code]['ratio'])*100);
                    if ($delta_percent<(int)$config['cbr_percent_update']){ //Если процент установленный для проверки отличается в меньшую сторону
                        continue; //Пропустим обновление
                    }
                 }
                 $currencies[$code]->update();
              }
          }
          return true; 
       }
       return false;
    }
}


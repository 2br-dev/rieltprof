<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Cdek;

/**
* Класс с доп. информацией доставки СДЭКа
*/
class CDEKInfo{

    const
        FILENAME_RUS_TARIFF   = 'rus_tariffs.csv', //Тарифы внутри страны
        FILENAME_INT_TARIFF   = 'int_tariffs.csv', //Тарифы международные
        FILENAME_RUS_CITIES   = 'city_russia.csv', //Города России
        FILENAME_UKR_CITIES   = 'city_ukraine.csv', //Города Украины
        FILENAME_KAZ_CITIES   = 'city_kazahstan.csv', //Города Казахстана
        FILENAME_BLR_CITIES   = 'city_belarus.csv', //Города Беларусии
        FILENAME_ADD_SERVICES = 'additional_services.csv', //Дополнительные услуги, доступные в админке
        FILENAME_ALL_SERVICES = 'additional_services_all.csv'; //Все дополнительные услуги
    
    private
        $tariff_type_compare = [ //Массив типа доставки и соответствия числу
           'дверь-дверь (Д-Д)' => 1,
           'дверь-склад (Д-С)' => 2, 
           'склад-дверь (С-Д)' => 3, 
           'склад-склад (С-С)' => 4,
    ];
    
    function __construct()
    {
        
    }
    
    /**
    * Получает полный путь к файлу CSV для чтения
    * 
    * @param string $filename - имя файла для получения
    */
    function getFilePath($filename)
    {
       $path = \Setup::$PATH."/modules/shop/model/deliverytype/cdek/csv/".$filename; 
       if (!file_exists($path)){
          throw new \RS\Exception(t("Нет файла CSV для обработки ").$path.". ",1); 
       }
       return $path; 
    }
    
    /**
    * Получает данные из CSV файла в виде массива 
    * 
    * @param string $filename - имя файла для получения
    * @param array $format - Массив формата соответствия колонок
    */
    function getCSVInfo($filename, $format= [])
    {
       $path = $this->getFilePath($filename); 
       
       $csv_delimiter = ";";
       $csv_enclosure = '"';
       
       $handler = fopen($path,"r");
       
       //Получим первую строку
       fgetcsv($handler, null, $csv_delimiter, $csv_enclosure);
       
       $arr = [];
       //Перебираем строки получая информацию по маске
       
       while(($row = fgetcsv($handler, null, $csv_delimiter, $csv_enclosure)) !== false) {
          foreach ($row as $key=>$field){
             $row_arr[$format[$key]] = $field; 
             if ($format[$key]=="regim"){
                $row_arr['regim_id'] = $this->tariff_type_compare[$field]; 
             }  
          }
          $arr[$row[0]] = $row_arr;
       }
       
       fclose($handler);
       return $arr;
    }
    
    /**
    * Получает массив тарифов международный
    * 
    * @return array
    */
    function getInternationTariffs()
    {
        return $this->getCSVInfo(self::FILENAME_INT_TARIFF, [
            'code',
            'title',
            'regim',
            'weight_limit',
            'group',
            'description',
            'ownerCode',
        ]);
    }
    
    /**
    * Получает массив тарифов внутри страны
    * 
    * @return array
    */ 
    function getRusTariffs()
    {
        return $this->getCSVInfo(self::FILENAME_RUS_TARIFF, [
            'code',
            'title',
            'regim',
            'weight_limit',
            'group',
            'description',
            'ownerCode',
        ]);
    }
    
    /**
    * Получает массив городов России
    * 
    * @return array
    */ 
    function getRusCities()
    {
        return $this->getCSVInfo(self::FILENAME_RUS_CITIES, [
            'code',
            'fullname',
            'title',
            'region',
        ]);
    }
    
    
    /**
    * Получает массив городов Казахстана
    * 
    * @return array
    */ 
    function getKazCities()
    {
        return $this->getCSVInfo(self::FILENAME_KAZ_CITIES, [
            'code',
            'fullname',
            'title',
            'region',
        ]);
    }
    
    /**
    * Получает массив городов Украины
    * 
    * @return array
    */ 
    function getUkrCities()
    {
        return $this->getCSVInfo(self::FILENAME_UKR_CITIES, [
            'code',
            'fullname',
            'title',
            'region',
        ]);
    }
    
    /**
    * Получает массив городов России
    * 
    * @return array
    */ 
    function getBlrCities()
    {
        return $this->getCSVInfo(self::FILENAME_BLR_CITIES, [
            'code',
            'fullname',
            'title',
            'region',
        ]);
    }
    
    /**
    * Сортировка городов 
    * 
    * @param array $a
    * @param array $b
    */
    private function strCitySort($a, $b)
    {
        return strcmp($a["title"], $b["title"]);
        
    }
    
    /**
    * Получает все города со странами
    */
    function getAllCities()
    {
       
        //Россия
        $cities = $this->getRusCities();
        
        if (!empty($cities)){
           usort($cities, [$this,'strCitySort']);
           $arr[t('Россия')] = $cities; 
        }
        
        //Украина
        $cities = $this->getUkrCities();
        
        if (!empty($cities)){
           usort($cities, [$this,'strCitySort']);
           $arr[t('Украина')] = $cities; 
        }
        
        //Казахстан
        $cities = $this->getKazCities();
        
        if (!empty($cities)){
           usort($cities, [$this,'strCitySort']);
           $arr[t('Казахстан')] = $cities; 
        }
        
        //Беларусь
        $cities = $this->getBlrCities();
        
        if (!empty($cities)){
           usort($cities, [$this,'strCitySort']);
           $arr[t('Беларусь')] = $cities; 
        }
        return $arr; 
    }
    
    /**
    * Получает массив доп. услуг
    * 
    * @return array
    */ 
    public static function getAdditionalServices()
    {
        $_this = new self();
        return $_this->getCSVInfo(self::FILENAME_ADD_SERVICES, [
            'code',
            'agree',
            'title',
            'description',
        ]);
    }
    
    /**
    * Получает полный массив массив доп. услуг
    * 
    * @return array
    */ 
    public static function getAllAdditionalServices()
    {
        $_this = new self();
        return $_this->getCSVInfo(self::FILENAME_ALL_SERVICES, [
            'code',
            'agree',
            'title',
            'description',
        ]);
    }
    
    /**
    * Получает все тарифы СДЭК на отправку
    * 
    * @return array
    */
    public static function getAllTariffs()
    {
        $class = new self();
        $arr   = [];
        //Россия
        $tariffs = $class->getRusTariffs();
        
        if (!empty($tariffs)){
           $rows = [];
           foreach ($tariffs as $tariff_id=>$tariff){
              $rows[$tariff_id] = $tariff['title']; 
           }
           $arr[t('Тарифы по России')] = $rows; 
        }
        
        //Международное
        $tariffs = $class->getInternationTariffs();
        
        if (!empty($tariffs)){
           $rows = [];
           foreach ($tariffs as $tariff_id=>$tariff){
              $rows[$tariff_id] = $tariff['title']; 
           }
           $arr[t('Тарифы международные')] = $rows; 
        }
        return $arr;
    }
    
    /**
    * put your comment there...
    * 
    */
    public static function getAllTariffsWithInfo()
    {
        $class = new self();
        $arr   = [];
        //Россия
        $tariffs = $class->getRusTariffs();
        
        if (!empty($tariffs)){
           foreach ($tariffs as $tariff_id=>$tariff){
              $arr[$tariff_id] = $tariff; 
           }
        }
        
        //Международное
        $tariffs = $class->getInternationTariffs();
        
        if (!empty($tariffs)){
           foreach ($tariffs as $tariff_id=>$tariff){
              $arr[$tariff_id] = $tariff; 
           }
        }
        return $arr;
    }
}

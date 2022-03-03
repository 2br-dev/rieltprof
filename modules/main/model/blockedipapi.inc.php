<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Http\Request;

class BlockedIpApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \Main\Model\Orm\BlockedIp(), [
            'idField' => 'ip'
        ]);
    }    
    
    /**
    * Возвращает true, если IP заблокирован 
    * 
    * @param string $ip
    */
    public static function isIpBanned($ip, $cache = true)
    {
        $ip_list = self::getIpList();
        $time = time();
        foreach($ip_list as $ip_mask => $expire) {
            if (($expire === null || $expire >= $time) && self::isIpInRange($ip, $ip_mask)) {
                return true;
            }
        }
        return false;
    }
    
    /**
    * Возвращает true, если IP подходит под маску
    * 
    * @param string $ip - IP адрес
    * @param string $ip_mask - Маска IP адресов. Например:
    * 192.168.1.1
    * 192.168-169.0.1
    * 192-193.168-169.0-5.10-20
    */
    public static function isIpInRange($ip, $ip_mask)
    {
        if (strpos($ip_mask, '-') !== false) {
            //Усложненная проверка
            $ip_arr = explode('.', $ip);
            $ip_mask_arr = explode('.', $ip_mask);
            
            foreach($ip_arr as $n => $section) {
                $range = explode('-', $ip_mask_arr[$n]);
                if (count($range) == 2) {
                    if ($range[0] > $section || $section > $range[1]) {
                        return false;
                    }
                } 
                elseif ($section != $ip_mask_arr[$n]) {
                    return false;
                }
            }
            return true;
        } else {
            return $ip == $ip_mask;
        }
    }
    
    /**
    * Возвращает список действующих заблокированных IP
    * 
    * @param bool $cache - Если true, то использовать кэширование
    * @return array
    */
    public static function getIpList($cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->expire(0)
                ->watchTables(new Orm\BlockedIp())
                ->request([__CLASS__, 'getIpList'], false);
        } else {
            $ips = [];
            try {
                $ips = \RS\Orm\Request::make()
                    ->from(new Orm\BlockedIp())
                    ->select('ip, UNIX_TIMESTAMP(expire) as expire')
                    ->exec()
                    ->fetchSelected('ip', 'expire');
            } catch (\RS\Db\Exception $e) {
                //Игнорируем ошибку "Таблица БД не существует"
                if ($e->getCode() != 1146) throw $e;
            }
                
            return $ips;
        }
    }

    function save($id = null, array $user_post = [])
    {
        //Запрещаем указывать свой IP
        $ip = Request::commonInstance()->post('ip', TYPE_STRING);
        $el = $this->getElement();
        if ($el && \Main\Model\BlockedIpApi::isIpInRange($_SERVER['REMOTE_ADDR'], $ip)) {
            $el->addError(t('Невозможно добавить IP, с которого вы работаете'), 'ip');
            return false;
        }

        return parent::save($id, $user_post);
    }
}

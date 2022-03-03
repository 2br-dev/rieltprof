<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;
use \RS\Orm\Type;

/**
 * Заблокированный IP адрес
 * --/--
 * @property string $ip IP-адрес
 * @property string $expire Дата разблокировки
 * @property string $comment Комментарий
 * --\--
 */
class BlockedIp extends \RS\Orm\AbstractObject
{   
    protected static
        $table = 'blocked_ip';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'ip' => new Type\Varchar([
                'maxLength' => 100,
                'primaryKey' => true,
                'description' => t('IP-адрес'),
                'hint' => t('Формат: XXX.XXX.XXX.XXX, где XXX - это число(от 0 до 255) или диапазон чисел. <br>Например:100.110.120.130 или 100.110-140.160-180.170'),
                'checker' => ['ChkPattern', t('Неверно указан IP'), '/[0-9\-]+\.[0-9\-]+\.[0-9\-]+\.[0-9\-]+/']
            ]),
            'expire' => new Type\Datetime([
                'description' => t('Дата разблокировки'),
                'template' => '%main%/form/blockedip/expire.tpl',
                'hint' => t('Если время не указано, то IP будет заблокирован бессрочно')
            ]),
            'comment' => new Type\Varchar([
                'description' => t('Комментарий')
            ])
        ]);
    }
    
    function getPrimaryKeyProperty()
    {
        return 'ip';
    }
    
    function beforeWrite($flag)
    {

        if ($this['expire'] == '') {
            $this['expire'] = null;
        }
    }
}

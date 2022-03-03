<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект, содержащий сведения о запрете на получение push уведомлений пользователями
 * --/--
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $app Приложение
 * @property string $push_class Класс уведомлений, all - запретить все
 * --\--
 */
class PushLock extends \RS\Orm\AbstractObject
{
    const
        PUSH_CLASS_ALL = 'all';
    
    protected static
        $table = 'pushsender_push_lock';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\Integer([
                'description' => t('Пользователь')
            ]),
            'app' => new Type\Varchar([
                'description' => t('Приложение'),
                'maxLength' => 100
            ]),
            'push_class' => new Type\Varchar([
                'description' => t('Класс уведомлений, all - запретить все'),
                'maxLength' => 100
            ])
        ]);
        
        $this->addIndex(['site_id', 'user_id', 'app', 'push_class'], self::INDEX_UNIQUE);
    }
    
    function getPrimaryKeyProperty()
    {
        return ['site_id', 'user_id', 'app', 'push_class'];
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Orm;

use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $dateof Дата
 * @property string $class Класс события
 * @property integer $oid ID объекта над которым произошло событие
 * @property integer $group ID Группы (перезаписывается, если событие происходит в рамках одной группы)
 * @property integer $user_id ID Пользователя
 * @property string $_serialized Дополнительные данные (скрыто)
 * @property array $data Дополнительные данные
 * --\--
 */
class Log extends OrmObject
{
    protected static $table = 'users_log';
    
    function _init()
    {
        parent::_init();

        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'dateof' => new Type\Datetime([
                'description' => t('Дата'),
            ]),
            'class' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Класс события'),
            ]),
            'oid' => new Type\Integer([
                'description' => t('ID объекта над которым произошло событие'),
            ]),
            'group' => new Type\Integer([
                'description' => t('ID Группы (перезаписывается, если событие происходит в рамках одной группы)'),
            ]),
            'user_id' => new Type\Bigint([
                'description' => t('ID Пользователя'),
            ]),
            '_serialized' => new Type\Varchar([
                'maxLength' => '4000',
                'description' => t('Дополнительные данные (скрыто)'),
                'visible' => false,
            ]),
            'data' => new Type\ArrayList([
                'description' => t('Дополнительные данные'),
            ]),
        ]);
        
        $this
            ->addIndex(['class', 'user_id', 'group'], self::INDEX_UNIQUE)
            ->addIndex(['site_id', 'class'])
            ->addIndex(['dateof']);
    }

    /**
     * @param string $save_flag
     * @throws DbException
     * @throws RSException
     */
    function afterWrite($save_flag)
    {
        $config = ConfigLoader::byModule('users');

        $clear_random = $config['clear_random'];
        $clear_hours  = $config['clear_for_last_time'];

        //Попытаемся очитить логи в зависимости от вероятности
        srand();
        $value = rand(0,100);
        if ($value <= $clear_random){ //Если попали в вероятность
            $time = time()-($clear_hours * 60 * 60);
            //Очистим логи
            OrmRequest::make()
                ->delete()
                ->from($this->_getTable())
                ->where("dateof < '".date('Y-m-d H:i:s',$time)."'")
                ->exec();
        }
    }

    function beforeWrite($flag)
    {
        $this['_serialized'] = serialize($this['data']);
    }
    
    function afterObjectLoad()
    {
        $this['data'] = @unserialize($this['_serialized']);
    }
}

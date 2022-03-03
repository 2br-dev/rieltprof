<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use Crm\Config\ModuleRights;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use RS\Event\Manager as EventManager;

/**
 * Статус для различных объектов CRM
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $object_type_alias Тип объекта, к которому привязан статус
 * @property string $title Наименование статуса
 * @property string $alias Англ. идентификатор
 * @property string $color Цвет
 * @property integer $sortn Порядок
 * @property integer $is_status_complete Этот статус означает, что задача завершена?
 * --\--
 */
class Status extends OrmObject
{
    protected static
        $table = 'crm_statuses';

    function _init()
    {
        parent::_init()->append([
            'object_type_alias' => new Type\Varchar([
                //Короткий идентификатор ORM объекта
                'maxLength' => 50,
                'description' => t('Тип объекта, к которому привязан статус'),
                'listFromArray' => [self::getObjectTypeAliases()],
                'index' => true,
                'readOnly' => true,
            ]),
            'title' => new Type\Varchar([
                'description' => t('Наименование статуса'),
            ]),
            'alias' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Англ. идентификатор'),
                'hint' => t('Придумайте любой идентификатор'),
                'checker' => ['ChkAlias', t('Неверно задан англ.идентификатор')],
                'allowEmpty' => false
            ]),
            'color' => new Type\Color([
                'description' => t('Цвет')
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок'),
                'visible' => false
            ]),
            'is_status_complete' => new Type\Integer([
                'description' => t('Этот статус означает, что задача завершена?'),
                'hint' => t('Данный флаг помогает системе понять, что задача или сделка завершена и необходимо выполнить какие-то действия, например, установить дату завершения, если она не задана.'),
                'checkboxView' => [1, 0]
            ])
        ]);

        $this->addIndex(['object_type_alias', 'alias'], self::INDEX_UNIQUE);
    }

    /**
     * Обработчик, выполняется перед сохранением объекта
     *
     * @param $flag
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'object_type_alias' => $this['object_type_alias']
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Возвращает список возможных типов объектов, для которых возможно создать статусы
     *
     * @return string[]
     */
    public static function getObjectTypeAliases()
    {
        $deal = new Deal();
        $task = new Task();
        $allow_object_types = [
            $deal->getShortAlias() => t('Сделки'),
            $task->getShortAlias() => t('Задачи')
        ];

        $event_result = EventManager::fire('crm.status.gettypes', $allow_object_types);
        $allow_object_types = $event_result->getResult();

        return $allow_object_types;
    }


    /**
     * Возвращает список объектов статусов
     *
     * @param string $object_type_alias псевдоним ORM объекта, к которому привязаны статусы
     * @return array
     */
    public static function getStatusesByObjectType($object_type_alias)
    {
        return \RS\Orm\Request::make()
            ->from(new self())
            ->where([
                'object_type_alias' => $object_type_alias
            ])
            ->orderby('sortn')
            ->objects();

    }

    /**
     * Возвращает список названий статусов
     *
     * @param string $object_type_alias
     * @param array $first Элемент массива, который будет добавлен вначало
     * @return array
     */
    public static function getStatusesTitles($object_type_alias, $first = [])
    {
        $result = [];

        $statuses = self::getStatusesByObjectType($object_type_alias);
        foreach($statuses as $item) {
            $result[$item['id']] = $item['title'];
        }

        return $first + $result;
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::STATUS_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return ModuleRights::STATUS_CREATE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::STATUS_UPDATE;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::STATUS_DELETE;
    }

}
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

/**
 * ORM объект - один сохраненный пресет с фильтрацией задач
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $user_id Пользователь, для которого настраивается фильтр
 * @property string $title Название выборки
 * @property string $filters Значения фильтров
 * @property array $filters_arr Значения фильтров - массив
 * @property integer $sortn Порядок
 * --\--
 */
class TaskFilter extends OrmObject
{
    protected static
        $table = 'crm_task_filter';

    function _init()
    {
        parent::_init()->append([
            'user_id' => new Type\User([
                'description' => t('Пользователь, для которого настраивается фильтр'),
                'visible' => false
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название выборки'),
                'hint' => t('Придумайте название выборки. С помощью неё вы сможете быстро отбирать нужные задачи.'),
                'checker' => ['chkEmpty', t('Укажите название выборки')]
            ]),
            'filters' => new Type\Text([
                'description' => t('Значения фильтров'),
                'visible' => false,
                'listenPost' => false
            ]),
            'filters_arr' => new Type\ArrayList([
                'description' => t('Значения фильтров - массив'),
                'visible' => false,
                'listenPost' => false
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок'),
                'visible' => false
            ])
        ]);
    }

    function beforeWrite($flag)
    {
        if ($this->isModified('filters_arr')) {
            $this['filters'] = serialize($this['filters_arr']);
        }

        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'user_id' => $this['user_id']
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    function afterObjectLoad()
    {
        $this['filters_arr'] = unserialize($this['filters']);
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::TASK_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return ModuleRights::TASK_FILTER_CREATE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::TASK_FILTER_UPDATE;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::TASK_FILTER_DELETE;
    }
}
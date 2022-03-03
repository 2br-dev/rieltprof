<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Config\Loader as ConfigLoader;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;

/**
 * Пльзовательский статус заказа
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Статус
 * @property integer $parent_id Родитель
 * @property string $bgcolor Цвет фона
 * @property string $type Идентификатор(Англ.яз)
 * @property string $copy_type Дублирует системный статус
 * @property integer $is_system Это системный статус. (его нельзя удалять)
 * --\--
 */
class UserStatus extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_WAITFORPAY = 'waitforpay';
    const STATUS_PAYMENT_METHOD_SELECTED = 'payment_method_selected';
    const STATUS_HOLD = 'hold';
    const STATUS_INPROGRESS = 'inprogress';
    const STATUS_NEEDRECEIPT = 'needreceipt'; //Особый статус для выбивания чека
    const STATUS_SUCCESS = 'success';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_USER = 'other';

    protected static $table = 'order_userstatus';
    protected static $sort = [
        self::STATUS_NEW,
        self::STATUS_WAITFORPAY,
        self::STATUS_PAYMENT_METHOD_SELECTED,
        self::STATUS_HOLD,
        self::STATUS_INPROGRESS,
        self::STATUS_NEEDRECEIPT,
        self::STATUS_USER,
        self::STATUS_SUCCESS,
        self::STATUS_CANCELLED
    ];

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Статус')
            ]),
            'parent_id' => new Type\Integer([
                'description' => t('Родитель'),
                'list' => [['\Shop\Model\UserStatusApi', 'staticRootList']],
                'default' => 0,
                'allowEmpty' => false,
            ]),
            'bgcolor' => new Type\Color([
                'maxLength' => 7,
                'description' => t('Цвет фона')
            ]),
            'type' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Идентификатор(Англ.яз)')
            ]),
            'copy_type' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Дублирует системный статус'),
                'hint' => t('Данный статус будет дублировать поведение выбранного системного статуса'),
                'list' => [[__CLASS__, 'getDefaultStatusesTitles'], ['' => t('- Не выбрано -')]],
                'visible' => false,
                'otherVisible' => true
            ]),
            'is_system' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Это системный статус. (его нельзя удалять)'),
                'visible' => false,
                'listenPost' => false,
                'default' => 0,
                'allowEmpty' => false
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Сорт. номер'),
                'visible' => false,
            ])
        ]);

        $this->addIndex(['site_id', 'type'], self::INDEX_UNIQUE);
    }

    /**
     * Функция срабатывает перед записью объекта
     *
     * @param string $flag - insert или update флаг текущего действия
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $max = (new OrmRequest())
                ->select('MAX(sortn) as max')
                ->from($this)
                ->exec()->getOneField('max', 0);

            $this['sortn'] = $max + 1;
        }

        if ($flag == self::UPDATE_FLAG) { //Если обновление
            //Проверим на зарезервированные alias статусов. Исключим подмену alias
            $old_status = new UserStatus($this['id']);

            if (in_array($old_status['type'], self::$sort)) {
                unset($this['type']);
            }
        }
        // Проверка и корректировка parent_id
        if ($this['parent_id'] != 0) {
            $parent = new UserStatus($this['parent_id']);
            if (empty($parent['id']) || $this['id'] == $parent['id']) {
                $this['parent_id'] = 0;
            } else {
                if ($parent['parent_id'] != 0) {
                    $this['parent_id'] = $parent['parent_id'];
                }
                OrmRequest::make()
                    ->update(new UserStatus)
                    ->set(['parent_id' => $this['parent_id']])
                    ->where(['parent_id' => $this['id']])
                    ->exec();
            }
        }
    }

    /**
     * Добавляет в базу данных стандартные статусы
     *
     * @param integer $site_id - ID сайта, на котором небходимо добавить статусы
     * @return void
     */
    public static function insertDefaultStatuses($site_id)
    {
        $default_names = self::getDefaultStatues();
        $assoc = [];
        foreach ($default_names as $type => $data) {
            $record = new self();
            $record->getFromArray($data);
            $record['site_id'] = $site_id;
            $record['type'] = $type;
            $record['is_system'] = 1;
            $record->insert();
            $assoc[$type] = $record['id'];
        }
        //Устанавливаем в настройки модуля статус заказа по умолчанию "Ожидает оплаты"
        $config = ConfigLoader::byModule('shop', $site_id);
        $config['first_order_status'] = $assoc[self::STATUS_WAITFORPAY];
        $config->update();
    }

    /**
     * Возвращает статусы по умолчанию
     *
     * @return array
     */
    public static function getDefaultStatues()
    {
        return [
            self::STATUS_NEW => [
                'title' => t('Новый'),
                'bgcolor' => '#83b7b3',
            ],
            self::STATUS_WAITFORPAY => [
                'title' => t('Ожидает оплату'),
                'bgcolor' => '#687482',
            ],
            self::STATUS_PAYMENT_METHOD_SELECTED => [
                'title' => t('Выбран метод оплаты'),
                'bgcolor' => '#4d76ad',
            ],
            self::STATUS_INPROGRESS => [
                'title' => t('В обработке'),
                'bgcolor' => '#f2aa17',
            ],
            self::STATUS_NEEDRECEIPT => [
                'title' => t('Ожидание чека'),
                'bgcolor' => '#808000',
            ],
            self::STATUS_SUCCESS => [
                'title' => t('Выполнен и закрыт'),
                'bgcolor' => '#5f8456',
            ],
            self::STATUS_CANCELLED => [
                'title' => t('Отменен'),
                'bgcolor' => '#ef533a',
            ]
        ];
    }

    /**
     * Возвращает ассоциативный массив со статусами по-умолчанию
     *
     * @param string[] $first_element - элементы, добавляемые в начало списка
     * @return string[]
     */
    public static function getDefaultStatusesTitles(array $first_element = [])
    {
        $result = [];
        foreach (self::getDefaultStatues() as $key => $data) {
            $result[$key] = $data['title'];
        }
        return $first_element + $result;
    }

    /**
     * Возвращает массив с порядком статусов
     *
     * @return array
     */
    public static function getStatusesSort()
    {
        return self::$sort;
    }

    /**
     * Возвращает true, если статус является системным, иначе - false
     *
     * @return bool
     */
    function isSystem()
    {
        static $defaults;

        if ($defaults === null) {
            $defaults = self::getDefaultStatues();
        }
        return isset($defaults[$this['type']]);
    }

    /**
     * Возвращает количество заказов для текущего статуса
     *
     * @param bool $cache - использовать кэш
     * @return integer
     */
    function getOrdersCount($cache = true)
    {
        static $cache_taxonomy;
        if (!$cache || $cache_taxonomy === null) {
            $cache_taxonomy = $this->getTaxonomy();
        }

        return isset($cache_taxonomy[$this['id']]) ? $cache_taxonomy[$this['id']] : 0;
    }

    /**
     * Возвращает количество заказов для каждого статуса
     *
     * @return array
     */
    private function getTaxonomy()
    {
        $count_by_status = OrmRequest::make()
            ->select('status, COUNT(*) as cnt')
            ->from(new Order())
            ->where(['site_id' => SiteManager::getSiteId()])
            ->groupby('status')
            ->exec()->fetchSelected('status', 'cnt');

        $count_by_status[0] = 0;
        foreach ($count_by_status as $value) {
            $count_by_status[0] += $value;
        }
        return $count_by_status;
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    public function delete()
    {
        if ($this['is_system'] == 0) {
            return parent::delete();
        }

        return $this->addError(t('Невозможно удалить системный статус'));
    }
}

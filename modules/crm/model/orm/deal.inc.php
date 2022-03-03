<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use Crm\Config\ModuleRights;
use Crm\Model\InteractionApi;
use Crm\Model\Links\LinkManager;
use Crm\Model\Links\Type\LinkTypeDeal;
use Crm\Model\Links\Type\LinkTypeOneClickItem;
use Crm\Model\Links\Type\LinkTypeOrder;
use Crm\Model\Links\Type\LinkTypeReservation;
use Files\Model\FileApi;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Orm\Type;
use Users\Model\Orm\User;
use Crm\Model\OrmType;
use RS\Event\Manager as EventManager;
use RS\Module\Manager as ModuleManager;

/**
 * ORM объект - сделка. Сделка - это документ, который создается менеджером перед продажей.
 * В документе Сделка может быть видна вся история взаимодействия с клиентом.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $deal_num Уникальный номер сделки
 * @property array $links Связь с другими объектами
 * @property string $title Название сделки
 * @property integer $status_id Статус
 * @property integer $manager_id Менеджер, создавший сделку
 * @property string $client_type Тип клиента
 * @property string $client_name Имя клиента
 * @property integer $client_id Клиент, для которого создается сделка
 * @property string $date_of_create Дата создания
 * @property string $message Комментарий
 * @property float $cost Сумма сделки
 * @property integer $board_sortn Сортировочный индекс на доске
 * @property integer $is_archived Сделка архивная?
 * --\--
 */
class Deal extends \RS\Orm\OrmObject
{
    const
        FILES_LINK_TYPE = 'Crm-CrmDeal',

        CLIENT_TYPE_GUEST = 'guest',
        CLIENT_TYPE_USER = 'user';

    protected static
        $table = 'crm_deal';
    
    function _init()
    {
        $property_iterator = parent::_init();
        $property_iterator->append([
            t('Основные'),
                '_tmpid' => new Type\Hidden([
                    'appVisible' => false,
                    'meVisible' => false
                ]),
                'deal_num' => new Type\Varchar([
                    'description' => t('Уникальный номер сделки'),
                    'hint' => t('Может использоваться для быстрой идентификации сделки внутри компании'),
                    'maxLength' => 20,
                    'unique' => true,
                    'meVisible' => false
                ]),
                'links' => new OrmType\Link([
                    'description' => t('Связь с другими объектами'),
                    'allowedLinkTypes' => [self::getAllowedLinkTypes()],
                    'linkSourceType' => self::getLinkSourceType(),
                    'hint' => t('После связывания с другими объектами, вы сможете найти данное взаимодействие прямо в карточках привязанных объектов'),
                    'meVisible' => false,
                ]),
                'title' => new Type\Varchar([
                    'description' => t('Название сделки'),
                    'hint' => t('Любое произвольное название, которое описывает суть сделки'),
                    'meVisible' => false,
                ]),
                'status_id' => new Type\Integer([
                    'description' => t('Статус'),
                    'list' => [['\Crm\Model\Orm\Status', 'getStatusesTitles'], $this->getShortAlias()]
                ]),
                'manager_id' => new Type\User([
                    'description' => t('Менеджер, создавший сделку'),
                ]),
                'client_type' => new Type\Enum(array_keys(self::getClientTypeTitles()), [
                    'description' => t('Тип клиента'),
                    'list' => [[__CLASS__, 'getClientTypeTitles']],
                    'radioListView' => true,
                    'default' => self::CLIENT_TYPE_GUEST,
                    'hint' => t('Укажите `Незарегистрированный пользователь`, если вы создаете сделку для нового клиента, который еще не имеет собственного аккаунта в вашем интернет-магазине. Выберите `Зарегистрированный пользователь`, если вы желаете связать сделку с существующим пользователем.'),
                    'meVisible' => false,
                ]),
                'client_name' => new Type\Varchar([
                    'description' => t('Имя клиента'),
                    'attr' => [[
                        'placeholder' => t('Например, Петров Иван Иванович'),
                    ]],
                    'visible' => false,
                ]),
                'client_id' => new Type\UserDialog([
                    'description' => t('Клиент, для которого создается сделка'),
                    'hint' => t('Вы можете зарегистрировать нового клиента или выбрать существующего. В случае регистрации нового, на почту клиента придет уведомление с его логином и паролем.'),
                    'template' => '%crm%/form/deal/client_id.tpl',
                    'meVisible' => false,
                ]),
                'date_of_create' => new Type\Datetime([
                    'description' => t('Дата создания')
                ]),
                'message' => new Type\Text([
                    'description' => t('Комментарий'),
                    'hint' => t('Любой произвольный комментарий')
                ]),
                'cost' => new Type\Decimal([
                    'description' => t('Сумма сделки'),
                    'decimal' => 2,
                    'maxLength' => 20
                ]),
                'board_sortn' => new Type\Integer([
                    'description' => t('Сортировочный индекс на доске'),
                    'visible' => false
                ]),
                'is_archived' => new Type\Integer([
                    'allowEmpty' => false,
                    'description' => t('Сделка архивная?'),
                    'hint' => t('Архивные сделки не отображаются на Kanban доске'),
                    'checkboxView' => [1,0]
                ])
        ]);

        $user_field_manager = Loader::byModule($this)
            ->getDealUserFieldsManager()
            ->setArrayWrapper('custom_fields');

        if ($user_field_manager->notEmpty()) {
            $property_iterator->append([
                t('Доп. поля'),
                    'custom_fields' => new \Crm\Model\OrmType\CustomFields([
                        'description' => t('Доп.поля'),
                        'fieldsManager' => $user_field_manager,
                        'checker' => [['\Crm\Model\Orm\CustomData', 'validateCustomFields'], 'custom_fields']
                    ])
            ]);
        }

        $property_iterator->append([
            t('Файлы'),
                '__files__' => new Type\UserTemplate('%crm%/form/deal/files.tpl'),
            t('Взаимодействия'),
                '__interaction__' => new \Crm\Model\OrmType\InteractionBlock([
                    'linkType' => self::getSelfLinkManagerType()
                ]),
            t('Задачи'),
                '__tasks__' => new \Crm\Model\OrmType\TaskBlock([
                    'linkType' => self::getSelfLinkManagerType()
                ])
        ]);

        //Включаем в форму hidden поле id.
        $this['__id']->setVisible(true);
        $this['__id']->setMeVisible(false);
        $this['__id']->setHidden(true);
    }

    /**
     * Устанавливает права для полей ORM объекта
     *
     * @param string $flag
     * @return void
     */
    public function initUserRights($flag)
    {
        $user = Auth::getCurrentUser();
        if (!$user->isSupervisor()) {
            //Только супервизор может выбирать создателя сделки
            $this['__manager_id']->setReadOnly(true);
            $this['__manager_id']->setListenPost(false);
            $this['__manager_id']->setMeVisible(false);
        }
    }

    /**
     * Возвращает пользователя, создавшего сделку
     *
     * @return User
     */
    public function getCreatorUser()
    {
        return new User($this['manager_id']);
    }


    /**
     * Возвращает список возможных родительских объектов
     *
     * @return string[]
     */
    public static function getAllowedLinkTypes()
    {
        $allow_link_types = [];

        $event_result = EventManager::fire('crm.deal.getlinktypes', $allow_link_types);
        $allow_link_types = $event_result->getResult();

        return $allow_link_types;
    }

    /**
     * Возвращает идентификатор связываемого объекта
     *
     * @return string
     */
    public static function getLinkSourceType()
    {
        return 'deal';
    }

    /**
     * Возвращает список типов пользователей
     *
     * @return array
     */
    public static function getClientTypeTitles()
    {
        return [
            self::CLIENT_TYPE_GUEST => t('Незарегистрированный пользователь'),
            self::CLIENT_TYPE_USER => t('Зарегистрированный пользователь')
        ];
    }

    /**
     * Возвращает объект пользователя, для которого создается сделка
     * Если пользователь незарегистрирован, то у него не будет id
     *
     * @return User
     */
    public function getClientUser()
    {
        $user = new User();
        switch($this['client_type']) {
            case self::CLIENT_TYPE_GUEST:
                $user['name'] = $this['client_name'];
                break;
            case self::CLIENT_TYPE_USER:
                $user->load($this['client_id']);
        }

        return $user;
    }

    /**
     * Обработчик, вызывается перед сохранением объекта
     *
     * @param string $flag
     */
    public function beforeWrite($flag)
    {
        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        if ($flag == self::INSERT_FLAG) {
            //Устанавливаем максимальный сортировочный индекс
            $this['board_sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(board_sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Обработчик, вызывается сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        //Сохраняем значения доп. полей в дополнительную таблицу
        $this['custom_fields'] = CustomData::loadCustomFields($this->getShortAlias(), $this['id']);
    }

    /**
     * Обработчик, вызывается после созранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        //Переносим временные объекты, если таковые имелись
        if ($this['_tmpid'] < 0) {

            //Переносим файлы к сохраненному объекту
            FileApi::changeLinkId($this['_tmpid'], $this['id'], self::FILES_LINK_TYPE);

            //Переносим взаимодействия и задачи к сохраненному объекту
            LinkManager::updateLinkId($this['_tmpid'], $this['id'], self::getSelfLinkManagerType());
        }

        if ($this->isModified('links')) {
            LinkManager::saveLinks($this->getLinkSourceType(), $this['id'], $this['links']);
        }

        CustomData::saveCustomFields($this->getShortAlias(), $this['id'], $this['custom_fields']);
    }

    /**
     * Возвращает строку, идентифицирующую данную сделку
     *
     * @return string
     */
    public function getPublicTitle()
    {
        $client = $this->getClientUser();
        return t('Сделка №%num c %user_name', [
            'num' => $this['deal_num'],
            'user_name' => $client->getFio()
        ]);
    }

    /**
     * Возвращает тип связи с данным объектом
     *
     * @return string
     */
    public static function getSelfLinkManagerType()
    {
        return LinkTypeDeal::getId();
    }

    /**
     * Удаляет текущий объект, а также все ссылки на него
     *
     * @return bool
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            //Удаляем ссылки связи с объектами
            LinkManager::removeLinks($this->getLinkSourceType(), $this['id']);
        }
        return $result;
    }

    /**
     * Возвращает количество взаимодействий с данной сделкой
     *
     * @return integer
     */
    public function getInteractionCount()
    {
        return LinkManager::getLinkedSourceObjectCount(
            Interaction::getLinkSourceType(),
            LinkTypeDeal::getId(),
            $this['id']);
    }

    /**
     * Возвращает количество задач для данной сделки
     *
     * @return integer
     */
    public function getTaskCount()
    {
        return LinkManager::getLinkedSourceObjectCount(
            Task::getLinkSourceType(),
            LinkTypeDeal::getId(),
            $this['id']);
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::DEAL_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return ModuleRights::DEAL_CREATE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::DEAL_UPDATE;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::DEAL_DELETE;
    }
}
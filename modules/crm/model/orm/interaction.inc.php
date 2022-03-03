<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use Crm\Config\ModuleRights;
use Crm\Model\Links\LinkManager;
use Crm\Model\Links\Type\LinkTypeCall;
use Crm\Model\Links\Type\LinkTypeDeal;
use Crm\Model\Links\Type\LinkTypeOrder;
use Crm\Model\Links\Type\LinkTypeUser;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Type;
use Crm\Model\OrmType;
use Users\Model\Orm\User;

/**
 * ORM объект - взаимодействие с клиентом.
 * Данный объект описывает один звонок клиенту или одну встречу.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property array $links Связь с другими объектами
 * @property string $title Короткое описание
 * @property string $date_of_create Дата создания
 * @property string $duration Продолжительность
 * @property integer $creator_user_id Создатель взаимодействия
 * @property string $message Комментарий
 * --\--
 */
class Interaction extends \RS\Orm\OrmObject
{
    const
        PARENT_TYPE_DEAL = 'deal',
        PARENT_TYPE_ORDER = 'order';

    protected static
        $table = 'crm_interaction';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
            '_tmpid' => new Type\Hidden([
                'appVisible' => false,
                'meVisible' => false
            ]),
            'links' => new OrmType\Link([
                'description' => t('Связь с другими объектами'),
                'allowedLinkTypes' => [self::getAllowedLinkTypes()],
                'linkSourceType' => self::getLinkSourceType(),
                'hint' => t('После связывания с другими объектами, вы сможете найти данное взаимодействие прямо в карточках привязанных объектов')
            ]),
            'title' => new Type\Varchar([
                'description' => t('Короткое описание'),
                'checker' => ['chkEmpty', t('Укажите короткое описание')],
                'hint' => t('Опишите коротко что обсуждали, какие договоренности достигли')
            ]),
            'date_of_create' => new Type\Datetime([
                'description' => t('Дата создания')
            ]),
            'duration' => new Type\Varchar([
                'description' => t('Продолжительность'),
                'hint' => t('d - дней, h - часов, m - минут, s - секунд. Например: 1d 4h 2m 1s')
            ]),
            'creator_user_id' => new Type\User([
                'description' => t('Создатель взаимодействия')
            ]),
            'message' => new Type\Text([
                'description' => t('Комментарий'),
                'hint' => t('Опишите в произвольной форме о результате взаимодействия с вашим клиентом')
            ])
        ]);

        $user_field_manager = Loader::byModule($this)
            ->getInteractionUserFieldsManager()
            ->setArrayWrapper('custom_fields');

        if ($user_field_manager->notEmpty()) {
            $this->getPropertyIterator()->append([
                t('Доп. поля'),
                'custom_fields' => new \Crm\Model\OrmType\CustomFields([
                    'description' => t('Доп.поля'),
                    'fieldsManager' => $user_field_manager,
                    'checker' => [['\Crm\Model\Orm\CustomData', 'validateCustomFields'], 'custom_fields']
                ])
            ]);
        }

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
        if (!$user->isSupervisor()) { //Только supervisor может изменять создателя
            $this['__creator_user_id']->setReadOnly(true);
            $this['__creator_user_id']->setListenPost(false);
        }
    }

    /**
     * Возвращает объект пользователя, создателя
     *
     */
    public function getCreatorUser()
    {
        return new User($this['creator_user_id']);
    }

    /**
     * Возвращает список возможных родительских объектов
     *
     * @return string[]
     */
    public static function getAllowedLinkTypes()
    {
        $allow_link_types = [
            LinkTypeDeal::getId(),
            LinkTypeUser::getId(),
            LinkTypeCall::getId()
        ];

        $event_result = EventManager::fire('crm.interaction.getlinktypes', $allow_link_types);
        $allow_link_types = $event_result->getResult();

        return $allow_link_types;
    }

    /**
     * Возвращает идентификатор в менеджере связей
     *
     * @return string
     */
    public static function getLinkSourceType()
    {
        return 'interaction';
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

    }

    /**
     * Обработчик сохранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        if ($this->isModified('links')) {
            LinkManager::saveLinks($this->getLinkSourceType(), $this['id'], $this['links']);
        }

        CustomData::saveCustomFields($this->getShortAlias(), $this['id'], $this['custom_fields']);
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
     * Удаляет взаимодействие, а также все ссылки на него
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
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::INTERACTION_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return ModuleRights::INTERACTION_CREATE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::INTERACTION_UPDATE;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::INTERACTION_DELETE;
    }
}
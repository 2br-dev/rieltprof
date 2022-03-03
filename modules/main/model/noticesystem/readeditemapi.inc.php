<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;

use Main\Model\Orm\ReadedItem;
use RS\Application\Auth;
use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;

/**
 * Вспомогательный класс, отвечает за получение и установку сведений о прочитанных и непрочитанных объектах.
 */
class ReadedItemApi
{
    const
        LAST_ID_POSTFIX = '_LASTID';

    protected
        $entity,
        $site_id,
        $user_id;

    function __construct($site_id = null, $user_id = null)
    {
        $this->setSiteId($site_id);
        $this->setUserId($user_id);
    }

    /**
     * Устанавливает ID метки, с которой будет работать текущий экземпляр класса
     *
     * @return
     */
    function setDefaultEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Возвращает тип объектов приоритетный или по умолчанию
     *
     * @param $priority_entity приоритетный entity, возвращается именно он, если задан
     * @return string
     *
     * @throws \RS\Exception Бросает исключение, если entity не задан
     */
    protected function getEntity($priority_entity)
    {
        if ($priority_entity) {
            return $priority_entity;
        }

        if ($this->entity) {
            return $this->entity;
        }

        throw new \RS\Exception(t('Не задан entity, для рассчета счетчиков'));
    }

    /**
     * Устанавливает текущий сайт, в рамках которого будет идти учет счетчиков
     *
     * @param integer $site_id ID сайта
     * @return void
     */
    public function setSiteId($site_id)
    {
        $this->site_id = ($site_id === null) ? SiteManager::getSiteId() : $site_id;
    }

    /**
     * Устанавливает текущего пользователя, в рамках которого будет идти учет счетчиков
     * @param integer $user_id ID пользователя
     * @return void
     */
    public function setUserId($user_id)
    {
        $this->user_id = ($user_id === null) ? Auth::getCurrentUser()->id : $user_id;
    }

    /**
     * Возвращает ID последнего прочитанного объекта
     *
     * @param string $entity Тип объектов
     * @return integer
     */
    public function getLastReadedId($entity = null)
    {
        $last_id = Request::make()
                        ->select('last_id')
                        ->from(new ReadedItem)
                        ->where([
                            'site_id' => $this->site_id,
                            'user_id' => $this->user_id,
                            'entity' => $this->getEntity($entity).self::LAST_ID_POSTFIX,
                            'entity_id' => 0
                        ])->exec()->getOneField('last_id', 0);

        return $last_id;
    }

    /**
     * Возвращает ID прочитанных объектов
     *
     * @param string $entity Тип объектов
     * @param null|array $entity_ids_filter Фильтр по ID объектов
     * @return array
     */
    public function getReadedIds($entity = null, $entity_ids_filter = null)
    {
        if (!$entity_ids_filter && $entity_ids_filter !== null)
            return [];

        $q = Request::make()
            ->select('entity_id')
            ->from(new ReadedItem)
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->user_id,
                'entity' => $this->getEntity($entity)
            ]);

        if ($entity_ids_filter) {
            $q->whereIn('entity_id', (array)$entity_ids_filter);
        }

        return $q->exec()->fetchSelected(null, 'entity_id');
    }

    /**
     * Возвращает количество прочитанных объектов
     *
     * @param string $entity Тип объектов
     * @return mixed
     */
    public function getReadedCount($entity = null)
    {
        return Request::make()
            ->select('entity_id')
            ->from(new ReadedItem)
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->user_id,
                'entity' => $this->getEntity($entity)
            ])->count();
    }

    /**
     * Возвращает количество непрочитанных объектов
     *
     * @param integer|\RS\Orm\AbstractObject|\RS\Orm\Request $total_or_object Общее количество объектов
     * Если integer, то должно содержать общее количество объектов
     * Если \RS\Orm\AbstractObject, то будет построен запрос на получение общего количества объектов
     * Если \RS\Orm\Request, то будет построен запрос на основе данного запроса для получения общего количества объектов
     *
     * @param string $entity Тип объектов
     * @param string $site_id_field Поле, в котором содержится ID сайта
     * @param string $id_field Поле, в котором содержится ID объекта
     * @return integer
     * @throws \RS\Exception Бросает исключение в случае некорректного типа объекта $total_or_object
     */
    public function getUnreadCount($total_or_object,
                                   $entity = null,
                                   $site_id_field = 'site_id',
                                   $id_field = 'id')
    {
        if (is_object($total_or_object)) {

            if ($total_or_object instanceof AbstractObject) {
                $total_or_object = Request::make()
                                    ->from($total_or_object);

                if ($site_id_field) {
                    $total_or_object->where([$site_id_field => $this->site_id]);
                }
            }

            if ($total_or_object instanceof Request) {
                $total_or_object->where("{$id_field} > '#id'", [
                    'id' => $this->getLastReadedId($entity)
                ]);

                //Получаем общее количество заказов
                $total_or_object = $total_or_object->count();
            } else {
                throw new \RS\Exception(t('Передан неподдерживаемый тип объекта'));
            }
        }

        $result = $total_or_object - $this->getReadedCount($entity);
        return $result>0 ? $result : 0;
    }

    /**
     * Отмечает прочитанным объект
     *
     * @param array|integer $entity_ids ID или список ID объектов
     * @param string $entity Тип объекта
     * @return void
     */
    public function markAsReaded($entity_ids, $entity = null)
    {
        foreach((array)$entity_ids as $id) {
            $last_id_data = new ReadedItem();
            $last_id_data['site_id'] = $this->site_id;
            $last_id_data['user_id'] = $this->user_id;
            $last_id_data['entity'] = $this->getEntity($entity);
            $last_id_data['entity_id'] = $id;
            $last_id_data->insert();
        }
    }

    /**
     * Возвращает количество непрочитанных объектов
     *
     * @param integer|\RS\Orm\AbstractObject|\RS\Orm\Request $last_id_or_object Общее количество объектов
     * Если integer, то должно содержать общее количество объектов
     * Если \RS\Orm\AbstractObject, то будет построен запрос на получение общего количества объектов
     * Если \RS\Orm\Request, то будет построен запрос на основе данного запроса для получения общего количества объектов
     *
     * @param string $entity Тип объектов
     * @param string $site_id_field Поле, в котором содержится ID сайта
     * @param string $user_id_field Поле, в котором содержится ID пользователя
     * @param string $id_field Поле, в котором содержится ID объекта
     * @return void
     * @throws \RS\Exception Бросает исключение в случае некорректного типа объекта $total_or_object
     */
    public function markAllAsReaded($last_id_or_object,
                                    $entity = null,
                                    $site_id_field = 'site_id',
                                    $user_id_field = 'user_id',
                                    $id_field = 'id')
    {
        if (is_object($last_id_or_object)) {

            if ($last_id_or_object instanceof AbstractObject) {
                $last_id_or_object = Request::make()
                    ->from($last_id_or_object);

                if ($site_id_field) {
                    $last_id_or_object->where([$site_id_field => $this->site_id]);
                };
                if ($user_id_field) {
                    $last_id_or_object->where([$user_id_field => $this->user_id]);
                };
            }

            if ($last_id_or_object instanceof Request) {
                $last_id_or_object->select('MAX('.$id_field.') as maxid');

                //Получаем общее количество заказов
                $last_id_or_object = $last_id_or_object->exec()->getOneField('maxid');


            } else {
                throw new \RS\Exception(t('Передан неподдерживаемый тип объекта'));
            }
        }

        //Удалим сведения о прочитанных записях
        Request::make()
            ->delete()
            ->from(new ReadedItem)
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->user_id,
                'entity' => $this->getEntity($entity)
            ])->exec();

        //Запишем id последнего прочитанного элемента
        $last_id_data = new ReadedItem();
        $last_id_data['site_id'] = $this->site_id;
        $last_id_data['user_id'] = $this->user_id;
        $last_id_data['entity'] = $this->getEntity($entity).self::LAST_ID_POSTFIX;
        $last_id_data['entity_id'] = 0;
        $last_id_data['last_id'] = $last_id_or_object;
        $last_id_data->replace();
    }

    /**
     * Удаляет информацию о фактах просмотра объекта
     *
     * @param $ids
     * @param string|null $entity
     * @return integer
     */
    function removeReadFlag($ids, $entity = null)
    {
        $ids = (array)$ids;

        return \RS\Orm\Request::make()
            ->delete()
            ->from(new ReadedItem())
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->user_id,
                'entity' => $this->getEntity($entity)
            ])
            ->whereIn('entity_id', $ids)
            ->exec()->affectedRows();
    }
}
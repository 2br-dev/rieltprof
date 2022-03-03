<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

/**
 * Класс предоставляет API связанные со счетчиком для CRUD контроллера
 */
class TopicMeterApi implements \Main\Model\NoticeSystem\MeterApiInterface
{
    protected
        $topic_api;

    function __construct(TopicApi $topic_api)
    {
        $this->topic_api = $topic_api;
    }

    /**
     * Возвращает идентификатор счетчика
     *
     * @return string
     */
    function getMeterId()
    {
        return 'rs-admin-menu-support';
    }

    /**
     * Возвращает количество непросмотренных объектов
     *
     * @param integer|null $user_id
     * @return integer
     */
    function getUnviewedCounter()
    {
        $q = $this->topic_api->queryObj();
        $q->select('SUM(newadmcount) as sum');
        return $q->exec()->getOneField('sum', 0);
    }

    /**
     * Отмечает просмотренным один объект
     * Возвращает количество непросмотренных объектов
     *
     * @param integer $id
     * @return integer
     */
    function markAsViewed($ids)
    {
        $q = $this->topic_api->queryObj();
        $q->update()
            ->set(['newadmcount' => 0])
            ->whereIn('id', (array) $ids)
            ->exec();

        \RS\Orm\Request::make()
            ->update(new \Support\Model\Orm\Support())
            ->set(['processed' => 1])
            ->where([
                'site_id' => $this->topic_api->getSiteContext(),
                'is_admin' => 1
            ])
            ->whereIn('topic_id', (array) $ids)
            ->exec();

        $new_counter = $this->getUnviewedCounter();

        //Обновляем хранилище счетчиков на сервере
        $meters = \Main\Model\NoticeSystem\Meter::getInstance();
        $meters->updateNumber($this->getMeterId(), $new_counter);

        return $new_counter;
    }

    /**
     * Отмечает просмотренными все объекты
     *
     * @param integer|null $user_id
     * @return integer
     */
    function markAllAsViewed()
    {
        $q = $this->topic_api->queryObj();
        $q->update()->set([
            'newadmcount' => 0
        ])->exec();

        \RS\Orm\Request::make()
            ->update(new \Support\Model\Orm\Support())
            ->set([
                'processed' => 1
            ])
            ->where([
                'site_id' => $this->topic_api->getSiteContext(),
                'is_admin' => 1
            ])
            ->exec();

        //Обновляем счетчик на сервере
        $meters = \Main\Model\NoticeSystem\Meter::getInstance();
        $meters->updateNumber($this->getMeterId(), 0);

        return 0;
    }

    /**
     * Удаляет сведения о просмотрах объектов
     *
     * @return bool
     */
    function removeViewedFlag($ids)
    {
        return 0;
    }
}
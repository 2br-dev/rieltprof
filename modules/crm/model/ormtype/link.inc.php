<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\OrmType;

/**
 * Поле, которое отображает связь с другими объектами
 */
class Link extends \RS\Orm\Type\ArrayList
{
    protected $form_template = '%crm%/admin/ormtype/link.tpl';
    protected $source_id_field = 'id';
    protected $source_type;
    protected $allowed_link_types = [];

    /**
     * Устанавливает, в каком поле у объекта источника хранится ID
     *
     * @param string $field Имя поля
     * @return void
     */
    public function setSourceIdField($field)
    {
        $this->source_id_field = $field;
    }

    /**
     * Возвращает поле, в котором есть ID
     *
     * @return string
     */
    public function getSourceIdField()
    {
        return $this->source_id_field;
    }

    /**
     * Устанавливает идентификатор связи исходного объекта
     *
     * @param string $source_type Строковый идентификатор
     */
    public function setLinkSourceType($source_type)
    {
        $this->source_type = $source_type;
    }

    /**
     * Возвращает идентификатор связи исходного объекта
     *
     * @return string
     */
    public function getLinkSourceType()
    {
        return $this->source_type;
    }

    /**
     * Устанавливает идентификаторы возможных объектов для связи
     *
     * @param string[] $link_types Список ID объектов связи, потомков Crm\Model\Links\Type\AbstractType
     * @return void
     */
    function setAllowedLinkTypes($link_types)
    {
        $this->allowed_link_types = $link_types;
    }

    /**
     * Возвращает список идентификаторов возможных объектов связи
     *
     * @return string[]
     */
    function getAllowedLinkTypes()
    {
        return $this->allowed_link_types;
    }


    /**
     * Возвращает список связей из базы данных
     *
     * @param $source_object
     * @return array
     * @throws \RS\Exception
     */
    function getLinkedObjects($source_object)
    {
        if (!$this->source_type) {
            throw new \RS\Exception(t('Не задан тип связи исходного объекта. (Используйте метод setLinkSourceType)'));
        }

        $data = $this->get();
        $allow_link_types = $this->getAllowedLinkTypes();

        if ($allow_link_types) {
            if (!$data) {
                $links = \RS\Orm\Request::make()
                    ->from(new \Crm\Model\Orm\Link())
                    ->where([
                        'source_type' => $this->getLinkSourceType(),
                        'source_id' => $source_object[$this->getSourceIdField()]
                    ])
                    ->whereIn('link_type', $allow_link_types)
                    ->objects();
            } else {
                $links = [];
                if (is_array($data)) {
                    foreach ($data as $link_type => $link_ids) {
                        foreach($link_ids as $link_id) {
                            $link = new \Crm\Model\Orm\Link();
                            $link['source_type'] = $this->getLinkSourceType();
                            $link['source_id'] = $source_object[$this->getSourceIdField()];
                            $link['link_type'] = $link_type;
                            $link['link_id'] = $link_id;
                            $links[] = $link;
                        }
                    }
                }
            }
        } else {
            $links = [];
        }

        $result = [];
        foreach($links as $link) {
            $result[] = $link->getLinkTypeObject($source_object);
        }

        return $result;
    }

}
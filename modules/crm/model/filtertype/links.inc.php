<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;

use Crm\Model\Links\Type\AbstractType;
use Crm\Model\Orm\Link;
use RS\Orm\Request;

/**
 * Класс обеспечивает фильтрацию по связям (OrmType\Link)
 */
class Links extends \RS\Html\Filter\Type\AbstractType
{
    public
        $tpl = '%crm%/admin/filtertype/links.tpl';

    protected
        $search_type = 'links',
        $source_link_type,
        $allow_link_types = [];

    function __construct($key, array $allow_link_types, $link_source_type,  array $options = [])
    {
        parent::__construct($key, '', $options);
        $this->setAllowLinkTypes($allow_link_types);
        $this->setLinkSourceType($link_source_type);
    }

    /**
     * Устанавливает список допустимых идентификаторов типов связи
     *
     * @param array $allow_link_types массив идентификаторов
     * @return void
     */
    function setAllowLinkTypes(array $allow_link_types)
    {
        $this->allow_link_types = $allow_link_types;
    }

    /**
     * Устанавливает идентификатор исходного объекта связи
     *
     * @param string $source_link_type
     * @return void
     */
    function setLinkSourceType($source_link_type)
    {
        $this->source_link_type = $source_link_type;
    }

    /**
     * Возвращает идентификатор исходного объекта связи
     *
     * @return string
     */
    function getLinkSourceType()
    {
        return $this->source_link_type;
    }

    /**
     * Возвращает список допустимых идентификаторов связи
     *
     * @return array
     */
    function getAllowLinkTypes()
    {
        return $this->allow_link_types;
    }

    /**
     * Возвращает список допустимых объектов типов связи
     *
     * @return array
     */
    function getAllowLinkTypesObject()
    {
        $result = [];
        foreach($this->getAllowLinkTypes() as $link_type_id) {
            $result[$link_type_id] = AbstractType::makeById($link_type_id);
        }

        return $result;
    }

    /**
     * Возвращает HTML формы для одного типа связи
     *
     * @param AbstractType $link_type
     * @return string
     */
    function getLinkTypeForm(AbstractType $link_type)
    {
        $values = $this->getValue();

        $form_object = $link_type->getTabForm();
        $form_object->getPropertyIterator()->arrayWrap($this->getName().'['.$link_type->getId().']');
        if (isset($values[$link_type->getId()])) {
            $form_object->getFromArray($values[$link_type->getId()]);
        }

        $form_object->setFormTemplate($this->getName().'_'.$link_type->getId());
        return $form_object->getForm(null, null, false, null, '%crm%/admin/filtertype/links_src_form.tpl');
    }

    /**
     * Возвращает true, если фильтр в текущий момент вдется поиск по одному из полей связи
     *
     * @return bool
     */
    function isActiveFilter()
    {
        return $this->getValue() == true;
    }

    /**
     * Возвращает выражение для поиска по связям
     *
     * @return string
     */
    protected function where_links()
    {
        $values = $this->getValue();
        if (!$values) return null;

        $q = Request::make()
            ->select('source_id')
            ->from(new Link())
            ->where([
                'source_type' => $this->getLinkSourceType()
            ]);

        $q->openWGroup();
        foreach($this->getValue() as $link_type_id => $fields) {
            $link_id = AbstractType::makeById($link_type_id)->getLinkIdByTabFormObject($fields);

            $q->where("(`link_type` = '#link_type' AND `link_id` = '#link_id')", [
                'link_type' => $link_type_id,
                'link_id' => $link_id
            ], 'OR');
        }
        $q->closeWGroup();
        $ids = $q->exec()->fetchSelected(null, 'source_id');

        if (!$ids) {
            $ids = [0]; //Если ничего не найдено добавим невыполнимое условие
        }

        return 'A.id IN ('.implode(',', $ids).')';
    }

    /**
     * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
     *
     * @param array $current_filter_values - значения установленных фильтров
     * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
     * @return array of array ['title' => string, 'value' => string, 'href_clean']
     */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];

        if ($this->getValue()) {
            foreach ($this->getValue() as $link_type_id => $fields) {
                $link = AbstractType::makeById($link_type_id);
                $link_id = $link->getLinkIdByTabFormObject($fields);
                $link->init($link_id);

                $without_this = $current_filter_values;
                unset($without_this[$this->getKey()][$link_type_id]);

                $parts[] = [
                    'title' => t('Связь'),
                    'value' => $link->getLinkText(),
                    'href_clean' => \RS\Http\Request::commonInstance()->replaceKey([$this->wrap_var => $without_this]) //Url, для очистки данной части фильтра
                ];
            }
        }
        return $parts;
    }

}


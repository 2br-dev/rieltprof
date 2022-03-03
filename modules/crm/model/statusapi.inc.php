<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Orm\Status;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;

/**
 * Класс для организации выборок ORM объекта
 */
class StatusApi extends EntityList
{
    public $uniq;

    public function __construct()
    {
        parent::__construct(new Orm\Status(), [
            'defaultOrder' => 'sortn',
            'sortField' => 'sortn',
            'nameField' => 'title'
        ]);
    }

    /**
     * Возвращает список категорий для построения рубрик в административной панели
     *
     * @return array
     */
    public function getObjectTypesCategoryList()
    {
        $result = [];
        foreach (Status::getObjectTypeAliases() as $id => $title) {
            $result[] = [
                'id' => $id,
                'title' => $title,
            ];
        }

        return $result;
    }

    /**
     * Возвращает список для отображения в html элементе select
     *
     * @param array $first
     * @param null|string $object_type_alias
     * @return mixed
     */
    public static function staticSelectList($first = [], $object_type_alias = null)
    {
        $_this = new static();
        if ($object_type_alias) {
            $_this->setFilter('object_type_alias', $object_type_alias);
        }
        return $_this->getSelectList((array)$first);
    }

    /**
     * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
     *
     * @param int $from - id элемента, который переносится
     * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     * @param \RS\Orm\Request $extra_expr - объект с установленными уточняющими условиями, для выборки объектов сортировки
     * @return bool
     */
    public function moveElement($from, $to, $flag, OrmRequest $extra_expr = null)
    {
        if (!$extra_expr) {
            $from_obj = $this->getOneItem($from);

            $extra_expr = OrmRequest::make()->where([
                'object_type_alias' => $from_obj['object_type_alias']
            ]);
        }

        return parent::moveElement($from, $to, $flag, $extra_expr);
    }
}

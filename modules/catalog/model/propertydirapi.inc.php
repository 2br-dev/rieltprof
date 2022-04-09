<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Orm\Property\Dir as PropertyDir;
use RS\Module\AbstractModel\EntityList;

/**
 * API, отвечающее за работу с объектами групп характеристик
 */
class PropertyDirApi extends EntityList
{
    public $uniq;

    function __construct()
    {
        parent::__construct(new PropertyDir, [
            'multisite' => true,
            'defaultOrder' => 'sortn',
            'nameField' => 'title',
            'sortField' => 'sortn',
        ]);
    }

    /**
     * Возвращает плоский список групп характеристик
     *
     * @return array
     */
    public static function selectList()
    {
        return self::staticSelectList([0 => t('Без группы')]);
    }

    /**
     * Возвращает особый список данных для отображения в админ.панели
     *
     * @param integer|null $page Номер страницы
     * @param integer|null $page_size Количество элементов на страницу
     * @param string|null $order Сортировка
     *
     * @return \RS\Orm\AbstractObject[]
     */
    public function getTableList($page = null, $page_size = null, $order = null)
    {
        $list = $this->getList($page, $page_size, $order);
        return array_merge([[
            'id' => 0,
            'title' => t('Без группы'),
            'noOtherColumns' => true,
            'noCheckbox' => true,
            'noDraggable' => true,
            'noRedMarker' => true
        ]], $list);
    }
}

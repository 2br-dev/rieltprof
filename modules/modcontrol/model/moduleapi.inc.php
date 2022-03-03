<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ModControl\Model;

use Main\Model\ModuleLicenseApi;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Html\Filter;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Exception as OrmException;
use RS\Html\Table;

class ModuleApi
{
    const SORT_BY_MODULE_NAME = 'name';
    //const DEFAULT_SO = 'id';

    protected $filter;
    protected $sort_field = 'class';
    protected $sort_direction = SORTABLE_ASC;

    /**
     * @param null $sort
     * @return array
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    function tableData($sort = null, $sort_direction = null)
    {
        $sort = $sort ?: $this->sort_field;
        $sort_direction = $sort_direction ?: $this->sort_direction;

        $module_manager = new ModuleManager();
        $list = $module_manager->getAllConfig();

        $table_rows = [];
        $i = 0;
        foreach ($list as $alias => $module)
        {
            $i++;

            $disable = ($module['is_system'] || $module->isLicenseUpdateExpired()) ? ['disabled' => 'disabled'] : null;
            $highlight = (time() - $module['lastupdate']) < 60*60*24 ? ['class' => 'highlight_new'] : null;
            $module['class'] = $alias;

            if ($this->filter) {
                foreach($this->filter as $key => $val) {
                    if ($val != '' && mb_stripos($module[$key], $val) === false) {
                        continue 2;
                    }
                }
            }

            $information_level = '';

            $table_rows[] = [
                'num' => $i,
                'name' => $module['name'],
                'description' => $module['description'],
                'license_text' => ModuleLicenseApi::getLicenseDataText($alias, $information_level),
                'license_text_level' => $information_level,
                'checkbox_attribute' => $disable,
                'row_attributes' => $highlight
                ] + $module->getValues();
        }

        usort($table_rows, function($a, $b) use ($sort, $sort_direction) {

            if (!isset($a[$sort])) return 0;

            $result = ($sort_direction == SORTABLE_ASC)
                ? strcmp($a[$sort], $b[$sort])
                : strcmp($b[$sort], $a[$sort]);

            return $result;
        });

        return $table_rows;
    }

    /**
     * Применяет сортировку к выборке
     *
     * @param Table\Control $table_control
     */
    function addTableControl(Table\Control $table_control)
    {
        $sort_column = $table_control->getTable()->getSortColumn();
        $this->sort_field = $sort_column->getField();
        $this->sort_direction = $sort_column->property['CurrentSort'];
    }

    /**
     * Добавляет фильтрацию к выборке
     *
     * @param Filter\Control $filter_control
     * @return $this
     */
    function addFilterControl(Filter\Control $filter_control)
    {
        $key_val = $filter_control->getKeyVal();
        $this->filter = $key_val;
        return $this;
    }
}

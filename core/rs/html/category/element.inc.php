<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Category;

use RS\Html\AbstractHtml;
use RS\Html\Table\Type as TableType;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\AbstractObject;
use RS\View\Engine as ViewEngine;

class Element extends AbstractHtml
{
    const VIEWTYPE_BIGTREE = 'bigtree';
    const VIEWTYPE_INLINE = 'inline';

    protected $checked = [];
    protected $checkbox_name = 'chk[]';
    /** @var TableType\Actions */
    protected $tools;
    /** @var TableType\AbstractType */
    protected $main_column;
    /** @var TableType\AbstractType[] */
    protected $other_columns;
    protected $head_buttons;
    /** @var AbstractTreeListIterator */
    protected $data;

    /**
     * Задает массив выделенных элементов. id Выделенных элементов должны быть в ключах массива
     *
     * @param array $fliped_keys
     * @return Element
     */
    function setChecked(array $fliped_keys)
    {
        $this->checked = $fliped_keys;
        return $this;
    }

    /**
     * Возвращает true, если елемент с заданным id выделен
     *
     * @param mixed $id
     * @return bool
     */
    function isChecked($id)
    {
        return isset($this->checked[$id]);
    }

    /**
     * Возвращает имя переменной для checkbox'ов
     * @return string
     */
    function getCheckboxName()
    {
        return $this->checkbox_name;
    }

    /**
     * Устанавливает имя переменной для checkbox'ов
     *
     * @param mixed $name
     * @return Element
     */
    function setCheckboxName($name)
    {
        $this->checkbox_name = $name;
        return $this;
    }

    /**
     * Устанавливает набор инструментов, выводимых в каждой строке элемента
     *
     * @param TableType\Actions $tools
     * @return Element
     */
    function setTools(TableType\Actions $tools)
    {
        $this->tools = $tools;
        return $this;
    }

    /**
     * Возвращает набор инструментов для конкретной строки элемента
     *
     * @param mixed $row
     * @return TableType\Actions
     */
    function getTools($row = null)
    {
        if ($row !== null) {
            $field = $this->tools->getField();
            $value = isset($field) ? $this->getCellValue($row, $field) : null;

            $this->tools->setValue($value);
            $this->tools->setRow($row);
        }
        return $this->tools;
    }

    /**
     * Устанавливает главную ячейку с данными
     *
     * @param TableType\AbstractType $main_column
     * @return Element
     */
    function setMainColumn(TableType\AbstractType $main_column)
    {
        $this->main_column = $main_column;
        return $this;
    }

    /**
     * Устанавливает ячейки, которые выводятся справа от основного элемента
     *
     * @param array of \RS\Html\Table\Type\AbstractType $columns
     * @return Element
     */
    function setOtherColumns(array $columns)
    {
        $this->other_columns = $columns;
        return $this;
    }

    /**
     * Устанавливает кнопки, которые выводятся над деревом
     *
     * @param array $head_buttons
     * @return Element
     */
    function setHeadButtons(array $head_buttons)
    {
        $this->head_buttons = $head_buttons;
        return $this;
    }

    /**
     * Возвращает главную ячейку для конкретной строки
     *
     * @param mixed $row - ORM объект или ассоциативный массив с данными
     * @return TableType\AbstractType
     */
    function getMainColumn($row = null)
    {
        if ($row === null) {
            return $this->main_column;
        } else {
            $field = $this->main_column->getField();
            $value = isset($field) ? $this->getCellValue($row, $field) : null;

            $item = $this->main_column; //Можно добавить clone
            $item->setValue($value);
            $item->setRow($row);
            return $item;
        }
    }

    /**
     * Возвращает ячейки, которые выводятся справа от основного элемента для одной строки данных
     *
     * @param mixed $row - ORM объект или ассоциативный массив с данными
     * @return array
     */
    function getOtherColumns($row = null)
    {
        if ($row === null) {
            return $this->other_columns;
        } else {
            $line = [];
            foreach ($this->other_columns as $key => $coltype_obj) {
                $field = $coltype_obj->getField();
                $value = isset($field) ? $this->getCellValue($row, $field) : null;

                $item = $coltype_obj; //Можно добавить clone
                $item->setValue($value);
                $item->setRow($row);

                $line[$key] = $item;
            }
            return $line;
        }
    }

    /**
     * Возвращает значение для отображение в ячейке
     *
     * @param mixed $row - ORM объект или ассоциативный массив для одной строки данных
     * @param string $field - поле, которое необходимо вывести
     * @return mixed
     */
    protected function getCellValue($row, $field)
    {
        if ($row instanceof AbstractObject && isset($row[$field])) {
            return $row['__' . $field]->textView();
        } else {
            return isset($row[$field]) ? $row[$field] : '';
        }
    }

    /**
     * Возвращает кнопки, находящиеся над деревом объектов
     *
     * @return array
     */
    function getHeadButtons()
    {
        return $this->head_buttons;
    }

    /**
     * Устанавливает списковые данные
     *
     * @param AbstractObject[] $data
     * @return void
     */
    function setData(array $data)
    {
        $this->data = $data;
    }

    function getData($with_root = true)
    {
        if ($with_root && !empty($this->options['rootItem'])) {
            return array_merge([$this->options['rootItem']], $this->data);
        }
        return $this->data;
    }

    /**
     * Устанавливает корневой элемент, если таковой существует
     *
     * @param array $element элемент
     * @return void
     */
    function setRootItem(array $element)
    {
        $this->options['rootItem'] = $element + ['is_root_element' => true];
    }

    /**
     * Возвращает HTML древовидного списка
     *
     * @param array $local_options - набор параметров
     * @return string
     * @throws \SmartyException
     */
    function getView($local_options = null)
    {
        $view = new ViewEngine();
        $view->assign([
            'category' => $this,
            'local_options' => $local_options
        ]);

        $template = 'system/admin/html_elements/category/category.tpl';

        return $view->fetch($template);
    }

    /**
     * Устанавливает поле, в котором содержится информация об активности строки(неактивная строка - бледная)
     *
     * @param string $field - имя поля
     * @return Element
     */
    function setDisabledField($field)
    {
        $this->options['disabledField'] = $field;
        return $this;
    }

    /**
     * Устанавливает значение, которое если содержится в поле disabledField, то означает, что строка - неактивная
     *
     * @param mixed $value
     * @return Element
     */
    function setDisabledValue($value)
    {
        $this->options['disabledValue'] = $value;
        return $this;
    }

    /**
     * Устанавливает поле, в котором содержится имя класса, который нужно добавить к HTML строки с данными
     *
     * @param string $field - имя поля
     * @return Element
     */
    function setClassField($field)
    {
        $this->options['classField'] = $field;
        return $this;
    }

    /**
     * Устанавливает поле, значение из которого необходимо передавать контроллеру при сортировке элементов
     *
     * @param string $field - имя поля
     * @return Element
     */
    function setSortIdField($field)
    {
        $this->options['sortIdField'] = $field;
        return $this;
    }

    /**
     * Устанавливает url для сортировки элементов
     *
     * @param string $url
     * @return Element
     */
    function setSortUrl($url)
    {
        $this->options['sortUrl'] = $url;
        return $this;
    }

    /**
     * Устанавливает поле, значение в котором система будет сравнивать с activeValue. Если значение совпадет, то строка с данными будет выделена, как текущая
     *
     * @param string $field - имя поля
     * @return Element
     */
    function setActiveField($field)
    {
        $this->options['activeField'] = $field;
        return $this;
    }

    /**
     * Устанавливает значение текущего выделенного элемента.
     *
     * @param string $field - имя поля
     * @return Element
     */
    function setActiveValue($field)
    {
        $this->options['activeValue'] = $field;
        return $this;
    }

    /**
     * Устанавливает, должна ли быть доступна сортировка элементов дерева
     *
     * @param bool $bool - если true, то сортировка будет присутствовать в дереве элементов
     * @return Element
     */
    function setSortable($bool)
    {
        $this->options['sortable'] = $bool;
        return $this;
    }

    /**
     * Устанавливает кнопки, которые должны выводиться при отображении дерева в одну строку
     *
     * @param array $buttons
     * @return Element
     * @deprecated В сокращенном виде данные кнопки исключены из отображения.
     */
    function setInlineButtons(array $buttons)
    {
        $this->options['inlineButtons'] = $buttons;
        return $this;
    }

    /**
     * Устанавливает текст, который будет отображаться в сокращенном виде,
     * если не выбран ни один элемент
     *
     * @param string $title - текст
     */
    function setUnselectedTitle($title)
    {
        $this->options['unselectedTitle'] = $title;
    }

    /**
     * Возвращает HTML цепочки до текущей папки
     *
     * @return string
     * @throws \SmartyException
     */
    public function getPathView()
    {
        $view = new ViewEngine();
        $title = $this->options['rootItem']['title'];
        foreach ($this->data as $item) {
            if ($item[$this->options['activeField']] == $this->options['activeValue']) {
                $title_field = $this->getMainColumn()->getField();
                $title = $item[$title_field];
            }
        }

        $view->assign([
            'active_category_title' => $title,
        ]);

        return $view->fetch('system/admin/html_elements/category/path_to_current.tpl');
    }

    /**
     * Устанавливает максимальный уровень допустимой вложенности
     * @param $number
     *
     * @return void
     */
    function setMaxLevels($number)
    {
        $this->options['maxLevels'] = $number;
    }
}


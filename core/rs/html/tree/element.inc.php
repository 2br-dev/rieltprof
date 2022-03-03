<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Tree;

use RS\Html\AbstractHtml;
use RS\Html\Table\Type as TableType;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Module\AbstractModel\TreeList\TreeListFakeNode;
use RS\Orm\AbstractObject;
use RS\View\Engine as ViewEngine;

class Element extends AbstractHtml
{
    const VIEWTYPE_BIGTREE = 'bigtree';
    const VIEWTYPE_INLINE = 'inline';
    const VALID_CALLBACKS = [
        'noCheckbox', // скрывает checkbox у элемента
        'noOtherColumns', // скрывает у элемента колонку инструментов
        'noDraggable', // запрещает перемещать элемент
        'noRedMarker', // скрывает красную полоску справа (видна при выделении элемента)
        'disabledCheckbox', // checkbox у элемента становится disabled
    ];

    protected $checked = [];
    protected $checkbox_name = 'chk[]';
    /** @var TableType\Actions */
    protected $tools;
    /** @var TableType\AbstractType */
    protected $main_column;
    /** @var TableType\AbstractType[] */
    protected $other_columns;
    protected $head_buttons;
    protected $path_to_first = [];
    /** @var AbstractTreeListIterator */
    protected $data;
    protected $callbacks = [];

    /**
     * Возвращает скрывать ли checkbox у элемента
     *
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return bool
     */
    public function isNoCheckbox($tree_item)
    {
        return $this->getCallbackResult('noCheckbox', $tree_item);
    }

    /**
     * Возвращает скрывать ли у элемента колонку инструментов
     *
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return bool
     */
    public function isNoOtherColumns($tree_item)
    {
        return $this->getCallbackResult('noOtherColumns', $tree_item);
    }

    /**
     * Возвращает запрещать ли перемещать элемент
     *
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return bool
     */
    public function isNoDraggable($tree_item)
    {
        return $this->getCallbackResult('noDraggable', $tree_item);
    }

    /**
     * Возвращает скрывать ли красную полоску справа (видна при выделении элемента)
     *
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return bool
     */
    public function isNoRedMarker($tree_item)
    {
        return $this->getCallbackResult('noRedMarker', $tree_item);
    }

    /**
     * Возвращает блокировать ли checkbox у элемента
     *
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return bool
     */
    public function isDisabledCheckbox($tree_item)
    {
        return $this->getCallbackResult('disabledCheckbox', $tree_item);
    }

    /**
     * Возвращает результат выполнения указанного callback-а
     * если указанный callback не установлен - возвращает значение свойства элемента с таким же именем
     *
     * @param string $callback_name - имя callback-а
     * @param array|object $tree_item - элемент для которого вызывается callback
     * @return mixed
     */
    public function getCallbackResult($callback_name, $tree_item) {
        if (isset($this->callbacks[$callback_name])) {
            return $this->callbacks[$callback_name]($tree_item);
        } else {
            return $tree_item[$callback_name] ?? null;
        }
    }

    /**
     * Устанавливает список callback-ов
     *
     * @param array $callback_list
     */
    public function setCallbacks(array $callback_list)
    {
        $this->callbacks = [];
        foreach ($callback_list as $callback_name => $callback_function) {
            if (in_array($callback_name, self::VALID_CALLBACKS)) {
                $this->callbacks[$callback_name] = $callback_function;
            }
        }
    }

    /**
     * Задает массив выделенных элементов. id Выделенных элементов должны быть в ключах массива
     *
     * @param array $fliped_keys
     * @return Element
     */
    public function setChecked(array $fliped_keys)
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
    public function isChecked($id)
    {
        return isset($this->checked[$id]);
    }

    /**
     * Возвращает имя переменной для checkbox'ов
     * @return string
     */
    public function getCheckboxName()
    {
        return $this->checkbox_name;
    }

    /**
     * Устанавливает имя переменной для checkbox'ов
     *
     * @param mixed $name
     * @return Element
     */
    public function setCheckboxName($name)
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
    public function setTools(TableType\Actions $tools)
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
    public function getTools($row = null)
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
    public function setMainColumn(TableType\AbstractType $main_column)
    {
        $this->main_column = $main_column;
        return $this;
    }

    /**
     * Устанавливает ячейки, которые выводятся справа от основного элемента
     *
     * @param TableType\AbstractType[] $columns
     * @return Element
     */
    public function setOtherColumns(array $columns)
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
    public function setHeadButtons(array $head_buttons)
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
    public function getMainColumn($row = null)
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
    public function getOtherColumns($row = null)
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
    public function getHeadButtons()
    {
        return $this->head_buttons;
    }

    public function setData(AbstractTreeListIterator $data)
    {
        $this->data = $data;
    }

    public function getData($with_root = true)
    {
        if ($with_root && !empty($this->options['rootItem'])) {
            return array_merge([new TreeListFakeNode($this->options['rootItem'])], $this->data->getItems());
        }
        return $this->data;
    }

    /**
     * Устанавливает путь от текущего элемента к корню дерева
     *
     * @param array $path_to_first - массив элементов начиная с поседнего листа заканчивая корнем дерева.
     * @return Element
     */
    public function setPathToFirst(array $path_to_first)
    {
        $this->path_to_first = $path_to_first;
        return $this;
    }

    /**
     * Возвращает путь к корневому элементу
     *
     * @return array
     */
    public function getPathToFirst()
    {
        if (!$this->path_to_first && $this->options['activeField']) {
            $this->path_to_first = $this->findActivePath($this->data);
        }

        if (isset($this->options['rootItem'])) {
            return array_merge([$this->options['rootItem']], $this->path_to_first);
        }

        return $this->path_to_first;
    }

    /**
     * Ищет путь к активному элементу, путем обхода дерева
     * Возвращает массив с элементами от корня до активного элемента
     *
     * @param AbstractTreeListIterator $data
     * @param array $current_path
     * @return array
     */
    private function findActivePath($data, $current_path = [])
    {
        foreach ($data as $item) {
            if ($item['fields'][$this->options['activeField']] == $this->options['activeValue']) {
                return array_merge($current_path, [$item->getObject()]);
            }
            if (count($item->getChilds())) {
                if ($result = $this->findActivePath($item->getChilds(), array_merge($current_path, [$item->getObject()]))) {
                    return $result;
                }
            }
        }
        return [];
    }

    /**
     * Устанавливает корневой элемент, если таковой существует
     *
     * @param array $element элемент
     * @return void
     */
    public function setRootItem(array $element)
    {
        $this->options['rootItem'] = $element + ['is_root_element' => true];
    }

    /**
     * Возвращает HTML древовидного списка
     *
     * @param array $local_options - дополнительные настройки отображения
     * @return string
     * @throws \SmartyException
     */
    public function getView($local_options = null)
    {
        $view = new ViewEngine();
        $view->assign([
            'tree' => $this,
            'local_options' => $local_options
        ]);

        $template = 'system/admin/html_elements/tree/tree.tpl';

        return $view->fetch($template);
    }

    /**
     * Устанавливает поле, в котором содержится информация об активности строки(неактивная строка - бледная)
     *
     * @param string $field - имя поля
     * @return Element
     */
    public function setDisabledField($field)
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
    public function setDisabledValue($value)
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
    public function setClassField($field)
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
    public function setSortIdField($field)
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
    public function setSortUrl($url)
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
    public function setActiveField($field)
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
    public function setActiveValue($field)
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
    public function setSortable($bool)
    {
        $this->options['sortable'] = $bool;
        return $this;
    }

    /**
     * Устанавливает кнопки, которые должны выводиться при отображении дерева в одну строку
     *
     * @deprecated В сокращенном виде данные кнопки исключены из отображения.
     * @param array $buttons
     * @return Element
     */
    public function setInlineButtons(array $buttons)
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
    public function setUnselectedTitle($title)
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
        $view->assign([
            'tree' => $this,
        ]);

        return $view->fetch('system/admin/html_elements/tree/path_to_current.tpl');
    }

    /**
     * Устанавливает максимальный уровень допустимой вложенности
     * @param $number
     *
     * @return void
     */
    public function setMaxLevels($number)
    {
        $this->options['maxLevels'] = $number;
    }
}


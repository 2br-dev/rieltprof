<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;

abstract class AbstractType
{
    const ESCAPE_TYPE_NONE = false; //Не экранировать
    const ESCAPE_TYPE_ENTITY = 'entity'; //Переводить спецсимволы в html entity
    const ESCAPE_TYPE_HTML = 'html'; //Зарезервировано.

    protected $listfunc; //callback возвращающей ассоциативный массив
    protected $listfunc_param = []; //Дополнительные параметры для $listfunc
    protected $tree_function;
    protected $tree_function_params = [];
    protected $list; //Сам список
    protected $parent_object;
    protected $value;
    protected $hidden = false;
    protected $requesttype;
    protected $checkers = [];
    protected $use_to_save = true; //Сохранять свойство в хранилище
    protected $read_only = false;
    protected $array_wrap_name = false;
    protected $form_template = '%system%/coreobject/type/form/string.tpl';
    protected $template;
    protected $me_template;
    protected $change_size_for_list = true;
    protected $always_modify = false;
    protected $errors = []; //Ошибки данного свойства
    protected $listen_post = true; //Принимать переменные из пост при вызове метода save()
    protected $hint = ''; //Подсказка поля
    protected $checkbox_param = [];
    protected $checkbox_list = false; // Отображать список в виде чекбокосв
    protected $radio_list = false;
    protected $radio_list_inline = false;
    protected $vis_form = true; //Отображать в формах
    protected $me_visible;
    protected $view_attr = [];
    protected $php_type = ''; //Mixed
    protected $escape_type = self::ESCAPE_TYPE_ENTITY; //Тип экранирования значения, при получении из GET, POST
    protected $runtime = false; //Это поле не связано с базой
    protected $sql_notation = '';
    protected $autoincrement = false; //Поле автоинкрементное
    protected $allowempty = true;
    protected $primary_key = false; //Это первичный ключ
    protected $default = null;
    protected $is_default_func;
    protected $unique = false;
    protected $index = false;
    protected $has_len = true; //Имеется ли длина в обозначении SQL типа
    protected $max_len; //Максимальная длинна
    protected $decimal;
    protected $form_name;
    protected $trim_string = false;

    public $name; //Название поля
    public $description; //Описание поля
    public $formtype = "input"; //Имя шаблона формы

    /**
     * Конструктор свойства
     *
     * @param array $options - массив для быстрой установки параметров
     */
    function __construct(array $options = null)
    {
        $this->processOptions($options);
    }

    /**
     * Вызывает методы set.... или add... для ключей массива options
     *
     * @param array $options
     * @return static
     */
    function processOptions($options)
    {
        if ($options !== null) {
            foreach ($options as $key => $value) {
                $key = trim($key);
                if (method_exists($this, $method = 'set' . $key) || method_exists($this, $method = 'add' . $key)) {
                    if (!is_array($value)) $value = [$value];
                    call_user_func_array([$this, $method], $value);
                } else {
                    $this->$key = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Вызывается для каждого свойства перед сохранением
     *
     * @return void
     */
    public function beforesave()
    {
    }

    /**
     * Возвращает параметры для checkbox'а
     *
     * @param string $key
     * @return mixed
     */
    public function getCheckboxParam($key = null)
    {
        return ($key === null) ? $this->checkbox_param : $this->checkbox_param[$key];
    }

    /**
     * Устанавливает значение свойству
     *
     * @param mixed $value
     * @return static
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Устанавливает, принимать ли данную переменную из POST во время вызова метода Save у ORM-объекта
     *
     * @param mixed $boolean
     * @return static
     */
    public function setListenPost($boolean)
    {
        $this->listen_post = $boolean;
        return $this;
    }

    /**
     * Возвращает true, если данное свойство можно заполнять из POST
     *
     * @return bool
     */
    public function isListenPost()
    {
        return $this->listen_post;
    }

    /**
     * Возвращает значение свойства или значение по-умолчанию
     *
     * @return mixed
     */
    public function get()
    {
        return $this->value !== null ? $this->value : $this->getDefault();
    }

    /**
     * Возвращает краткую подсказку для формы свойства
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Устанавливат краткую подсказку для свойства
     *
     * @param string $text
     * @return static
     */
    public function setHint($text)
    {
        $this->hint = $text;
        return $this;
    }

    /**
     * Устанавливает тип экранирования значения, получаемого из GET, POST, REQUEST
     *
     * @param mixed $escape_type - может принимать значение - false, 'entity', 'html'. См константы AbstractType::ESCAPE_TYPE_...
     * @return static
     */
    public function setEscapeType($escape_type)
    {
        $this->escape_type = $escape_type;
        return $this;
    }

    /**
     * Возвращает тип экранирования значения, получаемого из GET, POST, REQUEST
     *
     * @return mixed
     */
    public function getEscapeType()
    {
        return $this->escape_type;
    }

    /**
     * Устанавливет знаение в null
     *
     * @return void
     */
    public function unsetvalue()
    {
        unset($this->value);
    }

    /**
     * Проверяет, соответствует ли $value заявленному типу
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * Устанавливает обработчик входящих данных
     * Можно установить несколько обработчиков
     *
     * @param callback $callmethod - функция провери значения данного свойства
     * @param string $errortxt - текст ошибки
     * @param mixed $callback_param - дополнительный параметр для callback
     * .....
     * @return static
     */
    public function setChecker($callmethod, $errortxt = '')
    {
        $this->checkers[] = [
            'callmethod' => $callmethod,
            'errortext' => $errortxt,
            'param' => array_slice(func_get_args(), 1),
        ];
        return $this;
    }

    /**
     * Удаляет все checker'ы
     *
     * @return void
     */
    public function removeAllCheckers()
    {
        $this->checkers = [];
    }

    /**
     * Возвращает массив с checker'ами
     *
     * @return array
     */
    public function getCheckers()
    {
        return $this->checkers;
    }

    /**
     * Устанавливает, отображать ли данное свойство в форме
     *
     * @param boolean Отображение в форме
     * @param string $switch
     * @return static
     */
    public function setVisible($bool, $switch = null)
    {
        $property = ($switch !== null) ? $switch . 'Visible' : 'vis_form';
        $this->$property = $bool;
        return $this;
    }

    /**
     * Возвращает true, если свойство должо быть видимым в форме
     *
     * @param string $switch - префикс свойства видимости, которое отвечает за отображение свойства в контексте
     * @param boolean $use_default_visible - Если true, то по умолчанию возвращается значение общей видимости (Visible)
     * @return bool
     */
    public function isVisible($switch = null, $use_default_visible = true)
    {

        if ($switch !== null) {
            $property = $switch . 'Visible';
            if (isset($this->$property)) return $this->$property;
        }
        return $use_default_visible ? $this->vis_form : false;
    }

    /**
     * Возвращает true, если поле видимо в форме мультиредактирования
     *
     * @param string $switch - префикс свойства видимости, которое отвечает за отображение свойства в контексте
     * @param boolean $use_default_visible - Если true, то по умолчанию возвращается значение общей видимости (Visible)
     * @return bool
     */
    public function isMeVisible($switch = null, $use_default_visible = true)
    {
        if ($switch !== null) {
            $property = $switch . 'MeVisible';
            if (isset($this->$property)) return $this->$property;
        }

        if ($this->me_visible === null) {
            return $use_default_visible ? $this->isVisible() : false;
        }
        return $this->me_visible;
    }

    /**
     * Устанавливает, видимо ли поле в форме мультиредактирования
     *
     * @param mixed $bool
     * @return static
     */
    public function setMeVisible($bool)
    {
        $this->me_visible = $bool;
        return $this;
    }

    /**
     * Возвращает итератор для отображения формы в виде элемента SELECT
     *
     * @return AbstractTreeListIterator
     */
    public function getTreeList()
    {
        return call_user_func_array($this->tree_function, $this->tree_function_params);
    }

    /**
     * Устанавливает callback, который должен вернуть итератор для отображения формы в виде элемента SELECT
     *
     * @param callback $tree_function - устанавливаемый callback
     * @param array $params - аргументы callback-функции
     * @return static
     */
    public function setTree($tree_function, ...$params)
    {
        $this->tree_function = $tree_function;
        $this->tree_function_params = $params;
        return $this;
    }

    /**
     * Возвращает функцию назначенную у ORM объекта
     *
     * @return mixed
     */
    public function getListFunc()
    {
        return $this->listfunc;
    }

    /**
     * Устанавливает callback, который должен вернуть ассоциативный массив для отображения формы в виде элемента SELECT
     *
     * @param callback $listfunc - устанавливаемый callback
     * @param array $params - аргументы callback-функции
     * @return static
     */
    public function setList($listfunc, ...$params)
    {
        $this->listfunc = $listfunc;
        $this->listfunc_param = $params;
        return $this;
    }

    /**
     * Устанавливает ассоциативный массив, элементы которого должны использоваться для отображения формы в виде элемента SELECT
     *
     * @param array $list
     * @return static
     */
    public function setListFromArray(array $list)
    {
        $this->list = $list;
        if (!isset($this->view_attr['multiple']) && $this->change_size_for_list) {
            $this->setAttr(['size' => 1]);
        }
        return $this;
    }

    /**
     * Устанавливает, изменять ли атрибут size=1 для списков
     *
     * @param mixed $bool - Если true - то size будет устанавливаться в 1
     * @return static
     */
    public function setChangeSizeForList($bool)
    {
        $this->change_size_for_list = $bool;
        return $this;
    }

    /**
     * Возвращает список возможных значений, установленных фцнкуиями: setList или setListFromArray
     *
     * @return array | null
     */
    public function getList()
    {
        if (isset($this->listfunc) && !isset($this->list)) {
            $tmp = $this->value; //Сохраняем значение, т.к. внутри вызова может измениться значение текущего объекта
            $this->setListFromArray(call_user_func_array($this->listfunc, $this->listfunc_param));
            $this->value = $tmp; //Восстанавливаем значение
        }

        return $this->list;
    }

    /**
     * Отображать свойство в форме в виде checkbox
     *
     * @param mixed $onValue - значение для включенного checkbox
     * @param mixed $offValue - значение для ВЫключеннго checkbox
     * @return static
     */
    public function setCheckboxView($onValue, $offValue)
    {
        $this->checkbox_param = ['on' => $onValue, 'off' => $offValue];
        $this->form_template = '%system%/coreobject/type/form/checkbox.tpl';
        return $this;
    }

    public function setCheckboxListView($bool)
    {
        $this->checkbox_list = $bool;
        $this->form_template = '%system%/coreobject/type/form/checkboxlist.tpl';
        return $this;
    }

    public function setRadioListView($bool, $inline = false)
    {
        $this->radio_list = $bool;
        $this->radio_list_inline = $inline;
        $this->form_template = '%system%/coreobject/type/form/radiolist.tpl';
        return $this;
    }

    public function isRadioListInline()
    {
        return $this->radio_list_inline;
    }

    /**
     * Вызывается у каждого свойства перед сохранением ORM объекта.
     *
     * @return void
     */
    public function selfSave()
    {
    }

    /**
     * Если у вас есть разница между видом данных загруженных во время загрузки объекта и видом данных полученных с POST'a,
     * то в этой функции нужно приводить данные, полученные с поста к виду данных загружаемых из БД.
     *
     * @return void
     */
    public function normalizePost()
    {
    }

    /**
     * Устанавливает атрибуты для формы, которые нужно вставить в html в виде строки
     *
     * @param array $view_attr - ассоциативный массив АТРИБУТ=>ЗНАЧНИЕ
     * @return static
     */
    function setAttr(array $view_attr)
    {
        $this->view_attr = array_replace_recursive($this->view_attr, $view_attr);
        return $this;
    }

    /**
     * Возвращает атрибуты для элемента формы
     *
     * @return string
     */
    function getAttr()
    {
        $ready_attr = '';
        $attr = $this->view_attr;

        if ($this->hasErrors()) {
            @$attr['class'] .= ' has-error';
        }

        foreach ($attr as $key => $val) {
            $ready_attr .= " {$key}=\"{$val}\"";
        }
        return $ready_attr;
    }

    /**
     * Возвращает аттрибуты в виде массива
     * @return array
     */
    function getAttrArray()
    {
        return $this->view_attr;
    }

    /**
     * Возвращает значение назначеного аттрибута по ключу или false
     *
     * @param string $key - ключ массива с аттрибутами
     *
     * @return mixed|false
     */
    function getAttrByKey($key)
    {
        return isset($this->view_attr[$key]) ? $this->view_attr[$key] : false;
    }

    /**
     * Возвращает true если существует ключ аттрибута
     *
     * @param string $key - ключ массива с аттрибутами
     *
     * @return boolean
     */
    function isHaveAttrKey($key)
    {
        return isset($this->view_attr[$key]);
    }

    /**
     * Возвращает значение свойства в текстовом виде
     *
     * @return string
     */
    function textView()
    {
        if (isset($this->listfunc)) {
            $tmp = $this->value; //Сохраняем значение, т.к. внутри вызова может измениться значение текущего объекта
            $this->setListFromArray(call_user_func_array($this->listfunc, $this->listfunc_param));
            $this->value = $tmp; //Восстанавливаем значение
        }
        if (isset($this->list)
            && in_array(gettype($this->value), ['integer', 'string', 'double', 'float'])
            && isset($this->list[$this->value])) {
            return $this->list[$this->value];
        }

        return $this->value;
    }

    /**
     * Возвращает HTML код формы свойства
     *
     * @param array|null $view_options - параметры отображения формы. если null, то отображать все
     *     Возможные элементы массива:
     *         'form' - отображать форму,
     *         'error' - отображать блок с ошибками,
     *         'hint' - оторажать ярлык с подсказкой,
     *
     * @param object | null $orm_object - orm объект, которому принадлежит поле
     *
     * @return string
     * @throws \SmartyException
     */
    function formView($view_options = null, $orm_object = null)
    {
        $sm = new \RS\View\Engine();
        $sm->assign([
            'field' => $this,
            'view_options' => $view_options !== null ? array_combine($view_options, $view_options) : null,
            'orm_object' => $orm_object
        ]);

        return $sm->fetch($this->getRenderTemplate());
    }

    /**
     * Устанавливает имя свойства
     *
     * @param string $name
     * @return static
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Возвращает имя свойства
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Оборачивает имя формы в массив с заданным именем
     *
     * @param string $array_wrap_name имя массива-обертки
     * @return static
     */
    function setArrayWrap($array_wrap_name)
    {
        $this->array_wrap_name = $array_wrap_name;
        return $this;
    }

    /**
     * Устанавливает имя формы свойства
     *
     * @param string $form_name - имя для формы
     * @return static
     */
    function setFormName($form_name)
    {
        $this->form_name = $form_name;
        return $this;
    }

    /**
     * Возвращает имя формы свойства
     *
     * @return string
     */
    function getFormName()
    {
        if ($this->form_name) {
            return $this->form_name;
        }
        return $this->array_wrap_name ? $this->array_wrap_name . '[' . $this->name . ']' : $this->name;
    }

    /**
     * Устанавливает максимальную длину значения свойства
     *
     * @param integer $length
     * @return static
     */
    function setMaxLength($length)
    {
        $this->max_len = $length;
        return $this;
    }

    /**
     * Возвращает максимальную длину значения свойства
     *
     * @return integer
     */
    function getMaxLength()
    {
        return $this->max_len;
    }

    /**
     * Возвращает true, если это свойство нужно читать всегда модифицированным
     *
     * @return bool
     */
    function isAlwaysModify()
    {
        return $this->always_modify;
    }

    /**
     * Устанавливает, считать ли данное свойство всегда модифицированным
     *
     * @param mixed $bool
     * @return static
     */
    function setAlwaysModify($bool)
    {
        $this->always_modify = $bool;
        return $this;
    }

    /**
     * Устанавливает, считать ли данное свойство доступным только для чтения
     *
     * @param bool $readonly
     * @return static
     */
    function setReadOnly($readonly = true)
    {
        if ($readonly) {
            $this->view_attr = array_merge($this->view_attr, ['disabled' => 'disabled']);
        } else {
            unset($this->view_attr['disabled']);
        }
        return $this;
    }

    /**
     * Устанавливает список ошибок для данного свойства
     *
     * @param array $errors
     * @return static
     */
    function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Возвращает массив с ошибками данного свойства
     *
     * @return array
     */
    function getErrors()
    {
        return $this->errors;
    }

    /**
     * Возвращает true, если есть ошибки у данного свойства
     *
     * @return bool
     */
    function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Возвращает описание данного свйства
     *
     * @return string
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Устанавливает описание для свойства
     *
     * @param string $description
     * @return static
     */
    function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Устанавливает, сохранять ли данное свойство. Если нет, то будет всегда испоьзоваться значение по-умолчанию
     *
     * @param bool $bool
     * @return static
     */
    function setUseToSave($bool)
    {
        $this->use_to_save = $bool;
        return $this;
    }

    /**
     * Возвращает true, если свойство можно сохранять, иначе - false
     *
     * @return bool
     */
    function isUseToSave()
    {
        return $this->use_to_save;
    }

    /**
     * Возвращает true, если данное поле не связано с хранилищем
     *
     * @return bool
     */
    function isRuntime()
    {
        return $this->runtime;
    }

    /**
     * Устанавливает связано ли данное поле с базой данных
     *
     * @param bool $bool - если true, то не связано(записи БД не будет)
     * @return static
     */
    function setRuntime($bool)
    {
        $this->runtime = $bool;
        return $this;
    }

    /**
     * Возвращает true, если поле является автоинкрементным
     *
     * @return bool
     */
    function isAutoincrement()
    {
        return $this->autoincrement;
    }

    /**
     * Устанавливает, является ли поле автоинкрементным
     *
     * @param bool $bool
     * @return static
     */
    function setAutoincrement($bool)
    {
        $this->autoincrement = $bool;
        return $this;
    }

    /**
     * Возвращает true, если поле имеет длину в SQL обозначении
     *
     * @return bool
     */
    function hasLength()
    {
        return $this->has_len;
    }

    /**
     * Устанавливает, может ли данное поле принимать значение null
     *
     * @param bool $bool
     * @return static
     */
    function setAllowEmpty($bool)
    {
        $this->allowempty = $bool;
        return $this;
    }

    /**
     * Возвращает true, если поле позволяет иметь значение NULL
     *
     * @return bool
     */
    function isAllowEmpty()
    {
        return $this->allowempty;
    }

    /**
     * Возвращает обозначение данного типа в SQL
     *
     * @return string
     */
    function getSQLNotation()
    {
        return $this->sql_notation;
    }

    /**
     * Возвращает строковое значение параметра, которое подставляется после SQL - типа
     * Для большинства типов это его длина, например INT(11), VARCHAR(255),
     * но для некоторых типов это могут быть другие значения, например: ENUM('Y', 'N') или DECIMAL(10,2)
     *
     * @return string
     */
    function getSQLTypeParameter()
    {
        if ($this->hasLength()) {
            if ($this->getDecimal() !== null) {
                return "({$this->getMaxLength()},{$this->getDecimal()})";
            } else {
                return '(' . $this->getMaxLength() . ')';
            }
        }
        return '';
    }

    /**
     * Устанавливает количество знаков дробной части
     *
     * @param integer $decimal
     * @return static
     */
    function setDecimal($decimal)
    {
        $this->decimal = $decimal;
        return $this;
    }

    /**
     * Возвращает количество знаков дробной части
     * @return integer | null
     */
    function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * Устанавливает, что данное поле является первичным ключем
     *
     * @param bool $bool
     * @return static
     */
    function setPrimaryKey($bool)
    {
        $this->primary_key = $bool;
        return $this;
    }

    /**
     * Возвращает, является ли данное поле первичным ключем
     *
     * @return bool
     */
    function isPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * Устанавливает, содержит ли данное поле уникальный индекс
     *
     * @param bool $bool
     * @return static
     */
    function setUnique($bool)
    {
        $this->unique = $bool;
        return $this;
    }

    /**
     * Возвращает true, если данное поле содержит уникальный индекс
     * @return bool
     */
    function isUnique()
    {
        return $this->unique;
    }

    /**
     * Устанавливает, содержит ли данное поле индекс
     *
     * @param bool $bool
     * @return static
     */
    function setIndex($bool)
    {
        $this->index = $bool;
        return $this;
    }

    /**
     * Возвращает true, если данное поле содержит индекс
     * @return bool
     */
    function isIndex()
    {
        return $this->index;
    }

    /**
     * Устанавливает значение по-умолчанию в поле базы данных
     *
     * @param mixed $default - false означает, что значение по-умолчанию не задано
     * @param $is_func - если true, значит $default содержит функцию, иначе $default - это значение (влияет на обрамление ковычками во время синхронизации)
     * @return static
     */
    function setDefault($default, $is_func = false)
    {
        $this->default = $default;
        $this->is_default_func = $is_func;
        return $this;
    }

    /**
     * Возвращает значение поля в базе по-умолчанию
     *
     * @param bool $db_format - привести результат к хранимому в БД виду
     * @return mixed
     */
    public function getDefault($db_format = false)
    {
        $result = $this->default;
        if ($db_format && !in_array(gettype($result), ['NULL', 'bool', 'string'])) {
            $result = (string)$result;
        }
        return $result;
    }

    /**
     * Взвращает true, если значением поля по-умолчанию является функция
     *
     * @return bool
     */
    function isDefaultFunc()
    {
        return $this->is_default_func;
    }

    /**
     * Возвращает тип данного свойства в PHP, основываясь на gettype()
     * @return string
     */
    function getPhpType()
    {
        return $this->php_type;
    }

    /**
     * Устанавливает тип значения в PHP. См. идентификаторы скалярных типов в PHP
     *
     * @param $type
     * @return static
     */
    function setPhpType($type)
    {
        $this->php_type = $type;
        return $this;
    }

    /**
     * Устанавливает шаблон, который будет использован для отображения свойства
     *
     * @param string $template
     * @return static
     */
    function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Устанавливает шаблон, кторый будет использован для отображения свойства в режиме мультиредактирования
     *
     * @param string $template
     * @return static
     */
    function setMeTemplate($template)
    {
        $this->me_template = $template;
        return $this;
    }

    /**
     * Возвращает шаблон по-умолчанию для данного поля
     *
     * @return string
     */
    function getOriginalTemplate()
    {
        return $this->form_template;
    }

    /**
     * Возвращает шаблон, который будет использован для отображения свойства
     *
     * @param bool $multiedit - вернуть шаблон для мультиредактирования
     * @return string
     */
    public function getRenderTemplate($multiedit = false)
    {
        //Меняем шаблон, если это поле имеет список
        if (isset($this->list) || isset($this->listfunc)) {
            if (!$this->checkbox_list && !$this->radio_list) {
                $this->form_template = '%system%/coreobject/type/form/listbox.tpl';
            }
        }
        if (isset($this->tree_function)) {
            $this->form_template = '%system%/coreobject/type/form/treelistbox.tpl';
        }

        $form = ($this->template) ? $this->template : $this->getOriginalTemplate();
        if ($multiedit) {
            return ($this->me_template) ? $this->me_template : $form;
        }
        return $form;
    }

    /**
     * Возвращает название свойства для отображения пользователям
     *
     * @return string
     */
    function getTitle()
    {
        return $this->getDescription() === null ? $this->getName() : $this->getDescription();
    }

    /**
     * Отображать даное свойство в виде input[type=hidden]
     *
     * @param bool $bool - Если true, то поле будет скрытым, иначе видимым.
     * @return static
     */
    function setHidden($bool)
    {
        $this->hidden = $bool;
        if ($bool) {
            $this->setTemplate('%system%/coreobject/type/form/hidden.tpl');
        }
        return $this;
    }

    /**
     * Возвращает true, если поле необходимо отображать в виде input[type="hidden"]
     *
     * @return bool
     */
    function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Возвращает экранированное значение value согласно типу экранирования данного класса
     *
     * @param mixed $value
     * @return mixed
     */
    function escape($value)
    {
        switch ($this->escape_type) {
            case self::ESCAPE_TYPE_ENTITY:
                {
                    if (is_array($value)) {
                        $value = \RS\Helper\Tools::escapeArrayRecursive($value);
                    } else {
                        $value = \RS\Helper\Tools::toEntityString($value);
                    }
                    break;
                }
        }
        return $value;
    }

    /**
     * Проверяет значение $value и подставляет значение по-умолчанию, если таковое действие требуется
     *
     * @param mixed $value
     * @return mixed $value
     */
    function checkDefaultRequestValue($value)
    {
        return $value;
    }

    /**
     * Устанавливает тип, к которому должна приводиться переменная при получении её через POST
     *
     * @param string $var_type - Тип переменной string, float, integer, array, boolean, пустая строка означает - MIXED
     * @return static
     */
    function setRequestType($var_type)
    {
        $this->requesttype = $var_type;
        return $this;
    }

    /**
     * Заполняет, экранирует и возвращает значение поля из сведений переданных браузером
     *
     * @param array $src - массив исходных данных (обычно это _POST + _FILES)
     * @return mixed
     */
    function getFromRequest(array $src)
    {
        $value = isset($src[$this->getName()]) ? $src[$this->getName()] : null;

        if (count($this->getCheckboxParam()) && !isset($value)) $value = $this->getCheckboxParam('off');
        $value = $this->checkDefaultRequestValue($value);

        if ($value !== null) {
            $value = $this->escape($value);
            $type = isset($this->requesttype) ? $this->requesttype : $this->getPhpType();
            if ($type == TYPE_FLOAT) {
                $value = str_replace(' ', '', $value);
            }
            if ($type != TYPE_MIXED) {
                @settype($value, $type);
            }
            if ($type == TYPE_STRING && $this->getTrimString()) {
                $value = trim($value);
            }
            $this->set($value);
            $this->normalizePost();
        } else {
            $this->set($value);
        }
        return $this->get();
    }

    /**
     * Выполняет анонимную функцию, назначенную свойству текущего объекта.
     * Метод необходим для реализации выполнения анонимных функций в шаблонах Smarty.
     *
     * @param string $property_name - назнание свойства
     * @param array $params - параметры, которые будут переданы в анонимную функцию
     * @return mixed
     */
    function callPropertyFunction($property_name, ...$params)
    {
        if (isset($this->$property_name) && $this->$property_name instanceof \Closure) {
            return call_user_func_array($this->$property_name, [$this, $params]);
        }

        return null;
    }

    /**
     * Устанавливает свойство TrimString
     *
     * @param $bool
     * @return static
     */
    function setTrimString($bool)
    {
        $this->trim_string = $bool;
        return $this;
    }

    /**
     * Возвращает свойство TrimString
     *
     * @return bool
     */
    function getTrimString()
    {
        return $this->trim_string;
    }

    /**
     * Возвращает false, если данное поле не кодируется в базе данных
     *
     * @return bool
     */
    function isHtmlEncodedField()
    {
        return false;
    }
}

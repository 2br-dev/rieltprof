<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms;

use \Designer\Model\DesignAtoms\CSSProperty;
use \Designer\Model\DesignAtoms\CSSPresets;
use \Designer\Model\DesignAtoms;
use Main\Model\ModuleLicenseApi;
use Shop\Controller\Admin\ReturnsCtrl;

/**
 * Class AbstractAtom - абстракный класс для одного отдельного компонента компонента
 */
abstract class AbstractAtom{
    protected $default_attr_preset_name = 'Элемент';

    protected $title = ""; //Название компонента

    protected $attrs = []; //Аттрибуты компонента для редактирования
    protected $attr_groups = []; //Группы аттрибутов
    /**
     * @var CSSPresets\AbstractCssPreset[] $css_presets
     */
    protected $css_presets = []; //Массив пресетов с группированными CSS свойств
    /**
     * @var CSSProperty\AbstractCssProperty[] $css
     */
    protected $css = []; //CSS cвойства компонента для редактирования
    protected $css_title = null; //Подзаголовок элемента для вкладки со стилями
    protected $additional_class = ""; //Дополнительный класс для
    protected $html = "";      //Содержимое внутри
    protected $html_type = "";
    protected $type = "atom";
    protected $name = "Element #{n}"; //Имя данного атома на странице
    protected $html_visible = false; //Видимость содержимого HTML
    protected $tag  = "";      //Тег с помощью которого будет формироваться содержимое
    protected $tags = [];      //Тег с помощью которого будет формироваться содержимое
    public static $css_for_wrapper = []; //CSS стили для оборачивающего блока
    public static $virtual_attrs   = []; //Массив виртуальных аттрибутов
    public static $reset_attrs     = []; //Массив аттрибутов, которые можно сбросить, если есть то, будет показана кнопка сбросить
    public static $public_js       = []; //Массив дополнительных JS, которые нужно подключить в публичной части
    public static $public_css      = []; //Массив дополнительных CSS, которые нужно подключить в публичной части

    //Данные картинки
    protected $image_relative_path = "/modules/designer/view/img/designatoms/"; //Относительный путь к картинке
    protected $image = ""; //Картинка компонента
    protected $is_server_image = false; //Картинка хранится на сервере ReadyScript?
    protected static $paid = 0; //Платный это атом, или нет 0 - нет, 1 - да
    protected $hidden = [ //массив размеров экрана, когда надо скрывать
        'xs' => false,
        'sm' => false,
        'md' => false,
        'lg' => false,
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        $this->name = $this->title." #{n}";
        $margin_top = new CSSProperty\Size('margin-top', t('Внешний верхний отступ'), '0px');
        $margin_top->setVisible(false);
        $textarea = new \Designer\Model\DesignAtoms\Attrs\TextAreaStyle('style', t('Стиль'));
        $textarea->setVisible(false);
        $this->setClass("")
            ->setStyle($textarea)
            ->addCSS($margin_top);
    }

    /**
     * Устанавливает текущий тег для отображения
     *
     * @param string $tagName - нужный тег в формате "SPAN", "A", "TD"
     *
     * @return $this
     */
    function setTag($tagName)
    {
        $this->tag = $tagName;
        return $this;
    }

    /**
     * Возращает тег для отображения
     *
     * @return string
     */
    function getTag()
    {
        return $this->tag;
    }

    /**
     * Устанавливает тип сущности
     *
     * @param string $type - нужный тип сущности. Например subatom
     *
     * @return $this
     */
    function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Возращает тип сущности
     *
     * @return string
     */
    function getType()
    {
        return $this->type;
    }

    /**
     * Возращает возможные теги для отображения
     *
     * @return array
     */
    function getTags()
    {
        return $this->tags;
    }

    /**
     * Возвращает название компонета
     *
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * Устанавливает название компонета
     *
     * @param string $title - название заголовка
     *
     * @return $this
     */
    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Возвращает название CSS заголовка для вкладки стилей
     *
     * @return string
     */
    function getCSSTitle()
    {
        return $this->css_title;
    }

    /**
     * Устанавливает название CSS заголовка для вкладки стилей
     *
     * @param string $title - название заголовка
     *
     * @return $this
     */
    function setCSSTitle($title)
    {
        $this->css_title = $title;
        return $this;
    }

    /**
     * Возвращает имя компонета на странице
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливает имя компонета на странице
     *
     * @param string $name - имя компонента
     * @return string
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Возвращает путь к картинке
     *
     * @return string
     */
    function getImage()
    {
        if (!$this->is_server_image){ //Если хранится локально
            return \Setup::$FOLDER.$this->image_relative_path.$this->image;
        }
        //TODO: Дописать абсолютный путь на сервере RS
        return $this->image;
    }

    /**
     * Добавляет содержимое HTML внутрь компонента
     *
     * @param string $body - текст или HTML
     *
     * @return $this
     */
    function setHtml($body = "")
    {
        $this->html = $body;
        return $this;
    }

    /**
     * Возвращает текущий HTML внутри
     *
     * @return string
     */
    function getHtml()
    {
        return $this->html;
    }

    /**
     * Возвращает текущий тип HTML внутри
     *
     * @return string
     */
    function getHtmlType()
    {
        return $this->html_type;
    }

    /**
     * Устанавливает класс к элементу
     *
     * @param string $class - класс или классы для установки
     *
     * @return $this
     */
    function setClass($class)
    {
        $class = new Attrs\TextArea('class', t('Класс'), $class);
        $class->setVisible(false);
        $this->setAttr($class);
        return $this;
    }

    /**
     * Добавление необходимого класса
     *
     * @param string $class - класс или классы для установки
     *
     * @return $this
     */
    function appendClass($class)
    {
        $old_classes = $this->getAttr('class');
        return !empty($old_classes) ? $this->setClass($old_classes." ".$class) : $this->setClass($class);
    }

    /**
     * Добавляет стили к элементу
     *
     * @param Attrs\AbstractAttr $style - стили
     * @return $this
     */
    function setStyle($style)
    {
        $this->setAttr($style);
        return $this;
    }

    /**
     * Добавляет aтрибут атома
     *
     * @param Attrs\AbstractAttr|Attrs\AbstractAttr[] $attribute - аттрибут для установки
     * @param string $group - название группы группы, которой принадлежат оттрибудты
     *
     * @return $this
     */
    function setAttr($attribute, $group = null)
    {
        if (!$group){
            $group = $this->default_attr_preset_name;
        }
        if (is_array($attribute)){
            foreach ($attribute as $attr){
                $this->attrs[$attr->getName()] = $attr->getInfo();
                $this->attr_groups[$group][] = $attr->getName();
            }
        }else{
            $this->attrs[$attribute->getName()] = $attribute->getInfo();
            $this->attr_groups[$group][] = $attribute->getName();
        }

        return $this;
    }

    /**
     * Возврашает аттрибут по ключу или null если аттрибута и не существовало. Если ключ не указан, то возвращает все атррибуты
     *
     * @param string $key - ключ аттрибута
     * @return mixed
     */
    function getAttr($key = null)
    {
        if ($key === null){
            return $this->attrs;
        }
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

     /**
     * Возврашает группу если укаан ключ или массив груп
     *
     * @param string $key - ключ аттрибута
     * @return mixed
     */
    function getAttrGroups($key = null)
    {
        if ($key === null){
            return $this->attr_groups;
        }
        return isset($this->attr_groups[$key]) ? $this->attr_groups[$key] : null;
    }

    /**
     * Устанавливает для группы аттрибутов, событие, которое нужно вызвать
     *
     * @param array $attr_keys - массив ключей аттрибутов
     * @param string $event - событие которое будет назначаться
     * @return $this
     */
    function setAttrGroupDebugEvent($attr_keys, $event)
    {
        foreach ($attr_keys as $key){
            $this->attrs[$key]['debug_event'] = $event;
        }
        return $this;
    }

    /**
     * Добавляет к атому CSS для управления шириной и горизонтальным расположение
     *
     * @return $this
     */
    function addMaxWidthAndAlignSelfCSS()
    {
        $max_width = new CSSProperty\Size('max-width', t('Ширина'), '');
        $max_width->setVisible(false);
        $this->addCSS([
            new CSSProperty\FlexAlignItems('align-self', t('Позиция атома'), 'center'),
            $max_width
        ]);
        return $this;
    }

    /**
     * Добавляет к атому CSS для управления шириной и горизонтальным расположение вставляя его в конкретный preset
     *
     * @return $this
     */
    function addMaxWidthAndAlignSelfCSSToPreset($preset)
    {
        $max_width = new CSSProperty\Size('max-width', t('Ширина'), '');
        $max_width->setVisible(false);
        $preset->addCSS([
            $max_width,
            new CSSProperty\FlexAlignItems('align-self', t('Позиция атома'), 'center'),
        ]);

        return $this;
    }

    /**
     * Добавляет пресет с CSS свойствами, добавляющими правки CSS, но объединенных в группы
     *
     * @param $preset_or_presets - CSS пресет или массив пресетов
     * @return $this
     */
    function addCSSPreset($preset_or_presets)
    {
        if (is_array($preset_or_presets)){
            foreach ($preset_or_presets as $preset){
                $this->css_presets[] = $preset;
            }
        }else{
            $this->css_presets[] = $preset_or_presets;
        }
        return $this;
    }

    /**
     * Добавляет CSS свойство атома
     *
     * @param CSSProperty\AbstractCssProperty|CSSProperty\AbstractCssProperty[] $css_property - объект CSS характеристики или массив характеристик для установки
     * @return $this
     */
    function addCSS($css_property)
    {
        if (is_array($css_property)){
            foreach ($css_property as $css){
                $this->css[$css->getPropertyName()] = $css;
            }
        }else{
            $this->css[$css_property->getPropertyName()] = $css_property;
        }
        return $this;
    }

    /**
     * Возвращает CSS свойство по ключу. Если ключ не указан, то возвращает все css свойства
     *
     * @param string $title - название CSS свойства
     *
     * @return CSSProperty\AbstractCssProperty|CSSProperty\AbstractCssProperty[]|null
     */
    function getCSS($title = null)
    {
        if (!$title){
            return $this->css;
        }
        return isset($this->css[$title]) ? $this->css[$title] : null;
    }

    /**
     * Возвращает массив данных параметров для настроек заднего фона
     *
     * @param string $background_color - цвет заднего фона в восьмиричном HEX формате (#00000000)
     *
     * @return DesignAtoms\CSSPresets\AbstractCssPreset
     */
    public static function getBackgroundParamsSettings($background_color = '')
    {
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => $background_color
        ]);
        return $background;
    }

    /**
     * Возвращает массив данных параметров для настроек текста
     *
     * @param string $font_size - размер шрифта
     * @param string $color - цвет
     *
     * @return DesignAtoms\CSSPresets\AbstractCssPreset
     */
    public static function getTextEditParamsSettings($font_size = '14px', $color = '#000000FF')
    {
        $textedit = new DesignAtoms\CSSPresets\TextEdit();
        $textedit->setDefaults([
            'font-size' => $font_size,
            'color' => $color
        ]);
        return $textedit;
    }

    /**
     * Возвращает данные для генерации CSS Align-items
     *
     * @param string $type - тип значения по умолчанию
     *
     * @return DesignAtoms\CSSProperty\Select
     */
    public static function getFlexDirectionParamsData($type = 'row')
    {
        $flex_direction_row = new DesignAtoms\CSSProperty\Select('flex-direction', t('Расположение'), $type);
        $flex_direction_row->setOptions([
            'row'    => t('Строка'),
            'column' => t('Колонка'),
        ]);
        return $flex_direction_row;
    }

    /**
     * Возвращает данные для генерации CSS Align-items
     *
     * @return DesignAtoms\CSSProperty\Select
     */
    public static function getAlignItemsParamsData()
    {
        $align_items = new DesignAtoms\CSSProperty\Select('align-items', t('Выравнивание по горизонтали'), 'flex-start');
        $align_items->setOptions([
            'flex-start' => t('Начало'),
            'center' => t('Центр'),
            'flex-end' => t('Конец'),
            'stretch' => t('Растянуть'),
        ]);
        return $align_items;
    }

    /**
     * Возвращает данные для генерации CSS Justify-content
     *
     * @return DesignAtoms\CSSProperty\Select
     */
    public static function getJustifyContentParamsData()
    {
        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Выравнивание по вертикали'), 'flex-start');
        $justify_content->setOptions([
            'flex-start'    => t('Начало'),
            'center' => t('Центр'),
            'flex-end' => t('Конец'),
            'space-between' => t('Расстояние между'),
            'space-around' => t('Расстояние вокруг'),
        ]);

        return $justify_content;
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $atomTypeExplode = explode("\\", get_class($this));
        $data = [
            'type'     => $this->getType(),
            'atomType' => array_pop($atomTypeExplode),
            'html'     => $this->getHtml(),

            'html_visible'     => $this->html_visible,
            'additional_class' => $this->additional_class,

            'title'       => $this->getTitle(),
            'css_title'   => $this->getCSSTitle(),
            'name'        => $this->getName(),
            'image'       => $this->getImage(),
            'tag'         => $this->getTag(),
            'tags'        => $this->getTags(),
            'attrs'       => $this->getAttr(),
            'attr_groups' => $this->getAttrGroups(),

            'reset_attrs'     => $this::$reset_attrs,
            'virtual_attrs'   => $this::$virtual_attrs,
            'css_for_wrapper' => $this::$css_for_wrapper,
            'hidden'          => $this->hidden,

            'paid' => self::isAtomPaid(), //Платный атом или нет 1 - да, 0 - нет

            'presets' => [],
            'css'     => [],
        ];

        $css = [];
        //Если есть пресеты, то добавим их
        if (!empty($this->css_presets)){
            foreach ($this->css_presets as $preset){
                $data['presets'][] = $preset->getData();
                $css = $preset->getCSSData() + $css;
            }
        }

        $data['css'] = $css;

        //Если есть CSS свойства, то добавим их
        if (!empty($this->css)){
            foreach ($this->css as $css_item){
                $property_info = $css_item->getPropertyInfo();
                $data['css'][$property_info['name']] = $property_info;
            }
        }

        if (method_exists($this, 'getChildParamsData')){
            $data['childs'] = $this->getChildParamsData();
        }

        return $data;
    }

    /**
     * Устанавливает массив css свойств для обёртки атома
     *
     * @param array $css_for_wrapper
     */
    public static function setAtomWrapperCSSStylesArray($css_for_wrapper = [])
    {
        static::$css_for_wrapper = $css_for_wrapper;
    }

    /**
     * Возвращае пустой элемент для ренденра
     *
     * @param string $class - класс элемента
     * @param string $tag - массив данных атома
     * @param array $attrs - массив атрибутов элемента ключ => значение
     * @return array
     */
    public static function getEmptySubAtomForRender($class = "", $tag = "div", $attrs = [])
    {
        $child = [];
        $child['tag']  = $tag;
        $child['type'] = 'subatom';
        if (!empty($class)){
            $child['attrs']['class']['value'] = $class;
        }
        if (!empty($attrs)){
            foreach ($attrs as $key => $val){
                $child['attrs'][$key]['value'] = $val;
            }
        }
        return $child;
    }

    /**
     * Возвращает массив стилей для обертки атома
     *
     * @param array $data - массив данных атома
     * @return array
     */
    public static function getAtomWrapperStyles($data)
    {
        $wrap_styles = [];

        if (!empty(static::$css_for_wrapper)){
            foreach (static::$css_for_wrapper as $css_item){
                if (isset($data['css'][$css_item]['value'])){
                    $wrap_styles[$css_item] = $data['css'][$css_item]['value'];
                }
            }
        }
        return $wrap_styles;
    }

    /**
     * Действия перед удалением атома
     *
     * @param array $data - массив данных атома в виде массива
     */
    function beforeDelete($data){}

    /**
     * Возвращает true, если атом платный
     *
     * @return bool
     */
    public static function isAtomPaid()
    {
        return (static::$paid > 0);
    }

    /**
     * Добавляет дополнительные аттрибуты атому
     *
     * @param array $data - массив данных элемента
     * @param array $attrs - аттрибуты для изменения
     *
     * @return array
     */
    public static function addAdditionalAttributesToAtom(&$data, $attrs)
    {
        if (isset($data['tag']) && $data['tag'] == 'img'){
            $attrs['alt'] = "";
        }
        $attrs['class'] .= " d-atom-item";
        return $attrs;
    }

    /**
     * Проверяет доступен ли атом, если он платный и добавляет нужный класс для отображения информации в публичной части
     *
     * @param array $data - массив данных элемента
     * @param array $attrs - аттрибуты для изменения
     *
     * @return array
     */
    public static function checkPaidAtomAvailableAndAddClass($data, $attrs)
    {
        if (self::isAtomPaid() && !ModuleLicenseApi::isLicenseRenewalActive()){ //Если атом платный
            $attrs['class'] .= " d-atom-license-blocked";
            $attrs['data-license-error'] = t("Блок заблокирован - отсутствует Pro подписка");
        }
        return $attrs;
    }

    /**
     * Заполняет значения по умолчанию в данные атома после добавления из пресета
     *
     * @param array $data - данные атома из пресета
     * @param array $preset - все данные пресета
     */
    public static function setDefaultsAfterPresetInsert(&$data, $preset){}
}
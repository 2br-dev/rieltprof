<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;

/**
 * Class Form - класс формы
 */
class Form extends DesignAtoms\AbstractAtom {
    protected $title = "Форма"; //Название компонента
    protected $tag   = "form";//Тег с помощью которого будет формироваться содержимое
    protected $tags  = [ //Допустиые теги
        "form" => 'Форма'
    ];
    protected $image   = "form.svg"; //Картинка компонента
    protected $html    = ""; //Картинка компонента
    protected $form_id = 0; //id кормы к которой привязаны
    public static $virtual_attrs = [ //Массив виртуальных аттрибутов
        'link',
        'success',
        'error'
    ];
    public static $public_js       = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '%designer%/atoms/form.js'
    ];
    public static $reset_attrs = [
        'form_id'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $this->setCSSTitle(t('Форма'));
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '10px',
                'left'   => '10px',
                'bottom' => '10px',
                'right'  => '10px'
            ]),
        ]);
        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $this->addFormAttributes();
    }

    /**
     * Добавляет нужнвые аттрибуты к форме
     */
    function addFormAttributes()
    {
        $error = new \Designer\Model\DesignAtoms\Attrs\ToggleButton('error', t('Показать сообщение об ошибке?'));
        $error->setOptions([
            t('Показать'),
            t('Скрыть'),
        ]);

        $agreement_show_type = new DesignAtoms\Attrs\AttrSelect('agreement_show_type', t('Где показывать согласие на обработку данных, если доступно?'), 'in');
        $agreement_show_type->setOptions([
            'in' => t('Внутри перед кнопкой "Отправить"'),
            'out' => t('После кнопки "Отправить"')
        ]);
        //Зададим доп параметры отображения
        $this->setAttr([
            new DesignAtoms\Attrs\SelectFieldValueFromJSONData('form_id', t('Форма'), 'form/formsList'),
            new \Designer\Model\DesignAtoms\Attrs\DirectLink('link', t('Ссылка на форму'), \RS\Router\Manager::obj()->getAdminUrl(null, null, 'feedback-ctrl'), [
                'title' => t('Перейти к форме'),
                'dir' => 'form_id'
            ]),
            new DesignAtoms\Attrs\ToggleCheckbox('show_header', t('Показывать заголовок формы?')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_field_header', t('Показывать заголовок поля?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_hint', t('Показывать пояснения поля?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('success', t('Успешное сообщение')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_agree_checkbox', t('Показывать галочку о согласии на обработку персональных данных?'), 1),
            $agreement_show_type,
            $error
        ]);
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $data = parent::getData();
        $data['form_id'] = $this->form_id;
        return $data;
    }

    /**
     * Возвращает массив данных параметров для настройки границ
     *
     * @return DesignAtoms\CSSPresets\AbstractCssPreset
     */
    public static function getBorderParamsSettings()
    {
        $border = new DesignAtoms\CSSPresets\Border();
        $border->setDefaults([
            'border' => [
                'border-color' => "#929292FF",
                'border-type' => "solid",
                'bottom' => "1px",
                'left' => "1px",
                'right' => "1px",
                'top' => "1px"
            ]
        ]);
        return $border;
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для заголовка
     *
     * @return array
     */
    public static function getChildParamsDataForTitle()
    {
        $textedit = self::getTextEditParamsSettings();

        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '5px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '100%')
        ]);
        $title = new DesignAtoms\Items\SubAtom();
        $title->setTag('div')
            ->setTitle(t('Заголовок'))
            ->setClass('d-form-title')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $textedit,
                $preset
            ]);
        return $title->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для Обёртка строки
     *
     * @return array
     */
    public static function getChildParamsDataForField()
    {
        //Заголовки
        $fieldPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $fieldPreset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '15px',
            'left'  => '0px'
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка поля'))
            ->setClass('d-atom-form-field')
            ->addCSSPreset([
                $fieldPreset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для заголовка формы
     *
     * @return array
     */
    public static function getChildParamsDataForHeader()
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'color' => '#000000FF',
            'font-size' => '24px',
            'font-weight' => 'bold'
        ]);
        //Заголовки
        $headerPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $headerPreset->addCSS([
            new DesignAtoms\CSSProperty\AlignItems('text-align', t('Расположение'), 'center'),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '15px'),
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Заголовок формы'))
            ->setClass('d-form-header')
            ->addCSSPreset([
                $textedit,
                $headerPreset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров детей обёртки группы полей
     *
     * @return array
     */
    public static function getChildParamsDataForFieldsWrapper()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление полей'), 'column');
        $flex_direction->setOptions([
            'row' => t('В строку'),
            'column' => t('Колонкой'),
        ]);

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Позиционирование элементов внутри по оси X'), 'flex-start');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
            'space-around' => t('Отступ вокруг'),
            'space-between' => t('Отступ между'),
        ]);

        $preset->addMarginAndPaddingCSS()->addCSS([
            $flex_direction,
            $justify_content,
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование элементов внутри по оси Y'), 'flex-start')
        ]);
        $wrapper = new DesignAtoms\Items\SubAtom();
        $wrapper->setTitle(t('Общая обёртка группы полей с телом формы'))
            ->setClass('d-atom-form-fields-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $preset
            ]);
        return $wrapper->getData();
    }

    /**
     * Возвращает массив данных параметров детей обёртки заголовка и поля
     *
     * @return array
     */
    public static function getChildParamsDataForFieldWrapper()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление полей'), 'column');
        $flex_direction->setOptions([
            'row' => t('В строку'),
            'column' => t('Колонкой'),
        ]);

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Позиционирование элементов внутри по оси X'), 'flex-start');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
            'space-around' => t('Отступ вокруг'),
            'space-between' => t('Отступ между'),
        ]);

        $preset->addMarginAndPaddingCSS()->addCSS([
            $flex_direction,
            $justify_content,
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование элементов внутри по оси Y'), 'flex-start'),
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина'), '100%')
        ]);
        $wrapper = new DesignAtoms\Items\SubAtom();
        $wrapper->setTag('div')
            ->setTitle(t('Обёртка для заголовка и поля'))
            ->setClass('d-atom-form-title-field-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $preset
            ]);
        return $wrapper->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для полей ввода
     *
     * @return array
     */
    public static function getChildParamsDataForInput()
    {
        $textedit = self::getTextEditParamsSettings();
        $background = self::getBackgroundParamsSettings('#FFFFFFFF');
        $border = self::getBorderParamsSettings();

        $inputPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $inputPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота строки'), '32px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '36px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '2px',
                'left'   => '5px',
                'bottom' => '2px',
                'right'  => '5px'
            ]),
        ]);

        $input = new DesignAtoms\Items\SubAtom();
        $input->setTag('input')
            ->setTitle(t('Поля ввода'))
            ->setClass('d-form-input')
            ->addCSSPreset([
                $background,
                $border,
                $textedit,
                $inputPreset
            ]);
        return $input->getData();
    }


    /**
     * Возвращает массив данных параметров детей внутри составного элемента для поля ввода текста
     *
     * @return array
     */
    public static function getChildParamsDataForTextArea()
    {
        $textedit   = self::getTextEditParamsSettings();
        $background = self::getBackgroundParamsSettings('#FFFFFFFF');
        $border     = self::getBorderParamsSettings();

        //Поля ввода большое
        $textareaPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $textareaPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '100px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '2px',
                'left'   => '5px',
                'bottom' => '2px',
                'right'  => '5px'
            ]),
        ]);

        $textarea = new DesignAtoms\Items\SubAtom();
        $textarea->setTag('textarea')
            ->setTitle(t('Поле текста'))
            ->setClass('d-form-textarea')
            ->addCSSPreset([
                $background,
                $border,
                $textedit,
                $textareaPreset
            ]);
        return $textarea->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для заголовка галочек и радиокнопок
     *
     * @return array
     */
    public static function getChildParamsDataForLabel()
    {
        $vertical_align = new DesignAtoms\CSSProperty\Select('vertical-align', t('Выравнивание'), 'middle');
        $vertical_align->setOptions([
            'top' => t('Верх'),
            'middle' => t('Середина'),
            'bottom' => t('Низ'),
        ]);

        //Заголовок для галочек и радиокнопок
        $labelPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $labelPreset->addCSS([
            $vertical_align,
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '8px'),
            new DesignAtoms\CSSProperty\Size('margin-left', t('Внешний отступ снизу'), '3px'),
            new DesignAtoms\CSSProperty\Size('margin-right', t('Внешний отступ снизу'), '5px'),
            new DesignAtoms\CSSProperty\Size('max-width', t('Максимальная ширина'), '85%')
        ]);
        $label = new DesignAtoms\Items\SubAtom();
        $label->setTag('label')
            ->setTitle(t('Заголовок для галочек и радиокнопок'))
            ->setClass('d-form-label')
            ->addCSSPreset([
                self::getTextEditParamsSettings(),
                $labelPreset
            ]);

        return $label->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для ссылки в переключателе подтверждения
     *
     * @return array
     */
    public static function getChildParamsDataForAgreeLink()
    {
        $label = new DesignAtoms\Items\SubAtom();
        $label->setTag('label')
            ->setTitle(t('Ссылка на обработку персональных данных'))
            ->setClass('d-form-label a')
            ->addCSSPreset([
                self::getTextEditParamsSettings('14px', '')
            ]);

        return $label->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для поля файла
     *
     * @return array
     */
    public static function getChildParamsDataForFile()
    {
        $textedit   = self::getTextEditParamsSettings();
        $background = self::getBackgroundParamsSettings('#FFFFFFFF');
        $border     = self::getBorderParamsSettings();

        $filePreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $filePreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '10px',
                'left'   => '10px',
                'bottom' => '10px',
                'right'  => '10px'
            ])
        ]);
        $file = new DesignAtoms\Items\SubAtom();
        $file->setTag('input')
            ->setTitle(t('Файл'))
            ->setClass('d-form-input-file')
            ->addCSSPreset([
                $textedit,
                $background,
                $border,
                $filePreset
            ]);
        return $file->getData();
    }


    /**
     * Возвращает массив данных параметров детей внутри составного элемента для обёртки кнопки отправки
     *
     * @return array
     */
    public static function getChildParamsDataForButtonWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина блока'), '100%'),
        ]);
        $wrapper = new DesignAtoms\Items\SubAtom();
        $wrapper->setTitle(t('Обёртка кнопки отправки'))
            ->setClass('d-atom-form-send-button-wrapper')
            ->setType('subatom')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $preset
            ]);
        return $wrapper->getData();
    }


    /**
     * Возвращает массив данных параметров детей внутри составного элемента для кнопки отправки
     *
     * @return array
     */
    public static function getChildParamsDataForButton()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('margin-top', t('Внешний отступ сверху'), '15px'),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '15px'),
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота'), '40px'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '200px'),
        ]);
        $button = new DesignAtoms\Items\Button();
        $button->setTitle(t('Кнопка отправки'))
            ->setClass('d-form-button')
            ->setType('subatom')
            ->addCSSPreset([
                $preset
            ]);
        return $button->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для нижней подсказки
     *
     * @return array
     */
    public static function getChildParamsDataForHint()
    {
        $hintTextedit = new DesignAtoms\CSSPresets\TextEdit();
        $hintTextedit->setDefaults([
            'font-size' => '10px',
            'color' => '#c0c0c0FF'
        ]);

        $hintPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $marginTop = new DesignAtoms\CSSProperty\Size('margin-top', t('Внешний отступ сверху'), '3px');
        $marginTop->setVisible(true);
        $hintPreset->addCSS([
            $marginTop
        ]);

        $hint = new DesignAtoms\Items\SubAtom();
        $hint->setTag('div')
            ->setTitle(t('Пояснение'))
            ->setClass('d-form-hint')
            ->addCSSPreset([
                $hintTextedit,
                $hintPreset
            ]);
        return $hint->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для значка звездочки
     *
     * @return array
     */
    public static function getChildParamsDataForRequired()
    {
        $requiredPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $requiredPreset->addCSS([
            new DesignAtoms\CSSProperty\Color('color', t('Цвет'), '#FF0000FF')
        ]);

        $required = new DesignAtoms\Items\SubAtom();
        $required->setTag('sup')
            ->setTitle(t('Звездочка'))
            ->setClass('d-atom-form-required')
            ->addCSSPreset([
                $requiredPreset
            ]);
        return $required->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для ошибки
     *
     * @return array
     */
    public static function getChildParamsDataForError()
    {
        $textedit    = self::getTextEditParamsSettings();
        $background  = self::getBackgroundParamsSettings();
        $errorPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $errorPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('margin-top', t('Внешний отступ сверху'), '7px'),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '7px'),
            new DesignAtoms\CSSProperty\Color('color', t('Цвет'), '#FF0000FF'),
        ]);

        $error = new DesignAtoms\Items\SubAtom();
        $error->setTag('div')
            ->setTitle(t('Ошибка'))
            ->setClass('d-form-error')
            ->addCSSPreset([
                $textedit,
                $background,
                $errorPreset
            ]);
        return $error->getData();
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента для успешного сообщения
     *
     * @return array
     */
    public static function getChildParamsDataForSuccess()
    {
        $textedit    = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'font-weight' => 'bold',
            'font-size' => '17px',
            'color' => '#008000FF',
        ]);
        $background  = self::getBackgroundParamsSettings();
        $successPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $successPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ сверху'), [
                'top'    => '100px',
                'left'   => '10px',
                'bottom' => '100px',
                'right'  => '10px'
            ]),
            new DesignAtoms\CSSProperty\AlignItems('text-align', t('Позиция'), 'center'),
            new DesignAtoms\CSSProperty\Size('margin-top', t('Внешний отступ сверху'), '7px'),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '7px'),
        ]);

        $success = new DesignAtoms\Items\SubAtom();
        $success->setTag('div')
            ->setTitle(t('Успешное сообщение'))
            ->setClass('d-form-success')
            ->addCSSPreset([
                $textedit,
                $background,
                $successPreset
            ]);
        return $success->getData();
    }


    /**
     * Вовзращает заполные данные потомка для заголовка
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormFieldItem $field - данные с параметрами поля
     *
     * @return array
     */
    private static function fillChildForFieldTitle($children_params, $field)
    {
        $child = $children_params['title'];
        $title = $field['title'];

        //Если поле обязательное, то добавим зведочку
        if ($field['required']){
            $title .= "<sup class='d-atom-form-required'>*</sup>";
        }
        $child['html'] = $title;
        return $child;
    }

    /**
     * Заполняет данными дополнительные аттрибуты исходя из настроек поля формы
     *
     * @param array $child - массив данных потомка
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildFieldAdditionalAttributes($child, $field)
    {
        $attributes = $field->getAdditionalAttributes();
        if (!empty($attributes)){
            foreach ($attributes as $key=>$val){
                $child['attrs'][$key]['value'] = $val;
            }
        }
        if ($field['required']){
            $child['attrs']['required']['value'] = 'required';
        }
        if (empty($child['attrs']['placeholder']['value'])){
            $child['attrs']['placeholder']['value'] = $field['title'];
        }
        return $child;
    }

    /**
     * Возвращает аттрибут id для простого поля формы
     *
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - данные с параметрами поля
     * @return string
     */
    private static function getFieldIdAttributeValue($form, $field)
    {
        return "d-form-{$form['id']}-field-{$field['id']}";
    }

    /**
     * Возвращает аттрибут id для спискового поля формы
     *
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - данные с параметрами поля
     * @param string $option - номер опции
     * @return string
     */
    private static function getFieldIdAttributeValueForSelect($form, $field, $option)
    {
        return self::getFieldIdAttributeValue($form, $field)."-".$option;
    }

    /**
     * Возвращает детей option из настроек поля типа select
     *
     * @param \Feedback\Model\Orm\FormFieldItem $field - данные с параметрами поля
     *
     * @return array
     */
    private static function getOptionsFromSelectField($field)
    {
        $items = explode("\n", $field['anwer_list']);
        $childs = [];
        if (!empty($items)){
            foreach ($items as $item){
                $option['tag']  = 'option';
                $option['html'] = $item;
                $option['attrs']['value']['value'] = $item;
                $childs[] = $option;
            }
        }
        return $childs;
    }

    /**
     * Вовзращает заполные данные потомка для типа select (выпадающий список)
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForSelectTypeSelect($children_params, $form, $field)
    {
        $child = $children_params['string'];

        $child['tag'] = 'select';
        $child['attrs']['name']['value'] = $field['alias'];
        $child['childs'] = self::getOptionsFromSelectField($field);

        return $child;
    }

    /**
     * Возвращает потомка для тега label
     *
     * @param array $children_params - массив сведений о потомках залоголовка
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     * @param string $title - название заголовка
     * @param integer $num - дополнительный признак для генерации
     *
     * @return array
     */
    private static function getChildFieldLabel($children_params, $form, $field, $title, $num = null)
    {
        $child = $children_params['label'];
        $child['html'] = $title;
        if ($num === null){
            $child['attrs']['for']['value'] = self::getFieldIdAttributeValue($form, $field);
        }else{
            $child['attrs']['for']['value'] = self::getFieldIdAttributeValueForSelect($form, $field, $num);
        }

        return $child;
    }

    /**
     * Возвращает потомка для тега label
     *
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     * @param string $title - название заголовка
     * @param integer $num - дополнительный признак для генерации
     *
     * @return array
     */
    private static function getChildFieldInputForRadioOrCheckbox($form, $field, $title, $num = null)
    {
        $child['tag']  = 'input';
        $child['type'] = 'subatom';
        $child['attrs']['type']['value']  = ($field['show_type'] == 'list') ? $field['show_list_as'] : 'checkbox';
        $child['attrs']['name']['value']  = $field['alias'];
        $child['attrs']['value']['value'] = $title;
        if ($num === null){
            $child['attrs']['id']['value']    = self::getFieldIdAttributeValue($form, $field);
        }else{
            $child['attrs']['id']['value']    = self::getFieldIdAttributeValueForSelect($form, $field, $num);
        }
        return $child;
    }

    /**
     * Вовзращает заполные данные для одного элемента галочки или радиокнопки с оберткой
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param string $item - сведения о позиции опции
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     * @param integer $k - номер позиции
     *
     * @return array
     */
    private static function fillChildForOneItemCheckboxOrRadio($children_params, $form, $item, $field, $k)
    {
        $sub_childs = [];
        //Выбор
        $sub_childs[] = self::getChildFieldInputForRadioOrCheckbox($form, $field, $item, $k);

        //Заголовок к ней
        $sub_childs[] = self::getChildFieldLabel($children_params, $form, $field, $item, $k);

        $wrapper_child = self::getEmptySubAtomForRender('d-form-hint');
        $wrapper_child['childs'] = $sub_childs;
        return $wrapper_child;
    }

    /**
     * Вовзращает заполные данные потомка для типа radio (радиокнопки)
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForSelectTypeRadio($children_params, $form, $field)
    {
        $childs = [];
        $items = explode("\n", $field['anwer_list']);
        if (!empty($items)){
            foreach ($items as $k=>$item){
                $childs[] = self::fillChildForOneItemCheckboxOrRadio($children_params, $form, $item, $field, $k);
            }

            //Выделим первый элемент
            $childs[0]['childs'][0]['attrs']['checked']['value'] = 'checked';
        }

        return $childs;
    }


    /**
     * Вовзращает заполные данные потомка для типа radio (галочки)
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForSelectTypeCheckbox($children_params, $form, $field)
    {
        $childs = [];
        $items = explode("\n", $field['anwer_list']);
        if (!empty($items)){
            foreach ($items as $k=>$item){
                $childs[] = self::fillChildForOneItemCheckboxOrRadio($children_params, $form, $item, $field, $k);
            }
        }
        return $childs;
    }


    /**
     * Вовзращает заполные данные потомка для поля ввода
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForFieldString($children_params, $form, $field)
    {
        $child = $children_params[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_STRING];
        $child['attrs']['type']['value'] = 'text';
        $child['attrs']['name']['value'] = $field['alias'];
        return $child;
    }

    /**
     * Вовзращает заполные данные потомка для поля ввода типа E-mail
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForFieldText($children_params, $form, $field)
    {
        $child = $children_params[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_TEXT];
        $child['attrs']['name']['value'] = $field['alias'];
        return $child;
    }

    /**
     * Вовзращает заполные данные потомка для поля ввода типа E-mail
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForFieldEmail($children_params, $form, $field)
    {
        $child = $children_params[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_STRING];
        $child['attrs']['type']['value'] = 'email';
        $child['attrs']['name']['value'] = $field['alias'];
        return $child;
    }

    /**
     * Вовзращает заполные данные потомка для поля ввода типа E-mail
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForFieldFile($children_params, $form, $field)
    {
        $child = $children_params[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_FILE];
        $child['attrs']['type']['value'] = 'file';
        $child['attrs']['name']['value'] = $field['alias'];
        return $child;
    }

    /**
     * Вовзращает заполные данные потомка для поля ввода типа E-mail
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForFieldYesNo($children_params, $form, $field)
    {
        $childs[] = self::getChildFieldInputForRadioOrCheckbox($form, $field, 1);
        $childs[] = self::getChildFieldLabel($children_params, $form, $field, $field['title']);

        return $childs;
    }

    /**
     * Вовзращает данные для генерации подсказки
     *
     * @param array $children_params - массив данных для типов с установленными данными
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     *
     * @return array
     */
    private static function fillChildForHint($children_params, $form, $field)
    {
        $child = self::getEmptySubAtomForRender('d-form-hint');
        $child['html'] = $field['hint'];

        return $child;
    }

    /**
     * Подготавливает потомков для полей формы
     *
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     * @param array $attrs - настройки полей
     *
     * @return array
     */
    private static function fillFieldsChild($children_params, $form, $field, $attrs)
    {
        //Если каптча то пропустим, т.к. её невозможно стилизовать.
        if ($field['show_type'] == \Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_CAPTCHA){
            return [];
        }

        //Получит заголовок
        if ($field['show_type'] != \Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_YESNO && $attrs['show_field_header']['value']){
            $children[] = self::fillChildForFieldTitle($children_params, $field);
        }

        $wrapper_child = self::getEmptySubAtomForRender('d-atom-form-field');

        //Получим само поле
        if ($field['show_type'] == 'list') { //Если это список
            $fillMethod = "fillChildForSelectType" . ucfirst($field['show_list_as']);
        }else{
            $fillMethod = "fillChildForField" . ucfirst($field['show_type']);
        }
        $child = self::$fillMethod($children_params, $form, $field);
        if (isset($child[0])){ //Если вернулось несколько потомков, т.е. составной элемент
            $child[0] = self::fillChildFieldAdditionalAttributes($child[0], $field);
            foreach ($child as $ch){
                $sub_children[] = $ch;
            }
        }else{
            $child = self::fillChildFieldAdditionalAttributes($child, $field);
            $sub_children[] = $child;
        }

        //Получим пояснение
        if (!empty($field['hint']) && $attrs['show_hint']['value']){
            $field_types = [
                'string',
                'text',
                'email'
            ];
            if (in_array($field['show_type'], $field_types)){
                $sub_children[] = self::fillChildForHint($children_params, $form, $field);
            }
        }

        $wrapper_child['childs'] = $sub_children;
        $children[] = $wrapper_child;

        return $children;
    }

    /**
     * Возвращает потомка для кнопки отправки формы
     *
     * @param array $children_params - массив данных с установками для разных типов
     * @return array
     */
    private static function getSubmitButtonChild($children_params)
    {
        $button_wrapper = $children_params['button_wrapper'];
        $button = $children_params['button'];
        $button['attrs']['type']['value'] = 'submit';
        $button['html'] = t('Отправить');

        //Обертка для кнопки
        $child = self::getEmptySubAtomForRender('d-atom-instance');
        $child['childs'][] = $button;
        $button_wrapper['childs'][] = $child;

        return $button_wrapper;
    }

    /**
     * Возвращает потомка для кнопки отправки формы
     *
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     * @param \Feedback\Model\Orm\FormFieldItem $field - объект поля формы
     * @param array $attrs - массив аттрибутов атома
     * @return array
     */
    private static function getAgreementChilds($children_params, $form, $field, $attrs)
    {
        $wrapper_child = self::getEmptySubAtomForRender('d-atom-form-field');
        if ($attrs['show_agree_checkbox']['value']){
            $input = self::getChildFieldInputForRadioOrCheckbox($form, $field, 1);
            $input['attrs']['required']['value'] = 'required';

            $childs[] = $input;
        }

        //Обертка для кнопки
        $child = $children_params['label'];
        $child['attrs']['for']['value'] = $input['attrs']['id']['value'];
        if ($attrs['show_agree_checkbox']['value']){
            $text = t('Я даю согласие на <a href="%0" target="_blank">обработку персональных данных</a>', [\RS\Router\Manager::obj()->getUrl('site-front-policy-agreement')]);
        }else{
            $text = t('Нажимая кнопку "Отправить" я подтверждаю согласие на <a href="%0" target="_blank">обработку персональных данных</a>', [\RS\Router\Manager::obj()->getUrl('site-front-policy-agreement')]);
        }
        $child['html'] = $text;

        $childs[] = $child;
        $wrapper_child['childs'] = $childs;

        return $wrapper_child;
    }

    /**
     * Возвращает потомка CSRF формы
     *
     * @return array
     */
    private static function getCSRFChild()
    {
        $child['tag'] = 'input';
        $child['attrs']['type']['value']  = 'hidden';
        $child['attrs']['name']['value']  = 'csrf_protection';
        $child['attrs']['value']['value'] = \RS\Http\Request::commonInstance()->setCsrfProtection();
        return $child;
    }


    /**
     * Возвращает потомка для заголовка формы
     *
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Feedback\Model\Orm\FormItem $form - объект формы
     *
     * @return array
     */
    private static function getFormHeaderChild($children_params, $form)
    {
        $child = $children_params['header'];
        $child['html'] = $form['title'];
        return $child;
    }

    /**
     * Возвращает массив данных параметров детей внутри составного элемента
     *
     * @return array
     */
    public static function getChildParamsData()
    {
        static $childs;
        if (!$childs){
            $childs['header']         = self::getChildParamsDataForHeader();

            $childs['fields_wrapper'] = self::getChildParamsDataForFieldsWrapper();
            $childs['field_wrapper']  = self::getChildParamsDataForFieldWrapper();

            $childs['title']          = self::getChildParamsDataForTitle();
            $childs['required']       = self::getChildParamsDataForRequired();
            $childs['field']          = self::getChildParamsDataForField();
            $childs['label']          = self::getChildParamsDataForLabel();

            $childs[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_STRING] = self::getChildParamsDataForInput();
            $childs[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_TEXT]   = self::getChildParamsDataForTextArea();
            $childs[\Feedback\Model\Orm\FormFieldItem::SHOW_TYPE_FILE]   = self::getChildParamsDataForFile();

            $childs['hint']     = self::getChildParamsDataForHint();

            $childs['button_wrapper'] = self::getChildParamsDataForButtonWrapper();
            $childs['button']         = self::getChildParamsDataForButton();

            $childs['agree_link']     = self::getChildParamsDataForAgreeLink();

            $childs['error']    = self::getChildParamsDataForError();
            $childs['success']  = self::getChildParamsDataForSuccess();
        }
        return $childs;
    }

    /**
     * Возвращает массив данных детей внутри составного элемента для отображения в публичной части
     *
     * @param array $data - массив данных элемента
     * @return array|string
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    public static function getFillChildsDataForRender($data)
    {
        $children = "<p class='d-no-element'>".t('Форма не назначена')."</p>";

        $attrs = $data['attrs'];
        if ($data['form_id']){ //Если форма назначена
            $form = new \Feedback\Model\Orm\FormItem($data['form_id']);
            if (!$form['id']){
                return "<p class='d-no-element'>".t('Форма с id=%1 не найдена или удалена', [$data['form_id']])."</p>";
            }

            $children = [];
            $children_params = self::getChildParamsData();

            if ($attrs['show_header']['value']){
                $children[] = self::getFormHeaderChild($children_params, $form);
            }

            //Добавим скрытые обязательные поля
            if ($form['use_csrf_protection']){
                $children[] = self::getCSRFChild();
            }

            $fields_wrapper = $children_params['fields_wrapper'];
            $site_config = \RS\Config\Loader::getSiteConfig();

            //Подгрузим поля формы
            $form_fields = $form->getFields(); //Получим поля, чтобы потом наполнить
            if (!empty($form_fields)){
                foreach ($form_fields as $field){
                    $field_childs = self::fillFieldsChild($children_params, $form, $field, $attrs);
                    if (!empty($field_childs)){
                        $field_wrapper = $children_params['field_wrapper'];
                        foreach ($field_childs as $field_child){
                            $field_wrapper['childs'][] = $field_child;
                        }
                        $fields_wrapper['childs'][] = $field_wrapper;
                    }
                }
            }

            //Добавим обработку персональных данных если нужно
            if ($site_config['enable_agreement_personal_data'] && ($attrs['agreement_show_type']['value'] == 'in')){
                $fields_wrapper['childs'][] = self::getAgreementChilds($children_params, $form, $field, $attrs);
            }

            //Добавим кнопку отправки
            $fields_wrapper['childs'][] = self::getSubmitButtonChild($children_params);

            $children[] = $fields_wrapper;

            //Добавим обработку персональных данных если нужно
            if ($site_config['enable_agreement_personal_data'] && ($attrs['agreement_show_type']['value'] == 'out')){
                $children[] = self::getAgreementChilds($children_params, $form, $field, $attrs);
            }
        }

        return $children;
    }


    /**
     * Добавляет дополнительные аттрибуты элементу
     *
     * @param array $data - массив данных элемента
     * @param array $attrs - аттрибуты для изменения
     *
     * @return array
     */
    public static function addAdditionalAttributesToAtom(&$data, $attrs)
    {
        $attrs = parent::addAdditionalAttributesToAtom($data, $attrs);
        if ($data['form_id']) { //Если форма назначена
            $form = new \Feedback\Model\Orm\FormItem($data['form_id']);
            if ($form['id']){ //Если форма найдена
                $attrs['method']  = 'POST';
                $attrs['action']  = \RS\Router\Manager::obj()->getUrl('designer-front-form', ['form_id' => $form['id'], 'ajax' => 1]);
                $attrs['enctype'] = 'multipart/form-data';
            }
        }
        return $attrs;
    }

    /**
     * Создаёт форму подписки возвращая её
     *
     * @return \Feedback\Model\Orm\FormItem
     */
    private static function createFormSubscribe()
    {
        $form = new \Feedback\Model\Orm\FormItem();
        $form['title'] = t('Подписка');
        $form->insert();

        //Создадим поле к форме
        $formItem = new \Feedback\Model\Orm\FormFieldItem();
        $formItem['title']     = 'E-mail';
        $formItem['alias']     = 'email';
        $formItem['form_id']   = $form['id'];
        $formItem['required']  = 1;
        $formItem['show_type'] = $formItem::SHOW_TYPE_EMAIL;
        $formItem['use_mask']  = 'email';
        $formItem->insert();

        return $form;
    }


    /**
     * Возвращает id демо формы подписки на рассылку
     *
     * @return integer
     */
    private static function getFormSubscribeId()
    {
        $form = \RS\Orm\Request::make()
                    ->from(new \Feedback\Model\Orm\FormItem())
                    ->where([
                        'title' => t('Подписка'),
                    ])->object();

        if (!$form){
            $form = self::createFormSubscribe();
        }
        return $form['id'];
    }


    /**
     * Заполняет значения по умолчанию в данные атома после добавления из пресета
     *
     * @param array $data - данные атома из пресета
     * @param array $preset - все данные пресета
     */
    public static function setDefaultsAfterPresetInsert(&$data, $preset){
        switch($preset['alias']){
            case "form2": //Для формы подписки
            case "cover5":
                $id = self::getFormSubscribeId();
                break;
            default:
                $id = \RS\Orm\Request::make()
                    ->from(new \Feedback\Model\Orm\FormItem())
                    ->where([
                        'site_id' => \RS\Site\Manager::getSiteId(),
                    ])
                    ->orderby('sortn ASC')
                    ->limit(1)
                    ->exec()
                    ->getOneField('id', 0);
                break;
        }

        if ($id){
            $data['form_id'] = $id;
        }
    }
}
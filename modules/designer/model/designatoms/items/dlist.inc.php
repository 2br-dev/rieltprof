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
 * Class DList - класс список
 */
class DList extends DesignAtoms\AbstractAtom {
    protected $title = "Список"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "list.svg"; //Картинка компонента

    public static $public_js       = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '%designer%/atoms/list.js'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление'), 'column');
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

        $this->setCSSTitle(t('Список'));
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            $flex_direction,
            $justify_content,
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование элементов внутри по оси Y'), 'flex-start'),
        ]);

        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $show_type = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('image_show_type', t('Где показывать верхнюю картинку?'), 'top');
        $show_type->setOptions([
            'top' => t('Первой в элементе'),
            'title' => t('В обёртке заголовка'),
        ]);


        $this->setAttr([
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_top_image', t('Показывать верхнюю картинку?'), 1),
            $show_type,
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_title', t('Показывать заголовок?'), 1),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_plus_icon', t('Показывать иконку раскрытия?'), 0),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_descr', t('Показывать описание?'), 1),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_all_open', t('Описание элемента всегда раскрыты?'), 1),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('show_bottom_image', t('Показывать нижнюю картинку?'), 0),
            new \Designer\Model\DesignAtoms\Attrs\DList('list', t('Список пунктов')),
        ]);
    }

    /**
     * Возвращает массив данных параметров для обёртки вопроса
     *
     * @return array
     */
    public static function getChildParamsDataForItem()
    {
        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление'), 'row');
        $flex_direction->setOptions([
            'row' => t('В строку'),
            'column' => t('Колонкой'),
        ]);

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '10px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '100%'),
            $flex_direction,
            new DesignAtoms\CSSProperty\FlexAlignItems('justify-content', t('Позиционирование по оси X'), 'flex-start'),
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование по оси Y'), 'flex-start'),
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка элемента'))
            ->setClass('d-atom-list-item')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для обёртки вопроса открытое
     *
     * @return array
     */
    public static function getChildParamsDataForItemOpened()
    {
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка элемента (Открытая)'))
            ->setClass('d-atom-list-item.d-open')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border()
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для обёртки верхней картинки
     *
     * @return array
     */
    public static function getChildParamsDataForTopImageWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 0);
        $order->setMin(0)->setMax(100)->setStep(1);
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '5px',
            'bottom' => '5px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина')),
            new DesignAtoms\CSSProperty\FlexAlignItems('justify-content', t('Позиционирование по оси X'), 'center'),
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование по оси Y'), 'center'),
            $order
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка верхней картинки'))
            ->setClass('d-atom-list-image-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки нижней картинки
     *
     * @return array
     */
    public static function getChildParamsDataForBottomImageWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 1);
        $order->setMin(0)->setMax(100)->setStep(1);
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '5px',
            'right'   => '5px',
            'bottom' => '0px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина')),
            new DesignAtoms\CSSProperty\FlexAlignItems('justify-content', t('Позиционирование по оси X'), 'center'),
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование по оси Y'), 'center'),
            $order
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка нижней картинки'))
            ->setClass('d-atom-list-image-bottom-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для картинки
     *
     * @return array
     */
    public static function getChildParamsDataForImage()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '32px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '32px')
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('img')
            ->setTitle(t('Картинка элемента списка'))
            ->setClass('d-atom-list-image')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для картинки
     *
     * @return array
     */
    public static function getChildParamsDataForBottomImage()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '32px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '32px')
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('img')
            ->setTitle(t('Нижняя картинка списка'))
            ->setClass('d-atom-list-image-bottom-wrapper .d-img')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для картинки
     *
     * @return array
     */
    public static function getChildParamsDataForInfoWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '100%'),
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка с информацией'))
            ->setClass('d-atom-list-info')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для переключателя открытия вопроса
     *
     * @return array
     */
    public static function getChildParamsDataForToggler()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $align_items = new DesignAtoms\CSSProperty\Select('align-items', t('Расположение горизонтальное'), 'center');
        $align_items->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);

        $justify_items = new DesignAtoms\CSSProperty\Select('justify-items', t('Расположение вертикальное'), 'flex-start');
        $justify_items->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);

        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 1);
        $order->setMin(0)->setMax(100)->setStep(1);

        $preset->addMarginAndPaddingCSS([
            'top'    => '5px',
            'right'   => '5px',
            'bottom' => '5px',
            'left'  => '5px'
        ], [])->addCSS([
            $align_items,
            $justify_items,
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '100%'),
            $order
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка для заголовка и переключателя'))
            ->setClass('d-atom-list-toggler')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#0c0c0c0d'),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для картинки переключателя
     *
     * @param string $type - тип картинки принимает open или closed
     *
     * @return array
     */
    public static function getChildParamsDataForTogglerImage($type = "closed")
    {
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => '12px 12px',
        ]);

        $params = [
            $background,
            self::getTextEditParamsSettings(),
            new DesignAtoms\CSSPresets\Border(),
        ];

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $class = 'd-atom-list-toggler-image';
        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 1);
        $order->setMin(0)->setMax(100)->setStep(1);

        $field = new DesignAtoms\Items\SubAtom();
        if ($type == 'closed'){
            $preset->addMarginAndPaddingCSS([], [
                'top'    => '0px',
                'right'   => '7px',
                'bottom' => '0px',
                'left'  => '0px'
            ])->addCSS([
                new DesignAtoms\CSSProperty\Size('height', t('Высота'), '12px'),
                new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '12px'),
                $order
            ]);
            $params[] = $preset;
            $background->setDefaults([
                'background-image' => '/modules/designer/view/img/iconsset/action/plus.svg',
            ]);
            $field->setTitle(t('Картинка переключателя (Закрытая)'));
        }else{
            $background->setDefaults([
                'background-image' => '/modules/designer/view/img/iconsset/action/minus.svg',
            ]);
            $class .= ".d-open";
            $field->setTitle(t('Картинка переключателя (Открытая)'));
        }


        $field->setTag('span')
            ->setClass($class)
            ->addCSSPreset($params);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для текста вопроса
     *
     * @return array
     */
    public static function getChildParamsDataForTogglerTitle()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS();

        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 1);
        $order->setMin(0)->setMax(100)->setStep(1);
        $text_align = new DesignAtoms\CSSProperty\Select('text-align', t('Расположение текста'), 'left');
        $text_align->setOptions([
            'left' => t('Слева'),
            'center' => t('Центр'),
            'right' => t('Справа'),
        ]);
        $preset->addCSS([
            $text_align,
            $order
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('span')
            ->setTitle(t('Текст заголовка'))
            ->setClass('d-atom-list-toggler-text')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                self::getTextEditParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для текста вопроса
     *
     * @return array
     */
    public static function getChildParamsDataForWrapperTitleImage()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление'), 'row');
        $flex_direction->setOptions([
            'row' => t('В строку'),
            'column' => t('Колонкой'),
        ]);

        $align_items = new DesignAtoms\CSSProperty\Select('align-items', t('Расположение горизонтальное'), 'flex-start');
        $align_items->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Позиционирование элементов внутри по оси X'), 'center');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
            'space-around' => t('Отступ вокруг'),
            'space-between' => t('Отступ между'),
        ]);

        $order = new DesignAtoms\CSSProperty\Number('order', t('Порядок'), 0);
        $order->setMin(0)->setMax(100)->setStep(1);
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '100%'),
            $flex_direction,
            $align_items,
            $justify_content,
            $order
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка для картинки и заголовка'))
            ->setClass('d-atom-list-title-image-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                self::getTextEditParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров для текста ответа
     *
     * @return array
     */
    public static function getChildParamsDataForItemDesc()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([
            'top'    => '10px',
            'right'   => '10px',
            'bottom' => '10px',
            'left'  => '10px'
        ], [])->addCSS([
            new DesignAtoms\CSSProperty\AlignItems('text-align', t('Направление текста'), 'left')
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка описания'))
            ->setClass('d-atom-list-desc')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                self::getTextEditParamsSettings('12px'),
                $preset
            ]);
        return $field->getData();
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
            $childs['item']                 = self::getChildParamsDataForItem();
            $childs['item_opened']          = self::getChildParamsDataForItemOpened();
            $childs['item_image_wrapper']   = self::getChildParamsDataForTopImageWrapper();
            $childs['item_image']           = self::getChildParamsDataForImage();

            $childs['item_info_wrapper']    = self::getChildParamsDataForInfoWrapper();

            $childs['wrapper_title_image']  = self::getChildParamsDataForWrapperTitleImage();
            $childs['toggler_title']        = self::getChildParamsDataForTogglerTitle();

            $childs['toggler']              = self::getChildParamsDataForToggler();
            $childs['toggler_image']        = self::getChildParamsDataForTogglerImage();
            $childs['toggler_image_opened'] = self::getChildParamsDataForTogglerImage('open');

            $childs['item_desc']            = self::getChildParamsDataForItemDesc();
            $childs['item_image_bottom_wrapper'] = self::getChildParamsDataForBottomImageWrapper();
            $childs['item_bottom_image']         = self::getChildParamsDataForBottomImage();
        }
        return $childs;
    }

    /**
     * Создаёт потомков для верхней картинки и возвращает обёртку
     *
     * @param array $item - текущий элемент
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $attrs - массив аттрибутов
     *
     * @return array
     */
    private static function createTopImageChilds($item, $children_params, $attrs)
    {
        $item_image_wrapper = $children_params['item_image_wrapper'];
        $item_image = $children_params['item_image'];
        $item_image['attrs']['src']['value'] = $item['image'];
        $item_image_wrapper['childs'][] = $item_image;
        return $item_image_wrapper;
    }


    /**
     * Создаёт потомков для блока с заголовком
     *
     * @param array $item - текущий элемент
     * @param array $info_wrapper - родительская обёртка
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $attrs - массив аттрибутов
     */
    private static function createTitleChilds($item, &$info_wrapper, $children_params, $attrs)
    {
        $wrapper_title_image = $children_params['wrapper_title_image'];

        //Верхняя картинка в заголовке
        if (($attrs['show_top_image']['value'] == 1)  && ($attrs['image_show_type']['value'] == 'title') && !empty($item['image'])){ //верхняя картика в заголовке
            $wrapper_title_image['childs'][] = self::createTopImageChilds($item, $children_params, $attrs);
        }

        $toggler_title = $children_params['toggler_title'];
        $toggler_title['html'] = $item['title'];

        $item_toggler = $children_params['toggler'];
        if ($attrs['show_plus_icon']['value']) { //Если нужно показать переключатель
            $item_toggler['tag'] = 'a';
        }
        if ($attrs['show_plus_icon']['value']) { //Если нужно показать переключатель
            $toggler_image = $children_params['toggler_image'];
            $item_toggler['childs'][] = $toggler_image;
        }

        $item_toggler['childs'][] = $toggler_title;
        $wrapper_title_image['childs'][] = $item_toggler;
        $info_wrapper['childs'][] = $wrapper_title_image;
    }

    /**
     * Возвращает потомка для обёртки вопросника
     *
     * @param array $children - массив потомков
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $list - массив элементов списка
     * @param array $data - массив данных атома
     * @return array
     */
    private static function getListChilds(&$children, $children_params, $list, $data)
    {
        $attrs = $data['attrs'];
        foreach ($list as $item){
            $item_wrapper = $children_params['item'];

            //Верхняя картинка вне заголовка
            if (($attrs['show_top_image']['value'] == 1) && ($attrs['image_show_type']['value'] == 'top') && !empty($item['image'])){

                $item_wrapper['childs'][] = self::createTopImageChilds($item, $children_params, $attrs);
            }

            $info_wrapper = $children_params['item_info_wrapper'];

            //Заголовок
            if ($attrs['show_title']['value'] == 1) {
                self::createTitleChilds($item, $info_wrapper, $children_params, $attrs);
            }elseif (($attrs['show_top_image']['value'] == 1)  && ($attrs['image_show_type']['value'] == 'title') && !empty($item['image'])){ //верхняя картика в заголовке
                $info_wrapper['childs'][] = self::createTopImageChilds($item, $children_params, $attrs);
            }

            if ($attrs['show_descr']['value'] == 1 && !empty($item['description'])) { //Описание
                $item_desc = $children_params['item_desc'];
                if ($attrs['show_all_open']['value'] == 1){
                    $item_desc['attrs']['class']['value'] .= " d-open";
                }

                $item_desc['html'] = $item['description'];
                $info_wrapper['childs'][] = $item_desc;
            }

            //Нижняя картинка
            if ($attrs['show_bottom_image']['value'] == 1 && !empty($item['endImage'])) {
                $item_image_wrapper = $children_params['item_image_bottom_wrapper'];
                $item_image = self::getEmptySubAtomForRender('d-img', 'img', [
                    "src" => $item['endImage'],
                    "alt" => ""
                ]);
                $item_image_wrapper['childs'][] = $item_image;

                $info_wrapper['childs'][] = $item_image_wrapper;
            }

            $item_wrapper['childs'][] = $info_wrapper;

            $children[] = $item_wrapper;
        }
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
        $children = "<p class='d-no-element'>".t('Список пуст')."</p>";

        if (!empty($data['attrs']['list']['value'])) { //Если форма назначенан
            $list = $data['attrs']['list']['value'];

            $children = [];
            $children_params = self::getChildParamsData();

            self::getListChilds($children, $children_params, $list, $data);
        }
        return $children;
    }

}
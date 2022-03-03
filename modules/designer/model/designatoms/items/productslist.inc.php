<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use Designer\Model\AtomApis\ProductsListApi;
use \Designer\Model\DesignAtoms;

/**
 * Class ProductsList - класс списка фото
 */
class ProductsList extends DesignAtoms\AbstractAtom {
    protected $title = "Товары"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "productslist.svg"; //Картинка компонента
    protected $category_id = null; //id категории

    protected static $init_list_photo_event = 'designer.productslist-update.photos';

    public static $reset_attrs = [
        'category_id',
        'show_always_button_buy',
        'show_always_button_reservation',
        'show_always_button_oneclick'
    ];

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '/resource/js/rs.ajaxpagination.js',
    ];

    /**м
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);

        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $this->setListAttributes();
    }

    /**
     * Установка списка атрибутов для товаров
     */
    function setListAttributes()
    {
        $photoType = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('photo_type', t('Тип обрезки'), 'axy');
        $photoType->setOptions([
            'xy'  => t('По умолчанию (xy)'),
            'axy' => t('Всегда по точным размерам (axy)'),
            'cxy' => t('Обрезка по одной из сторон (cxy)'),
        ]);

        $items_count = new DesignAtoms\Attrs\Number('items_count', t('Количество товаров для показа на одной странице?'), 20);
        $items_count->setMin(1)->setStep(1);

        $show_always_button_buy = new DesignAtoms\Attrs\ToggleButton('show_always_button_buy', t('Показать кнопку купить для редактирования?'), 0);
        $show_always_button_buy->setHint(t('Игнорируя правила движка'));
        $show_always_button_reservation = new DesignAtoms\Attrs\ToggleButton('show_always_button_reservation', t('Показать кнопку заказать для редактирования?'), 0);
        $show_always_button_reservation->setHint(t('Игнорируя правила движка'));
        $show_always_button_oneclick = new DesignAtoms\Attrs\ToggleButton('show_always_button_oneclick', t('Показать кнопку купить в один клик для редактирования?'), 0);
        $show_always_button_oneclick->setHint(t('Игнорируя правила движка'));

        $moreLinkType = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('more_link_type', t('Тип ссылки показать ещё'), 'load_more');
        $moreLinkType->setOptions([
            'load_more'  => t('Подгрузка товаров'),
            'load_category'  => t('Переход в категорию'),
        ]);


        $this->setAttr([
            $items_count,
            new DesignAtoms\Attrs\SelectFieldValueAsTree('category_id', t('Корневая категория'), \RS\Router\Manager::obj()->getAdminUrl('getCategoryList', ['ajax' => 1], 'designer-atomproductslistctrl'))
        ], t('Настройки'))->setAttr([
            $photoType,
            new DesignAtoms\Attrs\AttrSize('photo_width', t('Ширина картинки'), '200px'),
            new DesignAtoms\Attrs\AttrSize('photo_height', t('Высота картинки'), '180px'),
        ], t('Фото'))
            ->setAttr([
            new DesignAtoms\Attrs\ToggleCheckbox('show_barcode', t('Показывать артикул?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_old_price', t('Показывать старую цену?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_price', t('Показывать цену?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buttons', t('Показывать кнопки в товаре?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_load_more', t('Показывать подгрузку товаров?'), 1),
            $moreLinkType,
            new DesignAtoms\Attrs\ToggleCheckbox('load_more_text', t('Текст для кнопки подгрузки товаров?'), t('Показать ещё')),
            new DesignAtoms\Attrs\ToggleCheckbox('auto_load_auto', t('Подгружать товары при прокрутке?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_button', t('Показать кнопку купить или заказать?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_icon', t('Показать иконку кнопки купить?'), 1),
            new DesignAtoms\Attrs\Text('buy_button_text', t('Текст кнопки купить'), t('Купить')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_reservation_icon', t('Показать иконку кнопки заказать?'), 1),
            new DesignAtoms\Attrs\Text('reservation_button_text', t('Текст кнопки заказать'), t('Заказать')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_oneclick', t('Показать кнопку купить в один клик?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_oneclick_icon', t('Показать иконку кнопки купить в один клик?'), 1),
            new DesignAtoms\Attrs\Text('oneclick_button_text', t('Текст кнопки купить в один клик'), t('Купить в 1 клик')),
            $show_always_button_buy,
            $show_always_button_reservation,
            $show_always_button_oneclick,
        ], t('Товар'))->setAttrGroupDebugEvent([ //Добавим события при изменении
            'items_count',
            'photo_type',
            'photo_width',
            'photo_height',
        ], self::$init_list_photo_event);
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $data = parent::getData();
        $data['category_id'] = $this->category_id;
        return $data;
    }


    /**
     * Возвращает массив данных параметров для обёртки товаров
     *
     * @return array
     */
    public static function getChildParamsDataForListWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление'), 'row');
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
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('ul')
            ->setTitle(t('Обёртка для списка товаров'))
            ->setClass('d-productslist-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }


    /**
     * Возвращает массив данных параметров для элемента товара
     *
     * @return array
     */
    public static function getChildParamsDataForItem()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '10px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '25%'),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('li')
            ->setTitle(t('Обёртка одного товара'))
            ->setClass('d-productslist-item')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки одного товара внуктри
     *
     * @return array
     */
    public static function getChildParamsDataForItemWrapper()
    {
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

        $preset->addMarginAndPaddingCSS([
            'top'    => '10px',
            'right'   => '10px',
            'bottom' => '10px',
            'left'  => '10px'
        ], [
            'top'    => '0px',
            'right'   => '5px',
            'bottom' => '0px',
            'left'  => '5px'
        ])->addCSS([
            $flex_direction,
            $justify_content,
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование элементов внутри по оси Y'), 'flex-start'),
        ]);


        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Внутренная колонка товара'))
            ->setClass('d-productslist-item-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#FFFFFFFF'),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для картинки товара
     *
     * @return array
     */
    public static function getChildParamsDataForItemImage()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '10px',
            'left'  => '0px'
        ])->addCSS([
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '180px'),
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина')),
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain'
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle(t('Обёртка для фото товара'))
            ->setClass('d-productslist-item-photo')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обертки с информацией
     *
     * @return array
     */
    public static function getChildParamsDataForItemInfoBlock()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина'))
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка с информацией'))
            ->setClass('d-productslist-item-infoblock')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки цены
     *
     * @return array
     */
    public static function getChildParamsDataForPriceWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '10px',
            'left'  => '0px'
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка для цены'))
            ->setClass('d-productslist-item-cost-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для элемента текста
     *
     * @param string $title - название блока
     * @param string $class - класс блока
     * @param string $tag - тег элемента
     * @param string $height - высота поля (32px)
     * @param string $font_size - размер шрифта
     * @param string $color - цвет
     * @param bool $font_weight_bold - жирный текст или нет
     * @param bool $line_through - показывать зачеркнутым
     *
     * @return array
     */
    public static function getChildParamsDataForItemText($title, $class, $tag = null, $height = null, $font_size = '14px', $color = '#000000FF', $font_weight_bold = false, $line_through = false)
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '10px',
            'left'  => '0px'
        ]);
        if ($height){
            $preset->addCSS([
                new DesignAtoms\CSSProperty\Size('height', t('Высота'), $height),
            ]);
        }
        $textedit = self::getTextEditParamsSettings($font_size, $color);
        if ($font_weight_bold){ //Жирный текст?
            $textedit->setDefaults([
                'font-weight' => 'bold'
            ]);
        }
        if ($line_through){
            $textedit->setDefaults([
                'text-decoration' => 'line-through',
            ]);
        }

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag($tag ? $tag : 'div')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обертки кнопок
     *
     * @return array
     */
    public static function getChildParamsDataForItemButtonsWrapper()
    {
        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление центрирования'), 'column');
        $flex_direction->setOptions([
            'column' => t('колонка'),
            'row' => t('строка'),
        ]);
        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Позиционирование элементов внутри по оси X'), 'center');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
            'space-around' => t('Отступ вокруг'),
            'space-between' => t('Отступ между'),
        ]);
        $align_items = new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Центрирование по оси Y (align-items)'), 'center');
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            $flex_direction,
            $justify_content,
            $align_items
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка для кнопок'))
            ->setClass('d-productslist-button-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для кнопки
     *
     * @param string $title - название блока
     * @param string $background_color - цвет заднего фона
     * @param string $class - класс блока
     *
     * @return array
     */
    public static function getChildParamsDataForItemButton($title, $background_color = '#4F9077FF', $class = 'd-productslist-buy-button')
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'color' => '#FFFFFFFF'
        ]);

        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => $background_color
        ]);

        $align_items = new DesignAtoms\CSSProperty\AlignItems('justify-content', t('Центрирование'), 'center');
        $align_items->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
        ]);
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([
            'top' => '10px',
            'left' => '10px',
            'bottom' => '10px',
            'right' => '10px'
        ], [
            'top' => '0px',
            'left' => '0px',
            'bottom' => '10px',
            'right' => '0px'
        ])->addCSS([
            $align_items,
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина'), '300px'),
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для иконок кнопок
     *
     * @param string $title - название атома
     * @param string $icon_name - название иконки в папке с иконками
     * @param string $class - класс
     *
     * @return array
     */
    public static function getChildParamsDataForButtonIcon($title, $icon_name, $class)
    {
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/'.$icon_name,
        ]);
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '16px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '16px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => '0px',
                'right' => '7px',
                'bottom' => '0px',
                'left' => '0px'
            ]),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('span')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки кнопки загрузить ещё
     *
     * @return array
     */
    public static function getChildParamsDataForLoadMoreWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS([], [
            'top' => '10px',
            'right' => '0px',
            'bottom' => '20px',
            'left' => '0px'
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка для кнопки показать ещё'))
            ->setClass('d-productlist-loadmore-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для кнопки загрузить ещё
     *
     * @return array
     */
    public static function getChildParamsDataForLoadMore()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\VAlignItems('align-self', t('Позиция'), 'center'),
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота'), '40px'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '140px'),
            new DesignAtoms\CSSProperty\VAlignItems('justify-content', t('Позиция текст'), 'center'),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle(t('Кнопка "Показать ещё"'))
            ->setClass('d-productslist-loadmore')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#000000FF'),
                new DesignAtoms\CSSPresets\Border(),
                self::getTextEditParamsSettings('14px', '#FFFFFFFF'),
                $preset
            ]);
        return $item->getData();
    }




    /**
     * Возвращает потомка для фотографий с обёрткой
     *
     * @param array $attrs - установленные аттрибуты
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $product_info - массив данных товара
     *
     * @return array
     * @throws \RS\Exception
     */
    public static function getLinkInfoForItemChilds($attrs, $children_params, $product_info)
    {
        $link = self::getEmptySubAtomForRender('', 'a', [
            'href' => $product_info['url']
        ]);

        $title = $children_params['title'];
        $title['html'] = $product_info['title'];

        $price_wrapper = $children_params['price_wrapper'];

        if ($attrs['show_price']['value']) {
            $cost = $children_params['price'];
            $cost['html'] = $product_info['cost'] . " " . $product_info['currency'];
            $price_wrapper['childs'][] = $cost;
        }

        if (isset($product_info['old_cost']) && $attrs['show_old_price']['value']){
            $old_cost         = $children_params['old_price'];
            $old_cost['html'] = $product_info['old_cost']." ".$product_info['currency'];
            $price_wrapper['childs'][] = $old_cost;
        }

        $link['childs'][] = $title;

        if ($attrs['show_barcode']['value']){
            $barcode = $children_params['barcode'];
            $barcode['html'] = $product_info['barcode'];
            $link['childs'][] = $barcode;
        }
        $link['childs'][] = $price_wrapper;

        return $link;
    }


    /**
     * Заполняет данными сведения о потомках для генерации кнопки купить
     *
     * @param array $attrs - установленные аттрибуты
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $product_info - массив данных товара
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderBuyButton($attrs, $children_params, $product_info)
    {
        $buy_button = $children_params['buy_button'];
        $buy_button['html'] = (($attrs['show_buy_icon']['value']) ? '<span class="d-atom-productslist-icon-buy"></span> ': "").$attrs['buy_button_text']['value'];
        $buy_button['attrs']['href']['value']  = $product_info['buttons']['buy'];
        $buy_button['attrs']['title']['value'] = t('В корзину');
        //$buy_button['attrs']['class']['value'] .= " addToCart rs-to-cart";
        return $buy_button;
    }

    /**
     * Заполняет данными сведения о потомках для генерации кнопки зарезервировать
     *
     * @param array $attrs - установленные аттрибуты
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $product_info - массив данных товара
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderReservationButton($attrs, $children_params, $product_info)
    {
        $reservation_button = $children_params['reservation_button'];
        $reservation_button['html'] = (($attrs['show_reservation_icon']['value']) ? '<span class="d-atom-productslist-icon-reservation"></span> ': "").$attrs['reservation_button_text']['value'];
        $reservation_button['attrs']['href']['value']  = $product_info['buttons']['reservation'];
        $reservation_button['attrs']['title']['value'] = t('Заказать');
        $reservation_button['attrs']['class']['value'] .= " inDialog rs-in-dialog";
        return $reservation_button;
    }

    /**
     * Заполняет данными сведения о потомках для генерации кнопки купить в один клик
     *
     * @param array $attrs - установленные аттрибуты
     * @param array $children_params - массив данных с установками для разных типов
     * @param array $product_info - массив данных товара
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderOneClickButton($attrs, $children_params, $product_info)
    {
        $oneclick_button = $children_params['oneclick_button'];
        $oneclick_button['html'] = (($attrs['show_oneclick_icon']['value']) ? '<span class="d-atom-productslist-icon-oneclick"></span> ' : "") . $attrs['oneclick_button_text']['value'];
        $oneclick_button['attrs']['href']['value']  = $product_info['buttons']['oneclick'];
        $oneclick_button['attrs']['title']['value'] = t('Купить в 1 клик');
        $oneclick_button['attrs']['class']['value'] .= " inDialog rs-in-dialog";
        return $oneclick_button;
    }

    /**
     * Возвращает потомка для фотографий с обёрткой
     *
     * @param array $children - массив детей категории
     * @param array $data - данные для текущего атома
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Catalog\Model\Orm\Dir $dir - объект каталога
     * @param integer $page - нужная страница
     *
     * @throws \RS\Exception
     */
    public static function getListWrapperChilds(&$children, $data, $children_params, $dir, $page = 1)
    {
        $attrs = $data['attrs'];

        //Подгрузим информацию по товарам
        $api = new ProductsListApi();
        $list = $api->getProducts($dir['id'] ? $dir['id'] : 0, $page, $attrs['items_count']['value'], [
            'width'  => $attrs['photo_width']['value'],
            'height' => $attrs['photo_height']['value'],
            'type'   => $attrs['photo_type']['value'],
        ]);

        if (!empty($list)){
            $child = $children_params['list_wrapper'];

            $n = 0;
            foreach ($list as $product_info){
                $n++;
                $item_wrapper        = $children_params['item'];
                $item_inside_wrapper = $children_params['item_wrapper'];

                $image = $children_params['image'];
                $image['attrs']['style']['value'] = "background-image: url(".$product_info['image']['url'].")";
                $image['attrs']['title']['value'] = $product_info['image']['title'];
                $image['attrs']['href']['value']  = $product_info['url'];
                $item_inside_wrapper['childs'][] = $image;


                $info_block = $children_params['info_block'];
                $info_block['childs'][] = self::getLinkInfoForItemChilds($attrs, $children_params, $product_info);



                if ($attrs['show_buttons']['value']) {
                    $buttons_wrapper = $children_params['buttons_wrapper'];
                    if ($attrs['show_buy_button']['value'] && isset($product_info['buttons']['buy'])){
                        $buttons_wrapper['childs'][] = self::getFillChildsDataForRenderBuyButton($attrs, $children_params, $product_info);
                    }

                    if ($attrs['show_buy_button']['value'] && isset($product_info['buttons']['reservation'])){
                        $buttons_wrapper['childs'][] = self::getFillChildsDataForRenderReservationButton($attrs, $children_params, $product_info);
                    }

                    if ($attrs['show_buy_oneclick']['value'] && isset($product_info['buttons']['oneclick'])) {
                        $buttons_wrapper['childs'][] = self::getFillChildsDataForRenderOneClickButton($attrs, $children_params, $product_info);
                    }
                    $info_block['childs'][] = $buttons_wrapper;
                }

                $item_inside_wrapper['childs'][] = $info_block;

                $item_wrapper['childs'][] = $item_inside_wrapper;
                $child['childs'][] = $item_wrapper;
            }

            $children[] = $child;
            if ($data['attrs']['show_load_more']['value'] && count($list) >= $attrs['items_count']['value']){
                $children[] = self::getAutoLoadChilds($data, $children_params, $dir, $data['page'] ? $data['page'] : 1);
            }
        }else{
            $children = "<p class='d-no-element'>" . t('Категория не назначена') . "</p>";
        }
    }

    /**
     * Возвращает потомка для фотографий с обёрткой
     *
     * @param array $data - данные для текущего атома
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Catalog\Model\Orm\Dir $dir - объект каталога
     * @param integer $page - нужная страница
     *
     * @return array
     * @throws \RS\Exception
     */
    public static function getAutoLoadChilds($data, $children_params, $dir, $page = 1)
    {
        $attrs = $data['attrs'];
        $item_wrapper = $children_params['load_more_wrapper'];
        $link = $children_params['load_more'];

        if (isset($attrs['more_link_type']) && $attrs['more_link_type']['value'] == 'load_category'){
            $config = \RS\Config\Loader::byModule('catalog');
            if ($config['show_all_products']){ //Если доступно показывать Все
                $link['attrs']['href']['value'] = $dir['id'] ? $dir->getUrl() : "/catalog/all/";
            }elseif ($dir['id'] > 0){
                $link['attrs']['href']['value'] = $dir->getUrl();
            }else{
                $link['attrs']['onclick']['value'] = "return false;";
            }

            $link['attrs']['target']['value'] = '_blank';
        }else{
            $link['attrs']['class']['value'] .= " rs-ajax-paginator";
            $link['attrs']['data-url']['value'] = \RS\Router\Manager::obj()->getUrl('designer-front-productslist', [
                'category' => $dir['_alias'] ? $dir['_alias'] : 'all',
                'ajax' => 1,
                'id' => $data['id'],
                'p' => $page + 1
            ]);
            if ($data['attrs']['auto_load_auto']['value']) {
                $link['attrs']['data-click-on-scroll']['value'] = 1;
            }
            $link['attrs']['data-append-element']['value'] = '.d-productslist-wrapper';
        }

        $link['html'] = $attrs['load_more_text']['value'];
        $item_wrapper['childs'][] = $link;
        $child['childs'][] = $item_wrapper;
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
        if (!$childs) {
            $childs['list_wrapper']  = self::getChildParamsDataForListWrapper();
            $childs['item']          = self::getChildParamsDataForItem();
            $childs['item_wrapper']  = self::getChildParamsDataForItemWrapper();
            $childs['image']         = self::getChildParamsDataForItemImage();
            $childs['info_block']    = self::getChildParamsDataForItemInfoBlock();
            $childs['title']         = self::getChildParamsDataForItemText(t('Название товара'), 'd-productslist-item-title', null, '32px', '16px');
            $childs['barcode']       = self::getChildParamsDataForItemText(t('Артикул'), 'd-productslist-item-barcode', null, null);
            $childs['price_wrapper'] = self::getChildParamsDataForPriceWrapper();
            $childs['old_price']     = self::getChildParamsDataForItemText(t('Зачеркнутая цена'), 'd-productslist-item-old-cost', 'span', null, '12px', '#000000FF', false, true);
            $childs['price']         = self::getChildParamsDataForItemText(t('Цена'), 'd-productslist-item-cost', 'span', null, '18px', '#000000FF', true);

            $childs['buttons_wrapper'] = self::getChildParamsDataForItemButtonsWrapper();

            $childs['buy_button']      = self::getChildParamsDataForItemButton(t('Кнопка купить'));
            $childs['buy_button_icon'] = self::getChildParamsDataForButtonIcon(t('Иконка кнопки купить'), 'basket_white.svg', 'd-atom-productslist-icon-buy');

            $childs['reservation_button']      = self::getChildParamsDataForItemButton(t('Кнопка заказать'), '#B10404FF', 'd-productslist-order-button');
            $childs['reservation_button_icon'] = self::getChildParamsDataForButtonIcon(t('Иконка кнопки заказать'), 'reservation_white.svg', 'd-atom-productslist-icon-reservation');

            $childs['oneclick_button']      = self::getChildParamsDataForItemButton(t('Кнопка купить в один клик'), '#0461B1FF', 'd-productslist-oneclick-button');
            $childs['oneclick_button_icon'] = self::getChildParamsDataForButtonIcon(t('Иконка кнопки купить в 1 клик'), 'phone_white.svg', 'd-atom-productslist-icon-oneclick');

            $childs['load_more_wrapper'] = self::getChildParamsDataForLoadMoreWrapper();
            $childs['load_more']         = self::getChildParamsDataForLoadMore();
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
        $children = "<p class='d-no-element'>" . t('Категория не назначена') . "</p>";

        if ($data['category_id'] !== false) { //Если форма назначена

            $dir = new \Catalog\Model\Orm\Dir($data['category_id']);
            if ((int)$data['category_id'] > 0){
                if (!$dir['id']) {
                    return "<p class='d-no-element'>" . t('Категория с id=%0 не найдена или удалена', [$data['category_id']]) . "</p>";
                }
                if (!$dir['public']) {
                    return "<p class='d-no-element'>" . t('Категория с id=%0 выключена', [$data['category_id']]) . "</p>";
                }
            }

            $children_params = self::getChildParamsData();

            $children = [];
            self::getListWrapperChilds($children, $data, $children_params, $dir, $data['page'] ? $data['page'] : 1);
        }

        return $children;
    }

    /**
     * Заполняет значения по умолчанию в данные атома после добавления из пресета
     *
     * @param array $data - данные атома из пресета
     * @param array $preset - все данные пресета
     */
    public static function setDefaultsAfterPresetInsert(&$data, $preset){
        $data['category_id'] = 0;
    }
}
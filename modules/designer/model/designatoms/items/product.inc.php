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
 * Class Product - класс товар
 */
class Product extends DesignAtoms\AbstractAtom {
    protected $title = "Товар"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "product.svg"; //Картинка компонента
    protected $product_id  = 0; //id товара
    protected $offer_sortn = false; //Номер комплектации

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '/resource/js/lightgallery/lightgallery-nojquery.min.js',
        '/resource/js/swiper/swiper.min.js',
        '%designer%/atoms/product.js',
    ];
    public static $public_css = [//Массив дополнительных CSS, которые нужно подключить в публичной части
        '/resource/css/common/lightgallery/css/lightgallery.min.css',
        '/resource/css/common/swiper/swiper.min.css'
    ];
    public static $reset_attrs = [ //Массив атррибутов для блока
        'product_id',
        'offer_sortn'
    ];
    public static $virtual_attrs   = [ //Массив виртуальных аттрибутов
        'show_not_in_stock',
        'show_always_button_buy',
        'show_always_button_reservation',
        'show_always_button_oneclick'
    ];

    protected static $init_slider_event         = 'designer.product-reinit.slider';
    protected static $init_slider_event_reload  = 'designer.product-reinit.slider-reload';
    protected static $init_product_event_reload = 'designer.product-reinit.product-reload';
    protected static $init_product_change_panel = 'designer.product-reinit.product-change-panel';

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

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

        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => '#FFFFFFFF'
        ]);

        $this->setCSSTitle(t('Обёртка товара'));

        $this->addCSSPreset([
            $background,
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $typeShow = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('type_show', t('Тип позиционирования'), 'horizontal');
        $typeShow->setOptions([
            'horizontal' => t('Горизонтально'),
            'vertical' => t('Вертикально')
        ]);
        $typeShow->setVisible(false);

        //Зададим доп. параметры отображения
        $this->setAttr([
            new DesignAtoms\Attrs\ResetRootData(null, t('Сменить товар?')),
            $typeShow,
            new DesignAtoms\Attrs\ToggleCheckbox('show_photos', t('Выводить фото товара?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_product', t('Выводить выводить данные товара?'), 1)
        ]);
        $this->setSliderInfoAttrs();
        $this->setProductInfoAttrs();
    }

    /**
     * Добавляет аттрибуты относящиеся непосредственно к атрибутам сведений слайдера
     */
    function setSliderInfoAttrs()
    {
        $photoType = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('photo_type', t('Тип обрезки'), 'axy');
        $photoType->setOptions([
            'xy'  => t('По умолчанию (xy)'),
            'axy' => t('Всегда по точным размерам (axy)'),
            'cxy' => t('Обрезка по одной из сторон (cxy)'),
        ]);

        $thumb_count = new DesignAtoms\Attrs\Number('thumb_count', t('Количество иконок для показа (>=767px)?'), 4);
        $thumb_count->setMin(1)->setStep(1);

        $photo_autoplay_speed = new DesignAtoms\Attrs\Number('photo_autoplay_speed', t('Скорость автопроигрывания в милисекундах в слайдере?'), 2500);
        $photo_autoplay_speed->setMin(100)->setStep(100);

        $photoEffect = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('photo_effect', t('Эфект слайдера'), '');
        $photoEffect->setOptions([
            ''  => t('Пролистывание'),
            'fade'  => t('Появление'),
            'cube'  => t('3d куб'),
            'coverflow'  => t('Обложки'),
            'flip'  => t('3d перевертывание'),
        ]);
        $photoEffect->initDebugEventOnChange(self::$init_slider_event_reload);

        $this->setAttr([
            new DesignAtoms\Attrs\AttrSize('photo_width', t('Ширина картинки'), '500px'),
            new DesignAtoms\Attrs\AttrSize('photo_height', t('Высота картинки'), '400px'),
            new DesignAtoms\Attrs\AttrSize('photo_big_width', t('Ширина картинки для детального просмотра'), '800px'),
            new DesignAtoms\Attrs\AttrSize('photo_big_height', t('Высота картинки для детального просмотра'), '700px'),
            $photoType,
            $photoEffect,
            new DesignAtoms\Attrs\ToggleCheckbox('show_thumbs', t('Показывать миниатюры в слайдере?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_thumb_arrows', t('Показывать стрелки пролистывания для миниатюры в слайдере?'), 1),
            $thumb_count,
            new DesignAtoms\Attrs\AttrSize('thumb_width', t('Ширина иконки слайдера'), '60px'),
            new DesignAtoms\Attrs\AttrSize('thumb_height', t('Высота иконки слайдера'), '60px'),
            new DesignAtoms\Attrs\ToggleCheckbox('photo_show_loop', t('Зациклить слайдер?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('photo_show_pagination', t('Показать значки пагинации в слайдере?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('photo_autoplay', t('Показать автопроигрывание в слайдере?'), 0),
            $photo_autoplay_speed,
        ], t('Фото'))->setAttrGroupDebugEvent([//Добавим событи я при изменении
            'photo_show_loop',
            'photo_show_pagination',
            'show_thumb_arrows',
            'show_thumbs',
        ], self::$init_slider_event)
        ->setAttrGroupDebugEvent([
            'photo_width',
            'photo_height',
            'photo_big_width',
            'photo_big_height',
            'photo_type',
            'thumb_width',
            'thumb_count',
            'thumb_height',
        ], self::$init_product_event_reload);
    }

    /**
     * Добавляет аттрибуты относящиеся непосредственно к атрибутам сведений товара
     */
    function setProductInfoAttrs()
    {
        $leftPanelSize  = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('left_column', t('Размер левой панели. При разрешении ≥992px'), '6');
        $leftPanelSize->setOptions([
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
        ]);
        $leftPanelSize->initDebugEventOnChange(self::$init_product_change_panel);

        $rightPanelSize = new \Designer\Model\DesignAtoms\Attrs\Text('right_column', t('Размер правой панели'), '6');
        $rightPanelSize->setVisible(false);

        $headerShowPlace = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('header_show_place', t('Место показа названия товара'), 'right');
        $headerShowPlace->setOptions([
            'top'  => t('Наверху'),
            'right'  => t('Справа'),
        ]);

        $headerTag = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('header_tag', t('Тег заголовка'), 'h1');
        $headerTag->setOptions([
            'h1'  => 'h1',
            'h2'  => 'h2',
            'h3'  => 'h3',
            'h4'  => 'h4',
            'h5'  => 'h5',
            'div' => 'div'
        ]);

        $show_always_button_buy = new DesignAtoms\Attrs\ToggleButton('show_always_button_buy', t('Показать кнопку купить для редактирования?'), 0);
        $show_always_button_buy->setHint(t('Игнорируя правила движка'));
        $show_always_button_reservation = new DesignAtoms\Attrs\ToggleButton('show_always_button_reservation', t('Показать кнопку заказать для редактирования?'), 0);
        $show_always_button_reservation->setHint(t('Игнорируя правила движка'));
        $show_always_button_oneclick = new DesignAtoms\Attrs\ToggleButton('show_always_button_oneclick', t('Показать кнопку купить в один клик для редактирования?'), 0);
        $show_always_button_oneclick->setHint(t('Игнорируя правила движка'));

        $this->setAttr([
            $leftPanelSize,
            $rightPanelSize,
            $headerShowPlace,
            $headerTag,
            new DesignAtoms\Attrs\ToggleCheckbox('show_offer_title', t('Показать название комплектации?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_barcode', t('Показать артикул?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_brand', t('Показать бренд?'), 1),

            new DesignAtoms\Attrs\ToggleCheckbox('show_short_description', t('Показать короткое описание?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buttons', t('Показать кнопки товара?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_button', t('Показать кнопку купить или заказать?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_icon', t('Показать иконку кнопки купить?'), 1),
            new DesignAtoms\Attrs\Text('buy_button_text', t('Текст кнопки купить'), t('Купить')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_reservation_icon', t('Показать иконку кнопки заказать?'), 1),
            new DesignAtoms\Attrs\Text('reservation_button_text', t('Текст кнопки заказать'), t('Заказать')),
            new DesignAtoms\Attrs\ToggleCheckbox('show_amount', t('Показывать количество?'), 0),
            new DesignAtoms\Attrs\ToggleCheckbox('show_old_price', t('Показывать старую цену?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_price', t('Показывать цену?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_buy_oneclick', t('Показать кнопку купить в один клик?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_oneclick_icon', t('Показать иконку кнопки купить в один клик?'), 1),
            new DesignAtoms\Attrs\Text('oneclick_button_text', t('Текст кнопки купить в один клик'), t('Купить в один клик')),
            $show_always_button_buy,
            $show_always_button_reservation,
            $show_always_button_oneclick,
            new DesignAtoms\Attrs\ToggleCheckbox('show_description', t('Показать полное описание?'), 1),
            new DesignAtoms\Attrs\ToggleButton('show_stock', t('Показать блок с наличием?'), 1),
            new DesignAtoms\Attrs\ToggleButton('show_not_in_stock', t('Показать не в наличии для редактирования?'), 0)
        ], t('Товар'));
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $data = parent::getData();
        $data['product_id'] = $this->product_id;
        $data['offer_sortn'] = $this->offer_sortn;

        return $data;
    }

    /**
     * Возвращает обычные настройки пресета для текста
     *
     * @param string $margin_bottom - отступ снизу
     * @param \Designer\Model\DesignAtoms\CSSPresets\AbstractCssPreset $preset - отступ снизу
     *
     * @return DesignAtoms\CSSPresets\AbstractCssPreset
     */
    private static function getUsualTextPreset($margin_bottom = '10px', $preset = null)
    {
        if (!$preset){
            $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        }
        $preset->addCSS([
            new DesignAtoms\CSSProperty\AlignItems('text-align', t('Расположение'), 'left'),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => '0px',
                'left' => '0px',
                'bottom' => $margin_bottom,
                'right' => '0px',
            ]),
        ]);
        return $preset;
    }


    /**
     * Возвращает массив данных параметров для правой половины товара
     *
     * @return array
     */
    public static function getChildParamsDataForProductRight()
    {
        //Заголовки
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Правая половина товара'))
            ->setClass('d-atom-product-right')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                self::getTextEditParamsSettings()
            ]);
        return $item->getData();
    }


    /**
     * Возвращает массив данных параметров для заголовка товара
     *
     * @return array
     */
    public static function getChildParamsDataForHeader()
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'font-family' => 'Arial',
            'font-size' => '24px',
            'font-weight' => 'bold'
        ]);
        //Заголовки
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Заголовок товара'))
            ->setClass('d-atom-product-header')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                self::getUsualTextPreset('15px')
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для под заголовка товара
     *
     * @return array
     */
    public static function getChildParamsDataForSubHeader()
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'font-family' => 'Arial',
            'font-size' => '16px',
            'font-weight' => 'bold'
        ]);
        //Заголовки
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Заголовок комплектации'))
            ->setClass('d-atom-product-subheader')
            ->addCSSPreset([
                $textedit,
                new DesignAtoms\CSSPresets\Border(),
                self::getUsualTextPreset()
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для текстовых блоков
     *
     * @param string $title - заголовок
     * @param string $class - класс
     * @param string $font_size - размер шрифта (например 14px)
     * @param bool $line_through - показывать зачеркнутым
     * @return array
     */
    public static function getChildParamsDataForText($title, $class, $font_size = null, $line_through = false)
    {
        $item = new DesignAtoms\Items\SubAtom();

        $textedit = self::getTextEditParamsSettings();
        if ($font_size){
            $textedit->setDefaults([
                'font-size' => $font_size,
            ]);
        }
        if ($line_through){
            $textedit->setDefaults([
                'text-decoration' => 'line-through',
            ]);
        }

        $item->setTag('div')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                $textedit,
                new DesignAtoms\CSSPresets\Border(),
                self::getUsualTextPreset()
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки количества
     *
     * @param string $title - заголовок
     * @param string $class - класс
     * @return array
     */
    public static function getChildParamsDataForAmountWrapper()
    {
        $item = new DesignAtoms\Items\SubAtom();

        $item->setTag('div')
            ->setTitle(t('Обёртка для ввода количества'))
            ->setClass('d-atom-product-amount-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border()
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для количества
     *
     * @param string $title - заголовок
     * @param string $class - класс
     * @return array
     */
    public static function getChildParamsDataForAmount()
    {
        $item = new DesignAtoms\Items\SubAtom();

        $border = new DesignAtoms\CSSPresets\Border();
        $border->setDefaults([
            'border' => [
                'top' => '1px',
                'left' => '1px',
                'bottom' => '1px',
                'right' => '1px',
                'border-type' => 'solid',
                'border-color' => '#C0C0C0FF'
            ]
        ]);

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота'), '26px'),
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина'), '50px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '4px',
                'right'   => '6px',
                'bottom' => '4px',
                'left'  => '6px'
            ]),
        ]);

        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'font-family' => 'Arial',
            'font-size' => '12px',
            'font-weight' => 'bold'
        ]);

        $item->setTag('input')
            ->setTitle(t('Ввод количества'))
            ->setClass('d-atom-product-amount')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $border,
                $textedit,
                self::getUsualTextPreset('10px', $preset)
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для "В наличии"
     *
     * @param string $title - название атома
     * @param string $color - цвет
     * @param string $class - класс
     * @return array
     */
    public static function getChildParamsDataForInStock($title, $color = '#008000FF', $class = "d-atom-product-instock")
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'color' => $color
        ]);
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\AlignItems('text-align', t('Расположение'), 'left'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => '0px',
                'left' => '0px',
                'bottom' => '10px',
                'right' => '0px',
            ]),
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина')),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                $textedit,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки кнопок
     *
     * @return array
     */
    public static function getChildParamsDataForButtonsWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина')),
            self::getFlexDirectionParamsData(),
            self::getJustifyContentParamsData(),
            self::getAlignItemsParamsData()
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка кнопок действий'))
            ->setClass('d-atom-product-button-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для кнопок
     *
     * @param string $title - название атома
     * @param string $background_color - цвет заднего фона
     * @param string $class - класс
     * @return array
     */
    public static function getChildParamsDataForButton($title, $background_color = '#4F9077FF', $class = "d-atom-product-button")
    {
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'color' => '#FFFFFFFF'
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => $background_color
        ]);
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\AlignItems('justify-content', t('Расположение'), 'center'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '180px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => '0px',
                'left' => '7px',
                'bottom' => '10px',
                'right' => '7px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top' => '15px',
                'left' => '15px',
                'bottom' => '15px',
                'right' => '15px'
            ]),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                $background,
                $textedit,
                new DesignAtoms\CSSPresets\Border(),
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
     * Возвращает массив данных параметров для обёртки верхней галлереи
     *
     * @return array
     */
    public static function getChildParamsDataForTopGallery()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS();
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Верхний слайдер'))
            ->setClass('d-top-swiper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для пагинации
     *
     * @return array
     */
    public static function getChildParamsDataForTopGalleryPagination()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Color('--swiper-theme-color', t('Цвет активного круга')),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Пагинация'))
            ->setClass('swiper-pagination')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для пагинации
     *
     * @return array
     */
    public static function getChildParamsDataForTopGalleryPaginationItem()
    {
        $preset  = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $opacity = new DesignAtoms\CSSProperty\Number('opacity', t('Прозрачность'), 0.2);
        $opacity->setMin(0)->setStep(0.1)->setMax(1);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top'    => '0px',
                'right'   => '4px',
                'bottom' => '0px',
                'left'  => '0px'
            ]),
            $opacity,
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '8px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '8px')
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => '#000000FF'
        ]);
        $border = new DesignAtoms\CSSPresets\Border();
        $border->setDefaults([
            'border-radius' => [
                'top'    => '50%',
                'right'   => '50%',
                'bottom' => '50%',
                'left'  => '50%'
            ]
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('span')
            ->setTitle(t('Значки пагинации'))
            ->setClass('swiper-pagination-bullet')
            ->addCSSPreset([
                $background,
                $border,
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для стрелок галлереи
     *
     * @param string $title - название атома
     * @param string $class - класс
     * @param string $color - цвет иконок в формате #FFFFFFFF
     * @param string $size - размер в пикселях напрмер 44px
     *
     * @return array
     */
    public static function getChildParamsDataForGalleryArrows($title, $class, $color = "", $size = "", $margin_top = "0px")
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => $margin_top,
                'right' => '0px',
                'bottom' => '0px',
                'left' => '0px'
            ]),
            new DesignAtoms\CSSProperty\Color('--swiper-theme-color', t('Цвет стрелки'), $color),
            new DesignAtoms\CSSProperty\Size('--swiper-navigation-size', t('Размер стрелки'), $size),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle($title)
            ->setClass($class)
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для внешней обёртки иконок
     *
     * @return array
     */
    public static function getChildParamsDataForThumbsWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top' => '10px',
                'right' => '0px',
                'bottom' => '10px',
                'left' => '0px'
            ]),
            new DesignAtoms\CSSProperty\AlignItems('justify-content', t('Расположение'), 'center'),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Внешняя обёртка иконок слайдера'))
            ->setClass('d-product-swiper-thumbs-wrapper')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для внутренней обёртки иконок
     *
     * @return array
     */
    public static function getChildParamsDataForThumbs()
    {
        $width = new DesignAtoms\CSSProperty\Size('width', t('Ширина'));
        $width->initDebugEventOnChange(self::$init_slider_event);
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'))
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Обёртка иконок слайдера'))
            ->setClass('swiper-wrapper-thumbs')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки иконок
     *
     * @return array
     */
    public static function getChildParamsDataForThumbIcon()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Иконка миниатюры слайдера'))
            ->setClass('swiper-wrapper-thumbs .swiper-slide')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
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
            $childs['photo_top_gallery']         = self::getChildParamsDataForTopGallery();
            $childs['photo_top_pagination_item'] = self::getChildParamsDataForTopGalleryPaginationItem();
            $childs['photo_top_pagination']      = self::getChildParamsDataForTopGalleryPagination();
            $childs['photo_top_gallery_arrows']  = self::getChildParamsDataForGalleryArrows(t('Стрелки для верхной галлереи'), 'd-top-swiper .d-swiper-arrow');

            $childs['photo_thumbs_out_wrapper'] = self::getChildParamsDataForThumbsWrapper();
            $childs['photo_thumbs_wrapper']     = self::getChildParamsDataForThumbs();
            $childs['photo_thumbs_icon']        = self::getChildParamsDataForThumbIcon();
            $childs['photo_thumbs_arrows']      = self::getChildParamsDataForGalleryArrows(t('Стрелки для галереи иконок'), 'd-product-swiper-thumbs-wrapper .d-swiper-arrow', '', '16px', '-6px');

            $childs['product_right'] = self::getChildParamsDataForProductRight();
            $childs['header']        = self::getChildParamsDataForHeader();
            $childs['sub_header']    = self::getChildParamsDataForSubHeader();
            $childs['barcode']       = self::getChildParamsDataForText(t('Артикул'), 'd-atom-product-barcode');
            $childs['brand']         = self::getChildParamsDataForText(t('Бренд'), 'd-atom-product-brand');

            $childs['short_description']  = self::getChildParamsDataForText(t('Краткое описание'), 'd-atom-product-shortdescription');
            $childs['old_cost']           = self::getChildParamsDataForText(t('Старая цена'), 'd-atom-product-old-cost', '16px');
            $childs['cost']               = self::getChildParamsDataForText(t('Цена'), 'd-atom-product-cost', '24px');
            $childs['amount_wrapper']     = self::getChildParamsDataForAmountWrapper();
            $childs['amount']             = self::getChildParamsDataForAmount();
            $childs['in_stock']           = self::getChildParamsDataForInStock(t('В наличии'));
            $childs['out_stock']          = self::getChildParamsDataForInStock(t('Не в наличии'), '#FF0000FF', 'd-atom-product-instock-red');
            $childs['buttons_wrapper']    = self::getChildParamsDataForButtonsWrapper();
            $childs['buy_button']         = self::getChildParamsDataForButton(t('Кнопка купить'));
            $childs['buy_button_icon']    = self::getChildParamsDataForButtonIcon(t('Иконка кнопки купить'), 'basket_white.svg', 'd-atom-product-icon-buy');

            $childs['reservation_button']      = self::getChildParamsDataForButton(t('Кнопка заказать'), '#B10404FF', 'd-atom-product-button-order');
            $childs['reservation_button_icon'] = self::getChildParamsDataForButtonIcon(t('Иконка кнопки заказать'), 'reservation_white.svg', 'd-atom-product-icon-reservation');
            $childs['oneclick_button']         = self::getChildParamsDataForButton(t('Кнопка купить в 1 клик'), '#0461B1FF', 'd-atom-product-button-buy-one-click');
            $childs['oneclick_button_icon']    = self::getChildParamsDataForButtonIcon(t('Иконка кнопки купить в 1 клик'), 'phone_white.svg', 'd-atom-product-icon-oneclick');
            $childs['description']             = self::getChildParamsDataForText(t('Описание'), 'd-atom-product-description');
        }
        return $childs;
    }


    /**
     * Заполняет данными сведения о потомках для генерации описания товара
     *
     * @param \Catalog\Model\Orm\Product $product - товар
     * @return array
     */
    private static function getFillChildsDataForRenderProductDescription($product)
    {
        $block  = self::getEmptySubAtomForRender('d-atom-product-description');
        $block['html'] = $product['description'];

        $column = self::getEmptySubAtomForRender('d-col-12');
        $column['childs'][] = $block;

        $wrapper_child = self::getEmptySubAtomForRender('d-row d-product-row');
        $wrapper_child['childs'][] = $column;

        return $wrapper_child;
    }

    /**
     * Возвращает фото товара
     *
     * @param array $data - установленные данные атома
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getProductImages($data, $product)
    {
        $attrs = $data['attrs'];

        $images = [];
        $images_arr = $product->getImages();
        $offer_sortn = $attrs['offer_sortn'];

        $need_all_photos = true; //Нужны все фото?
        if ($product->isOffersUse() && $product['offers']['items'][$offer_sortn]) { //Если есть комплектации
            /**
             * @var \Catalog\Model\Orm\Offer $offer
             */
            $offer        = $product['offers']['items'][$offer_sortn];
            $offer_photos = $offer['photos_arr'];
            if (!empty($offer_photos)){
                $need_all_photos = false;
                foreach ($images_arr as $photo_id => $photo){
                    if (in_array($photo_id, $offer_photos)){ //Отображаем все фото комплектации
                        $images[] = $photo;
                    }
                }
            }
        }

        if ($need_all_photos){ //Отображаем все фото товара
            foreach ($images_arr as $photo_id=>$photo){
                $images[] = $photo;
            }
        }

        if (empty($images_arr)){ //Если фото вообще не добавлено
            $images[] = $product->getMainImage();
        }
        return $images;
    }

    /**
     * Заполняет данными сведения о потомках для генерации массива иконок для верхнего слайдера
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Photo\Model\Orm\Image[] $images - массив картинко
     * @param array $photo_params - массив параметров картинок
     *
     * @return array
     *
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderPhotoTopIcons($data, $children_params, $images, $photo_params)
    {
        $img_childs = [];
        foreach ($images as $image){
            /**
             * @var \Photo\Model\Orm\Image $image
             */
            $img = self::getEmptySubAtomForRender('', 'img', [
                'src' => $image->getUrl($photo_params['thumb_width'], $photo_params['thumb_height'], $photo_params['type'])
            ]);

            $a = self::getEmptySubAtomForRender('', 'a');
            $a['childs'][] = $img;

            $swiper_slide = self::getEmptySubAtomForRender('swiper-slide');
            $swiper_slide['childs'][] = $a;
            $img_childs[] = $swiper_slide;
        }

        $swiper_wrapper = self::getEmptySubAtomForRender('swiper-wrapper');
        $swiper_wrapper['childs'] = $img_childs;

        $swiper_container = self::getEmptySubAtomForRender('swiper-container swiper-wrapper-thumbs', 'div', [
            'id' => 'd-swiper-top-thumbs-' . $data['id'],
        ]);
        $swiper_container['childs'][] = $swiper_wrapper;

        if ($data['attrs']['show_thumb_arrows']['value'] && !empty($images) && count($images) > 1){
            $arrow = $children_params['photo_thumbs_arrows'];
            $arrow['attrs']['id']['value']    = 'd-swiper-thumbs-nav-next-' . $data['id'];
            $arrow['attrs']['class']['value'] = 'swiper-button-next d-swiper-arrow';
            $swiper_container['childs'][] = $arrow;

            $arrow = $children_params['photo_thumbs_arrows'];
            $arrow['attrs']['id']['value']    = 'd-swiper-thumbs-nav-prev-' . $data['id'];
            $arrow['attrs']['class']['value'] = 'swiper-button-next d-swiper-arrow';
            $swiper_container['childs'][] = $arrow;
        }

        $top_swiper_thumbs_wrapper = $children_params['photo_thumbs_out_wrapper'];
        $top_swiper_thumbs_wrapper['childs'][] = $swiper_container;
        return $top_swiper_thumbs_wrapper;
    }


    /**
     * Заполняет данными сведения о потомках для генерации массива фото для верхнего слайдера
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     *
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderPhotosTop($data, $children_params, $product)
    {
        $children = [];
        $attrs = $data['attrs'];
        $photo_params = [ //Соберём параметры
            'big_width'    => intval($attrs['photo_big_width']['value']),
            'big_height'   => intval($attrs['photo_big_height']['value']),
            'width'        => intval($attrs['photo_width']['value']),
            'height'       => intval($attrs['photo_height']['value']),
            'thumb_width'  => intval($attrs['thumb_width']['value']),
            'thumb_height' => intval($attrs['thumb_height']['value']),
            'type'         => $attrs['photo_type']['value']
        ];
        $images = self::getProductImages($data, $product);

        $top_swiper_wrapper = $children_params['photo_top_gallery'];

        $img_childs = [];
        foreach ($images as $image){
            /**
             * @var \Photo\Model\Orm\Image $image
             */
            $img = self::getEmptySubAtomForRender('', 'img', [
                'src' => $image->getUrl($photo_params['width'], $photo_params['height'], $photo_params['type']),
                'alt' => $image['title'] ? $image['title'] : $product['title'],
            ]);

            $a = self::getEmptySubAtomForRender('', 'a', [
                'href' => $image->getUrl($photo_params['big_width'], $photo_params['big_height'], $photo_params['type'])
            ]);
            $a['childs'][] = $img;
            
            $swiper_slide = self::getEmptySubAtomForRender('swiper-slide');
            $swiper_slide['childs'][] = $a;
            $img_childs[] = $swiper_slide;
        }

        $swiper_wrapper = self::getEmptySubAtomForRender('swiper-wrapper');
        $swiper_wrapper['childs'] = $img_childs;

        $swiper_container = self::getEmptySubAtomForRender('swiper-container', 'div', [
            'id' => 'd-swiper-top-' . $data['id']
        ]);
        $swiper_container['childs'][] = $swiper_wrapper;

        if ($attrs['photo_show_pagination']['value']){
            $pagination = $children_params['photo_top_pagination'];
            $pagination['attrs']['id']['value'] = 'd-swiper-pagination-' . $data['id'];
            $swiper_container['childs'][] = $pagination;
        }

        if (!empty($images) && count($images) > 1){
            $arrow = $children_params['photo_top_gallery_arrows'];
            $arrow['attrs']['id']['value']    = 'd-swiper-nav-next-' . $data['id'];
            $arrow['attrs']['class']['value'] = 'swiper-button-next d-swiper-arrow';
            $swiper_container['childs'][] = $arrow;

            $arrow = $children_params['photo_top_gallery_arrows'];
            $arrow['attrs']['id']['value']    = 'd-swiper-nav-prev-' . $data['id'];
            $arrow['attrs']['class']['value'] = 'swiper-button-prev d-swiper-arrow';
            $swiper_container['childs'][] = $arrow;
        }

        $top_swiper_wrapper['childs'][] = $swiper_container;
        $children[] = $top_swiper_wrapper;

        if ($attrs['show_thumbs']['value'] && count($images) > 1){
            $children[] = self::getFillChildsDataForRenderPhotoTopIcons($data, $children_params, $images, $photo_params);
        }

        return $children;
    }

    /**
     * Заполняет данными сведения о потомках для генерации блока с картинками
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderPhotos($data, $children_params, $product)
    {
        $attrs = $data['attrs'];

        $wrapper_child  = self::getEmptySubAtomForRender('d-col-12');
        if ($attrs['type_show']['value'] == 'horizontal') {
            $wrapper_child['attrs']['class']['value'] .= 'd-col-md-6 d-col-lg-'.$attrs['left_column']['value'];
        }

        $wrapper_child['childs'] = self::getFillChildsDataForRenderPhotosTop($data, $children_params, $product);

        return $wrapper_child;
    }

    /**
     * Заполняет данными сведения о потомках для генерации блока с количеством
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderProductAmount($data, $children_params, $product)
    {
        $attrs = $data['attrs'];
        $wrapper = $children_params['amount_wrapper'];
        $amount = $children_params['amount'];
        $amount['attrs']['name']['value'] = 'amount';
        $amount['attrs']['type']['value'] = 'number';
        $step = $product->getAmountStep();
        $amount['attrs']['min']['value']   = $step;
        $amount['attrs']['step']['value']  = $step;
        $amount['attrs']['value']['value'] =  $step;
        $wrapper['childs'][] = $amount;

        return $wrapper;
    }

    /**
     * Заполняет данными сведения о потомках для генерации блока с кнопками действий
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderProductButtons($data, $children_params, $product)
    {
        $attrs = $data['attrs'];
        $wrapper = $children_params['buttons_wrapper'];
        $offer_sortn = $data['offer_sortn'];

        $shop_config     = \RS\Config\Loader::byModule('shop');
        $catalog_config  = \RS\Config\Loader::byModule('catalog');
        $info['buttons'] = [];

        $use_offers = $product->isOffersUse();
        if ($use_offers){
            /**
             * @var \Catalog\Model\Orm\Offer $offer
             */
            $offer = $product['offers']['items'][$offer_sortn];
        }else{
            $offer = new \Catalog\Model\Orm\Offer();
        }

        if ($product->isAvailable()){
            if ($shop_config && !$product['disallow_manually_add_to_cart']){
                if ($product['reservation'] != 'forced' && (!$shop_config['check_quantity'] || $product->getNum($offer_sortn) > 0)){ //Если не только кнопка заказать
                    $wrapper['childs'][] = self::getFillChildsDataForRenderBuyButton($attrs, $children_params, $product, $use_offers ? $offer_sortn : null);
                }elseif ($product->shouldReserve()){
                    $wrapper['childs'][] = self::getFillChildsDataForRenderReservationButton($attrs, $children_params, $product, $use_offers ?  $offer : null);
                }
            }

            if ((!$shop_config || (!$product->shouldReserve() && (!$shop_config['check_quantity'] || $product->getNum($offer_sortn) > 0))) && $catalog_config['buyinoneclick']){
                $wrapper['childs'][] = self::getFillChildsDataForRenderOneClickButton($attrs, $children_params, $product, $use_offers ? $offer : null);
            }
        }else{
            if ($shop_config && !$product['disallow_manually_add_to_cart'] && $product->shouldReserve()){
                $wrapper['childs'][] = self::getFillChildsDataForRenderReservationButton($attrs, $children_params, $product, $use_offers ? $offer : null);
            }
        }

        return $wrapper;
    }

    /**
     * Заполняет данными сведения о потомках для генерации кнопки купить
     *
     * @param array $attrs - установленные данные атома в аттрибутах
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     * @param integer $offer_sortn - порядковый номер комплектации
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderBuyButton($attrs, $children_params, $product, $offer_sortn = null)
    {
        $button = $children_params['buy_button'];
        $button['attrs']['href']['value'] = \RS\Router\Manager::obj()->getUrl('shop-front-cartpage', ["add" => $product['id'], "offer" => $offer_sortn]);
        $button['attrs']['title']['value'] = t('В корзину');
        //$button['attrs']['class']['value'] .= " addToCart rs-to-cart";

        $button_text = $attrs['buy_button_text']['value'];

        if ($attrs['show_buy_icon']['value']){
            $button_text = "<span class='d-atom-product-icon-buy'></span> ".$button_text;
        }

        $button['html'] = $button_text;
        return $button;
    }


    /**
     * Заполняет данными сведения о потомках для генерации кнопки зарезервировать
     *
     * @param array $attrs - установленные данные атома в аттрибутах
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     * @param \Catalog\Model\Orm\Offer $offer - комплектация
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderReservationButton($attrs, $children_params, $product, $offer = null)
    {
        $button = $children_params['reservation_button'];
        $button['attrs']['href']['value'] = \RS\Router\Manager::obj()->getUrl('shop-front-reservation', ["product_id" => $product['id'], 'offer_id' => $offer ? $offer['id'] : null]);
        $button['attrs']['title']['value'] = t('Заказать');
        $button['attrs']['class']['value'] .= " inDialog rs-in-dialog";

        $button_text = $attrs['reservation_button_text']['value'];

        if ($attrs['show_reservation_icon']['value']){
            $button_text = "<span class='d-atom-product-icon-reservation'></span> ".$button_text;
        }

        $button['html'] = $button_text;
        return $button;
    }

    /**
     * Заполняет данными сведения о потомках для генерации кнопки купить в один клик
     *
     * @param array $attrs - установленные данные атома в аттрибутах
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     * @param \Catalog\Model\Orm\Offer $offer - комплектация
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderOneClickButton($attrs, $children_params, $product, $offer = null)
    {
        $button = $children_params['oneclick_button'];
        $button['attrs']['href']['value'] = \RS\Router\Manager::obj()->getUrl('catalog-front-oneclick', ["product_id" => $product['id'], 'offer_id' => $offer ? $offer['id'] : null]);
        $button['attrs']['title']['value'] = t('Купить в 1 клик');
        $button['attrs']['class']['value'] .= " inDialog rs-in-dialog";

        $button_text = $attrs['oneclick_button_text']['value'];

        if ($attrs['show_oneclick_icon']['value']){
            $button_text = "<span class='d-atom-product-icon-oneclick'></span> ".$button_text;
        }

        $button['html'] = $button_text;
        return $button;
    }

    /**
     * Возвращает данные для заголовка
     *
     * @param array $attrs - установленные данные атома по аттрибутам
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     */
    private static function getHeaderTitleChild($attrs, $children_params, $product)
    {
        $header = $children_params['header'];
        $header['tag']  = $attrs['header_tag']['value'];
        $header['html'] = $product['title'];

        return $header;
    }

    /**
     * Заполняет данными сведения о потомках для генерации блока со сведениями товара
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Catalog\Model\Orm\Product $product - товар
     *
     * @return array
     * @throws \RS\Exception
     */
    private static function getFillChildsDataForRenderProductInfo($data, $children_params, $product)
    {
        $attrs = $data['attrs'];

        $wrapper_child  = self::getEmptySubAtomForRender('d-col-12');
        if ($attrs['type_show']['value'] == 'horizontal') {
            $wrapper_child['attrs']['class']['value'] .= 'd-col-md-6 d-col-lg-'.$attrs['right_column']['value'];
        }

        $product_right = $children_params['product_right'];

        if ($attrs['header_show_place']['value'] == 'right') {
            $product_right['childs'][] = self::getHeaderTitleChild($attrs, $children_params, $product);
        }

        if ($product->isOffersUse() && $attrs['show_offer_title']['value']){
            $item = $children_params['sub_header'];
            $item['html'] = (!empty($product['offer_caption']) ? $product['offer_caption'] : t('Комплектация')).": ".$product->getOfferTitle($data['offer_sortn']);
            $product_right['childs'][] = $item;
        }

        if ($attrs['show_barcode']['value']) {
            $item = $children_params['barcode'];
            $item['html'] = t('Артикул').": ".$product->getBarCode((int)$data['offer_sortn']);
            $product_right['childs'][] = $item;
        }

        if ($attrs['show_brand']['value'] && $product['brand_id']) {
            $item = $children_params['brand'];
            $item['html'] = t('Бренд').": ".$product->getBrand()->title;
            $product_right['childs'][] = $item;
        }

        if ($attrs['show_short_description']['value'] && !empty($product['short_description'])) {
            $item = $children_params['short_description'];
            $item['html'] = $product['short_description'];
            $product_right['childs'][] = $item;
        }

        if ($attrs['show_stock']['value']) {
            $num = $product->getNum((int)$data['offer_sortn']);
            if ($num > 0){
                $item = $children_params['in_stock'];
                $item['html'] = t('В наличии');
            }else{
                $item = $children_params['out_stock'];
                $item['html'] = t('Не в наличии');
            }

            $product_right['childs'][] = $item;
        }

        if ($attrs['show_amount']['value']) { //Показывать кнопки?
            $product_right['childs'][] = self::getFillChildsDataForRenderProductAmount($data, $children_params, $product);
        }

        if ($attrs['show_buttons']['value']) { //Показывать кнопки?
            $product_right['childs'][] = self::getFillChildsDataForRenderProductButtons($data, $children_params, $product);
        }

        $wrapper_child['childs'][] = $product_right;

        return $wrapper_child;
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
        $children = "<p class='d-no-element'>" . t('Товар не назначен') . "</p>";

        if ($data['product_id']) { //Если форма назначена
            $product = new \Catalog\Model\Orm\Product($data['product_id']);
            if (!$product['id']) {
                return "<p class='d-no-element'>" . t('Товар с id=%1 не найдена или удалена', [$data['product_id']]) . "</p>";
            }
            if (!$product['public']) {
                return "<p class='d-no-element'>" . t('Товар с id=%1 выключен', [$data['product_id']]) . "</p>";
            }
            if ($product->isOffersUse() && ($product['offer_sortn'] === false)){
                return "<p class='d-no-element'>" . t('У товара с id=%1 в режиме редактирования комплектация не задана', [$data['product_id']]) . "</p>";
            }

            $children = [];
            $children_params = self::getChildParamsData();

            $product_wrapper = self::getEmptySubAtomForRender('d-row d-product-row', [
                'data-id' => $product['id']
            ]);


            if ($data['attrs']['header_show_place']['value'] == 'top') {
                $top_block = self::getEmptySubAtomForRender('d-col-12');
                $top_block['childs'][] = self::getHeaderTitleChild($data['attrs'], $children_params, $product);

                $product_wrapper['childs'][] = $top_block;
            }

            if ($data['attrs']['show_photos']['value']) {
                $product_wrapper['childs'][] = self::getFillChildsDataForRenderPhotos($data, $children_params, $product);
            }
            if ($data['attrs']['show_product']['value']) {
                $product_wrapper['childs'][] = self::getFillChildsDataForRenderProductInfo($data, $children_params, $product);
            }

            $children[] = $product_wrapper;

            if ($data['attrs']['show_product']['value'] && $data['attrs']['show_description']['value']) {
                $children[] = self::getFillChildsDataForRenderProductDescription($product);
            }
        }

        return $children;
    }
}
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
 * Class Slider - класс показа слайдера
 */
class Slider extends DesignAtoms\AbstractAtom {
    protected $title = "Слайдер"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "slider.svg"; //Картинка компонента
    protected $zone = null; //id категории

    protected static $init_slider_event_reload = 'designer.slider-reinit';

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '/resource/js/swiper/swiper.min.js',
        '%designer%/atoms/slider.js'
    ];

    public static $public_css = [//Массив дополнительных CSS, которые нужно подключить в публичной части
        '/resource/css/common/swiper/swiper.min.css'
    ];

    public static $reset_attrs = [
        'zone'
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
        $photo_autoplay_speed = new DesignAtoms\Attrs\Number('autoplay_speed', t('Скорость автопроигрывания в милисекундах в слайдере?'), 5000);
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

        $itemsCount = new DesignAtoms\Attrs\Number('items_count', t('Количество слайдов для отображения?'), 1);
        $itemsCount->setMin(1)->setStep(1);

        $thumb_count = new DesignAtoms\Attrs\Number('thumb_count', t('Количество иконок для показа (>=767px)?'), 4);
        $thumb_count->setMin(1)->setStep(1);

        $this->setAttr([
            new DesignAtoms\Attrs\SelectFieldValueFromJSONData('zone', t('Банерная зона'), 'banners/zonesList'),
            $itemsCount,
            new DesignAtoms\Attrs\ToggleCheckbox('show_arrows', t('Показать стрелочки?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_loop', t('Зациклить слайдер?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_pagination', t('Показать значки пагинации в слайдере?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('autoplay', t('Автопроигрывание в слайдере'), 0),
            $photo_autoplay_speed,
            $photoEffect
        ], t('Слайдер'))->setAttr([
            new DesignAtoms\Attrs\ToggleCheckbox('show_thumbs', t('Показать миниатюры?'), 0),
            new DesignAtoms\Attrs\ToggleCheckbox('show_thumb_arrows', t('Показывать стрелки пролистывания для миниатюры в слайдере?'), 1),
            $thumb_count,
            new DesignAtoms\Attrs\AttrSize('thumb_width', t('Ширина иконки слайдера'), '220px'),
            new DesignAtoms\Attrs\AttrSize('thumb_height', t('Высота иконки слайдера'), '120px'),
        ], t('Миниатюры'))->setAttrGroupDebugEvent([//Добавим событи я при изменении
            'items_count',
            'show_arrows',
            'show_loop',
            'show_pagination',
            'autoplay_speed',
            'show_thumbs',
            'thumb_count',
            'thumb_width',
            'thumb_height',
        ], self::$init_slider_event_reload);
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $data = parent::getData();
        $data['zone'] = $this->zone;
        return $data;
    }

    /**
     * Возвращает массив данных параметров для обёртки всего слайдера
     *
     * @return array
     */
    public static function getChildParamsDataForSliderWrapper()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS();

        //Заголовки
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка для всего баннера'))
            ->setClass('d-slider-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки фото
     *
     * @return array
     */
    public static function getChildParamsDataForBannerWrapper()
    {
        //Заголовки
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка для одного фото'))
            ->setClass('d-slider-photo-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки фото
     *
     * @return array
     */
    public static function getChildParamsDataForBanner()
    {
        //Заголовки
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('img')
            ->setTitle(t('Само фото'))
            ->setClass('d-slider-photo')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для пагинации
     *
     * @return array
     */
    public static function getChildParamsDataForPagination()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Color('--swiper-theme-color', t('Цвет круга')),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Пагинация слайдера'))
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
    public static function getChildParamsDataForPaginationItem()
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
     *
     * @return array
     */
    public static function getChildParamsDataForArrows()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\Size('margin-left', t('Внешний отступ слева'), '0px'),
            new DesignAtoms\CSSProperty\Size('margin-right', t('Внешний отступ справа'), '0px'),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '0px'),
            new DesignAtoms\CSSProperty\Color('--swiper-theme-color', t('Цвет стрелки')),
            new DesignAtoms\CSSProperty\Size('--swiper-navigation-size', t('Размер стрелки')),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Стрелки'))
            ->setClass('d-slider-wrapper .d-swiper-arrow')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для левой стрелки
     *
     *
     * @return array
     */
    public static function getChildParamsDataForLeftArrow()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('top', t('Отступ сверху'), '50%'),
            new DesignAtoms\CSSProperty\Size('left', t('Отступ слева'), '10px'),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Левая стрелка'))
            ->setClass('d-slider-wrapper .swiper-button-prev')
            ->addCSSPreset([
                $preset
            ]);
        return $item->getData();
    }


    /**
     * Возвращает массив данных параметров для правой стрелки
     *
     *
     * @return array
     */
    public static function getChildParamsDataForRightArrow()
    {
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('top', t('Отступ сверху'), '50%'),
            new DesignAtoms\CSSProperty\Size('right', t('Отступ справа'), '10px'),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Правая стрелка'))
            ->setClass('d-slider-wrapper .swiper-button-next')
            ->addCSSPreset([
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
            ->setClass('d-slider-swiper-thumbs-wrapper')
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
        $width->initDebugEventOnChange(self::$init_slider_event_reload);
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
     * Возвращает массив данных параметров детей внутри составного элемента
     *
     * @return array
     */
    public static function getChildParamsData()
    {
        static $childs;
        if (!$childs){
            $childs['slider_wrapper']  = self::getChildParamsDataForSliderWrapper();
            $childs['banner_wrapper']  = self::getChildParamsDataForBannerWrapper();
            $childs['banner']          = self::getChildParamsDataForBanner();
            $childs['pagination']      = self::getChildParamsDataForPagination();
            $childs['pagination_item'] = self::getChildParamsDataForPaginationItem();
            $childs['arrows']          = self::getChildParamsDataForArrows();
            $childs['arrows_left']     = self::getChildParamsDataForLeftArrow();
            $childs['arrows_right']    = self::getChildParamsDataForRightArrow();

            //Миниатюры
            $childs['thumbs_out_wrapper']  = self::getChildParamsDataForThumbsWrapper();
            $childs['thumbs_wrapper']      = self::getChildParamsDataForThumbs();
            $childs['photo_thumbs_icon']   = self::getChildParamsDataForThumbIcon();
            $childs['photo_thumbs_arrows'] = self::getChildParamsDataForGalleryArrows(t('Стрелки для галереи иконок'), 'd-slider-swiper-thumbs-wrapper .d-swiper-arrow', '', '16px', '-6px');
        }
        return $childs;
    }

    /**
     * Возвращает потомка для фотографий с обёрткой
     *
     * @param array $data - данные для текущего атома
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Banners\Model\Orm\Zone $zone - объект зоны
     *
     * @return array
     */
    private static function getSliderWrapperChilds($data, $children_params, $zone)
    {
        $attrs = $data['attrs'];
        $child = $children_params['slider_wrapper'];

        $banners  = $zone->getBanners();
        $swiper_wrapper = self::getEmptySubAtomForRender("swiper-wrapper", 'div');

        $n = 0;
        foreach ($banners as $banner){
            $n++;
            $photo_wrapper = $children_params['banner_wrapper'];

            $photo = $children_params['banner'];
            $photo['attrs']['src']['value'] = $banner->getBannerUrl($zone['width'], $zone['height']);
            $photo['attrs']['alt']['value'] = $banner['title'];

            if (!empty($banner['link'])){
                $photo_wrapper['tag'] = 'a';
                $photo_wrapper['attrs']['href']['value'] = $banner['link'];
                if ($banner['targetblank']){
                    $photo_wrapper['attrs']['target']['value'] = '_blank';
                }
            }
            $photo_wrapper['childs'][] = $photo;

            $swiper_slide = self::getEmptySubAtomForRender("swiper-slide", 'div');
            $swiper_slide['childs'][]   = $photo_wrapper;
            $swiper_wrapper['childs'][] = $swiper_slide;
        }

        $swiper_container = self::getEmptySubAtomForRender("swiper-container", 'div');
        $swiper_container['attrs']['id']['value'] = "d-atom-slider-swiper-".$data['id'];
        $swiper_container['childs'][] = $swiper_wrapper;


        if ($n > 1 && $attrs['show_pagination']['value']){
            $swiper_pagination = self::getEmptySubAtomForRender("swiper-pagination", 'div');
            $swiper_pagination['attrs']['id']['value'] = "d-atom-slider-pagintaion-".$data['id'];
            $swiper_container['childs'][] = $swiper_pagination;
        }

        if ($n > 1 && $attrs['show_arrows']['value']){
            $next_item = self::getEmptySubAtomForRender("swiper-button-next d-swiper-arrow", 'div');
            $next_item['attrs']['id']['value'] = "d-atom-slider-next-".$data['id'];
            $swiper_container['childs'][] = $next_item;

            $next_item = self::getEmptySubAtomForRender("swiper-button-prev d-swiper-arrow", 'div');
            $next_item['attrs']['id']['value'] = "d-atom-slider-prev-".$data['id'];
            $swiper_container['childs'][] = $next_item;
        }

        $child['childs'][] = $swiper_container;

        $photo_params = [ //Соберём параметры
            'thumb_width'  => intval($attrs['thumb_width']['value']),
            'thumb_height' => intval($attrs['thumb_height']['value'])
        ];

        if ($attrs['show_thumbs']['value'] && $n > 1){
            $child['childs'][] = self::getFillChildsDataForRenderPhotoTopIcons($data, $children_params, $banners, $photo_params);
        }

        return $child;
    }

    /**
     * Заполняет данными сведения о потомках для генерации массива иконок для верхнего слайдера
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив параметров для рендеринга
     * @param \Banners\Model\Orm\Banner[] $images - массив картинко
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
                'src' => $image->getBannerUrl($photo_params['thumb_width'], $photo_params['thumb_height'])
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

        $top_swiper_thumbs_wrapper = $children_params['thumbs_out_wrapper'];
        $top_swiper_thumbs_wrapper['childs'][] = $swiper_container;
        return $top_swiper_thumbs_wrapper;
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
        $children = "<p class='d-no-element'>" . t('Зона для баннера не назначена') . "</p>";

        if ($data['zone']) { //Если форма назначена
            $zone_api = new \Banners\Model\ZoneApi();
            /**
             * @var \Banners\Model\Orm\Zone $zone
             */
            $zone = $zone_api->getById($data['zone']);
            if (!$zone['id']){
                return "<p class='d-no-element'>".t('Баннерная зона с id=%1 не найден или удален', [$data['zone']])."</p>";
            }

            $children = [];
            $children_params = self::getChildParamsData();
            $children[] = self::getSliderWrapperChilds($data, $children_params, $zone);
        }
        return $children;
    }

    /**
     * Возвращает зону, который должен быть для пресета
     *
     * @return \Photogalleries\Model\Orm\Album
     */
    public static function getZoneIdForInsert()
    {
        return \RS\Orm\Request::make()
            ->from(new \Banners\Model\Orm\Zone())
            ->where([
                'alias' => 'designer',
                'site_id' => \RS\Site\Manager::getSiteId()
            ])->object();
    }

    /**
     * Заполняет значения по умолчанию в данные атома после добавления из пресета
     *
     * @param array $data - данные атома из пресета
     * @param array $preset - все данные пресета
     */
    public static function setDefaultsAfterPresetInsert(&$data, $preset){
        $zone = self::getZoneIdForInsert();

        if (!$zone){
            $install = new \Designer\Config\Install();
            $install->importCSVBanners();
            $zone = self::getZoneIdForInsert();
        }
        $id = $zone['id'];

        if ($id){
            $data['zone'] = $id;
        }
    }
}
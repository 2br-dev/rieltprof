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
 * Class YandexMap - класс яндекс карты
 */
class YandexMap extends DesignAtoms\AbstractAtom {
    const API_KEY  = 'f0779502-abe1-46cb-9772-eacb78d13768'; //API ключ для Яндекс карты
    const map_center_lat  = '45.040491';
    const map_center_long = '38.976494';
    protected $title = "Я.Карта"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "yandexmap.svg"; //Картинка компонента

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '%designer%/atoms/yandexmap.js',
    ];

    public static $reset_attrs = [
        'move_point'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();
        $config = \RS\Config\Loader::byModule($this);
        self::$public_js[] = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.($config['ya_map_api_key'] ? $config['ya_map_api_key'] : self::API_KEY);

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $height = new DesignAtoms\CSSProperty\Size('height', t('Высота'), '400px');
        $height->initDebugEventOnChange('designer.yandexmap-reload');
        $preset->addCSS([
            $height,
            new DesignAtoms\CSSProperty\Size('padding-left', t('Внутренний отступ слева')),
            new DesignAtoms\CSSProperty\Size('padding-right', t('Внутренний отступ справа')),
            new DesignAtoms\CSSProperty\Size('margin-left', t('Внешний отступ слева')),
            new DesignAtoms\CSSProperty\Size('margin-right', t('Внешний отступ справа')),
        ]);
        $borderPreset = new DesignAtoms\CSSPresets\Border();
        $border = $borderPreset->getCSS('border');
        $border->initDebugEventOnChange('designer.yandexmap-reload');

        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            $borderPreset,
            $preset
        ]);
        $this->setMapAttributes();
    }

    /**
     * Установка атррибутов карты
     *
     */
    function setMapAttributes()
    {
        $zoom = new DesignAtoms\Attrs\Number('zoom', t('Масштаб'), 10);
        $zoom->setMin(1)->setMax(16)->setStep(1);

        //Зададим доп параметры отображения
        $this->setAttr([
            new DesignAtoms\Attrs\Number('map_lat', t('Долгота центра карты'), self::map_center_lat),
            new DesignAtoms\Attrs\Number('map_long', t('Широта центра карты'), self::map_center_long),
            $zoom,
            new DesignAtoms\Attrs\ToggleCheckbox('show_search', t('Показывать поиск на карте?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_type_selector', t('Показывать переключение вида карты?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_fullscreen', t('Показывать переключатель развертывания на всю ширину?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_route_button', t('Показывать кнопку прокладки маршрута?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_zoom', t('Показывать масштабирование?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('enable_scroll_zoom', t('Изменение масштаба карты при скроле мыши?'), 0),
        ], t('Карта'))
        ->setAttr([
            new DesignAtoms\Attrs\MapPoints('points', t('Список точек'), []),
        ], t('Точки'))->setAttrGroupDebugEvent([
            'points',            
            'map_lat',
            'map_long',
            'zoom',
            'show_search',
            'show_type_selector',
            'show_fullscreen',
            'show_route_button',
            'show_zoom',
            'enable_scroll_zoom'
        ],'designer.yandexmap-reload');
    }

}
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
 * Class Gallery - класс галлереи
 */
class Gallery extends DesignAtoms\AbstractAtom {
    protected $title = "Галерея"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "gallery.svg"; //Картинка компонента
    protected $gallery_id = 0; //Галлерея для использования

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '/resource/js/lightgallery/lightgallery-nojquery.min.js',
        '%designer%/atoms/gallery.js'
    ];

    public static $public_css = [//Массив дополнительных CSS, которые нужно подключить в публичной части
        '/resource/css/common/lightgallery/css/lightgallery.min.css',
    ];

    public static $reset_attrs = [
        'gallery_id'
    ];

    protected static $init_slider_event = 'designer.gallery-reinit.gallery';

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $this->addCSSPreset([
            self::getBackgroundParamsSettings(),
            new DesignAtoms\CSSPresets\Border()
        ]);

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
        ]);
        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $this->addGalleryAttributes();
    }

    /**
     * Добавляет нужнвые аттрибуты к галлереи
     */
    function addGalleryAttributes()
    {
        $photoType = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('photo_type', t('Тип обрезки'), 'axy');
        $photoType->setOptions([
            'xy'  => t('По умолчанию (xy)'),
            'axy' => t('Всегда по точным размерам (axy)'),
            'cxy' => t('Обрезка по одной из сторон (cxy)'),
        ]);

        $this->setAttr([
            new DesignAtoms\Attrs\SelectFieldValueFromJSONData('gallery_id', t('Банерная зона'), 'gallery/albumsList'),
            new \Designer\Model\DesignAtoms\Attrs\DirectLink('link', t('Ссылка на галерею'), \RS\Router\Manager::obj()->getAdminUrl(null, null, 'photogalleries-ctrl'), [
                'title' => t('Перейти к галерее'),
                'id' => 'gallery_id',
                'do' => 'edit',
            ]),
            new DesignAtoms\Attrs\ToggleCheckbox('show_title', t('Показывать название альбома'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_description', t('Показывать название фото'), 1),
            $photoType,
            new DesignAtoms\Attrs\AttrSize('photo_width', t('Ширина картинки'), '200px'),
            new DesignAtoms\Attrs\AttrSize('photo_height', t('Высота картинки'), '180px'),
            new DesignAtoms\Attrs\AttrSize('photo_big_width', t('Ширина картинки для детального просмотра'), '800px'),
            new DesignAtoms\Attrs\AttrSize('photo_big_height', t('Высота картинки для детального просмотра'), '700px'),
        ])->setAttrGroupDebugEvent([
            'photo_type',
            'photo_width',
            'photo_height',
            'photo_big_width',
            'photo_big_height',
        ], self::$init_slider_event);
    }

    /**
     * Вовзращает информацию по компоненту со всеми сведиями для хранилища данных для публичной части
     *
     * @return array
     */
    function getData()
    {
        $data = parent::getData();
        $data['gallery_id'] = $this->gallery_id;
        return $data;
    }


    /**
     * Возвращает массив данных параметров для обёртки всех фото
     *
     * @return array
     */
    public static function getChildParamsDataForWrapper()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление полей'), 'row');
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

        $preset->addMarginAndPaddingCSS([], [
            'top'    => '0px',
            'right'   => '0px',
            'bottom' => '20px',
            'left'  => '0px'
        ])->addCSS([
            $flex_direction,
            $justify_content,
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиционирование элементов внутри по оси Y'), 'flex-start')
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Общая обёртка'))
            ->setClass('d-gallery-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для названия альбома
     *
     * @return array
     */
    public static function getChildParamsDataForPhotoWrapperTitle()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top'    => '0px',
                'right'   => '0px',
                'bottom' => '20px',
                'left'  => '0px'
            ]),
        ]);
        $textedit = self::getTextEditParamsSettings();
        $textedit->setDefaults([
            'font-size' => '18px',
            'font-weight' => 'bold'
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Название альбома'))
            ->setClass('d-gallery-photo-title')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                $textedit,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для одного фото
     *
     * @return array
     */
    public static function getChildParamsDataForPhotoItem()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '25%')
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Обёртка для одного фото'))
            ->setClass('d-gallery-photo-item')
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
    public static function getChildParamsDataForPhotoWrapper()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS();
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('a')
            ->setTitle(t('Обёртка для ссылки на фото'))
            ->setClass('d-gallery-photo-wrapper')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для фото
     *
     * @return array
     */
    public static function getChildParamsDataForPhotoImage()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '180px'),
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-size' => 'cover',
            'background-repeat' => 'no-repeat',
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Фото'))
            ->setClass('d-gallery-photo')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для описания
     *
     * @return array
     */
    public static function getChildParamsDataForDescription()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $position = new DesignAtoms\CSSProperty\Select('position', t('Позиционирование'), 'absolute');
        $position->setOptions([
            'static' => t('Статическое'),
            'absolute' => t('Абсолютное'),
        ]);
        $preset->addCSS([
            $position,
            new DesignAtoms\CSSProperty\Size('top', t('Отступ сверху')),
            new DesignAtoms\CSSProperty\Size('right', t('Отступ справа')),
            new DesignAtoms\CSSProperty\Size('bottom', t('Отступ снизу'), '0px'),
            new DesignAtoms\CSSProperty\Size('left', t('Отступ слева'), '0px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '5px',
                'right'  => '5px',
                'bottom' => '5px',
                'left'   => '5px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ')),
            new DesignAtoms\CSSProperty\Size('height', t('Высота')),
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина')),
            new DesignAtoms\CSSProperty\VAlignItems('justify-content', t('Позиция текста'), 'center'),
            new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Позиция текста'), 'center'),
        ]);
        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('div')
            ->setTitle(t('Описание фото'))
            ->setClass('d-gallery-photo-description')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#000000cc'),
                self::getTextEditParamsSettings('14px', '#FFFFFFFF'),
                new DesignAtoms\CSSPresets\Border(),
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
            $childs['photo_wrapper_title'] = self::getChildParamsDataForPhotoWrapperTitle();
            $childs['wrapper']             = self::getChildParamsDataForWrapper();
            $childs['photo_item']          = self::getChildParamsDataForPhotoItem();
            $childs['photo_wrapper']       = self::getChildParamsDataForPhotoWrapper();
            $childs['photo_image']         = self::getChildParamsDataForPhotoImage();
            $childs['description']         = self::getChildParamsDataForDescription();
        }
        return $childs;
    }

    /**
     * Возвращает потомка для заголовка альбома
     *
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Photogalleries\Model\Orm\Album $album - объект альбома
     *
     * @return array
     */
    private static function getHeaderChild($children_params, $album)
    {
        $child = $children_params['photo_wrapper_title'];
        $child['html'] = $album['title'];
        return $child;
    }

    /**
     * Возвращает потомка для фотографий с обёрткой
     *
     * @param array $data - данные для текущего атома
     * @param array $children_params - массив данных с установками для разных типов
     * @param \Photogalleries\Model\Orm\Album $album - объект альбома
     *
     * @return array
     */
    private static function getPhotoWrapperChilds($data, $children_params, $album)
    {
        $attrs = $data['attrs'];
        $child = $children_params['wrapper'];

        $photos = $album->fillImages();

        $n = 0;
        foreach ($photos as $photo){
            $n++;
            $photo_item = $children_params['photo_item'];

            $photo_wrapper = $children_params['photo_wrapper'];
            $photo_wrapper['attrs']['href']['value'] = $photo->getUrl((int)$attrs['photo_big_width']['value'], (int)$attrs['photo_big_height']['value'], $attrs['photo_type']['value']);
            $photo_image = $children_params['photo_image'];
            $photo_image['attrs']['style']['value'] = "background-image: url(".$photo->getUrl((int)$attrs['photo_width']['value'], (int)$attrs['photo_height']['value'], $attrs['photo_type']['value']).")";

            $title = $photo['title'] ? $photo['title'] : t('Фото №%0', [$n]);
            $hidden_image  = self::getEmptySubAtomForRender("", 'img', [
                'src' => $photo->getUrl((int)$attrs['photo_width']['value'], (int)$attrs['photo_height']['value'], $attrs['photo_type']['value']),
                'alt' => $title,
            ]);

            $photo_image['childs'][] = $hidden_image;

            $photo_wrapper['childs'][] = $photo_image;
            if ($attrs['show_description']['value']){
                $photo_desc = $children_params['description'];
                $photo_desc['html'] = $title;
                $photo_wrapper['childs'][] = $photo_desc;
            }

            $photo_item['childs'][] = $photo_wrapper;

            $child['childs'][] = $photo_item;
        }
        return $child;
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
        $children = "<p class='d-no-element'>" . t('Галерея не назначена') . "</p>";

        if ($data['gallery_id']) { //Если форма назначена
            $album = new \Photogalleries\Model\Orm\Album($data['gallery_id']);
            if (!$album['id']){
                return "<p class='d-no-element'>".t('Альбом с id=%1 не найден или удален', [$album['id']])."</p>";
            }

            $children = [];
            $children_params = self::getChildParamsData();

            if ($data['attrs']['show_title']['value']) {
                $children[] = self::getHeaderChild($children_params, $album);
            }

            $children[] = self::getPhotoWrapperChilds($data, $children_params, $album);
        }
        return $children;
    }

    /**
     * Возвращает альбом, который должен быть для пресета
     *
     * @return \Photogalleries\Model\Orm\Album
     */
    public static function getAlbumIdForInsert()
    {
        return \RS\Orm\Request::make()
            ->from(new \Photogalleries\Model\Orm\Album())
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
        $album = self::getAlbumIdForInsert();

        if (!$album){
            $install = new \Photogalleries\Config\Install();
            $install->importCSVPhotogalleries();
            $album = self::getAlbumIdForInsert();
        }
        $id = $album['id'];

        if ($id){
            $data['gallery_id'] = $id;
        }
    }
}

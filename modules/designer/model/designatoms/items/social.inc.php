<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;
use \Designer\Model\DesignAtoms\Attrs\OrmFields;

/**
 * Class Social - социальные сети
 */
class Social extends DesignAtoms\AbstractAtom {
    protected $title   = "Соц. сети"; //Название компонента
    protected $tag     = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image   = "social.svg"; //Картинка компонента
    protected $site_id = null; //id сайта

    protected static $socials = [ //Название социальных сетей
        'facebook',
        'vkontakte',
        'twitter',
        'instagram',
        'youtube',
        'viber',
        'telegram',
        'whatsapp'
    ];

    /**
     * Конструктор класса
     *
     * @throws \RS\Exception
     */
    function __construct()
    {
        parent::__construct();
        $this->site_id = \RS\Site\Manager::getSiteId();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $this->setCSSTitle(t('Обёртка иконок'));

        $flex_direction = new DesignAtoms\CSSProperty\Select('flex-direction', t('Направление иконов'), 'row');
        $flex_direction->setOptions([
            'row' => t('По горизонтали'),
            'column' => t('По вертикали'),
        ]);
        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Центрирование иконок по оси X'), 'center');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'space-between' => t('Расстояние между'),
            'space-around' => t('Расстояние вокруг'),
            'center' => t('Центр'),
            'flex-end' => t('Справа'),
        ]);
        $preset->addCSS([
                $flex_direction,
                $justify_content,
                new DesignAtoms\CSSProperty\VAlignItems('align-items', t('Центрирование иконок по оси Y'), 'center'),
                new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Отступ слева')),
                new DesignAtoms\CSSProperty\Size('margin-left', t('Отступ слева')),
                new DesignAtoms\CSSProperty\Size('margin-right', t('Отступ справа')),
                new DesignAtoms\CSSProperty\Size('margin-bottom', t('Отступ снизу')),
        ]);

        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);



        $fields = [];
        foreach (self::$socials as $social){
            $fields[] = $social.'_group';
        }
        $this->setAttr([
            OrmFields::from(new \Site\Model\Orm\Config(), t('Социальные сети'), 'site_id')
                ->setNeededFields($fields)
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
        $data['site_id'] = $this->site_id;
        return $data;
    }

    /**
     * Возвращает массив данных параметров для иконки соц. сети
     *
     * @return array
     */
    public static function getChildParamsDataForSocialItem()
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $preset->addMarginAndPaddingCSS()
               ->addCSS([
                    new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '32px'),
                    new DesignAtoms\CSSProperty\Size('height', t('Высота'), '32px'),
               ]);

        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'cover',
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('a')
            ->setTitle(t('Обёртка иконки'))
            ->setClass('d-social-item')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $preset
            ]);
        return $field->getData();
    }

    /**
     * Возвращает массив данных параметров для иконки определённого типа
     *
     * @param string $icon - название иконки для подгрузки
     * @param string $icon_class - класс для иконки, если нет то будет генерироваться из названию иконки
     * @return array
     */
    public static function getChildParamsDataForSocialItemIcon($icon, $icon_class = null)
    {
        //Заголовки
        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $icon_value = "/modules/designer/view/img/iconsset/social/".$icon;
        $preset->addCSS(new DesignAtoms\CSSProperty\Image('background-image', t('Иконка'), $icon_value));

        $icon_base_name = strtok($icon, ".");
        if (!$icon_class){
            $icon_class = 'd-social-item-'.$icon_base_name;
        }

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('a')
            ->setTitle(t('Иконка '.$icon_base_name))
            ->setClass($icon_class)
            ->addCSSPreset([
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
            $childs['social_item'] = self::getChildParamsDataForSocialItem();

            foreach (self::$socials as $social){
                $childs[$social] = self::getChildParamsDataForSocialItemIcon($social.'.svg');
            }
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

        $attrs = $data['attrs'];

        $children = [];
        $children_params = self::getChildParamsData();

        $config = new \Site\Model\Orm\Config();
        $config->load($data['site_id']);
        foreach (self::$socials as $social){
            $item = $children_params['social_item'];
            $href = $config[$social.'_group'];

            if (!empty($href)){
                $item['attrs']['class']['value']  .= ' d-social-item-'.$social;
                $item['attrs']['href']['value']   = $href;
                $item['attrs']['target']['value'] = '_blank';
                $children[] = $item;
            }
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
        $data['site_id'] = \RS\Site\Manager::getSiteId();
    }

}
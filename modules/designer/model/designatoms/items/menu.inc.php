<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use Designer\Model\AtomApis\MenuApi;
use \Designer\Model\DesignAtoms;

/**
 * Class Menu - класс меню
 */
class Menu extends DesignAtoms\AbstractAtom {
    protected $title = "Меню"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $tags  = [];
    protected $image = "menu.svg"; //Картинка компонента

    protected $menu_type = 'none'; //Тип меню
    protected $root      = false; //id корневого элемента

    public static $virtual_attrs   = [//Массив виртуальных аттрибутов
    ];
    public static $reset_attrs = [ //Массив атррибутов для блоса
        'menu_type',
        'root'
    ];

    public static $public_js = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '%designer%/mobilemenu/dist/mobilemenu.js',
        '%designer%/atoms/menu.js'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $menuPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($menuPreset);
        $menuPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
        ]);

        $this->addCSSPreset([
            self::getBackgroundParamsSettings('#FFFFFFFF'),
            new DesignAtoms\CSSPresets\Border(),
            $menuPreset
        ]);
        $this->setCSSTitle(t('Обёртка всего меню'));

        //Зададим доп. параметры отображения
        $this->addMenuAttributes();
    }

    /**
     * Добавляет нужнвые аттрибуты к меню
     */
    function addMenuAttributes()
    {
        $menuShow = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('menu_show_level', t('Сколько уровней меню будет выведено?'), 1);
        $menuShow->setOptions([
            1 => t('1 уровень'),
            2 => t('2 уровня'),
            3 => t('3 уровня')
        ]);

        $subMenuTypeShow = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('second_menu_type', t('Тип вывода меню второго уровня'), 'd-bottom');
        $subMenuTypeShow->setOptions([
            'd-bottom' => t('Снизу'),
            'd-right' => t('Справа')
        ]);

        $subSubMenuTypeShow = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('third_menu_type', t('Тип вывода меню третьего уровня'), 'd-right');
        $subSubMenuTypeShow->setOptions([
            'd-bottom' => t('Снизу'),
            'd-right' => t('Справа')
        ]);

        $showMobileButton = new \Designer\Model\DesignAtoms\Attrs\ToggleMenuButton('show_mobile_button', t('Показать в мобильном разрешении (<=767px) кнопку меню вместо всего меню?'), 0);
        $showMobileButton->setOptions([
            t('Показать'),
            t('Скрыть'),
        ]);

        $router = \RS\Router\Manager::obj();
        $this->setAttr([
            new DesignAtoms\Attrs\SelectFieldsForMenu('root', t('Данные по меню'), [
                'menu' => $router->getAdminUrl('getMenuList', ['ajax' => 1], 'designer-atommenuctrl'),
                'category' => $router->getAdminUrl('getCategoryList', ['ajax' => 1], 'designer-atommenuctrl')
            ]),
        ], t('Настройки'))->setAttr([
            $menuShow,
            new DesignAtoms\Attrs\ToggleCheckbox('show_second_menu', t('Показать меню второго уровня? (для редактирования)'), 0),
            new DesignAtoms\Attrs\ToggleCheckbox('show_down_menu_icon', t('Показать значок выдающего меню?'), 1),
            $subMenuTypeShow,
            new DesignAtoms\Attrs\ToggleCheckbox('show_down_sub_menu_icon', t('Показать значок выдающего меню второго уровня?'), 1),
            new DesignAtoms\Attrs\ToggleCheckbox('show_third_menu', t('Показать меню третьего уровня? (для редактирования)'), 0),
            $subSubMenuTypeShow,
            $showMobileButton,
            new DesignAtoms\Attrs\Text('mobile_button_text', t('Текст кнопки мобильного меню'), t('Меню')),
            new DesignAtoms\Attrs\Text('mobile_menu_header', t('Текст заголовка открытого мобильного меню'), t('Меню'))
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
        $data['menu_type'] = $this->menu_type;
        $data['root'] = $this->root;
        return $data;
    }


    /**
     * Возвращает массив данных параметров для обёртки первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForFirstLevelMenu()
    {
        $firstLevelMenuPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $firstLevelMenuPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            self::getFlexDirectionParamsData(),
            self::getJustifyContentParamsData(),
            self::getAlignItemsParamsData()
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('ul')
            ->setTitle(t('Меню первого уровня'))
            ->setClass('d-atom-menu-items')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $firstLevelMenuPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега LI меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForFirstLevelMenuLi()
    {
        $firstLevelMenuLiPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $firstLevelMenuLiPreset->addMarginAndPaddingCSS();
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('li')
            ->setTitle(t('Подэлемент меню первого уровня'))
            ->setClass('d-atom-menu-item')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                $firstLevelMenuLiPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега A меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForFirstLevelMenuA()
    {
        $firstLevelMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Расположение'), 'flex-start');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);
        $firstLevelMenuAPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), ''),
            $justify_content,
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'),[
                'top'    => '10px',
                'left'   => '20px',
                'bottom' => '10px',
                'right'  => '20px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top'    => '0px',
                'right'  => '15px',
                'bottom' => '0px',
                'left'   => '0px'
            ])
        ]);
        $textedit = new DesignAtoms\CSSPresets\TextEdit();
        $textedit->setDefaults();
        $textedit->setDefaults([
            'font-size' => '16px',
            'color' => '#000000FF',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('a')
            ->setTitle(t('Ссылка меню первого уровня'))
            ->setClass('d-atom-menu-link')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $firstLevelMenuAPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега SPAN значка меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForFirstLevelMenuDown()
    {
        $firstLevelMenuLiPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $firstLevelMenuLiPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '16px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '16px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top'    => '0px',
                'right'   => '0px',
                'bottom' => '0px',
                'left'  => '5px'
            ])
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/arrow_down.svg',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('span')
            ->setTitle(t('Значок меню первого уровня'))
            ->setClass('d-atom-menu-down')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $firstLevelMenuLiPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега SPAN значка меню первого уровня (Открытое)
     *
     * @return array
     */
    private static function getChildParamsDataForFirstLevelMenuDownOpen()
    {
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/arrow_up.svg',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('span')
            ->setTitle(t('Значок меню первого уровня (Открытый на разрешении <=767px)'))
            ->setClass('d-atom-menu-down.d-open')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border()
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки второго уровня
     *
     * @return array
     */
    private static function getChildParamsDataForSecondLevelMenu()
    {
        $secondLevelMenuPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $secondLevelMenuPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            self::getFlexDirectionParamsData('column'),
            self::getJustifyContentParamsData(),
            self::getAlignItemsParamsData(),
            new DesignAtoms\CSSProperty\Size('left', t('Положение слева')),
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('ul')
            ->setTitle(t('Меню второго уровня'))
            ->setClass('d-atom-sub-menu-items')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#FFFFFFFF'),
                $secondLevelMenuPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега LI меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForSecondLevelMenuLi()
    {
        $secondLevelMenuLiPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $secondLevelMenuLiPreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px')
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('li')
            ->setTitle(t('Подэлемент меню второго уровня'))
            ->setClass('d-atom-sub-menu-item')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                $secondLevelMenuLiPreset
            ]);
        return $menu->getData();
    }


    /**
     * Возвращает массив данных параметров для обёртки тега A меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForSecondLevelMenuA()
    {
        $secondLevelMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Расположение'), 'flex-start');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);
        $secondLevelMenuAPreset->addCSS([
            $justify_content,
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'),[
                'top'    => '10px',
                'left'   => '20px',
                'bottom' => '10px',
                'right'  => '20px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'))
        ]);
        $textedit = new DesignAtoms\CSSPresets\TextEdit();
        $textedit->setDefaults();
        $textedit->setDefaults([
            'font-size' => '16px',
            'color' => '#000000FF',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('a')
            ->setTitle(t('Ссылка меню второго уровня'))
            ->setClass('d-atom-menu-sub-link')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $secondLevelMenuAPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега SPAN значка меню второго уровня
     *
     * @return array
     */
    private static function getChildParamsDataForSecondLevelMenuDown()
    {
        $secondLevelMenuLiPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $secondLevelMenuLiPreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('top', t('Отступ сверху'), '10px'),
            new DesignAtoms\CSSProperty\Size('right', t('Отступ снизу'), '5px'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '16px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '16px')
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/arrow_right.svg',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('span')
            ->setTitle(t('Значок меню второго уровня'))
            ->setClass('d-atom-sub-menu-down')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $secondLevelMenuLiPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега SPAN значка меню второго уровня открытое
     *
     * @return array
     */
    private static function getChildParamsDataForSecondLevelMenuDownOpen()
    {
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/arrow_down.svg',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('span')
            ->setTitle(t('Значок меню второго уровня (Открытое на разрешении <=767px)'))
            ->setClass('d-atom-sub-menu-down.d-open')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border()
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки третьего уровня
     *
     * @return array
     */
    private static function getChildParamsDataForThirdLevelMenu()
    {
        $thirdLevelMenuPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $thirdLevelMenuPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            self::getFlexDirectionParamsData('column'),
            self::getJustifyContentParamsData(),
            self::getAlignItemsParamsData()
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('ul')
            ->setTitle(t('Меню третьего уровня'))
            ->setClass('d-atom-sub-sub-menu-items')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#FFFFFFFF'),
                $thirdLevelMenuPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега LI меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForThirdLevelMenuLi()
    {
        $secondLevelMenuLiPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $secondLevelMenuLiPreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px')
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('li')
            ->setTitle(t('Подэлемент меню третьего уровня'))
            ->setClass('d-atom-sub-sub-menu-item')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                $secondLevelMenuLiPreset
            ]);
        return $menu->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки тега A меню первого уровня
     *
     * @return array
     */
    private static function getChildParamsDataForThirdLevelMenuA()
    {
        $thirdLevelMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Расположение'), 'flex-start');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);

        $thirdLevelMenuAPreset->addCSS([
            $justify_content,
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '300px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'),[
                'top'    => '10px',
                'left'   => '20px',
                'bottom' => '10px',
                'right'  => '20px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'))
        ]);
        $textedit = new DesignAtoms\CSSPresets\TextEdit();
        $textedit->setDefaults();
        $textedit->setDefaults([
            'font-size' => '16px',
            'color' => '#000000FF',
        ]);
        $menu = new DesignAtoms\Items\SubAtom();
        $menu->setTag('a')
            ->setTitle(t('Ссылка меню третьего уровня'))
            ->setClass('d-atom-menu-sub-sub-link')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $thirdLevelMenuAPreset
            ]);
        return $menu->getData();
    }


    /**
     * Возвращает массив данных параметров для обёртки мобильной кнопки
     *
     * @return array
     */
    private static function getChildParamsDataForMobileButton()
    {
        $buttonMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $align_self = new DesignAtoms\CSSProperty\Select('align-self', t('Расположение'), 'center');
        $align_self->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);

        $justify_content = new DesignAtoms\CSSProperty\Select('justify-content', t('Расположение внутри'), 'center');
        $justify_content->setOptions([
            'flex-start' => t('Слева'),
            'center' => t('Центр'),
            'flex-end' => t('Справа')
        ]);


        $buttonMenuAPreset->addCSS([
            $align_self,
            new DesignAtoms\CSSProperty\Size('max-width', t('Ширина'), '84px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '5px',
                'right'   => '0px',
                'bottom' => '5px',
                'left'  => '0px'
            ]),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'))
        ]);
        $link = new DesignAtoms\Items\SubAtom();
        $link->setTag('a')
            ->setTitle(t('Кнопка мобильного меню'))
            ->setClass('d-atom-menu-mobile-button')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $buttonMenuAPreset
            ]);
        return $link->getData();
    }

    /**
     * Возвращает массив данных параметров для иконки мобильной кнопки
     *
     * @return array
     */
    private static function getChildParamsDataForMobileButtonIcon()
    {
        $buttonIconMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $buttonIconMenuAPreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '26px'),
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '26px')
        ]);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-size' => 'contain',
            'background-image' => '/modules/designer/view/img/iconsset/action/menu.svg',
        ]);
        $icon = new DesignAtoms\Items\SubAtom();
        $icon->setTag('span')
            ->setTitle(t('Иконка кнопки мобильного меню'))
            ->setClass('d-atom-menu-mobile-button-icon')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $buttonIconMenuAPreset
            ]);
        return $icon->getData();
    }

    /**
     * Возвращает массив данных параметров для текста мобильной кнопки
     *
     * @return array
     */
    private static function getChildParamsDataForMobileButtonText()
    {
        $buttonTextMenuAPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $buttonTextMenuAPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), [
                'top'    => '0px',
                'right'   => '0px',
                'bottom' => '0px',
                'left'  => '10px'
            ])
        ]);
        $textedit = new DesignAtoms\CSSPresets\TextEdit();
        $textedit->setDefaults();
        $textedit->setDefaults([
            'font-size' => '16px',
            'color' => '#000000FF',
        ]);
        $link = new DesignAtoms\Items\SubAtom();
        $link->setTag('span')
            ->setTitle(t('Текст кнопки мобильного меню'))
            ->setClass('d-atom-menu-mobile-button-text')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Background(),
                new DesignAtoms\CSSPresets\Border(),
                $textedit,
                $buttonTextMenuAPreset
            ]);
        return $link->getData();
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
            $childs['menu_first_level']           = self::getChildParamsDataForFirstLevelMenu();
            $childs['menu_first_level_li']        = self::getChildParamsDataForFirstLevelMenuLi();
            $childs['menu_first_level_span']      = self::getChildParamsDataForFirstLevelMenuDown();
            $childs['menu_first_level_span_open'] = self::getChildParamsDataForFirstLevelMenuDownOpen();
            $childs['menu_first_level_a']      = self::getChildParamsDataForFirstLevelMenuA();
            $childs['menu_second_level']       = self::getChildParamsDataForSecondLevelMenu();
            $childs['menu_second_level_li']    = self::getChildParamsDataForSecondLevelMenuLi();
            $childs['menu_second_level_span']  = self::getChildParamsDataForSecondLevelMenuDown();
            $childs['menu_second_level_span_open']  = self::getChildParamsDataForSecondLevelMenuDownOpen();
            $childs['menu_second_level_a']     = self::getChildParamsDataForSecondLevelMenuA();
            $childs['menu_third_level']        = self::getChildParamsDataForThirdLevelMenu();
            $childs['menu_third_level_li']     = self::getChildParamsDataForThirdLevelMenuLi();
            $childs['menu_third_level_a']      = self::getChildParamsDataForThirdLevelMenuA();
            $childs['menu_mobile_button']      = self::getChildParamsDataForMobileButton();
            $childs['menu_mobile_button_icon'] = self::getChildParamsDataForMobileButtonIcon();
            $childs['menu_mobile_button_text'] = self::getChildParamsDataForMobileButtonText();
        }
        return $childs;
    }

    /**
     * Заполняет данными сведения о потомках для категорий или меню третьего уровня
     *
     * @param array $data - установленные данные атома
     * @param array $tree - дерево категорий или меню
     * @param array $children_params - массив данных параметров для генерации
     * @param array $children - ссылка на массив сгенерированных детей
     */
    private static function getFillChildsDataForRenderThirdLevel($data, $tree, $children_params, &$children)
    {
        $child = $children_params['menu_third_level'];
        $child['attrs']['class']['value'] .= " ".$data['attrs']['third_menu_type']['value'];

        foreach ($tree as $item){
            $liChild = $children_params['menu_third_level_li'];
            $aChild  = $children_params['menu_third_level_a'];
            $aChild['attrs']['href']['value'] = $item['link'];
            $aChild['html']      = $item['title'];
            $liChild['childs'][] = $aChild;
            $child['childs'][]   = $liChild;
        }
        $children[] = $child;
    }

    /**
     * Заполняет данными сведения о потомках для категорий или меню второго уровня
     *
     * @param array $data - установленные данные атома
     * @param array $tree - дерево категорий или меню
     * @param array $children_params - массив данных параметров для генерации
     * @param array $children - ссылка на массив сгенерированных детей
     */
    private static function getFillChildsDataForRenderSecondLevel($data, $tree, $children_params, &$children)
    {
        $child = $children_params['menu_second_level'];
        $child['attrs']['class']['value'] .= " ".$data['attrs']['second_menu_type']['value'];

        foreach ($tree as $item){
            $liChild = $children_params['menu_second_level_li'];
            $aChild  = $children_params['menu_second_level_a'];
            $aChild['attrs']['href']['value']   = $item['link'];
            $aHTMl = $item['title'];
            if ($data['attrs']['show_down_menu_icon']['value'] &&
                !empty($item['childs']) &&
                $data['attrs']['menu_show_level']['value'] == 3){ //Если нужно мобильное меню
                $aHTMl .= '<span class="d-atom-sub-menu-down"></span>';
            }
            $aChild['html']      = $aHTMl;
            $liChild['childs'][] = $aChild;

            if (!empty($item['childs']) && $data['attrs']['menu_show_level']['value'] == 3){ //Если есть потомки и второй уровень нужен
                self::getFillChildsDataForRenderThirdLevel($data, $item['childs'], $children_params, $liChild['childs']);
            }

            $child['childs'][] = $liChild;
        }

        $children[] = $child;
    }

    /**
     * Заполняет данными сведения о потомках для категорий или меню первого уровня
     *
     * @param array $data - установленные данные атома
     * @param array $tree - дерево категорий или меню
     * @param array $children_params - массив данных параметров для генерации
     * @param array $children - ссылка на массив сгенерированных детей
     */
    private static function getFillChildsDataForRenderFirstLevel($data, $tree, $children_params, &$children)
    {
        $child = $children_params['menu_first_level'];
        $child['attrs']['class']['value'] .= ' d-front';
        if ($data['attrs']['show_mobile_button']['value'] == 1){ //Если нужно мобильное меню
            $child['attrs']['class']['value'] .= ' d-show-mobile-button';
        }
        foreach ($tree as $item){
            $liChild = $children_params['menu_first_level_li'];
            if ($data['attrs']['show_mobile_button']['value'] != 1 && !empty($item['childs'])){
                $liChild['attrs']['class']['value'] .= " d-has-childs";
            }
            $aChild  = $children_params['menu_first_level_a'];
            $aChild['attrs']['href']['value'] = $item['link'];
            $aHTMl = $item['title'];
            if ($data['attrs']['show_down_menu_icon']['value'] &&
                !empty($item['childs']) &&
                $data['attrs']['menu_show_level']['value'] >= 2){ //Если нужно мобильное меню
                $aHTMl .= '<span class="d-atom-menu-down"></span>';
            }
            $aChild['html']      = $aHTMl;
            $liChild['childs'][] = $aChild;

            if (!empty($item['childs']) && $data['attrs']['menu_show_level']['value'] >= 2){ //Если есть потомки и второй уровень нужен
                self::getFillChildsDataForRenderSecondLevel($data, $item['childs'], $children_params, $liChild['childs']);
            }
            $child['childs'][] = $liChild;
        }

        $children[] = $child;
    }

    /**
     * Заполняет данными сведения о потомках для генерации кнопки меню
     *
     * @param array $data - установленные данные атома
     * @param array $children_params - массив данных параметров для генерации
     * @param array $children - ссылка на массив сгенерированных детей
     */
    private static function getFillChildsDataForRenderMobileButton($data, $children_params, &$children)
    {
        $wrapper_child = self::getEmptySubAtomForRender('d-atom-menu-mobile-button-wrapper');

        //Ссылка
        $child = $children_params['menu_mobile_button'];

        $child['attrs']['class']['value']      .= " designer-mmenu";
        $child['attrs']['data-id']['value']    .= $data['id'];

        //Иконка
        $child['childs'][] = $children_params['menu_mobile_button_icon'];

        //Текст иконки
        if (!empty($data['attrs']['mobile_button_text']['value'])){ //Если нужно показать текст меню
            $text = $children_params['menu_mobile_button_text'];
            $text['html'] = $data['attrs']['mobile_button_text']['value'];
            $child['childs'][] = $text;
        }
        $wrapper_child['childs'][] = $child;
        $children[] = $wrapper_child;
    }

    /**
     * Генерирует мобильное меню
     *
     * @param array $data - установленные данные атома
     * @param array $item - массив пункта меню
     * @param integer $next_level - текущий уровень меню
     *
     * @return array
     */
    private static function getFillChildsDataForRenderMobileMenuItem($data, $item, $next_level)
    {
        $a_child = self::getEmptySubAtomForRender('d-mobile-mmenu-level-a', 'a', [
            'href' => $item['link']
        ]);
        $a_child['html'] = $item['title'];

        $li_child = self::getEmptySubAtomForRender('d-mobile-mmenu-level-li', 'li');
        $li_child['childs'][] = $a_child;

        if (!empty($item['childs']) && ($data['attrs']['menu_show_level']['value'] >= $next_level)){ //Если есть подкатегории добавим значок
            $span_child = self::getEmptySubAtomForRender('d-mobile-mmenu-open-level', 'span', [
                'data-title' => $item['title']
            ]);
            $li_child['childs'][] = $span_child;
        }

        if (!empty($item['childs']) && ($data['attrs']['menu_show_level']['value'] >= $next_level)){
            self::getFillChildsDataForRenderMobileMenu($data, $item, $li_child['childs'], $next_level);
        }
        return $li_child;
    }

    /**
     * Генерирует мобильное меню
     *
     * @param array $data - установленные данные атома
     * @param array $category - пункт меню
     * @param array $children - ссылка на массив сгенерированных детей
     * @param integer $current_level - текущий уровень меню
     */
    private static function getFillChildsDataForRenderMobileMenu($data, $category, &$children, $current_level = 1)
    {
        $ul_child = self::getEmptySubAtomForRender(($current_level == 1) ? 'd-mobile-mmenu-level d-open' : 'd-mobile-mmenu-level', 'ul');
        $next_level = $current_level + 1;
        $m = 0;
        if ($category['childs']){
            $tree = $category['childs'];
        }else{
            $tree = $category;
        }

        foreach ($tree as $item){
            $m++;
            if (($next_level > 2) && ($m == 1)){
                $span_child = self::getEmptySubAtomForRender('d-mobile-mmenu-open-level d-mobile-mmenu-open-level-close', 'span', [
                    'data-title' => $category['title']
                ]);

                $a_child = self::getEmptySubAtomForRender('d-mobile-mmenu-level-a', 'a', [
                    'href' => $category['link']
                ]);
                $a_child['html'] = $category['title'];

                $li_child = self::getEmptySubAtomForRender('d-mobile-mmenu-level-li d-mobile-mmenu-close-level', 'li', [
                    'data-title' => $category['title']
                ]);
                $li_child['childs'][] = $span_child;
                $li_child['childs'][] = $a_child;

                $ul_child['childs'][] = $li_child;
            }
            $ul_child['childs'][] = self::getFillChildsDataForRenderMobileMenuItem($data, $item, $next_level);
        }
        $children[] = $ul_child;
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
        $children = "<p class='d-no-element'>".t('Меню не назначеню')."</p>";

        if ($data['menu_type'] != 'none') { //Если меню назначена
            $api = new MenuApi();
            if ($data['menu_type'] == 'category'){ //Если это категория товара
                $tree = $api->getTreeForCategory($data['root'] );
                if (empty($tree)){
                    return "<p class='d-no-element'>".t('Список категорий пуст')."</p>";
                }
            }else{ //Если пункт меню
                $tree = $api->getTreeForMenus(true, $data['root']);
                if (empty($tree)){
                    return "<p class='d-no-element'>".t('Список меню пуст')."</p>";
                }
            }

            $children = [];
            $children_params = self::getChildParamsData();

            self::getFillChildsDataForRenderFirstLevel($data, $tree, $children_params, $children);

            if ($data['attrs']['show_mobile_button']['value']){ //Если нужно показывать моибльное меню
                self::getFillChildsDataForRenderMobileButton($data, $children_params, $children);

                $mobile_menu_wrapper = self::getEmptySubAtomForRender('d-mmenu-wrapper', 'div', [
                    'data-mmenu-id' => $data['id'],
                    'data-title'    => $data['attrs']['mobile_menu_header']['value'],
                ]);

                $mobile_menu_wrapper['childs'] = [];
                self::getFillChildsDataForRenderMobileMenu($data, $tree, $mobile_menu_wrapper['childs']);
                $children[] = $mobile_menu_wrapper;
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
        $data['menu_type'] = 'menu';
        $data['root'] = 0;
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

        //Добавим доп. класс
        $attrs['class'] .= " d-atom-menu-wrapper";

        return $attrs;
    }
}
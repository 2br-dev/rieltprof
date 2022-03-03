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
 * Class MMenu - класс мобильного меню
 */
class MMenu extends DesignAtoms\AbstractAtom {
    protected $title = "Мобильное меню"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();
        $this->name = t("Мобильное меню");
        $this->setClass('d-mobile-mmenu');

        $menuPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $menuPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
        ]);

        $this->addCSSPreset([
            self::getBackgroundParamsSettings(),
            new DesignAtoms\CSSPresets\Border(),
            $menuPreset
        ]);
    }

    /**
     * Возвращает массив данных параметров для настроек заднего фона
     *
     * @param string $background_color - цвет заднего фона
     *
     * @return DesignAtoms\CSSPresets\AbstractCssPreset
     */
    public static function getBackgroundParamsSettings($background_color = '#FFFFFFFF')
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
            'font-size' => '14px',
            'color' => '#000000FF'
        ]);
        return $textedit;
    }

    /**
     * Возвращает массив данных параметров для заголовка
     *
     * @return array
     */
    private static function getChildParamsDataForHeader()
    {
        $headerPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $text_align = new DesignAtoms\CSSProperty\Select('text-align', t('Расположение'), 'center');
        $text_align->setOptions([
            'left'   => t('Слева'),
            'center' => t('Центр'),
            'right'  => t('Справа')
        ]);
        $headerPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '20px',
                'right'  => '20px',
                'bottom' => '20px',
                'left'   => '20px'
            ]),
            $text_align
        ]);
        $text_edit = self::getTextEditParamsSettings();
        $text_edit->setDefaults([
            'font-size' => '16px',
            'font-weight' => 'bold',
            'color' => '#FFFFFFFF'
        ]);
        $header = new DesignAtoms\Items\SubAtom();
        $header->setTag('div')
            ->setTitle(t('Заголовок меню'))
            ->setClass('d-mobile-mmenu-header')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#000000FF'),
                $text_edit,
                new DesignAtoms\CSSPresets\Border(),
                $headerPreset
            ]);
        return $header->getData();
    }

    /**
     * Возвращает массив данных параметров для кнопки закрытия
     *
     * @return array
     */
    private static function getChildParamsDataForHeaderClose()
    {
        $headerClosePreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-color' => '#00000000',
            'background-repeat' => 'no-repeat',
            'background-position' => 'center center',
            'background-image' => '/modules/designer/view/img/iconsset/action/close_white.svg',
            'background-size' => 'contain',
        ]);
        $text_edit = self::getTextEditParamsSettings();
        $headerClosePreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '16px'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '16px'),
            new DesignAtoms\CSSProperty\Size('top', t('Отступ сверху'), '5px'),
            new DesignAtoms\CSSProperty\Size('right', t('Отступ справа'), '5px')
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Иконка закрытия меню'))
            ->setClass('d-mobile-mmenu-close')
            ->addCSSPreset([
                $background,
                $text_edit,
                new DesignAtoms\CSSPresets\Border(),
                $headerClosePreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для кнопки обёртки контейнера
     *
     * @return array
     */
    private static function getChildParamsDataForContent()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addMarginAndPaddingCSS();
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Общий блок с пунктами меню'))
            ->setClass('d-mobile-mmenu-content')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для меню
     *
     * @return array
     */
    private static function getChildParamsDataForMenu()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addMarginAndPaddingCSS();
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('ul')
            ->setTitle(t('Меню'))
            ->setClass('d-mobile-mmenu-level')
            ->addCSSPreset([
                self::getBackgroundParamsSettings(),
                new DesignAtoms\CSSPresets\Border(),
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для обёртки пункта меню
     *
     * @return array
     */
    private static function getChildParamsDataForMenuLi()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addMarginAndPaddingCSS();
        $border = new DesignAtoms\CSSPresets\Border();
        $border->setDefaults([
            'border' => [
                'top' => '0px',
                'left' => '0px',
                'bottom' => '1px',
                'right' => '0px',
                'border-type' => 'solid',
                'border-color' => '#C0C0C0FF',
            ]
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('li')
            ->setTitle(t('Обёртка меню'))
            ->setClass('d-mobile-mmenu-level-li')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#00000000'),
                $border,
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для ссылки
     *
     * @return array
     */
    private static function getChildParamsDataForMenuA()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('margin', t('Внешний отступ')),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), [
                'top'    => '14px',
                'right'   => '10px',
                'bottom' => '14px',
                'left'  => '10px'
            ])
        ]);
        $border = new DesignAtoms\CSSPresets\Border();
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle(t('Ссылка'))
            ->setClass('d-mobile-mmenu-level-a')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#00000000'),
                self::getTextEditParamsSettings(),
                $border,
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для ссылки закрытия меню
     *
     * @return array
     */
    private static function getChildParamsDataForMenuClose()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'))
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('a')
            ->setTitle(t('Ссылка закрытия меню'))
            ->setClass('d-mobile-mmenu-close-level')
            ->addCSSPreset([
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для переключения уровня меню
     *
     * @return array
     */
    private static function getChildParamsDataForMenuOpen()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addMarginAndPaddingCSS()->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Внешний отступ'), '48px')
        ]);
        $background = self::getBackgroundParamsSettings('#00000000');
        $background->setDefaults([
            'background-position' => 'center center',
            'background-repeat' => 'no-repeat',
            'background-image' => '/modules/designer/view/img/iconsset/action/arrow_right.svg',
            'background-size' => '24px 24px',
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('span')
            ->setTitle(t('Кнопка открытия подменю'))
            ->setClass('d-mobile-mmenu-open-level')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border(),
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для переключения уровня меню
     *
     * @return array
     */
    private static function getChildParamsDataForMenuCloseOpen()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\Image('background-image', t('Картинка заднего фона'),
                '/modules/designer/view/img/iconsset/action/arrow_left.svg'
            ),
        ]);
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('span')
            ->setTitle(t('Кнопка закрытия подменю'))
            ->setClass('d-mobile-mmenu-open-level-close')
            ->addCSSPreset([
                new DesignAtoms\CSSPresets\Border(),
                $contentPreset
            ]);
        return $item->getData();
    }


    /**
     * Возвращает массив данных параметров для затенения
     *
     * @return array
     */
    private static function getChildParamsDataForMenuFog()
    {
        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Затенение'))
            ->setClass('d-mobile-mmenu-fog')
            ->addCSSPreset([
                self::getBackgroundParamsSettings('#000000CC')
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для прокрутки
     *
     * @return array
     */
    private static function getChildParamsDataForMenuScroll()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '4px'),
            new DesignAtoms\CSSProperty\Color('background-color', t('Цвет заднего фона'), '#F5F5F5FF')
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Прокрутка'))
            ->setClass('d-mobile-mmenu-level::-webkit-scrollbar')
            ->addCSSPreset([
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для ползунка
     *
     * @return array
     */
    private static function getChildParamsDataForMenuScrollbarTrack()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigitsForBorder('border', t('Граница'), [
                'top' => '1px',
                'left' => '1px',
                'bottom' => '1px',
                'right' => '1px',
                'border-type' => 'solid',
                'border-color' => '#C0C0C0FF',
            ]),
            new DesignAtoms\CSSProperty\Color('background-color', t('Цвет заднего фона'), '#F5F5F5FF'),
            new DesignAtoms\CSSProperty\SizeFourDigits('border-radius', t('Закругление')),
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Подзунок'))
            ->setClass('d-mobile-mmenu-level::-webkit-scrollbar-track')
            ->addCSSPreset([
                $contentPreset
            ]);
        return $item->getData();
    }

    /**
     * Возвращает массив данных параметров для стрелочки
     *
     * @return array
     */
    private static function getChildParamsDataForMenuScrollbarThumb()
    {
        $contentPreset = new DesignAtoms\CSSPresets\AbstractCssPreset();

        $contentPreset->addCSS([
            new DesignAtoms\CSSProperty\Color('background-color', t('Цвет заднего фона'), '#C0C0C0FF'),
        ]);

        $item = new DesignAtoms\Items\SubAtom();
        $item->setTag('div')
            ->setTitle(t('Стрелка ползунка'))
            ->setClass('d-mobile-mmenu-level::-webkit-scrollbar-thumb')
            ->addCSSPreset([
                $contentPreset
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
            $childs['menu_header']          = self::getChildParamsDataForHeader();
            $childs['menu_header_close']    = self::getChildParamsDataForHeaderClose();
            $childs['menu_content']         = self::getChildParamsDataForContent();
            $childs['menu_menu']            = self::getChildParamsDataForMenu();
            $childs['menu_menu_li']         = self::getChildParamsDataForMenuLi();
            $childs['menu_menu_a']          = self::getChildParamsDataForMenuA();
            $childs['menu_menu_open']       = self::getChildParamsDataForMenuOpen();
            $childs['menu_menu_close']      = self::getChildParamsDataForMenuClose();
            $childs['menu_menu_close_open'] = self::getChildParamsDataForMenuCloseOpen();

            //Задний фон затенения
            $childs['menu_menu_fog']        = self::getChildParamsDataForMenuFog();

            //Прокрутка
            $childs['menu_menu_scroll']          = self::getChildParamsDataForMenuScroll();
            $childs['menu_menu_scrollbar_track'] = self::getChildParamsDataForMenuScrollbarTrack();
            $childs['menu_menu_scrollbar_thumb'] = self::getChildParamsDataForMenuScrollbarThumb();
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
        $children = "<p class='d-no-form'>".t('Меню не назначеню')."</p>";

        if ($data['menu_type'] != 'none') { //Если меню назначена
//            $form = new \Feedback\Model\Orm\FormItem($data['form_id']);
//            if (!$form['id']){
//                return "<p class='d-no-form'>".t('Форма с id=%1 не найдена или удалена', [$data['form_id']])."</p>";
//            }

            $children = [];
            $children_params = self::getChildParamsData();
        }
        return $children;
    }
}
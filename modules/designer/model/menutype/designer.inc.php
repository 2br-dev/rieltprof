<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\MenuType;

use Menu\Model\MenuType\Page;
use Templates\Model\ContainerApi;
use Templates\Model\Orm\Section;
use Templates\Model\Orm\SectionModule;
use Templates\Model\Orm\SectionPage;

/**
 * Класс описывает
 */
class Designer extends Page
{

    /**
     * Возвращает уникальный идентификатор для данного типа
     *
     * @return string
     */
    public function getId()
    {
        return 'designer';
    }

    /**
     * Возвращает название данного типа
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Страница Дизайнера');
    }

    /**
     * Возвращает описание данного типа
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Добавляет страницу, которую вы далее сможете настроить в режиме отладки с помощью визуального конструктора под названием Дизайнер');
    }

    /**
     * Возвраает класс иконки из коллекции zmdi
     *
     * @return string
     */
    public function getIconClass()
    {
        return 'zmdi-puzzle-piece';
    }

    /**
     * Возвращает поля, которые должны быть отображены при выборе данного типа
     *
     * @return \RS\Orm\FormObject
     */
    public function getFormObject()
    {
       return null;
    }

    /**
     * Обработчик, запускается, перед созданием пункта меню.
     * Здесь можно выполнить валидацию всех параметров
     */
    public function onBeforeCreate()
    {
        $default_page = SectionPage::loadByRoute('default', 'theme');
        if ($default_page['template']) {
            return $this->menu->addError(t('Данный тип элемента не может быть использован, если в конструкторе сайта у страницы по умолчанию задан шаблон, вместо использования разметки по сетке.'));
        }

        $mainContentBlock = $this->findMainContentBlock($default_page['id']);
        if (!$mainContentBlock) {
            return $this->menu->addError(t('Данный тип элемента не может быть использован, если в конструкторе сайта на странице по умолчанию нет Блока "Главное содержимое страницы"'));
        }
    }

    /**
     * Находит Блок Главное содержимое на странице page_id
     *
     * @param $page_id
     * @return mixed
     */
    protected function findMainContentBlock($page_id)
    {
        $blocks_data = SectionModule::getPageBlocks($page_id);
        foreach($blocks_data as $section_id => $blocks) {
            foreach($blocks as $block) {
                if ($block['module_controller'] == 'main\controller\block\maincontent') {
                    return $block;
                }
            }
        }
    }

    /**
     * Определяет в каком контейнере находится блок
     *
     * @param SectionModule $block
     * @return bool|mixed|\RS\Orm\Type\AbstractType
     */
    protected function findContainerType(SectionModule $block)
    {
        $section = new Section($block['section_id']);
        while($section['parent_id'] > 0 && $section['id']) {
            $section['id'] = null;
            $section->load($section['parent_id']);
        }

        if ($section['parent_id'] < 0) {
            return $section['parent_id'];
        }
        return false;
    }

    /**
     * Создает страницу в конструкторе сайта для пункта меню и
     * заменяет блок Главное содержимое страницы на блок Дизайнер
     */
    public function onCreate()
    {
        $default_page = SectionPage::loadByRoute('default', 'theme');
        $mainContentBlock = $this->findMainContentBlock($default_page['id']);
        $containerType = abs($this->findContainerType($mainContentBlock));
        $containerFrom = $default_page->getContainers()[$containerType];

        if ($containerType !== false) {
            $page = new SectionPage();
            $page['route_id'] = 'menu.item_'.$this->menu['id'];
            $page['context'] = 'theme';
            if ($page->insert()) {
                $api = new ContainerApi();
                $api->copyContainer($containerFrom['defaultObject']['id'], $page['id'], $containerType);
                $newMainContentBlock = $this->findMainContentBlock($page['id']);
                $newMainContentBlock['module_controller'] = 'designer\controller\block\designer';
                $newMainContentBlock->update();
            }
        }
    }

    /**
     * Обработчик обновления пункта меню
     *
     * @param $before_state
     */
    public function onUpdate($before_state, $new_state)
    {
        if ($before_state['typelink'] != $this->getId()
            && $new_state['typelink'] == $this->getId()) {
            $this->onCreate();
        }

        if ($before_state['typelink'] == $this->getId()
            && $new_state['typelink'] != $this->getId()) {
            $this->onDelete();
        }
    }
}
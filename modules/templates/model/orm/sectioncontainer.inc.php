<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Templates\Model\Orm;
use \RS\Orm\Type;

/**
 * Контейнер, в котором будут находиться секции.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $page_id 
 * @property integer $columns Ширина
 * @property string $title Название
 * @property string $css_class CSS класс
 * @property integer $is_fluid Ширина 100%
 * @property string $wrap_element Внешний элемент
 * @property string $wrap_css_class CSS-класс оборачивающего блока
 * @property string $outside_template Внешний шаблон
 * @property string $inside_template Внутренний шаблон
 * @property integer $type Порядковый номер контейнера на странице
 * --\--
 */
class SectionContainer extends \rs\orm\OrmObject
{
    protected static
        $table = 'section_containers',
        $cache_sections = []; //Все секции для каждой page_id
        
    function _init()
    {
        parent::_init();
        $this->getPropertyIterator()->append([
            'page_id' => new Type\Integer([
                'visible' => false,
                'index' => true,
                'no_export' => true
            ]),
            'columns' => new Type\Integer([
                'description' => t('Ширина'),
                'hint' => t('Количество условных колонок, из которых состоит сеточный контейнер')
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название'),
                'hint' => t('Не публичное. Используется для удобства работы с конструктором')
            ]),
            'css_class' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('CSS класс'),
                'hint' => t('CSS класс, который будет добавлен к элементу контейнера')
            ]),
            'is_fluid' => new Type\Integer([
                'description' => t('Резиновый контейнер(fluid)'),
                'hint' => t('Установите данный флажок, если необходимо, чтобы контейнер не имел фиксированной ширины'),
                'maxLength' => 1,
                'allowEmpty' => false,
                'checkboxView' => [1,0],
                'visible' => false,
                'bootstrapVisible' => true
            ]),
            'is_fluid' => new Type\Integer([
                'description' => t('Ширина 100%'),
                'hint' => t('Ширина будет 100% у всего контейнера. Резинвый контейнер(fluid) будет игнориоваться.'),
                'maxLength' => 1,
                'allowEmpty' => false,
                'checkboxView' => [1,0],
                'visible' => false,
                'bootstrapVisible' => true
            ]),
            'wrap_element' => new Type\Varchar([
                'description' => t('Внешний элемент'),
                'hint' => t('Если задано, то контейнер будет помещен в другой, выбранный здесь элемент'),
                'listFromArray' => [[
                    '' => t('не оборачивать'),
                    'div' => 'div',
                    'header' => 'header',
                    'footer' => 'footer',
                    'section' => 'section'
                ]]
            ]),
            'wrap_css_class' => new Type\Varchar([
                'description' => t('CSS-класс оборачивающего блока'),
                'hint' => t('Актуально только если задан внешний элемент')
            ]),
            'outside_template' => new Type\Template([
                'description' => t('Внешний шаблон'),
                'hint' => t('Вы можете создать Smarty шаблон, в котором добавить любой HTML вокруг переменной {$wrapped_content}. Во $wrapped_content будет HTML контейнера.')
            ]),
            'inside_template' => new Type\Template([
                'description' => t('Внутренний шаблон'),
                'hint' => t('Вы можете создать Smarty шаблон, в котором добавить любой HTML вокруг переменной {$wrapped_content}. Во $wrapped_content будет внутреннее содержимое контейнера.')
            ]),
            'type' => new Type\Integer([
                'description' => t('Порядковый номер контейнера на странице'),
                'maxLength' => '5',
                'visible' => false,
            ]),
        ]);
    }
    
    /**
    * Возвращает название контейнера
    * @return string
    */
    public function getTitle()
    {
        return !empty($this['title']) ? $this['title'] : t('Контейнер %0', [$this['type']]);
    }

    /**
     * Возвращает иерархию секций, расположенных в данном контейнере
     * @return array
     * @throws \RS\Orm\Exception
     */
    public function getSections()
    {
        if (!isset(self::$cache_sections[ $this['page_id'] ])) {
        
            self::$cache_sections[ $this['page_id'] ] = \RS\Orm\Request::make()
                ->from(new Section())
                ->where(['page_id' => $this['page_id']])
                ->orderby('parent_id, sortn')
                ->objects(null, 'parent_id', true);
        }
            
        return $this->makeSectionsTree(self::$cache_sections[ $this['page_id'] ], -$this['type']);
    }
    
    /**
    * Возвращает дерево секций и блоков
    * @return array
    */
    private function makeSectionsTree($sections, $parent_id)
    {
        $result = [];
        if (isset($sections[$parent_id])) {
            $branch = $sections[$parent_id];
            foreach($branch as $section) {
                if (isset($sections[ $section['id'] ])) {
                    $childs = $this->makeSectionsTree($sections, $section['id']);
                } else {
                    $childs = [];
                }
                $result[] = ['section' => $section, 'childs' => $childs];
            }
        }
        return $result;
    }

    /**
     * Меняет местами текущий контейнер и контейнер $destination_container_id
     *
     * @param integer $destination_container_id - ID контейнера для обмена позициями
     * @return bool
     * @throws \RS\Db\Exception
     * @throws \RS\Orm\Exception
     */
    function changePosition($destination_container_id)
    {
        $dst_container = new self($destination_container_id);
        //Не позволяем обмениваться позициями контейнерами с разных страниц
        if ($dst_container['page_id'] != $this['page_id']){  //Если страницы разные, то сделаем не делаем перемещения
            return false;
        }
        $dst_type              = $dst_container['type'];
        $dst_container['type'] = $this['type'];
        $this['type']          = $dst_type;
        
        if ($dst_container->update() && $this->update()) {
            //Перемещаем секции между контейнерами
            \RS\Orm\Request::make()
                ->update(new Section())
                ->set([
                    'parent_id' => null
                ])->where([
                    'parent_id' => -$this['type'],
                    'page_id' => $this['page_id']
                ])->exec();
                
            \RS\Orm\Request::make()
                ->update(new Section())
                ->set([
                    'parent_id' => -$this['type']
                ])->where([
                    'parent_id' => -$dst_container['type'],
                    'page_id' => $this['page_id']
                ])->exec();
                
            \RS\Orm\Request::make()
                ->update(new Section())
                ->set([
                    'parent_id' => -$dst_container['type']
                ])
                ->where('parent_id is null')
                ->where(['page_id' => $this['page_id']])
                ->exec();                    
            return true;
        }
        return false;
    }

    /**
     * Удаление
     *
     * @return bool
     * @throws \RS\Orm\Exception
     */
    function delete()
    {
        //Удаляем все секции, которые находятся внутри данного
        $sub_sections = \RS\Orm\Request::make()
            ->from(new Section())
            ->where([
                'parent_id' => -$this['type'], 
                'page_id' => $this['page_id']
            ])
            ->objects();
            
        foreach($sub_sections as $section) {
            $section->delete();
        }
        
        return parent::delete();
    }
    
    /**
    * Устанавливает допустимый список колонок для текущей сеточной системы
    * 
    * @param string $grid_system - тип сеточного фреймворка
    * @return void
    */
    function setColumnList($grid_system)
    {
        switch($grid_system) {
            case SectionContext::GS_BOOTSTRAP:
            case SectionContext::GS_BOOTSTRAP4: {
                $gs = [12]; break;
            }
            case SectionContext::GS_GS960: {
                $gs = [12, 16]; break;
            }
            default: {
                $gs = []; break;
            }
        }
        $result = [];
        foreach($gs as $column) {
            $result[$column] = t('%0 колонок', [$column]);
        }
        $this['__columns']->setListFromArray($result);
    }
}


<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $menutype Тип меню
 * @property string $title Название
 * @property integer $hide_from_url Не использовать для построения URL
 * @property string $alias Симв. идентификатор
 * @property integer $parent Родитель
 * @property integer $public Публичный
 * @property string $typelink Тип элемента
 * @property integer $sortn Порядк. №
 * @property integer $closed 
 * @property string $content Статья
 * @property string $link Ссылка
 * @property integer $target_blank Открывать ссылку в новом окне
 * @property string $link_template Шаблон
 * --\--
 */
class Menu extends OrmObject
{
    protected static $table = "menu";
    protected static $act_path; //Путь до корневого элемента активного пункта меню
        
    protected $cache_type;

    protected function _init()
    {
        parent :: _init();
        
        $this->getPropertyIterator()->append([
            t('Основные'),
                    'site_id' => new Type\CurrentSite(),
                    'menutype' => new Type\Varchar([
                        'maxLength' => '70',
                        'description' => t('Тип меню'),
                        'visible' => false,
                    ]),
                    'title' => new Type\Varchar([
                        'maxLength' => '150',
                        'description' => t('Название'),
                        'Checker' => ['chkEmpty', t('Необходимо заполнить поле название')],
                        'specVisible' => false,
                        'attr' => [[
                            'data-autotranslit' => 'alias'
                        ]]
                    ]),
                    'hide_from_url' => new Type\Integer([
                        'maxLength' => 1,
                        'description' => t('Не использовать для построения URL'),
                        'hint' => t('При активации данной опции у дочерних элементов не будет текущей секции'),
                        'checkboxView' => [1,0],
                        'specVisible' => false
                    ]),
                    'alias' => new Type\Varchar([
                        'maxLength' => '150',
                        'description' => t('Симв. идентификатор'),
                        'specVisible' => false,
                        'meVisible' => false,
                        'Checker' => ['chkEmpty', t('Необходимо заполнить поле Символьный идентификатор')],
                        'hint' => t('Символьный идентификатор используется для формирования адреса страницы для пункта меню')
                    ]),
                    'parent' => new Type\Integer([
                        'maxLength' => '11',
                        'description' => t('Родитель'),
                        'tree' => [['\Menu\Model\Api', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]],
                        'default' => 0,
                    ]),
                    'public' => new Type\Integer([
                        'maxLength' => '1',
                        'default' => 1,
                        'description' => t('Публичный'),
                        'CheckboxView' => [1,0],
                        'specVisible' => false,
                    ]),
                    'typelink' => new Type\Varchar([
                        'maxLength' => '20',
                        'description' => t('Тип элемента'),
                        'list' => [['\Menu\Model\Api', 'getMenuTypesNames']],
                        'Attr' => [['size' => 0]],
                        'hint' => \Menu\Model\Api::getMenuTypeDescriptions(),
                        'default' => 'article',
                        'meVisible' => false,
                        'specVisible' => false,
                        'template' => '%menu%/form/menu/other.tpl',
                    ]),
                    'sortn' => new Type\Integer([
                        'maxLength' => '11',
                        'description' => t('Порядк. №'),
                        'visible' => false,
                    ]),
                    'closed' => new Type\Integer([
                        'maxLength' => '1',
                        'runtime' => true,
                        'visible' => false,
                    ])
        ]);
        
        //Добавляем свойства из типов пункта меню
        $types = \Menu\Model\Api::getMenuTypes(false);
        foreach($types as $type) {
            if ($form = $type->getFormObject()) {
                $iterator = $form->getPropertyIterator()->setPropertyOptions(['visible' => false]);
                $this->getPropertyIterator()->appendPropertyIterator($iterator);
            }
        }
        
        $this
            ->addIndex(['site_id', 'alias', 'parent'], self::INDEX_UNIQUE)
            ->addIndex(['parent', 'sortn'])
            ->addIndex('site_id');
    }
    
    /**
    * Возвращает отладочные действия, которые можно произвести с объектом
    * 
    * @return \RS\Debug\Action[]
    */
    public function getDebugActions()
    {
        return [
            new \RS\Debug\Action\Edit(\RS\Router\Manager::obj()->getAdminPattern('edit', [':id' => '{id}'], 'menu-ctrl')),
            new \RS\Debug\Action\Delete(\RS\Router\Manager::obj()->getAdminPattern('del', [':chk[]' => '{id}'], 'menu-ctrl')),
            new \RS\Debug\Action\Create(\RS\Router\Manager::obj()->getAdminPattern('add', [':pid' => '{id}'], 'menu-ctrl'), t('создать подменю'))
        ];
    }
    
    function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG && !isset($this['sortn']))
        {
            $q = \RS\Orm\Request::make()
                ->select('MAX(sortn) max_sort')
                ->from($this)
                ->where([
                    'site_id' => $this->__site_id->get(),
                    'parent' => $this['parent'],
                    'menutype' => $this['menutype']
                ]);
                
                $this['sortn'] = $q->exec()
                ->getOneField('max_sort', -1) + 1;
        }
        $api = new \Menu\Model\Api();
        $parents_arrs = $api-> getPathToFirst($this['parent']);
        if($this['id'] && isset($parents_arrs[$this['id']])){
            return $this->addError(t('Неверно указан родительский элемент'), 'parent');
        }
        if ($this['id'] && $this['parent'] == $this['id']) {
            return $this->addError(t('Неверно указан родительский элемент'), 'parent');
        }
    }
    
    /**
    * Возвращает объект типа
    * 
    * @return \Menu\Model\MenuType\AbstractType
    */
    function getTypeObject($cache = true)
    {
        if (!$cache || $this->cache_type === null) {
            $list = \Menu\Model\Api::getMenuTypes();
            if (isset($list[$this['typelink']])) {
                $this->cache_type = clone $list[$this['typelink']];
            } else {
                //Тип пункта меню по умолчанию
                $this->cache_type = new \Menu\Model\MenuType\Article();
            }
            $this->cache_type->init($this);
        }
            
        return $this->cache_type;
    }
    
    /**
    * Проверяет, есть ли права для работы с данным пунктом меню у пользователя
    */
    function checkUserRights(\Users\Model\Orm\User $user = null)
    {
        if ($user === null) $user = \RS\Application\Auth::getCurrentUser();
        $access_menu = $user->getMenuAccess();
        
        if ($this['menutype'] == 'user' && in_array(FULL_USER_ACCESS, $access_menu)) return true;
        if (in_array($this['id'], $access_menu)) return true;
        return false;
    }
    
    /**
    * Возвращает URL в зависимости от типа пункта меню
    */
    function getHref()
    {
        return $this->getTypeObject()->getHref();
    }
    
    function isAct()
    {
        return $this->getTypeObject()->isActive();
    }
    
    /**
    * Возвращает клонированный объект меню
    * @return Menu
    */
    function cloneSelf()
    {
        /** @var \Menu\Model\Orm\Menu $clone */
        $clone = parent::cloneSelf();
        unset($clone['alias']);
        return $clone;
    }
}

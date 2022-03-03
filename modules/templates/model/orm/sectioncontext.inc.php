<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model\Orm;
use \RS\Orm\Type;
use RS\Orm\Type\Richtext;

/**
 * Настройки темы в рамках "Контекста"
 * --/--
 * @property integer $site_id ID сайта
 * @property string $context Контекст темы оформления
 * @property string $grid_system Тип сеточного фреймворка
 * @property string $options Настройки темы в сериализованном виде
 * @property array $options_arr Настройки темы
 * --\--
 */
class SectionContext extends \RS\Orm\AbstractObject
{
    const
        GS_NONE = 'none',
        GS_GS960 = 'gs960',
        GS_BOOTSTRAP = 'bootstrap',
        GS_BOOTSTRAP4 = 'bootstrap4';

    protected static
        $table = 'section_context';
    
    protected
        $before_grid_system;
        
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'context' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Контекст темы оформления'),
                'visible' => false
            ]),
            'grid_system' => new Type\Enum(array_keys(self::getGridSystemTitles()), [
                'description' => t('Тип сеточного фреймворка'),
                'listFromArray' => [self::getGridSystemTitles()],
                'allowEmpty' => false,
                'template' => '%templates%/form/sectioncontext/grid_system.tpl'
            ]),
            'options' => new Type\Text([
                'description' => t('Настройки темы в сериализованном виде'),
                'visible' => false
            ]),
            'options_arr' => new Type\ArrayList([
                'description' => t('Настройки темы'),
                'visible' => false
            ])
        ]);
        
        $this->addIndex(['site_id', 'context'], self::INDEX_PRIMARY);
    }

    /**
     * Возвращает полный список названий сеток
     *
     * @return array
     */
    public static function getGridSystemTitles()
    {
        return [
            self::GS_NONE => t('Без сетки'),
            self::GS_GS960 => t('GridSystem 960'),
            self::GS_BOOTSTRAP => t('Bootstrap 3'),
            self::GS_BOOTSTRAP4 => t('Bootstrap 4')
        ];
    }
    
    /**
    * Возвращает первичный ключ ORM-объекта
    * 
    * @return array
    */
    function getPrimaryKeyProperty()
    {
        return ['site_id', 'context'];
    }

    /**
     * Выполняет действия перед созранением объекта
     *
     * @param string $flag - флаг операции insert, update, replace
     * @return void
     * @throws \RS\Theme\Exception
     */
    function beforeWrite($flag)
    {
        //Нормализуем POST от дополнительных полей
        $options_arr = $this['options_arr'];        
        $form_object = $this->getContextFormOptionsObject(new \RS\Orm\PropertyIterator());
        foreach ($form_object->getPropertyIterator() as $key => $field) {            
            if (count($field->getCheckboxParam()) 
                && !isset($options_arr[$key])) 
            {
                $options_arr[$key] = $field->getCheckboxParam('off');
            }
        }
        
        $this['options'] = serialize($options_arr);
        
        $before = self::loadByWhere([
            'site_id' => $this['site_id'],
            'context' => $this['context']
        ]);
        if ($before['context']) {
            $this->before_grid_system = $before['grid_system'];
        }
    }
    
    function afterWrite($flag)
    {
        if ($this->before_grid_system !== null && $this->before_grid_system != $this['grid_system']) {
            //Очищаем страницы, контейнеры при смене сеточного фреймворка
            $pages_id = \RS\Orm\Request::make()
                        ->select('id')
                        ->from(new SectionPage())
                        ->where([
                            'site_id' => $this['site_id'],
                            'context' => $this['context']
                        ])->exec()->fetchSelected(null, 'id');
            
            $page_api = new \Templates\Model\PageApi();
            $page_api->del($pages_id);
        }
    }
    
    /**
    * Выполняет действия сразу после загрузки объекта 
    * 
    * @return void
    */
    function afterObjectLoad()
    {
        $this['options_arr'] = @unserialize($this['options']);
    }

    /**
     * Возвращает объект, с настройками темы оформления в рамках контекста
     *
     * @return \RS\Orm\FormObject
     * @throws \RS\Theme\Exception
     */
    function getContextFormObject()
    {
        $properties = clone $this->getPropertyIterator();
        $form_object = $this->getContextFormOptionsObject($properties);
        
        $form_object->getFromArray( (array)$this['options_arr'] + $this->getValues() );
        return $form_object;
    }

    /**
     * Добавляет к $properties поля для настройки темы оформления
     *
     * @param \RS\Orm\PropertyIterator $properties
     * @return \RS\Orm\FormObject
     * @throws \RS\Theme\Exception
     */
    function getContextFormOptionsObject(\RS\Orm\PropertyIterator $properties)
    {
        $theme = \RS\Theme\Item::makeByContext($this['context']);
        $theme_xml = $theme->getThemeXml();
        
        if (isset($theme_xml->options) && isset($theme_xml->options->group)) {
            foreach($theme_xml->options->group as $group) {
                if (isset($group['name'])) {
                    $properties->group((string)$group['name']);
                }
                foreach($group->option as $option) {
                    $properties[ (string)$option['name'] ] = $this->generateField($option);
                }
            }
        }
        return new \RS\Orm\FormObject($properties);
    }
    
    /**
    * Возвращат объект одного поля
    * @return \RS\Orm\Type\AbstractType
    */
    private function generateField($option)
    {
        switch((string)$option['type']) {
            case 'checkbox': {
                $field = new Type\Integer([
                    'checkboxView' => [1,0]
                ]);
                break;
            }
            case 'colorpicker': {
                $field = new Type\Color(); 
                break;
            }
            case 'richtext': {
                $field = new Type\Richtext();
                break;
            }
            case 'select': {
                $items = [];
                foreach($option->values->value as $value) {
                    $items[ (string)$value['key'] ] = (string)$value;
                }
                $field = new Type\Varchar([
                    'listFromArray' => [$items]
                ]);
                break;
            }
            case 'text': {
                $field = new Type\Text();
                break;
            }
            default: {
                $field = new Type\Varchar();
            }
        }
        
        $field->setDescription($option->description);
        $field->setDefault($option->default);
        $field->setArrayWrap('options_arr');
        
        return $field;
    }
    
    /**
    * Возвращает тип сеточного фреймворка, используемого в теме оформления 
    * @return string
    */
    function getGridSystem()
    {
        return $this['grid_system'] ?: self::GS_GS960;
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model\Orm;
use RS\Config\Loader as ConfigLoader;
use \RS\Orm\Type;
use RS\Orm\Type\Richtext;
use Templates\Model\OrmType\ImageSelect;

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
        GS_BOOTSTRAP4 = 'bootstrap4',
        GS_BOOTSTRAP5 = 'bootstrap5';

    protected static
        $table = 'section_context';
    
    protected
        $grid_system_changed,
        $before_grid_system,
        $configs;

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
            self::GS_BOOTSTRAP4 => t('Bootstrap 4'),
            self::GS_BOOTSTRAP5 => t('Bootstrap 5')
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
        $this->configs = [];
        foreach ($form_object->getPropertyIterator() as $key => $field) {            
            if (count($field->getCheckboxParam()) && !isset($options_arr[$key])) {
                $options_arr[$key] = $field->getCheckboxParam('off');
            }

            if ($field instanceof \RS\Orm\Type\ArrayList && !isset($options_arr[$key])) {
                $options_arr[$key] = [];
            }

            if (isset($field->proxy_module)) {
                if ($config = ConfigLoader::byModule($field->proxy_module)) {
                    //Сохраняем proxy значения в объектах соответствующих конфигурации
                    $this->configs[$field->proxy_module] = $config;
                    $this->configs[$field->proxy_module]->$key = $options_arr[$key];
                }
                unset($options_arr[$key]);
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

            $this->grid_system_changed = true;
        }

        if ($this->configs) {
            foreach ($this->configs as $config) {
                $config->update();
            }
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
                    $key = (string)$option['name'];
                    $field = $this->generateField($option);
                    if ($field) {
                        $properties[$key] = $field;
                        if ((string)$option['type'] == 'proxy') {
                            $array = $this['options_arr'];
                            $array[$key] = $properties[$key]->get();
                            $this['options_arr'] = $array;
                        }
                    }
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
            case 'image-select': {
                $items = [];
                foreach($option->values->value as $value) {
                    $items[ (string)$value['key'] ] = (string)$value;
                }

                $field = new ImageSelect([
                    'listFromArray' => [$items]
                ]);
                if ((string)$option->imageExtenstion) {
                    $field->setImageExtension((string)$option->imageExtenstion);
                }

                break;
            }
            case 'proxy': {
                $config = ConfigLoader::byModule((string)$option['module']);
                if ($config) {
                    $field = $config['__' . (string)$option['name']];
                    $field->proxy_module = (string)$option['module'];
                } else {
                    return null;
                }
                break;
            }
            default: {
                $field = new Type\Varchar();
            }
        }
        
        $field->setDescription($option->description);
        $field->setDefault($option->default);
        $field->setHint($option->hint);
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

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Theme;
use Templates\Model as TemplatesModel,
    Templates\Model\Orm\SectionContext;

/**
* Класс - тема отображения
*/
class Item
{              
    protected
        $preview_file = 'preview{SHADE}.jpg',
        $template_path = SM_TEMPLATE_PATH, //Берется из \Setup;
        $selfpath,
        $simplexml,
        $blocks_xml,
        $relative_self_path,
        $shade,
        $name,
        $context,
        $fullname;
    
    /**
    * Конструктор объектов - "Тема оформления"
    * 
    * @param string $fullname - Полный идентификатор темы <ИМЯ ПАПКИ ТЕМЫ>[(НАЗВАНИЕ ОТТЕНКА В СКОБКАХ)][;КОНТЕКСТ БЛОКОВ]
    * @return Item
    */
    function __construct($fullname)
    {
        $this->init($fullname);
    }
    
    /**
    * Возвращает экземпляр данного класса, созданного по идентификатору контекста
    * 
    * @param string $context контекст темы оформления, по умолчанию
    * @param string shade идентификатор цветовой вариации
    * @return Item
    */
    public static function makeByContext($context = 'theme', $shade = null)
    {
        $context_list = Manager::getContextList();
        if (!isset($context_list[$context])) {
            throw new Exception(t("Контекст '%0' темы оформления не зарегистрирован в системе", [$context]));
        }        
        $theme = $context_list[$context]['theme'];
        $shade = $shade ? "($shade)" : '';
        return new self($theme.$shade.';'.$context);
    }
    
    /**
    * Инициализизирует тему оформления по полному идентификатору темы
    * 
    * @param string $fullname - Полный идентификатор темы <ИМЯ ПАПКИ ТЕМЫ>[(НАЗВАНИЕ ОТТЕНКА В СКОБКАХ)][;КОНТЕКСТ БЛОКОВ]
    * @return void
    */
    function init($fullname)
    {
        $theme = Manager::parseThemeValue($fullname);
        $this->name = $theme['theme'];
        $this->shade = $theme['shade'];
        $this->context = $theme['blocks_context'];
        $this->fullname = $fullname;
        $this->selfpath = $this->template_path.$this->name.'/';
        $this->relative_self_path = \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->name;
        if (!file_exists($this->getThemeXmlFilename())) {
            throw new Exception(t("Тема '%0' не найдена. (Нет файла с описанием темы '%1')", [$this->name, \Setup::$THEME_XML]));
        }        
    }
    
    /**
    * Возвращает имя папки Темы (он же уникальный идентификатор темы)
    * @return string
    */
    function getName()
    {
        return $this->name;
    }
    
    /**
    * Возвращает оттенок темы
    * @return string
    */
    function getShade()
    {
        return $this->shade;
    }
    
    /**
    * Возвращает контекст темы
    * @return string
    */
    function getContext()
    {
        return $this->context ?: 'theme';
    }
    
    /**
    * Возвращает полный строковый идентификатор темы
    * @return string
    */
    function getFullName()
    {
        return $this->fullname;
    }
    
    /**
    * Возвращает путь к корневой папке темы
    * @return string
    */
    function getSelfPath()
    {
        return $this->selfpath;
    }
    
    /**
    * Возвращает путь к шаблону относительно корня
    * @return string
    */
    function getRelativePath()
    {
        return $this->relative_self_path;
    }

    
    /**
    * Возвращает имя XML файла для текущей комплектации системы с информацией о структуре блоков
    * 
    * @return string
    */
    function getBlocksXmlFilename()
    {
        if (preg_match('/^(.*?)\.(.*?)$/', \Setup::$SCRIPT_TYPE, $match)) {
            $filename = $this->getSelfPath().'blocks_'.strtolower($match[2]).'.xml';
            if (file_exists($filename)) return $filename;
        }
        return $this->getSelfPath().'blocks.xml';
    }
    
    /**
    * Возвращает объект SimpleXml с настройками блоков
    * @return bool
    */
    function getBlocksXml()
    {
        $filename = $this->getBlocksXmlFilename();
        if (file_exists($filename)) {
            if ($this->blocks_xml === null) {
                $this->blocks_xml = new \SimpleXMLElement($filename, null, true);
            }
            return $this->blocks_xml;            
        }
        return false;
    }
    
    /**
    * Возвращает путь к файлу theme.xml
    * 
    * @return string
    */
    function getThemeXmlFilename()
    {
        return $this->selfpath.\Setup::$THEME_XML;
    }
    
    /**
    * Возвращает объект с конфигурацией темы
    * @return \SimpleXMLElement
    */
    function getThemeXml()
    {
        if ($this->simplexml === null) {
            $this->simplexml = new \SimpleXMLElement($this->getThemeXmlFilename(), null, true);
        }
        return $this->simplexml;
    }
    
    /**
    * Возвращает массив со списком оттенков темы
    * @return array
    */
    function getShades()
    {
        $result = [];
        $xml = $this->getThemeXml();

        if (isset($xml->shades)) {
            foreach($xml->shades as $shade) {
                foreach($shade as $attributes) {
                    $shade_arr = [];
                    foreach($attributes as $key => $value) {
                        $shade_arr[(string)$key] = (string)$value;
                    }
                    $result[$shade_arr['id']] = $shade_arr;
                }
            }
        }
        return $result;
    }
    
    /**
    * Возвращает массив с базовой информацией о теме
    */
    function getInfo()
    {
        $result = [];
        $xml = $this->getThemeXml();

        if (isset($xml->general)) {
            foreach($xml->general as $general) {
                $general_arr = [];
                foreach($general as $key => $value) {
                    $result[(string)$key] = t((string)$value, null, 'theme'); //Ищем перевод
                }
            }
        }
        return $result;
    }
    
    /**
    * Возвращает путь к preview изображения темы
    * 
    * @param mixed $shade
    */
    function getPreviewUrl($shade = '')
    {
        if (!empty($shade)) $shade = '_'.$shade;
        $preview_file = str_replace('{SHADE}', $shade, $this->preview_file);
        return $this->getRelativePath().'/'.$preview_file;
    }
    
    /**
    * Устанавливает тему в качестве действующей для текущего сайта
    * 
    * @param mixed $options - дополнительные параметры импорта (зарезервировано)
    * @param integer | null $site_id - ID сайта, у которого будет установлена тема. Если Null, то у текущего
    * @param string $context - идентификатор структуры блоков.
    */
    function setThisTheme($options = null, $site_id = null, $context = null, $set_in_config = true)
    {
        $context = $context ?: $this->getContext();
        $pageApi = new TemplatesModel\PageApi();
        $result = $pageApi->importFromXmlFile( $this->getBlocksXmlFilename(), $site_id, $context);
        if (!$result) {
            return $pageApi->getErrorsStr();
        }
        if ($set_in_config) {
            $load_site_id = (\RS\Site\Manager::getSiteId() == $site_id) ? null : $site_id;
            $config = \RS\Config\Loader::getSiteConfig( $load_site_id );
            $config['theme'] = $this->getFullName();
            return $config->replace();
        }
        return true;
    }
    
    /**
    * Удаляет структуру блоков для идентификатора
    * 
    * @param string $context - идентификатор структуры блоков
    * @param integer $site_id - ID сайта
    */
    function removeContext($context = null, $site_id = null)
    {
        $context = $context ?: $this->getContext();
        if ($context != 'theme') {
            return \RS\Orm\Request::make()
                ->delete()
                ->from(new TemplatesModel\Orm\SectionPage())
                ->where([
                    'site_id' => $site_id ?: \RS\Site\Manager::getSiteId(),
                    'context' => $context
                ])->exec();
        }
    }
    
    /**
    * Возвращает значения параметров темы по умолчанию
    * 
    * @return array
    */
    function getDefaultOptionValues()
    {
        $result = [];
        $theme_xml = $this->getThemeXml();
        if (isset($theme_xml->options)) {
            foreach($theme_xml->options->group as $group) {
                foreach($group->option as $option) {
                    $result[ (string)$option['name'] ] = (string)$option->default;
                }
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращает объект с настройками темы в рамках контекста.
    * 
    * @param integer $site_id - ID сайта, если null, то текущий сайт
    * @param \SimpleXMLElement $blocks_xml - внешний blocks.xml файл, из которого будут загружены параметры темы.
    * 
    * @return \Templates\Model\Orm\SectionContext | bool(false) 
    * False возвращается в случае, если модуль templates не обновлен и он еще не содержит класс настроек темы оформления
    */
    function getContextOptions($site_id = null, \SimpleXMLElement $blocks_xml = null)
    {
        if (!class_exists('\Templates\Model\Orm\SectionContext')) {
            return false;
        }
        
        $search_data = [
                'site_id' => $site_id ?: \RS\Site\Manager::getSiteId(),
                'context' => $this->getContext()
        ];
            
        $context_options = SectionContext::loadByWhere($search_data);
        
        if (!$context_options['context'] || $blocks_xml) {
            //Получаем тип сеточного фреймворка из blocks.xml
            if (!$blocks_xml) {
                $blocks_xml = $this->getBlocksXml();
            }
            if ($blocks_xml && isset($blocks_xml['grid_system'])) {
                $grid_system = (string)$blocks_xml['grid_system'];
            } else {
                //Или для совместимости с предыдущими версиями, по умолчанию присваиваем GS960
                $grid_system = SectionContext::GS_GS960;
            }
            
            //Если информации о контексте еще нет в базе создаем налету
            $context_options->getFromArray($search_data + [
                'grid_system' => $grid_system
                ]);
        }

        //Дописываем параметры темы по-умолчанию
        $context_options['options_arr'] = (array)$context_options['options_arr'] + $this->getDefaultOptionValues();        

        return $context_options;
    }
    
    /**
    * Сбрасывает настройки темы оформления в рамках контекста, 
    * которые описаны в объекте \Templates\Model\Orm\SectionContext.
    * Параметры темы, которые описаны в файле theme.xml не изменяются.
    * 
    * @param integer $site_id - ID сайта
    * @param \SimpleXMLElement | false $blocks_xml - объект файла blocks.xml
    * 
    * @return bool
    */
    function resetContextOptions($site_id = null, $blocks_xml = null)
    {
        if ($blocks_xml === false) {
            //Удаляем настройки из базы, если $blocks_xml отсутствует
            \RS\Orm\Request::make()
                ->delete()
                ->from(new SectionContext)
                ->where([
                    'site_id' => $site_id ?: \RS\Site\Manager::getSiteId(),
                    'context' => $this->getContext()
                ])->exec();
        } else {
            $context = $this->getContextOptions($site_id, $blocks_xml);
            return $context->replace();
        }
    }
    
    /**
    * Возвращает тип сеточного фреймворка, используемого темой оформления
    * 
    * @param integer $site_id - ID сайта, для которого необходимо вернуть сведения
    * @return \Templates\Model\Orm\SectionContext
    */
    function getGridSystem($site_id = null)
    {
        $context = $this->getContextOptions($site_id);
        return $context ? $context->getGridSystem() : 'gs960';
    }
    
}


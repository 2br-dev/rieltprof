<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use RS\Orm\Request as OrmRequest;
use RS\Theme\Manager as ThemeManager;
use Templates\Model\Orm\SectionModule;

/**
* Патчи к ядру
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает список имен существующих патчей
    */
    function init()
    {
        return [
            '200166',
            '5253'
        ];
    }

    /**
     * Патч, изменяет в таблице SectionModule старые block_id на новые.
     * Создает новые записи, не трогая при этом старые.
     *
     * @throws \RS\Event\Exception
     */
    function afterUpdate5253()
    {
        $theme = ThemeManager::getCurrentTheme('theme');
        $contexts = array_keys(ThemeManager::getContextList());

        $block_iterator = [];

        $root = \Setup::$PATH.'/templates/'.$theme;
        $root = str_replace('/', DIRECTORY_SEPARATOR, $root);

        if (is_dir($root)) {
            $Directory = new RecursiveDirectoryIterator($root);
            $Iterator = new RecursiveIteratorIterator($Directory);
            $Regex = new RegexIterator($Iterator, '/^.+\.tpl$/i', RecursiveRegexIterator::GET_MATCH);

            $exists_block_id = OrmRequest::make()->from(new SectionModule())
                ->where("template_block_id != ''")->exec()->fetchSelected("template_block_id", "template_block_id");

            foreach ($Regex as $file) {
                $filepath = $file[0];
                $relative_filepath = str_replace(['\\', \Setup::$PATH], ['/', ''], $filepath);
                $content = file_get_contents($filepath);
                if (preg_match_all('/{moduleinsert.*?name=[\'\"](.*?)[\'\"]/', $content, $match)) {
                    foreach($match[1] as $controller_name) {
                        $controller_name = trim($controller_name, '\\');
                        if (!isset($block_iterator[$filepath.$controller_name])) {
                            $block_iterator[$filepath.$controller_name] = 1;
                        } else {
                            $block_iterator[$filepath.$controller_name]++;
                        }
                        foreach ($contexts as $context) {
                            $old_block_id = crc32("{$filepath}_{$controller_name}_{$context}_" . $block_iterator[$filepath . $controller_name]);
                            $new_block_id = crc32("{$relative_filepath}_{$controller_name}_{$context}_" . $block_iterator[$filepath . $controller_name]);

                            $row = OrmRequest::make()->from(new SectionModule())
                                ->where([
                                    'template_block_id' => $old_block_id
                                ])->exec()->fetchRow();

                            if ($row && !isset($exists_block_id[$new_block_id])) {
                                $new_data = new SectionModule();
                                $new_data->getFromArray($row);
                                $new_data['id'] = null;
                                $new_data['template_block_id'] = $new_block_id;
                                $new_data->insert();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
    * Патч, удаляет ошибочную строку из модуля extcsv, site
    */
    function beforeUpdate200166()
    {
        $fix_file = \Setup::$PATH.'/modules/extcsv/config/handlers.inc.php';
        if (file_exists($fix_file)) {
            $content = file_get_contents($fix_file);
            $content = str_replace("\$this->bind('orm.init.catalog-product');", '', $content);
            file_put_contents($fix_file, $content);
        }
        
        $fix_file = \Setup::$PATH.'/modules/site/config/handlers.inc.php';
        if (file_exists($fix_file)) {
            $content = file_get_contents($fix_file);
            if (strpos($content, 'static function start()') === false) {
                $content = str_replace("->bind('start')", '', $content);
                file_put_contents($fix_file, $content);
            }
        }
            
        \RS\Cache\Cleaner::obj()->cleanOpcache();
        \RS\Cache\Cleaner::obj()->clean(\RS\Cache\Cleaner::CACHE_TYPE_COMMON);        
    }
}

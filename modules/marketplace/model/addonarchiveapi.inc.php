<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Marketplace\Model;

/**
 * Класс содержит методы по работе с запакованными файлами дополнений
 */
class AddonArchiveApi extends \RS\Module\AbstractModel\BaseModel
{
    const TYPE_MODULE = 'module';
    const TYPE_TEMPLATE = 'template';
    const TYPE_SOLUTION = 'solution';

    /**
     * Получить данные из архива модуля
     * @param $filename
     * @return array|bool
     */
    public function fetchModuleInfo($filename)
    {
        $zip = new \ZipArchive();
        $zip->open($filename);

        $first_folder_stat = $zip->statIndex(0);
        $mod_name = basename($first_folder_stat['name']);

        $config_content = $zip->getFromName("{$mod_name}/config/module.xml");

        if(!$config_content)
            return $this->addError(t('Отсутствует файл конфигурации <module-name>/config/module.xml'));

        $module_info = self::parseModuleConfig($config_content) + ['name' => $mod_name];

        if($mod_name !== strtolower($module_info['name'])){
            return $this->addError(t('Имя папки модуля должно соответствовать Namespace модуля в нижнем регистре'));
        }

        if(!$module_info['version'])
            return $this->addError(t('Не указана версия модуля в файле config/file.inc.php'));

        return $module_info;
    }


    /**
     * Получить данные из архива темы оформления
     * @param $filename
     * @return array|bool
     */
    public function fetchTemplateInfo($filename)
    {
        $zip = new \ZipArchive();
        $zip->open($filename);

        $first_folder_stat = $zip->statIndex(0);
        $theme_name = basename($first_folder_stat['name']);

        $config_content = $zip->getFromName("{$theme_name}/theme.xml");

        if(!$config_content)
            return $this->addError(t('Отсутствует файл информации о теме оформления <theme-folder>/theme.xml'));

        $template_info = self::parseThemeConfig($config_content);
        $template_info['name'] = $theme_name;

        if(!$template_info['version'])
            return $this->addError(t('Не указана версия темы в файле <theme-folder>/theme.xml'));

        return $template_info;
    }

    /**
     * Получить данные из архива решения
     * @param $filename
     * @return array|bool
     */
    public function fetchSolutionInfo($filename)
    {
        $info = $this->fetchModuleInfo($filename);
        $info['type'] = self::TYPE_SOLUTION;
        return $info;
    }


    /**
     * Определение типа дополнения исходя из его содержимого
     * @param $filename
     * @return string
     */
    public function getAddonType($filename)
    {

        if(!file_exists($filename))
            return $this->addError(t('Архив дополения не найден'));

        $zip = new \ZipArchive();
        $code = $zip->open($filename);

        if($code !== true){
            return $this->addError(t('Не удалось открыть архив. Код ошибки: %0', [$code]));
        }

        $root_items_count = $this->getZipItemsCount($zip);

        if($root_items_count != 1)
            return $this->addError(t('Архив дополения должен содержать только одну папку в корне. Найдено %0', [$root_items_count]));

        $first_folder_stat = $zip->statIndex(0);
        $first_folder_name = basename($first_folder_stat['name']);
        if($zip->getFromName("{$first_folder_name}/config/file.inc.php")){
            if($zip->getFromName("{$first_folder_name}/solutiontemplate/theme.xml"))
                return self::TYPE_SOLUTION;
            else
                return self::TYPE_MODULE;
        }
        else if($zip->getFromName("{$first_folder_name}/theme.xml"))
            return self::TYPE_TEMPLATE;
        else
            return $this->addError(t('Не найден файл конфигурации'));
    }


    /**
     * Получить количество элементов в ахриве по заданному пути
     * @param \ZipArchive $zip
     * @param string $path
     * @return int
     */
    private function getZipItemsCount(\ZipArchive $zip, $path = '')
    {
        return count($this->getZipItems($zip, $path));
    }


    /**
     * Получить имена элементов в ахриве по заданному пути
     * @param \ZipArchive $zip
     * @param string $path
     * @return array
     */
    private function getZipItems(\ZipArchive $zip, $path = '')
    {
        $items = [];
        for( $i = 0; $i < $zip->numFiles; $i++ ){
            $stat = $zip->statIndex( $i );
            if($path === '' || strpos($stat['name'], $path) === 0){
                $dir_or_file = strtok($stat['name'], '/');
                $items[$dir_or_file] = true;
            }
        }
        return array_keys($items);
    }

    public static function parseModuleConfig($module_xml_content)
    {
        $info = [];
        $info['type'] = self::TYPE_MODULE;
        $xml = new \SimpleXMLElement($module_xml_content);
        
        $info['version'] = (string)$xml->defaultValues[0]->version;
        $info['title'] = (string)$xml->defaultValues[0]->name;
        $info['description'] = (string)$xml->defaultValues[0]->description;
        return $info;
    }

    public static function parseThemeConfig($config)
    {
        $xml = new \SimpleXMLElement($config);
        $info = [];
        $info['type'] = self::TYPE_TEMPLATE;
        $info['name'] = null;
        $info['version'] = $xml->general->version ? strval($xml->general->version) : null;
        $info['title'] = $xml->general->name ? strval($xml->general->name) : null;
        $info['description'] = $xml->general->description ? strval($xml->general->description) : null;
        $info['shades'] = [];
        if (isset($xml->shades->shade)) {
            foreach ($xml->shades->shade as $shade) {
                $info['shades'][] = (array)$shade;
            }
        }
        return $info;
    }

}
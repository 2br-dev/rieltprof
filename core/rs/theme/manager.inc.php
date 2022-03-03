<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Theme;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Config\Loader as ConfigLoader;
use RS\File\Tools as FileTools;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс для работы со списком тем
 */
class Manager extends BaseModel
{
    protected static $current_theme;

    protected $tmp_folder = '/storage/tmp/theme';
    protected $allow_theme_mime = ['application/zip', 'application/x-zip-compressed'];
    protected $hide_themes = ['system'];
    protected $template_path = SM_TEMPLATE_PATH; //Берется из \Setup;

    /**
     * Возвращает список существующих тем
     * @return Item[]
     */
    function getList()
    {
        static $list;

        if ($list === null) {
            $list = [];
            $dirs = glob($this->template_path . "*", GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $name = basename($dir);
                if (!in_array($name, $this->hide_themes) && self::issetTheme($name)) {
                    $list[$name] = new Item($name);
                }
            }
        }

        return $list;
    }

    /**
     * Проверяет существование темы
     *
     * @param string $name - Идентификатор темы (имя папки)
     * @return boolean
     */
    public static function issetTheme($name)
    {
        $theme = self::parseThemeValue($name);
        $dirname = SM_TEMPLATE_PATH . $theme['theme'] . '/' . \Setup::$THEME_XML;
        return file_exists($dirname);
    }

    /**
     * Возвращает ассоциативный массив id темы => название
     * @return array
     */
    public static function selectList()
    {
        $obj = new self();
        $list = $obj->getList();
        $result = [];
        foreach ($list as $item) {
            $base = $item->getInfo();
            $result[$item->getName()] = (string)$base['name'];
        }
        return $result;
    }

    /**
     * Возвращает массив с названием текущей темы и текущим оттенком или значение заданного ключа из массива
     * @param string | null (theme, shade, blocks_context, full_name) $key - название ключа в массиве, значение которого нужно вернуть.
     *
     * @return array | string
     */
    public static function getCurrentTheme($key = null)
    {
        if (!isset(self::$current_theme)) {
            $config = ConfigLoader::getSiteConfig();
            self::$current_theme = self::parseThemeValue($config['theme']);
        }
        return $key ? self::$current_theme[$key] : self::$current_theme;
    }

    /**
     * Возвращает тип шаблонизатора текущей темы оформления. Например bootstrap, bootstrap4
     *
     * @return string
     */
    public static function getCurrentThemeGrid()
    {
        $theme_settings = \RS\Theme\Manager::getCurrentTheme();
        $theme = new \RS\Theme\Item($theme_settings['full_name']);
        return $theme->getGridSystem();
    }
    
    /**
     * Устанавливает название темы для текущего сайта
     *
     * @param string $theme_str - Название темы, цвета, ID схемы блоков в виде строки, например: default(black);theme
     * @return void
     */
    public static function setCurrentTheme($theme_str)
    {
        self::$current_theme = self::parseThemeValue($theme_str);
    }

    /**
     * Возвращает список возможных контекстов для темы.
     * Данный список должны наполнять те модули, которые создают контексты
     *
     * @return array [
     *   ['title' => НАЗВАНИЕ КОНТЕКСТА, 'theme' => 'НАЗВАНИЕ ТЕМЫ, К КОТОРОЙ ОН ПРИНАДЛЕЖИТ']
     * ]
     * @throws \RS\Event\Exception
     */
    public static function getContextList()
    {
        $event_result = \RS\Event\Manager::fire('theme.getcontextlist', []);
        $result = [
                'theme' => [
                    'title' => t('Основная тема'),
                    'theme' => ConfigLoader::getSiteConfig()->getThemeName()
                ]
            ] + $event_result->getResult();

        return $result;
    }

    /**
     * Парсит название темы, выделяет название, оттенок, контекст блоков
     * Формат темы: <ИМЯ ПАПКИ ТЕМЫ>[(НАЗВАНИЕ ОТТЕНКА В СКОБКАХ)][;КОНТЕКСТ БЛОКОВ]
     * Например: default или default(black) или default(black);theme или default;theme
     *
     * @param mixed $theme_str
     * @return array
     */
    public static function parseThemeValue($theme_str)
    {
        $result = [
            'theme' => $theme_str,
            'shade' => '',
            'blocks_context' => 'theme', //Контекст блоков по-умолчанию
            'full_name' => $theme_str
        ];

        if (preg_match('/^([^;\(]*?)\((.*?)\)/', $theme_str, $match)) {
            $result['theme'] = $match[1];
            $result['shade'] = $match[2];
        }

        //Если задан контекст блоков
        if (preg_match('/^([^;\(]*).*?;(.*?)$/', $theme_str, $match)) {
            $result['theme'] = $match[1];
            $result['blocks_context'] = $match[2];
        }
        return $result;
    }

    /**
     * Загружает zip файл с темой
     *
     * @param $post_file_arr - массив с параметрами файла из $_FILES
     * @param bool $overwrite
     * @return bool
     * @throws \RS\Exception
     */
    function uploadThemeZip($post_file_arr, $overwrite = false)
    {
        if ($acl_err = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE)) {
            $this->addError($acl_err);
            return false;
        }

        $fieldname = t('zip-файл с темой');
        if (empty($post_file_arr)) {
            return $this->addError(t('Файл не загружен на сервер'), $fieldname);
        }
        $error = FileTools::checkUploadError($post_file_arr['error']);
        if ($error !== false) {
            return $this->addError($error, $fieldname);
        }

        //Распаковываем тему
        $tmp_folder = \Setup::$PATH . $this->tmp_folder;
        FileTools::makePath($tmp_folder);
        FileTools::deleteFolder($tmp_folder, false);

        $filename = $post_file_arr['tmp_name'];
        $zip = new \ZipArchive;
        if (!$zip->open($filename) || !$zip->extractTo($tmp_folder)) {
            return $this->addError(t('Ошибка распаковки архива'), $fieldname);
        }
        $zip->close();

        //Проверяем корректность темы
        $check = $this->checkTmpTheme($tmp_folder, $overwrite);
        if ($check !== true) {
            return $this->addError($check, $fieldname);
        }
        //Переносим тему в основной каталог
        if (!$this->moveUploadedTheme($tmp_folder)) {
            return $this->addError(t('Не удалось перенести тему в основной каталог'), $fieldname);
        }

        return true;
    }

    /**
     * Проверяет корректность только что загруженной темы
     *
     * @param string $tmp_folder
     * @param $overwrite
     * @return boolean(true) | string
     */
    function checkTmpTheme($tmp_folder, $overwrite)
    {
        //Должна быть одна папка, в которой xml файл с описанием темы
        $folders = glob($tmp_folder . '/*', GLOB_ONLYDIR);
        if (count($folders) != 1) {
            return t('В архиве должна быть одна папка с именем темы');
        }
        if (!file_exists($folders[0] . '/' . \Setup::$THEME_XML)) {
            return t('В папке темы должен присутствовать файл %0', [\Setup::$THEME_XML]);
        }
        if (!$overwrite && self::issetTheme(basename(strtolower($folders[0])))) {
            return t('Загружаемая тема уже присутствует в системе');
        }
        return true;
    }

    /**
     * Переносит тему из временного хранилища в основную папку тем
     *
     * @param string $tmp_folder
     * @return boolean
     */
    protected function moveUploadedTheme($tmp_folder)
    {
        $folders = glob($tmp_folder . '/*', GLOB_ONLYDIR);
        $destination = $this->template_path . strtolower(basename($folders[0]));

        FileTools::deleteFolder($destination);
        return rename($folders[0], $destination);
    }

    /**
     * Устанавливает тему для текущего сайта
     *
     * @param mixed $theme
     * @return bool
     */
    function setTheme($theme)
    {
        if (self::issetTheme($theme)) {
            $theme_item = new Item($theme);
            $result = $theme_item->setThisTheme();
            if ($result !== true) {
                return $this->addError($result);
            }
            return true;
        } else {
            return $this->addError(t('Тема не найдена'));
        }
    }
}

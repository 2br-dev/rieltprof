<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Exception as RsException;
use RS\File\Tools as FileTools;
use RS\Html\Filter\Control as FilterControl;
use RS\Html\Table\Control as TableControl;
use RS\Module\AbstractModel\BaseModel;
use RS\Module\Manager as ModuleManager;
use RS\Theme\Manager as ThemeManager;
use Setup;
use ZipArchive;

/**
 * Класс содержит нобходимые методы для работы с языковыми файлами
 */
class LangApi extends BaseModel
{
    const TIMEOUT = 15; //секунд
    const FILTER_SESSION_VAR = 'lang_filter';
    const FILTER_SHOW_WITH_TRANSLATE = 'with_translate';
    const FILTER_SHOW_NO_TRANSLATE = 'no_translate';

    const CORE_MODULE_ID = '@core';
    const LANG_FILE_TYPE_JS = 'js';
    const LANG_FILE_TYPE_PHP = 'php';
    private $current_lang;
    private $filter;
    private $table_group_rows;
    /**
     * @var array
     */
    private $all_phrases;


    /**
     * Метод, создающий файлы локализации по всему проекту
     *
     * @param string $lang - язык для которого подготовить файлы
     * @param string $filter - фильтрует модули, для которых нужно создать языковый файл.
     * Может содержать "имя папки модуля" или "@core" или "#имя шаблона"
     * @param int $position Позиция, с которой начинать обработку
     * @return bool(true)|int Возвращает true, если операция завершена полностью.
     */
    public function createLangFiles($lang, $filter = null, $position = 0)
    {
        $folders = [];
        $lang = strtolower($lang);
        // Создаем файлы локализациия для всех модулей
        foreach(glob(Setup::$PATH.'/modules/*', GLOB_ONLYDIR) as $one){
            $module = basename($one);
            if (!$filter || $filter == $module) {
                $folders[] = [
                    'path' => $one,
                    'module' => $module
                ];
            }
        }

        // Создаем файлы локализациия для всех тем оформления
        foreach(glob(Setup::$PATH.'/templates/*', GLOB_ONLYDIR) as $one){
            $template = '#'.basename($one);
            if (!$filter || $filter == $template) {
                $folders[] = [
                    'path' => $one,
                    'module' => $template
                ];
            }
        }

        if (!$filter || $filter == self::CORE_MODULE_ID) {
            // Создаем файлы локализации для кода, находящегося в папке [core]
            $folders[] = [
                'path' => Setup::$PATH . '/core',
                'module' => self::CORE_MODULE_ID
            ];
            // Создаем файлы локализации для кода, находящегося в папке [resources]
            $folders[] = [
                'path' => Setup::$PATH . '/resource',
                'module' => self::CORE_MODULE_ID
            ];
            // Создаем файлы локализации для кода, находящегося в папке [templates/system]
            $folders[] = [
                'path' => Setup::$PATH . '/templates/system',
                'module' => self::CORE_MODULE_ID
            ];
        }

        $start_time = microtime(true);
        foreach($folders as $n => $item) {
            if ($n < $position) continue;
            $this->createLangFilesForDirectory($item['path'], $item['module'], $lang);

            if (microtime(true) - $start_time > self::TIMEOUT) {
                return $n + 1;
            }
        }

        return true;
    }

    /**
     * Создает языковые файлы для определенной директории
     *
     * @param string $directory_path - путь к папке
     * @param string $module Идентификатор модуля
     * @param string $lang Идентификатор языка
     */
    protected function createLangFilesForDirectory($directory_path, $module, $lang)
    {
        $php_strings_output_file = $this->getModuleLangFilepath($module, self::LANG_FILE_TYPE_PHP, $lang);
        $js_strings_output_file = $this->getModuleLangFilepath($module, self::LANG_FILE_TYPE_JS, $lang);

        // Поиск функции t()
        $php_strings = $this->getStringsFromDirByTokenizer($directory_path, array('php'));

        // Регулярное выражение для поиска tpl тэга {t} {/t}
        $patterns = [
            '/\{t(?:\s+?.*?)?}([^{}]+?)\{\/t\}/s',
        ];
        
        $tpl_strings = $this->getStringsFromDir($directory_path, ['tpl'], $patterns);
        $php_strings = array_merge($php_strings, $tpl_strings);

        // Регулярное выражение для поиска JS функции lang.t()
        $patterns = [
            '/\Wlang\.t\s*?\(\s*?\'(.*?)\'\s*?[,\)]/s',
            '/\Wlang\.t\s*?\(\s*?\"(.*?)\"\s*?[,\)]/s',
        ];
        $js_strings = $this->getStringsFromDir($directory_path, ['js', 'tpl'], $patterns);
        $js_strings = $this->prepareJsStrings($js_strings);

        FileTools::makePath($php_strings_output_file, true);
        FileTools::makePath($js_strings_output_file, true);
        array_unique($php_strings);
        array_unique($js_strings);
        
        // Сохраняем старые значения переведенных строк, котрые уже были сделаны в файле перевода
        if($old_strings = $this->loadTranslateFile($php_strings_output_file)) {
            $php_strings = array_merge($php_strings, $old_strings);
        }

        // Сохраняем старые значения переведенных строк, котрые уже были сделаны в файле перевода
        if($old_strings = $this->loadTranslateFile($js_strings_output_file)){
            $js_strings = array_merge($js_strings, $old_strings);
        }

        $this->writeLangFile($php_strings_output_file, $php_strings);
        $this->writeLangFile($js_strings_output_file, $js_strings);
    }

    /**
     * Обрабатывает JS фразы. Исключает из них экранирование
     *
     * @param array $js_strings
     * @return array
     */
    protected function prepareJsStrings($js_strings)
    {
        $result = [];
        foreach($js_strings as $phrase) {
            $phrase = preg_replace('/\\\\\r\n/', '', $phrase);
            $phrase = preg_replace('/\\\\\n/', '', $phrase);
            $phrase = preg_replace('/\\\\([\'\"])/', '$1', $phrase);
            $result[$phrase] = $phrase;
        }

        return $result;
    }


    /**
     * Получить все строки из директории
     *
     * @param string $directory_path - путь к папке
     * @param array $extensions - расширения файлов для поиска
     * @param array $patterns - массив парсинга
     * @return array
     */
    protected function getStringsFromDir($directory_path, array $extensions, array $patterns)
    {
        $ret = [];

        if(mb_stripos($directory_path,'node_modules') !== false){ //Пропускаем папку node_modules, чтобы она не учитывась
            return $ret;
        }
        
        foreach($extensions as $ex){
            foreach(glob($directory_path.'/*.'.$ex) as $one){
                if(preg_match('/tpl\.php$/', $one)){ //Пропускаем автоматически сгенерированные файлы кэша шаблонов
                    continue;
                }
                $ret = array_merge($ret, $this->getStringsFromFile($one, $patterns));
            }
        }
        
        $dirs = glob($directory_path.'/*', GLOB_ONLYDIR);
        foreach($dirs as $one){
            if(is_dir($one)){
                $substrings = $this->getStringsFromDir($one, $extensions, $patterns);
                $ret = array_merge($ret, $substrings);
            }
        }
        
        return $ret;
    }

    /**
     * Получить все строки из директории используя токинайзер PHP
     *
     * @param string $directory_path - путь к папке
     * @param array $extensions - расширения файлов для поиска
     * @return array
     */
    protected function getStringsFromDirByTokenizer($directory_path, array $extensions)
    {
        $ret = [];

        foreach($extensions as $ex){
            foreach(glob($directory_path.'/*.'.$ex) as $one){
                if(preg_match('/tpl\.php$/', $one)){ //Пропускаем автоматически сгенерированные файлы кэша шаблонов
                    continue;
                }
                $ret = array_merge($ret, $this->getStringsFromFileByTokenizer($one));
            }
        }

        $dirs = glob($directory_path.'/*', GLOB_ONLYDIR);
        foreach($dirs as $one){
            if(is_dir($one)){
                $substrings = $this->getStringsFromDirByTokenizer($one, $extensions);
                $ret = array_merge($ret, $substrings);
            }
        }

        return $ret;
    }

    /**
     * Получить все строки из конкретного файлы
     *
     * @param string $file_path - полный путь к файлу на диске
     * @param array $patterns - правила для парсинга
     * @return array
     */
    protected function getStringsFromFile($file_path, array $patterns)
    {
        $file_content = @file_get_contents($file_path);
        $ret = [];
        foreach($patterns as $one){
            preg_match_all($one, $file_content, $matches);
            $messages = [];
            if (!empty($matches[1])){
                foreach ($matches[0] as $key=>$match){
                    if (preg_match('/alias\s*?=\s*?[\'|"]([^\'"]+|)[\'|"]/i', $match, $aliases)){
                        $alias = "!".$aliases[1];
                        $messages[$alias] = $matches[1][$key];
                    }else{
                        $message = $matches[1][$key];
                        $messages[$message] = $message;
                    }
                }

                $ret = array_merge($ret, $messages);
            }
        }
        return $ret;
    }

    /**
     * Получить все строки из конкретного файла используя лексер для обхода
     *
     * @param string $file_path - полный путь к файлу на диске
     * @return array
     */
    protected function getStringsFromFileByTokenizer($file_path)
    {
        $tokens = token_get_all(file_get_contents($file_path));

        $ret = [];
        $t_function_token_start = false; //Началась ли конструкция обертки функцией t
        $t_array_token_start = false; //Началась ли конструкция обертки функцией t с параметрами
        $message_stack = []; //Стэк сообщений
        $brackets = 0; //Стэк скобок фукции t
        $brackets_array = 0; //Стэк скобок фукции t в массиве параметров
        $commas = 0; //Стэк запятых
        foreach ($tokens as $token) {
            if (is_array($token)) {
                $token_name  = token_name($token[0]); //Наименование токена
                $token_value = stripslashes(trim($token[1],"\"'"));
                if (!$t_function_token_start && ($token_name == "T_STRING") && ($token_value == "t")){
                    $t_function_token_start = true;
                }
                if ($t_function_token_start && ($token_name == "T_ARRAY")){ //Если начался массив параметров
                    $t_array_token_start = true;
                }
                if ($t_function_token_start && ($token_name == "T_CONSTANT_ENCAPSED_STRING")){
                    $message_stack[] = $token_value;
                }
            }else{
                if ($t_function_token_start && ($token == "(")){ //Увеличим стек скобок
                    $brackets++;
                }
                if ($t_function_token_start && ($token == ")")){ //Уменьшим стек скобок
                    $brackets--;
                }
                if ($t_array_token_start && ($token == "(")){ //Увеличим стек скобок
                    $brackets_array++;
                }
                if ($t_array_token_start && ($token == ")")){ //Уменьшим стек скобок
                    $brackets_array--;
                }
                if ($t_array_token_start && $brackets_array == 0){ //Исключим параметры массива
                    $t_array_token_start = false;
                }
                if ($t_function_token_start && !$t_array_token_start && ($token == ",")){ //Уменьшим стек скобок
                    $commas++;
                }
                if ($t_function_token_start && ($brackets == 0)){ //Если это конец обёртки функции t
                    $message = reset($message_stack);
                    if ((count($message_stack) > 1) && $commas > 1){ //Если присутствует alias
                        $alias = $message_stack[count($message_stack)-1];
                        $ret["!".$alias] = $message;
                    }else{ //Если, есть только фраза без псевдонима
                        $ret[$message] = $message;
                    }

                    //Сбросим на начало
                    $t_function_token_start = false;
                    $message_stack = [];
                    $brackets = 0;
                    $commas = 0;
                }
            }
        }

        return $ret;
    }

    /**
     * Возвращает ассоциативный массив со списком модулей и тем оформления, которые можно перевести
     *
     * @return array
     */
    static function getTranslateModuleList()
    {
        $module_manager = new ModuleManager();
        $modules = $module_manager->getList();

        $theme_manager = new ThemeManager();
        $themes = $theme_manager->getList();

        $result = [
            '' => t('Все'),
            '@core' => t('Ядро')
        ];
        foreach($modules as $name => $module) {
            $result[$name] = t('Модуль: %0', ['('.$name.') '.$module->getConfig()->name]);
        }

        foreach($themes as $name => $theme) {
            $result['#'.$name] = t('Тема оформления: %0', ['('.$name.') '.(string)$theme->getThemeXml()->general->name]);
        }

        return $result;
    }

    /**
     * Возвращает список языков, для которых созданы языковые файлы в системе
     *
     * @return array
     */
    static function getPossibleLang()
    {
        $result = [];
        $list = glob(Setup::$PATH.'/modules/*/view/lang/*');
        $list = array_merge($list, glob(Setup::$PATH.'/templates/*/resource/lang/*'));
        $list = array_merge($list, glob(Setup::$PATH.'/resource/lang/*'));

        foreach($list as $item) {
            if ($item !== false) {
                $lang = strtolower(basename($item));
                $result[$lang] = $lang;
            }
        }

        return $result;
    }

    /**
     * Возвращает относительную ссылку на созданный zip архив
     *
     * @param string $lang Идентификатор языка
     * @return string
     * @throws RsException
     */
    public function makeLangArchive($lang)
    {
        //Дополнительно валидируем
        $lang = preg_replace('/[^a-zA-Z]/', '', $lang);

        $filename = Setup::$TMP_REL_DIR.'/lang/RS_lang_'.strtolower($lang).'.zip';
        $file = Setup::$ROOT.$filename;
        FileTools::makePath($file, true);

        $zip = new ZipArchive();
        if ($zip->open($file, ZipArchive::CREATE) !== true) {
            throw new RsException(t('Не удалось создать zip архив'));
        }

        //Ядро
        $core = '/resource/lang/'.$lang;
        $this->addLangFolderToArchive($zip,  $core);

        //Модули и темы оформления
        $list = glob(Setup::$PATH.'/modules/*/view/lang/'.$lang);
        $list = array_merge($list, glob(Setup::$PATH.'/templates/*/resource/lang/'.$lang));

        foreach($list as $item) {
            $module_lang = str_replace(Setup::$PATH, '', $item);
            $this->addLangFolderToArchive($zip,  $module_lang);
        }

        $ok = $zip->numFiles > 0;

        $zip->close();
        return $ok ? $filename : false;
    }

    /**
     * Добавляет файлы одного модуля в архив
     *
     * @param ZipArchive $zip Объект архива
     * @param string $relative_folder Путь к папке, относительно корня сайта
     */
    protected function addLangFolderToArchive(ZipArchive $zip, $relative_folder)
    {
        $absolute = Setup::$PATH.$relative_folder;
        if (is_dir($absolute)) {
            $lng_php = '/messages.lng.php';
            if (file_exists($absolute.$lng_php)) {
                $zip->addFile($absolute.$lng_php, ltrim($relative_folder.$lng_php,'/'));
            }

            $lng_js = '/messages.js.php';
            if (file_exists($absolute.$lng_js)) {
                $zip->addFile($absolute.$lng_js, ltrim($relative_folder.$lng_js, '/'));
            }
        }
    }

    /**
     * Возвращает информацию по всем файлам переводов для всех модулей с учетом фильтра
     *
     * @return array
     */
    protected function getModulesLangFilepath()
    {
        $result = [];
        $module_manager = new ModuleManager();
        $modules = $module_manager->getList();

        $filter_by_module = $this->filter['module'] ?? '';

        foreach($modules as $name => $item) {
            if (!$filter_by_module || $filter_by_module == $name) {
                $result += $this->getLangFilepath($name, $item->getFolder() . '/view');
            }
        }

        $theme_manager = new ThemeManager();
        $current_theme = $theme_manager->getCurrentTheme('theme');

        $themes = $theme_manager->getList();
        foreach($themes as $name => $theme) {
            if (($filter_by_module == '' && $name == $current_theme) || ($filter_by_module == '#'.$name)) {
                $result += $this->getLangFilepath('#' . $name, $theme->getSelfPath() . 'resource');
            }
        }

        if ($filter_by_module == '' || $filter_by_module == '@core') {
            $result += $this->getLangFilepath('@core', Setup::$PATH . Setup::$RESOURCE_PATH);
        }

        return $result;
    }

    /**
     * Находит в указанной папке языковые файлы
     *
     * @param string $module Идентификатор модуля или шаблона или ядра
     * @param string $view_folder путь к папке, в которой должна находиться папка lang
     * @return array
     */
    protected function getLangFilepath($module, $view_folder)
    {
        $filter_by_type = $this->filter['type'] ?? '';
        $result = [];
        $lang_folder_mask = $view_folder.'/lang/'.($this->current_lang ?: '*').'/messages.*.php';
        $files = glob($lang_folder_mask);
        if ($files) {
            foreach($files as $file) {
                $file_name = basename($file);
                $file_lang = basename(dirname($file));
                $file_type = ($file_name == 'messages.js.php') ? self::LANG_FILE_TYPE_JS : self::LANG_FILE_TYPE_PHP;

                if (($filter_by_type == '' || $filter_by_type == $file_type)) {
                    $result[$module][$file_lang][$file_type] = $file;
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает существующие в системе языки в формате для отображения в списке
     *
     * @return array
     */
    public function getPossibleLangsData()
    {
        $result = [];
        foreach($this->getPossibleLang() as $lang) {
            $result[] = [
                'id' => $lang,
                'title' => $lang
            ];
        }

        return $result;
    }

    /**
     * Удаляет фразы из языкового файла
     *
     * @param array $translates_ids Массив с ID фраз, которые необходимо удалить
     * @return bool
     */
    public function removePhrases($translates_ids)
    {
        $by_files = $this->transformTranslatesByFiles(array_flip($translates_ids));

        foreach($by_files as $file => $new_translates) {
            $messages = $this->loadTranslateFile($file);
            if ($messages !== false)  {
                foreach ($messages as $source => $translate) {
                    $source_id = $this->getHashSourcePhrase($source);
                    if (isset($new_translates[$source_id])) {
                        unset($messages[$source]);
                    }
                }

                if ($messages) {
                    $this->writeLangFile($file, $messages);
                } else {
                    unlink($file);
                }
            }
        }

        return true;
    }

    /**
     * Применяет параметры таблицы к выборке. Зарезервировано
     *
     * @param TableControl $table_control
     */
    public function addTableControl(TableControl $table_control)
    {}

    /**
     * Устанавливает фильтры, от компонента \RS\Html\Filter\Control
     *
     * @param FilterControl $filter_control - объект фильтра
     */
    function addFilterControl(FilterControl $filter_control)
    {
        $this->setFilter($filter_control->getKeyVal());
        $this->saveFilters();
    }

    /**
     * Устанавливает массив с фильтрами по переводам
     *
     * @param $filter_array
     */
    function setFilter($filter_array)
    {
        $this->filter = $filter_array;
    }

    /**
     * Устанавливает фильтр по языку
     *
     * @param string $lang
     */
    function setLangFilter($lang)
    {
        $this->current_lang = $lang;
    }

    /**
     * Возвращает общее количество фраз для перевода
     *
     * @return int
     */
    public function getListCount()
    {
        return count($this->getAllPhrases());
    }

    /**
     * Возвращает полный список фраз с учетом фильтра
     *
     * @return array
     */
    protected function getAllPhrases()
    {
        $this->all_phrases = [];
        $lang_files = $this->getModulesLangFilepath();
        foreach($lang_files as $module => $langs)
            foreach($langs as $lang => $types)
                foreach ($types as $type => $filepath) {
                    $some_phrases = $this->loadLangFile($filepath, $module, $type, $lang);
                    $this->all_phrases = array_merge($this->all_phrases,  $some_phrases);
                }

        return $this->all_phrases;
    }

    /**
     * Возвращает список языковых файлов во всех модулях и актуальной теме оформления
     *
     * @param int $page Номер страницы
     * @param int $page_size Количество элементов на странице
     * @return array
     */
    public function getList($page = 1, $page_size = 100)
    {
        $all_phrases = $this->all_phrases ?? $this->getAllPhrases();

        if ($page) {
            $offset = $page_size * ($page-1);
            $all_phrases = array_slice($all_phrases, $offset, $page_size);
        }

        $this->table_group_rows = [];
        $module_titles = $this->getTranslateModuleList();
        $last = '';
        foreach($all_phrases as $n => $phrase_data) {
            $current = $phrase_data['module'].$phrase_data['type'];
            if ($last != $current) {
                $this->table_group_rows[] = [
                    'index' => $n, //Индекс вставки произвольной строки
                    'title' => $module_titles[$phrase_data['module']] . ' ' . $phrase_data['type'] . t('-фразы'),
                ];
                $last = $current;
            }
        }

        return $all_phrases;
    }

    /**
     * Возвращает массив с id, исходной фразой и переводом
     *
     * @param string $filepath
     * @param string $module
     * @param string $type
     * @param string $lang
     * @return array
     */
    protected function loadLangFile($filepath, $module, $type, $lang)
    {
        $filter_by_source = $this->filter['source'] ?? '';
        $filter_by_translate = $this->filter['translate'] ?? '';
        $filter_by_show = $this->filter['show'] ?? '';

        $result = [];

        if ($phrases = $this->loadTranslateFile($filepath)) {
            $file_id = $module.'-'.$lang.'-'.$type;

            foreach($phrases as $source => $translate) {
                $phrase_id = $file_id.'-'.$this->getHashSourcePhrase($source);
                $is_translate = $translate != '' && $translate != $source;

                if ((!$filter_by_source || mb_stripos($source, $filter_by_source) !== false) &&
                    (!$filter_by_translate || mb_strpos($translate, $filter_by_translate) !== false) &&
                    (!$filter_by_show || (($filter_by_show == self::FILTER_SHOW_NO_TRANSLATE && !$is_translate)
                                            || ($filter_by_show == self::FILTER_SHOW_WITH_TRANSLATE && $is_translate)))
                ) {

                    $result[] = [
                        'id' => $phrase_id,
                        'source' => $source,
                        'translate' => $translate,
                        'module' => $module,
                        'type' => $type,
                        'lang' => $lang
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает данные вставки групп в таблицу
     */
    public function getTableGroupRows()
    {
        return $this->table_group_rows;
    }

    /**
     * Сохраняет переводы
     *
     * @param $translates
     * @param bool $add Если true, то фразы либо обновляются, либо добавляются в $translates ожидается
     * массив [Хэш исходной фразы => ['source' => Исходная фраза, 'translate' => 'перевод']]
     * Если false, то [Хэш исходной фразы => 'перевод']
     * @return bool
     */
    public function saveTranslates($translates, $add = false)
    {
        $by_files = $this->transformTranslatesByFiles($translates);
        foreach($by_files as $file => $phrases) {
            if ($add) {
                $this->addTranslateInFile($file, $phrases);
            } else {
                $this->replaceTranslatesInFile($file, $phrases);
            }
        }

        return true;
    }

    /**
     * Обновляет или добавляет переводы в файле
     *
     * @param string $file путь к языковому файлу
     * @param array $new_translates массив [Хэш исходной фразы => ['source' => Исходная фраза, 'translate' => 'перевод']]
     * @return bool
     */
    protected function addTranslateInFile($file, $new_translates)
    {
        $messages = $this->loadTranslateFile($file) ?: [];

        foreach($new_translates as $source_id => $data) {
            $messages[$data['source']] = $data['translate'];
        }

        $this->writeLangFile($file, $messages);
        return true;
    }

    /**
     * Заменяет фразы в файле
     *
     * @param string $file Полный путь к файлу на диске
     * @param array $new_translates массив с переводами, [ID исходной фразы module-lang-type-phraseId => Перевод, ...]
     * @return bool
     */
    protected function replaceTranslatesInFile($file, $new_translates)
    {
        $messages = $this->loadTranslateFile($file);
        if ($messages !== false) {
            foreach($messages as $source => $translate) {
                $source_id = $this->getHashSourcePhrase($source);
                if (isset($new_translates[$source_id])) {
                    $messages[$source] = $new_translates[$source_id];
                }
            }
            $this->writeLangFile($file, $messages);
            return true;
        }
        return false;
    }

    /**
     * Возвращает массив с фразами, сгруппированными по полным путям к файлам
     *
     * @param array $translates массив с переводами, где в ключе составной ID исходной фразы module-lang-type-phraseId
     * @return array
     */
    protected function transformTranslatesByFiles($translates)
    {
        $result = [];
        foreach($translates as $key => $translate) {
            if (preg_match('/^(.*?)-(.*?)-(.*?)-(.*?)$/', $key, $match)) {
                $module = $match[1];
                $lang = $match[2];
                $type = $match[3];
                $phrase_id = $match[4];

                $filepath = $this->getModuleLangFilepath($module, $type, $lang);
                if ($filepath) {
                    $result[$filepath][$phrase_id] = $translate;
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает полный путь к языковому файлу
     *
     * @param string $module имя модуля
     * @param string $type тип файла для переводов
     * @param string $lang двухсимвольный идентификатор языка
     * @return string|bool(false)
     */
    public function getModuleLangFilepath($module, $type, $lang)
    {
        //Экранируем нежелательные символы, которые могут использоваться в пути
        $module = preg_replace('[^a-zA-Z0-9_-]', '', $module);
        $lang = preg_replace('[^a-zA-Z0-9_-]', '', $lang);

        if (!$module || !$lang) return false;

        $file = $type == self::LANG_FILE_TYPE_PHP ? 'messages.lng.php' : 'messages.js.php';
        $filepath = $this->getModuleViewPath($module).'/lang/'.$lang.'/'.$file;
        return $filepath;
    }

    /**
     * Возвращает путь к папке, где должна находиться папка lang для модуля $module
     *
     * @param string $module идентификатор модуля, темы, ядра
     * @return string
     */
    protected function getModuleViewPath($module)
    {
        if ($module == self::CORE_MODULE_ID) {
            $path = Setup::$ROOT. Setup::$RESOURCE_PATH;
        }
        elseif ($module[0] == '#') {
            $path = Setup::$PATH.'/templates/'.mb_substr($module, 1).'/resource/';
        }
        else {
            $path = Setup::$PATH. Setup::$MODULE_FOLDER.'/'.$module. Setup::$MODULE_TPL_FOLDER;
        }

        return $path;
    }

    /**
     * Записывает на диск файл с переводами фраз в формате ReadyScript
     *
     * @param string $file Полный путь к файлу перевода
     * @param array $messages Массив с исходной фразой в ключе и переводом в значении
     * @return bool|int
     */
    protected function writeLangFile($file, $messages)
    {
        return file_put_contents($file, '<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ return ' . var_export($messages, true) . ';');
    }

    /**
     * Возвращает сохраненные в сессии фильтры последнего отображения переводов
     *
     * @return array
     */
    public static function getSavedFilters()
    {
        return $_SESSION[self::FILTER_SESSION_VAR] ?? [];
    }

    /**
     * Сохраняет в сессии фильтры последнего отображения переводов
     *
     * @return void
     */
    public function saveFilters()
    {
        $_SESSION[self::FILTER_SESSION_VAR] = $this->filter + ['lang' => $this->current_lang];
    }

    /**
     * Возвращает хэш от исходной фразы для перевода
     *
     * @param string $phrase Исходная фраза
     * @return string
     */
    public function getHashSourcePhrase($phrase)
    {
        return md5($phrase);
    }

    /**
     * Возвращает имеющиеся фразы в файле переводов
     *
     * @param string $file Полный путь к файлу переводов
     * @return array|bool(false)
     */
    protected function loadTranslateFile($file)
    {
        if (file_exists($file)) {
            return  (array)include($file);
        }

        return false;
    }

    /**
     * Удаляет файлы переводов для заданных языков
     *
     * @param array $langs
     * @return bool
     */
    public function removeLangs($langs)
    {
        foreach($langs as $lang) {
            $list = glob(Setup::$PATH.'/modules/*/view/lang/*', GLOB_ONLYDIR);
            $list = array_merge($list, glob(Setup::$PATH.'/templates/*/resource/lang/*', GLOB_ONLYDIR));
            $list = array_merge($list, glob(Setup::$PATH.'/resource/lang/*', GLOB_ONLYDIR));

            foreach($list as $folder) {
                $folder_lang = strtolower(basename($folder));
                if ($folder_lang == $lang) {
                    FileTools::deleteFolder($folder);
                }
            }
        }

        return true;
    }
}
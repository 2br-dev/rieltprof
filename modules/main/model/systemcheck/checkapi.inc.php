<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck;
/**
 * Класс позволяет собирать из всех модулей системные тесты.
 * Тесты позволяют выявить корректно ли настроено серверное окружение для работы движка
 */
class CheckApi
{
    public
        $test_path = '%MODULE%/model/systemcheck/test';

    private
        $document_root,
        $module_folder;

    function __construct($document_root, $module_folder)
    {
        $this->document_root = $document_root;
        $this->module_folder = $module_folder;
    }

    /**
     * Производит поиск тестов во всех модулях. Возвращает массив инстанций тестов.
     *
     * @return array
     */
    public function findTests()
    {
        $test_instances = [];

        $module_manager = new \RS\Module\Manager();
        $modules = $module_manager->getActiveList();

        $module_path = $this->document_root.$this->module_folder;

        foreach($modules as $module) {
            $namespace = str_replace('%MODULE%', $module->getName(), $this->test_path);
            $test_folder_path = $module_path.'/'.$namespace;

            if (file_exists($test_folder_path)) {
                $files = scandir($test_folder_path);

                foreach($files as $file) {
                    if ($file == '.' || $file == '..' || !preg_match('/'.\Setup::$CLASS_EXT.'$/', $file)) continue;
                    $class_name = str_replace('/', '\\', $namespace.'/'.strtok($file, '.'));

                    if (!isset($test_instances[$class_name])
                        && class_exists($class_name)
                        && is_subclass_of($class_name, '\Main\Model\SystemCheck\AbstractSystemTest'))
                    {
                        $test_instances[$class_name] = new $class_name();
                    }
                }
            }
        }

        return $test_instances;
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config\Migration;

use RS\Exception;
use RS\Theme\Manager;

/**
 * Класс обеспечивает распаковку патч-архива в старые темы оформления.
 * Распаковка происходит БЕЗ ЗАМЕНЫ ФАЙЛОВ внутри папки темы.
 *
 * Это необходимо, когда ReadyScript внедряет новую тему в качестве темы по умолчанию.
 * То есть полностью заменяются шаблоны в папках модулей.
 */
class OldTemplatePatcher
{
    protected $patch_files = [
        [
            'file' => 'patch_old_templates_rs6.zip',
            'scriptMajorVersion' => 6 //Обновлять темы, которые ниже этой версии
        ]
    ];

    /**
     * Выполняет поиск всех тем оформления, у которых в theme.xml
     * значение атрибута script_major_version меньше заданного.
     *
     * Распаковывает в такие темы патч-архив пошагово
     *
     * @param integer $timeout Таймаут в секундах
     * @param array $previous_state Предыдущий возврат этой функции при повторном вызове.
     * Используется для продолжения с прежней позиции
     * @return mixed
     * true - в случае успеха
     * array - в случае, если нужна вторая итерация
     *
     * @throws Exception
     */
    public function patch($timeout = null, array $previous_state = [])
    {
        $start_time = microtime(true);
        $previous_state += [
            'next' => 0
        ];

        $n = 0;
        foreach($this->patch_files as $i => $patch_data) {

            $themes = $this->getThemesPathForUpdate($patch_data['scriptMajorVersion']);
            foreach ($themes as $theme_path) {
                $n++;
                if ($n <= $previous_state['next']) continue;

                $zip_path = __DIR__.'/'.$patch_data['file'];
                $zip = new \ZipArchive();
                if ($zip->open($zip_path) === true) {
                    $files = [];
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (!file_exists($theme_path . '/' . $filename)) {
                            $files[] = $filename;
                        }
                    }

                    if ($files) {
                        $zip->extractTo($theme_path, $files);
                    }
                    $zip->close();
                } else {
                    throw new Exception(t('Не удалось открыть архив %0', [basename($zip_path)]));
                }

                if ($timeout !== null && microtime(true) - $start_time > $timeout) {
                    return [
                        'next' => $n
                    ];
                }
            }
        }

        return true;
    }

    /**
     * Возвращает пути к корневым папкам тем оформления, которые подлежат обновлению
     *
     * @param string $theme_script_major_version Значение атрибута темы,
     * начиная с которого установка патча не будет происходить
     * @return array
     */
    protected function getThemesPathForUpdate($theme_script_major_version)
    {
        $folders = [];

        $theme_manager = new Manager();
        foreach($theme_manager->getList() as $theme) {
            $script_major_version = (string)$theme->getThemeXml()['scriptMajorVersion'];
            if (version_compare($script_major_version, $theme_script_major_version) < 0) {
                $folders[] = $theme->getSelfPath();
            }
        }

        return $folders;
    }
}
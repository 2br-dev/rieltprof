<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class WriteRight extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Права на запись в любую папку сайта для PHP');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректной работы системы обновления, кэширования и других систем, PHP должен иметь права на запись в любую папку сайта');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return $this->checkFolder(\Setup::$PATH)
        && $this->checkFolder(\Setup::$CACHE_FOLDER)
        && $this->checkFolder(\Setup::$PATH.\Setup::$STORAGE_DIR);
    }

    /**
     * Возвращает true, если удалось записать и затем удалить файл в папке
     *
     * @param $path
     * @return bool
     */
    protected function checkFolder($path)
    {
       $filename = $path."/test-".uniqid();
       return @file_put_contents($filename, 'write-test') && unlink($filename);
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Установите всем папкам права 755, всем файлам 644. Если на хостинге установлен apache mpm-itk, то PHP может работать в режиме модуля apache. Если на хостинге установлен обычный apache, то PHP должен работать в режиме CGI, в таком случае PHP запускается от вашего пользователя на сервере и имеет доступ ко всем вашим папкам и файлам.');
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class Memory extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Настройки памяти');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        $memory = \RS\File\Tools::fileSizeToStr($this->getMemoryInBytes());
        return t('Для комфортной работы функций обработки изображений требуется не менее 64 MB памяти для PHP процесса (memory_limit). У вас установлено: %0', [$memory]);
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return $this->getMemoryInBytes() >= 64000000;
    }

    /**
     * Возвращает значение настройки memory_limit в байтах
     *
     * @return int
     */
    private function getMemoryInBytes()
    {
        $memory = trim(ini_get('memory_limit'));
        $s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
        return intval($memory) * ($s[strtolower(substr($memory,-1))] ?: 1);
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Установите в PHP.ini значение memory_limit в значение не менее 64M');
    }
}
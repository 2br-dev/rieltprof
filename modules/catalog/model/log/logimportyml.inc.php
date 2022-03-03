<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования импорта YML
 */
class LogImportYml extends AbstractLog
{
    const LEVEL_MAIN = 'main';
    const LEVEL_OBJECT = 'object';
    const LEVEL_OBJECT_DETAIL = 'object_detail';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'import_yml';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Импорт YML');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_MAIN => t('Основные события'),
            self::LEVEL_OBJECT => t('Объекты'),
            self::LEVEL_OBJECT_DETAIL => t('Объекты детально'),
        ];
    }
}

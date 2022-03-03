<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Cron;

use RS\Log\AbstractLog;

/**
 * Класс логирования планировщика cron
 */
class LogCron extends AbstractLog
{
    const LEVEL_THROWABLE = 'throwable';
    const LEVEL_ERROR_HANDLER = 'error_handler';
    const LEVEL_OTHER = 'other';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'cron';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Планировщик cron');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     * Уровни логирования используются для настройки детальности логирования и фильтрации записей при просмотре лог-файлов
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_THROWABLE => t('Ошибка'),
            self::LEVEL_ERROR_HANDLER => t('Предупреждение'),
            self::LEVEL_OTHER => t('Прочее'),
        ];
    }
}

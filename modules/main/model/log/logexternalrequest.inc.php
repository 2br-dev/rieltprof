<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования внешних запросов
 */
class LogExternalRequest extends AbstractLog
{
    const LEVEL_REQUEST = 'request';
    const LEVEL_RESPONSE = 'response';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'external_request';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Внешние запросы');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_REQUEST => t('Запрос'),
            self::LEVEL_RESPONSE => t('Ответ'),
        ];
    }
}

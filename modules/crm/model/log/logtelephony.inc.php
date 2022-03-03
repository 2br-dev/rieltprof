<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Crm\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования телефонии
 */
class LogTelephony extends AbstractLog
{
    const LEVEL_OUTGOING_REQUEST = 'outgoing_request';
    const LEVEL_INCOMING_REQUEST = 'incoming_request';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'telephony';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return  t('Телефония');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_OUTGOING_REQUEST => t('Исходящий запрос'),
            self::LEVEL_INCOMING_REQUEST => t('Входящий запрос'),
        ];
    }
}

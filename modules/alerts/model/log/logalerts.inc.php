<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Alerts\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования уведомлений
 */
class LogAlerts extends AbstractLog
{
    const LEVEL_SMS = 'sms';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'alerts';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Уведомления');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_SMS => t('Отправка SMS'),
        ];
    }
}

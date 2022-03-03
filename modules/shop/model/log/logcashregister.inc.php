<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования онлайн-касс
 */
class LogCashRegister extends AbstractLog
{
    const LEVEL_OUT = 'out';
    const LEVEL_IN = 'in';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'cash_register';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Онлайн кассы');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_OUT => t('Исходящий запрос'),
            self::LEVEL_IN => t('Входящий запрос'),
        ];
    }
}

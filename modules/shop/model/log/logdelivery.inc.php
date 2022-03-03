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
class LogDelivery extends AbstractLog
{
    const LEVEL_MESSAGE = 'message';
    const LEVEL_EXTERNAL_REQUEST = 'external_request';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'delivery';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Доставка');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_MESSAGE => t('Внутреннее сообщение'),
            self::LEVEL_EXTERNAL_REQUEST => t('Внешний запрос'),
        ];
    }
}

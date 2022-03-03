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
class LogPaymentYandexKassaApi extends AbstractLog
{
    const LEVEL_MESSAGES = 'messages';
    const LEVEL_OUTGOING_REQUEST = 'outgoing_request';
    const LEVEL_WEB_HOOK = 'web_hook';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'payment_yandex_kassa_api';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Оплата "ЮKassa"');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_MESSAGES => t('Внутреннее сообщение'),
            self::LEVEL_OUTGOING_REQUEST => t('Запрос к ЮКассе'),
            self::LEVEL_WEB_HOOK => t('Web-hook'),
        ];
    }
}

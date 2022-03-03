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
class LogDeliveryCdek extends AbstractLog
{
    const LEVEL_AUTHORIZATION = 'authorization';
    const LEVEL_CALCULATE = 'calculate';
    const LEVEL_FIND_CITY = 'find_city';
    const LEVEL_PVZ = 'pvz';
    const LEVEL_ORDER = 'order';
    const LEVEL_WEB_HOOK = 'web_hook';
    const LEVEL_UPDATE_CDEK_REGIONS = 'uddate_cdek_regions';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'delivery_cdek';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Доставка "СДЭК"');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_AUTHORIZATION => t('Авторизация'),
            self::LEVEL_CALCULATE => t('Калькуляция'),
            self::LEVEL_FIND_CITY => t('Поиск id города'),
            self::LEVEL_PVZ => t('Запрос ПВЗ'),
            self::LEVEL_ORDER => t('Заказ на доставку'),
            self::LEVEL_WEB_HOOK => t('Web-hook'),
            self::LEVEL_UPDATE_CDEK_REGIONS => t('Загрузка регионов'),
        ];
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Db;

use RS\Log\AbstractLog;

/**
 * Класс логирования адаптера базы данных
 */
class LogDbAdapter extends AbstractLog
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->setSiteId(0);
        $this->setEnabled((bool)(\Setup::$LOG_SQLQUERY_TIME ?? false));
        $this->setEnabledLevels([self::LEVEL_INFO => self::LEVEL_INFO]);
        $this->setMaxFileSize((int)(\Setup::$LOG_SETTINGS_DB_ADAPTER_MAX_FILE_SIZE ?? $this->getDefaultMaxFileSize()));
    }

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'db_adapter';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Запросы к базе данных');
    }
}
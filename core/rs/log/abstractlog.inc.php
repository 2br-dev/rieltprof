<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Log;

use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Helper\Log;
use RS\Site\Manager as SiteManager;

/**
 * Базовый класс для создания классов логирования
 */
abstract class AbstractLog
{
    const LEVEL_INFO = 'info';

    /** @var int */
    private $site_id;
    /** @var bool */
    private $enabled;
    /** @var string[] */
    private $enabled_levels;
    /** @var int */
    private $max_file_size;
    /** @var Log */
    private $log;

    /**
     * Singleton, необходимо использовать ::getInstance()
     * для создания объекта
     */
    protected function __construct()
    {
        $this->setSiteId((int)SiteManager::getSiteId());
        $log_settings = ConfigLoader::getSystemConfig()['log_settings'][$this->getIdentifier()] ?? [];
        $this->enabled = (bool)($log_settings['enabled'] ?? false);
        $this->enabled_levels = (array)($log_settings['levels'] ?? []);
        $this->max_file_size = (int)($log_settings['max_file_size'] ?? $this->getDefaultMaxFileSize());
    }

    /**
     * Возвращает единственный экземпляр текущего класса
     *
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    abstract public function getIdentifier(): string;

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * Возвращает описание класса логирования
     *
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     * Использует статическое кэширование
     *
     * @return string[]
     */
    public final function getLogLevelList(): array
    {
        static $list;
        if ($list === null) {
            $list = $this->selfLogLevelList();
        }
        return $list;
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
            self::LEVEL_INFO => t('Лог'),
        ];
    }

    /**
     * Возвращает максимальный размер лог-файла по умолчанию (в мегабайтах)
     *
     * @return int
     */
    public function getDefaultMaxFileSize(): int
    {
        return 1;
    }

    /**
     * Возвращает объект LogReader для лог файла для сайта $site_id
     *
     * @param int $site_id ID сайта
     * @return LogReader
     * @throws RSException
     */
    public function getReaderForSite(int $site_id): LogReader
    {
        $file = $this->getFileNameBySite($site_id);
        if (file_exists($file)) {
            $reader = new LogReader();
            if (!$reader->openFile($file)) {
                throw new RSException(t('Лог-файл для указанного сайта пуст'));
            }
            return $reader;
        }
        throw new RSException(t('Лог-файл для указанного сайта не создан'));
    }

    /**
     * Производит запись в лог-файл
     *
     * @param string $text - лог-запись
     * @param string $level - уровень логирования, если не указан - используется первый уровень в списке
     */
    public function write(string $text, string $level = null): void
    {
        if ($level === null) {
            $levels = $this->getLogLevelList();
            $level = reset($levels);
        }

        if ($this->isEnabled() && $this->isEnabledLevel($level)) {
            $data = "[$level] $text";
            $this->log()->append($data);
        }
    }

    /**
     * Возвращает объект лог-файла, который непосредствнно пишет данные в файл
     *
     * @return Log
     */
    protected function log(): Log
    {
        if ($this->log === null) {
            $this->log = Log::file($this->getFileName());
            $this->log->setMaxLength($this->getMaxFileSize() * 1048576);
            $this->log->enableDate();
        }
        return $this->log;
    }

    /**
     * Возвращает имя лог-файла на диске
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return $this->getFileNameBySite($this->getSiteId());
    }

    /**
     * Возвращает ссылки на существующие лог-файлы
     *
     * @return string[]
     * @throws DbException
     */
    public function getFileLinks(): array
    {
        $result = [];
        $file = $this->getFileNameBySite(0);
        if (file_exists($file)) {
            $result[0] = $file;
        }
        foreach (SiteManager::getSiteList() as $site) {
            $file = $this->getFileNameBySite((int)$site['id']);
            if (file_exists($file)) {
                $result[$site['id']] = $file;
            }
        }
        return $result;
    }

    /**
     * Возвращает имя лог-файла для указанного сайта
     *
     * @param int $site_id - id сайта
     * @return string
     */
    protected function getFileNameBySite(int $site_id): string
    {
        return \Setup::$PATH . \Setup::$LOGS_DIR . "/{$site_id}_{$this->getIdentifier()}.log";
    }

    /**
     * Возвращает id текущего сайта
     *
     * @return mixed
     */
    public function getSiteId(): int
    {
        return $this->site_id;
    }

    /**
     * Устанавливает id текущего сайта
     *
     * @param int $site_id
     */
    public function setSiteId(int $site_id): void
    {
        $this->site_id = $site_id;
    }

    /**
     * Возвращает включено ли логирование
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Устанавливает включено ли логирование
     *
     * @param bool $enabled - значение
     * @return void
     */
    protected function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Возвращает разрешена ли запись логов указанного уровня
     *
     * @param string $level - проверяемый уровень лог-записей
     * @return bool
     */
    public function isEnabledLevel(string $level): bool
    {
        return isset($this->enabled_levels[$level]);
    }

    /**
     * Устанавливает разрешённые для записи уровни логов
     *
     * @param string[] $enabled_levels - значение
     * @return void
     */
    protected function setEnabledLevels(array $enabled_levels): void
    {
        $this->enabled_levels = $enabled_levels;
    }

    /**
     * Возвращает максимальный размер лог-файла (в мегабайтах)
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return $this->max_file_size;
    }

    /**
     * Устанавливает максимальный размер лог-файла (в мегабайтах)
     *
     * @param int $max_file_size - значение
     * @return void
     */
    protected function setMaxFileSize(int $max_file_size): void
    {
        $this->max_file_size = $max_file_size;
    }
}

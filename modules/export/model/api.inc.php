<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model;

use Export\Model\ExportType\AbstractType as AbstractExportType;
use Export\Model\Orm\ExportProfile;
use Export\Model\Orm\ExternalProductLink;
use RS\Application\Application;
use RS\Event\Exception as EventException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\HashStore\Api as HashStoreApi;
use RS\Orm\Request;
use RS\Exception;

class Api extends EntityList
{
    const PLAIN_EXPORT_EXCHANGE = 'PLAIN_EXPORT_EXCHANGE';

    public static $types;

    public function __construct()
    {
        parent::__construct(new ExportProfile(), [
            'multisite' => true,
            'alias_field' => 'alias',
        ]);
    }

    /**
     * Получить экспортированные данные для данного профиля
     * Кеширует результат в файле
     *
     * @param ExportProfile $profile
     * @return void
     * @throws \Exception
     */
    function printExportedData(ExportProfile $profile)
    {
        $cache_file = $profile->getTypeObject()->getCacheFilePath();
        // Если установлено "время жизни"
        if ($profile['life_time'] > 0) {
            $life_time_in_sec = $profile['life_time'] * 60;
            // Если время жизни еще не истекло
            if (file_exists($cache_file) && (time() < filemtime($cache_file) + $life_time_in_sec)) {
                readfile($cache_file);
                return;
            }
        }

        // Экспортируем данные в файл
        $profile->getTypeObject()->export();

        // Отправляем содержимое файла на вывод
        Application::getInstance()->headers->cleanCookies();
        readfile($cache_file);
    }

    /**
     * Возвращает объекты типов экспорта
     *
     * @return AbstractExportType[]
     * @throws EventException
     * @throws Exception
     */
    function getTypes()
    {
        if (self::$types === null) {
            $event_result = \RS\Event\Manager::fire('export.gettypes', []);
            $list = $event_result->getResult();
            self::$types = [];
            foreach ($list as $type_object) {
                if (!($type_object instanceof ExportType\AbstractType)) {
                    throw new Exception(t('Тип экспорта должен реализовать интерфейс \Export\Model\ExportType\AbstractType'));
                }
                self::$types[$type_object->getShortName()] = $type_object;
            }
        }

        return self::$types;
    }

    /**
     * Возвращает массив ключ => название типа
     *
     * @return array
     * @throws Exception
     */
    static public function getTypesAssoc()
    {
        $_this = new self();
        $result = [];
        foreach ($_this->getTypes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает объект экспорта доставки по идентификатору
     *
     * @param string $name - имя типа
     * @return AbstractExportType|null
     * @throws Exception
     */
    static public function getTypeByShortName($name)
    {
        $_this = new self();
        $list = $_this->getTypes();
        return isset($list[$name]) ? $list[$name] : null;
    }

    /**
     * Возвращает объект экспорта по типа класса и идентификатору
     *
     * @param string $class - класс типа объекта
     * @param string $alias - идентификатор или alias
     * @param integer $site_id - id сайта
     *
     * @return ExportProfile|false
     * @throws OrmException
     */
    function getObjectByAliasAndType($class, $alias, $site_id)
    {
        return OrmRequest::make()
            ->from(new ExportProfile())
            ->where([
                'site_id' => $site_id,
                'class' => $class
            ])
            ->where("(alias = '#alias' OR (alias = '' AND id='#alias'))", [
                'alias' => $alias
            ])
            ->object();
    }

    /**
     * Устанавливает фильтр, в результате которого
     * будут выбраны только те профили, которые поддерживают обмен по API
     *
     * @return void
     */
    function setFilterExchangableByApi()
    {
        $exchangable_names = [];

        foreach($this->getTypes() as $key => $type_object) {
            if ($type_object->canExchangeByApi()) {
                $exchangable_names[] = $key;
            }
        }

        if ($exchangable_names) {
            $this->setFilter('class', $exchangable_names, 'in');
        }
    }

    /**
     * Возвращает имя ключа профиля экспорта для запуска экспорта
     *
     * @param ExportProfile $profile
     * @return string
     */
    private static function getPlainHashKey(ExportProfile $profile)
    {
        return self::PLAIN_EXPORT_EXCHANGE.'-'.$profile['id'];
    }

    /**
     * Отправляет в хеш-стор отметку о том, что нужно начать экспорт по API
     *
     * @param integer $profile_id
     * @param null $site_id
     * @return bool
     */
    public function planExchange(ExportProfile $profile)
    {
        $export_type = $profile->getTypeObject();

        if (!$export_type->canExchangeByApi()) {
            return $this->addError(t('Профиль должен поддерживать экпсорт по API'));
        }

        $validate_result = $export_type->validateDataForExchangeByApi();
        if (!$validate_result) {
            return $this->addError($export_type->getErrorsStr());
        }

        HashStoreApi::set(self::getPlainHashKey($profile), true);

        return true;
    }

    /**
     * Проверяет, нужно ли экспортировать товары этого профиля
     *
     * @param $profile_id
     * @param null $site_id
     * @return string
     */
    public function isPlannedExchange(ExportProfile $profile)
    {
        $result = HashStoreApi::get(self::getPlainHashKey($profile), false);
        return $result;
    }

    /**
     * Удаляет профиль экспорта с очереди на экспорт
     *
     * @param $profile_id
     * @param null $site_id
     */
    public function endPlane($profile)
    {
        HashStoreApi::set(self::getPlainHashKey($profile), false);
    }

    /**
     * Отмечает все ранее выгруженные товары для обновления
     *
     * @return integer
     */
    public function markAllToExport()
    {
        return Request::make()
            ->update(new ExternalProductLink())
            ->set([
                'has_changed' => 1,
                'hash' => ''
            ])->exec()->affectedRows();
    }

    /**
     * @param $text
     */
    public static function highlightLogData($text)
    {
        $text = preg_replace_callback('/^(\[[0-9\.]+ [0-9\:]+\])/m', function($match) {
            return '<span class="log-date">'.$match[1].'</span>';
        }, $text);

        return $text;
    }
}

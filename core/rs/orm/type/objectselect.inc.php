<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Orm\Type;

use RS\Module\AbstractModel\EntityList;
use RS\Orm\Exception as OrmException;

class ObjectSelect extends Bigint
{
    protected $api;
    protected $object_id_field;
    protected $request_url;
    protected $icon_class = 'search';
    protected $form_template = '%system%/coreobject/type/form/object_select.tpl';

    protected static $cache = [];

    /**
     * Конструктор свойства
     *
     * @param EntityList $api - api для работы с искомыми объектами
     * @param string $request_url - URL, который будет возвращать результат поиска
     * @param array $options - массив для быстрой установки параметров
     */
    public function __construct(EntityList $api, string $request_url, array $options = null)
    {
        $this->setApi($api);
        $this->setRequestUrl($request_url);
        parent::__construct($options);
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     * @throws OrmException
     */
    function getPublicTitle()
    {
        $object_id = $this->get();
        if (!empty($object_id)) {
            if (!isset(self::$cache[$object_id])) {
                $object = $this->getApi()->getElement();
                if ($this->getObjectIdField()) {
                    $object = $object::loadByWhere([
                        $this->getObjectIdField() => $object_id,
                    ]);
                } else {
                    $object->load($object_id);
                }
                self::$cache[$object_id] = $object[$this->getApi()->getNameField()] . " ($object_id)";
            }
            return self::$cache[$object_id];
        }
        return '';
    }

    /**
     * Возвращает поле, которое будет использоваться как идентификатор объекта
     *
     * @return string|null
     */
    public function getObjectIdField(): ?string
    {
        return $this->object_id_field;
    }

    /**
     * Устанавливает поле, которое будет использоваться как идентификатор объекта
     *
     * @param string $value - имя поля
     * @return static
     */
    public function setObjectIdField(string $value)
    {
        $this->object_id_field = $value;
        return $this;
    }

    /**
     * @return EntityList
     */
    public function getApi(): ?EntityList
    {
        return $this->api;
    }

    /**
     * @param mixed $api
     */
    public function setApi(EntityList $api): void
    {
        $this->api = $api;
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->request_url;
    }

    /**
     * Устанавливает URL, который будет возвращать результат поиска
     *
     * @param string $url - URL, который будет возвращать результат поиска
     * @return void
     */
    public function setRequestUrl(string $url)
    {
        $this->request_url = $url;
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    public function getIconClass()
    {
        return $this->icon_class;
    }

    /**
     * Устанавливает класс иконки zmdi
     *
     * @param string $class - класс иконки zmdi
     * @return void
     */
    public function setIconClass(string $class): void
    {
        $this->icon_class = $class;
    }

    public function setBaseType($type)
    {
        switch ($type) {
            case 'varchar':
                $this->php_type = 'string';
                $this->sql_notation = 'varchar';
                $this->max_len = 255;
                break;
        }

        return $this;
    }
}

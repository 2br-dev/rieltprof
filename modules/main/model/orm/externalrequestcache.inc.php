<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\Orm;

use Main\Model\Requester\ExternalRequest;
use Main\Model\Requester\ExternalResponse;
use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * Кэш внешних запросов
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $date Время запроса
 * @property string $source_id Идентификатор инициатора запроса
 * @property string $request_url URL запроса
 * @property string $request_headers Заголовки запроса
 * @property string $request_params Параметры запроса
 * @property string $request_hash Хэш параметров запроса
 * @property string $idempotence_key Ключ идемпотентности
 * @property integer $response_status Статус ответа
 * @property string $response_headers Заголовки ответа
 * @property string $response_body Тело ответа
 * --\--
 */
class ExternalRequestCache extends OrmObject
{
    protected static $table = 'external_request_cache';

    /**
     * В данном методе должны быть заданы поля объекта.
     * Вызывается один раз для одного класса объектов в момент первого обращения к свойству
     */
    protected function _init()
    {
        parent::_init()->append([
            'date' => (new Type\Datetime())
                ->setDescription(t('Время запроса'))
                ->setReadOnly(true)
                ->setIndex(true),
            'source_id' => (new Type\Varchar())
                ->setDescription(t('Идентификатор инициатора запроса'))
                ->setReadOnly(true),
            'request_url' => (new Type\Varchar())
                ->setDescription(t('URL запроса'))
                ->setReadOnly(true),
            'request_headers' => (new Type\Blob())
                ->setDescription(t('Заголовки запроса'))
                ->setTemplate('%main%/form/externalrequestcache/serialized_field.tpl'),
            'request_params' => (new Type\Blob())
                ->setDescription(t('Параметры запроса'))
                ->setTemplate('%main%/form/externalrequestcache/serialized_field.tpl'),
            'request_hash' => (new Type\Varchar())
                ->setDescription(t('Хэш параметров запроса'))
                ->setReadOnly(true),
            'idempotence_key' => (new Type\Varchar())
                ->setDescription(t('Ключ идемпотентности'))
                ->setReadOnly(true),
            'response_status' => (new Type\Integer())
                ->setDescription(t('Статус ответа'))
                ->setReadOnly(true),
            'response_headers' => (new Type\Blob())
                ->setDescription(t('Заголовки ответа'))
                ->setTemplate('%main%/form/externalrequestcache/serialized_field.tpl'),
            'response_body' => (new Type\Mediumblob())
                ->setDescription(t('Тело ответа'))
                ->setTemplate('%main%/form/externalrequestcache/response_body.tpl'),
        ]);
    }

    /**
     * Возвращает ответ сервера в удобочитаемом виде
     *
     * @return string
     */
    function getSerializedValueView($value)
    {
        $value = unserialize($value);
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        return json_encode($value, $flags);
    }
}

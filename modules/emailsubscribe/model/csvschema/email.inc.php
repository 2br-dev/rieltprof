<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта E-mail в CSV
*/
class Email extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \EmailSubscribe\Model\Orm\Email(),
            'excludeFields' => [
                'id', 'site_id'
            ],
            'multisite' => true,
            'searchFields' => ['email']
        ]));
    }
}
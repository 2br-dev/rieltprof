<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Model\CsvSchema;

use RS\Csv\AbstractSchema;
use RS\Csv\Preset;
use Feedback\Model\Orm;

class Forms extends AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new Orm\FormFieldItem(),
            'excludeFields' => ['form_id', 'id', 'site_id'],
            'multisite' => true,
            'searchFields' => ['title', 'form_id']
        ]), [
            new Preset\LinkedTable([
                'ormObject' => new Orm\FormItem(),
                'fields' => ['title'],
                'titles' => ['title' => t('Форма')],
                'idField' => 'id',
                'multisite' => true,
                'linkForeignField' => 'form_id',
                'linkPresetId' => 0,
                'linkDefaultValue' => 0,
            ])
        ]);
    }
}

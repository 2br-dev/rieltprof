<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\CsvSchema;
use \RS\Csv\Preset,
    \Users\Model\Orm;

/**
* Схема импорта/экспорта в CSV файл групп пользователей
*/
class UsersGroup extends \RS\Csv\AbstractSchema
{
    function __construct()
    {        
        $config = \RS\Config\Loader::byModule($this);
        
        parent::__construct(
            new Preset\Base([
                'ormObject'     => new Orm\UserGroup(),
                'excludeFields' => [
                    'id'
                ],
                'savedRequest' => \Users\Model\GroupApi::getSavedRequest('Users\Controller\Admin\Ctrlgroup_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
                'searchFields' => $config['csv_id_fields'],
            ])
        );
    }
}

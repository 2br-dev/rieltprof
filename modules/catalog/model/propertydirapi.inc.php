<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Orm\Property\Dir as PropertyDir;
use RS\Module\AbstractModel\EntityList;

class PropertyDirApi extends EntityList
{
    public $uniq;

    function __construct()
    {
        parent::__construct(new PropertyDir, [
            'multisite' => true,
            'defaultOrder' => 'sortn',
            'nameField' => 'title',
            'sortField' => 'sortn',
        ]);
    }

    public static function selectList()
    {
        return self::staticSelectList([0 => t('Без группы')]);
    }
}

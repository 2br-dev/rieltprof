<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model;

use RS\Module\AbstractModel\TreeCookieList;

class CatApi extends TreeCookieList
{
    protected static $instance;

    public function __construct()
    {
        parent::__construct(new Orm\Category, [
            'parentField' => 'parent',
            'multisite' => true,
            'idField' => 'id',
            'aliasField' => 'alias',
            'nameField' => 'title',
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn'
        ]);
    }

    /**
     * @deprecated (19.03)
     */
    static function selectList($include_root = true)
    {
        $_this = self::getInstance();
        $list = $_this->getSelectList(0);
        return $include_root ? ['' => t('Верхний уровень')] + $list : $list;
    }
}

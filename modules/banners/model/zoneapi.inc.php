<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model;

use Banners\Model\Orm\Zone;
use RS\Module\AbstractModel\EntityList;

class ZoneApi extends EntityList
{
    public $uniq;

    function __construct()
    {
        parent::__construct(new Zone(), [
            'aliasField' => 'alias',
            'nameField' => 'title',
            'multisite' => true,
        ]);
    }

    public static function staticAdminSelectList()
    {
        return [0 => t('Без связи с зоной')] + self::staticSelectList();
    }

    /**
     * Возвращает список зон баннеров с ключами в alias
     *
     * @return string[]
     */
    public static function staticSelectAliasList()
    {
        $arr = ["" => t("Не выбрано")];
        $api = new self();
        $list = $api->getListAsArray();
        if (!empty($list)) {
            foreach ($list as $zone) {
                $arr[$zone['alias']] = $zone['title'];
            }
        }

        return $arr;
    }
}

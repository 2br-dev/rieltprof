<?php
namespace Rieltprof\Model\Behavior;

use Users\Model\Orm\User;

/**
* Объект - Расширения пользователя
*/
class CatalogDir extends \RS\Behavior\BehaviorAbstract
{
    public function getOtherCategory()
    {
        $orm = $this->owner;
        $other_category_id = $orm['category_other_action'];
        if($other_category_id){
            $other_category = new \Catalog\Model\Orm\Dir($other_category_id);
            return $other_category;
        }
        return false;
    }

    public function getMainParent()
    {
        $orm = $this->owner;
        $dir_api = new \Catalog\Model\DirApi();
        $parent_id = array_pop($dir_api->getParentsId($orm['id']));
        $parent = new \Catalog\Model\Orm\Dir($parent_id);
        return $parent;
    }
}


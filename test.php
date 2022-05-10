<?php

use Rieltprof\Model\Orm\Location;

require_once('setup.inc.php');
// Создаем директорию с локацией в каждой директории Вида объекта по всем видам оперции
$dir_api = new \Catalog\Model\DirApi();
// 1. Получаем директории верхнего уровня
$dirs_type_action = $dir_api->setFilter('parent', 0)->getList();

foreach ($dirs_type_action as $dir_type_action){
    $dir_api->clearFilter();
    $dirs_type_object = $dir_api->setFilter('parent', $dir_type_action['id'])->getList();

    foreach ($dirs_type_object as $dir_type_object){

    }
}

$parent = new Location(0);

var_dump(count($parent->getValues()) ? false : true);

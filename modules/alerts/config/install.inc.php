<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Config;
use \RS\Cache\Cleaner as CacheCleaner;

class Install extends \RS\Module\AbstractInstall
{
    function update()
    {
        if ($result = parent::update()) {
            //Очищаем загруженные раннее описания полей объектов
            CacheCleaner::obj()->clean( CacheCleaner::CACHE_TYPE_COMMON );
            \RS\Event\Manager::init();
            \Site\Model\Orm\Config::destroyClass();

            //Обновляем структуру базы данных
            $config = new \Site\Model\Orm\Config();
            $config->dbUpdate();
        }
        return $result;
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Config;

class Install extends \RS\Module\AbstractInstall
{
    
    /**
     * Выполняет установку модуля
     *
     * @return bool
     */
    function update()
    {
        if ($result = parent::update()) {
            //Обновляем структуру базы данных для объекта Товар
            if (\RS\Module\Manager::staticModuleExists('shop')) {
                $order = new \Shop\Model\Orm\Order();
                $order->dbUpdate();

                $delivery = new \Shop\Model\Orm\Delivery();
                $delivery->dbUpdate();
            }

            $menu = new \Menu\Model\Orm\Menu();
            $menu->dbUpdate();
            
            $dir = new \Catalog\Model\Orm\Dir();
            $dir->dbUpdate();

            $article_category = new \Article\Model\Orm\Category();
            $article_category->dbUpdate();
        }

        return $result;
    }

}
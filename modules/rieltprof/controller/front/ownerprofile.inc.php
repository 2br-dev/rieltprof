<?php
namespace Rieltprof\Controller\Front;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use Catalog\Model\Logtype\ShowProduct as LogtypeShowProduct;
use Catalog\Model\Orm\Dir;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Img\Core;
use Users\Model\LogApi as UserLogApi;

/**
* Просмотр данных владельца объявления
*/
class OwnerProfile extends Front
{
    protected $id;
    protected $lastpage;
    
    /** @var ProductApi */
//    public $api;
    /** @var Dirapi */
//    public $dirapi;
//    public $config;
    
    function init()
    {
        $this->id     = $this->url->get('id', TYPE_STRING);
//        $this->api    = new ProductApi();
//        $this->dirapi = new Dirapi();
//        $this->config = ConfigLoader::byModule($this);
    }
    
    /**
    * Обычный просмотр товара
    */
    function actionIndex()
    {
        /**
        * @var \Catalog\Model\Orm\Product $item
        */
        $user = new \Users\Model\Orm\User($this->id);
        if (!$user){
            $this->e404(t('Такого риелтора не существует'));
        }
        $this->view->assign('user', $user);
        //Пишем лог
//        UserLogApi::appendUserLog(new LogtypeShowProduct(), $item['id'], null, $item['id']);
        return $this->result->setTemplate( '%rieltprof%/ownerprofile.tpl' );
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Config;
use \RS\Orm\Type as OrmType;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('controller.front.beforewrap')
            ->bind('mailsender.getsource')
            ->bind('getroute')
            ->bind('getmenus');
    }
    
    public static function controllerFrontBeforewrap()
    {
        $config = \RS\Config\Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();
        if (!$router->isAdminZone() && $config->dialog_open_delay) {
            $show = \RS\Http\Request::commonInstance()->cookie('subscribe_is_shown', TYPE_BOOLEAN); //Флаг обозначающий, то что окно подписки показано ранее
            if (!$show) {
                //Подключаем скрипты и стили для отображения окна подписки
                $app = \RS\Application\Application::getInstance();
                $app->addJsVar('emailsubscribe_dialog_url', $router->getUrl('emailsubscribe-front-window'))
                    ->addJsVar('emailsubscribe_dialog_open_delay', $config->dialog_open_delay)
                    ->addJs('%emailsubscribe%/subdialog.js')
                    ->addCss('%emailsubscribe%/window.css');
            }
        }
    }
    
    public static function getRoute($routes) 
    {
        //Просмотр категории продукции
        $routes[] = new \RS\Router\Route('emailsubscribe-front-window', [
            '/emailsubscribe/'
        ], null, t('Подписка на E-mail рассылку. Окно.'));
        
        return $routes;
    }    
   
   
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('E-mail для рассылки'),
            'alias' => 'emailsubscribe',
            'link' => '%ADMINPATH%/emailsubscribe-ctrl/',
            'sortn' => 20,
            'typelink' => 'link',
            'parent' => 'modules'
        ];
        return $items;
    }
    
    /**
    * Возвращает дополнительный источник получателей рассылки
    * 
    * @param \EmailSubscribe\Model\Source\EmailSubscribe[] $list
    * @return \EmailSubscribe\Model\Source\EmailSubscribe[]
    */
    public static function mailSenderGetSource($list)
    {
        $list[] = new \EmailSubscribe\Model\Source\EmailSubscribe();
        return $list;
    }
}
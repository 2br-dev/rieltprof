<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Config;
use \RS\Router;
use \RS\Orm\Type as OrmType;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('start')
            ->bind('applyroute')
            ->bind('getmenus')
            ->bind('getroute')
            ->bind('comments.gettypes');
    }
    
    /**
    * Обрабатываем событие запуска скрипта.
    */
    public static function start()
    {
        $idnaconvert = new \RS\Helper\IdnaConvert();
        $site = \RS\Site\Manager::getSite();
        $redirect_to_main_domain = $site['redirect_to_main_domain'];
        $partner = false;
        //Если партнёрский модуль присутствует, то проверим не портёрский ли это сайт
        if (\RS\Module\Manager::staticModuleExists('partnership') && \RS\Module\Manager::staticModuleEnabled('partnership')){
            $partner = \Partnership\Model\Api::getCurrentPartner();
            if ($partner) { // если мы на партнёрском сайте - подменяем сайт партнёрским
                $site = $partner;
            }
        }
        
        if ($site !== false) {
            //Реализация опции "Перенаправлять на основной домен сайта"
            if ($redirect_to_main_domain) {
                $domains = $site->getDomainsList();
                if ($domains) {
                    $request = \RS\Http\Request::commonInstance();
                    $current_domain = $request->server('HTTP_HOST');

                    if ($current_domain != $domains[0] && $idnaconvert->decode($current_domain) != $domains[0]) {
                        //Отдаем 301 редирект с теми же параметрами, но на основной домен
                        $new_url = $site->getAbsoluteUrl($request->server('REQUEST_URI', TYPE_MIXED));
                        \RS\Application\Application::getInstance()->headers
                            ->addHeader('location', $new_url)
                            ->setStatusCode(301)
                            ->sendHeaders();

                        exit;
                    }
                }
            }
        }
    }

    public static function getRoute($routes)
    {
        $routes[] = new Router\Route('site-front-policy-personaldata', "/policy/", null, t('Политика обработки персональных данных'));
        $routes[] = new Router\Route('site-front-policy-agreement', "/policy-agreement/", null, t('Согласие на обработку персональных данных'));

        return $routes;
    }


    public static function applyroute()
    {
        // Обрабатываем редирект на HTTPS
        $partner = false;
        //Если партнёрский модуль присутствует, то проверим не портёрский ли это сайт
        if (\RS\Module\Manager::staticModuleExists('partnership') && \RS\Module\Manager::staticModuleEnabled('partnership')){
            $partner = \Partnership\Model\Api::getCurrentPartner();
        }

        if ($partner) {
            $redirect = $partner['redirect_to_https'];
        } else {
            $site = \RS\Site\Manager::getSite();
            $redirect = $site['redirect_to_https'];
        }

        if ($redirect) {
            //Редирект на HTTPS
            $protocol = \RS\Http\Request::commonInstance()->getProtocol();
            if ($protocol == 'http') {
                $http = \RS\Http\Request::commonInstance();
                $http->to_entity = false;

                $skip = false;
                if (\RS\Router\Manager::obj()->isTechRoute() ||  \RS\Router\Manager::isStorageUrl()) $skip = true;

                if (!$skip) {
                    $new_url = 'https://'.$http->server('HTTP_HOST').$http->server('REQUEST_URI');
                    \RS\Application\Application::getInstance()->headers
                        ->addHeader('Strict-Transport-Security', 'max-age=86400')
                        ->addHeader('location', $new_url)
                        ->setStatusCode(301)
                        ->sendHeaders();
                    exit;
                }
            }
        }

        //Проверим, не закрыт ли сайт
        $site = \RS\Site\Manager::getSite();
        $manager = \RS\Router\Manager::obj();
        if ($site['is_closed'] && !$manager->isAdminZone() && !\RS\Application\Auth::getCurrentUser()->isAdmin()) {
            if(!\RS\Router\Manager::obj()->isTechRoute()) {
                $closed_controller = new \Site\Controller\Front\SiteClosed();
                echo $closed_controller->renderClosePage($site);
                exit;
            }
        }
    }
    
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Настройка сайта'),
                'alias' => 'siteoptions',
                'link' => '%ADMINPATH%/site-options/',
                'parent' => 'website',
                'sortn' => 60,
                'typelink' => 'link',
        ];
        $items[] = [
                'title' => t('Сайты'),
                'alias' => 'sites',
                'link' => '%ADMINPATH%/site-control/',
                'parent' => 'control',
                'sortn' => 4,
                'typelink' => 'link',
        ];
        
        return $items;
    }
    
    /**
    * Регистрируем тип комментариев "комментарии к сайту"
    * 
    * @param array $list
    * @return array
    */
    public static function commentsGetTypes($list)
    {
        $list[] = new \Site\Model\CommentType\Site();
        return $list;
    }
}

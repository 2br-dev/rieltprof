<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Config;
use Main\Model\NoticeSystem\InternalAlerts;
use MobileSiteApp\Model\AppApi;
use RS\Application\Application;
use \RS\Orm\Type;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getapps')
            ->bind('mobilesiteapp.gettemplates')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.init.menu-menu')
            ->bind('orm.init.article-category')
            ->bind('orm.init.catalog-dir')
            ->bind('orm.init.shop-delivery')
            ->bind('getroute')
            ->bind('internalalerts.get')
            ->bind('getmenus')
            ->bind('start');
    }


    /**
     * Действия на старте системы
     */
    public static function start()
    {
        $config = \RS\Config\Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();

        if ($config->enable_app_sticker && !$router->isAdminZone()) {
            //Размещаем стикеры о наличии мобильного приложения для сайта
            $app     = Application::getInstance();
            $site    = \RS\Site\Manager::getSite();
            if (method_exists($site, 'getMainDomain')) { //Для совместимости со старыми версиями RS
                $domain = $site->getMainDomain() . $site->getRootUrl();
                $app_api = new AppApi();
                $data = $app_api->getSubscribeInfo($domain);

                if (isset($data['appstore_app_id']) && $data['appstore_app_id']) {
                    //Для Apple
                    $app->meta->add([
                        'name' => 'apple-itunes-app',
                        'content' => 'app-id=' . $data['appstore_app_id'],
                        'data-title' => $site['title']
                    ]);
                }

                if (isset($data['googleplay_app_id']) && isset($data['google_icon'])) {
                    //Для Android
                    $app->addCss('%mobilesiteapp%/jquery.smartbanner.css');
                    $app->addJs('%mobilesiteapp%/jquery.smartbanner.js');
                    $app->addCss($data['google_icon'], null, BP_ROOT, true, [
                        'rel' => 'apple-touch-icon'
                    ]);

                    $app->meta->add([
                        'name' => 'google-play-app',
                        'content' => 'app-id=' . $data['googleplay_app_id'],
                        'data-title' => $site['title']
                    ]);
                }
            }
        }
    }
    
    /**
    * Возвращает тип приложения для доступа к внешнему API
    * 
    * @param array $app_types - массив из уже существующих приложений
    * @return array
    */
    public static function getApps($app_types)
    {
        $app_types[] = new \MobileSiteApp\Model\AppTypes\MobileSiteApp();
        return $app_types;
    }

    /**
     * Добавляет пункты меню в административной панели
     * @param array $items Пункты меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Мобильное приложение'),
            'alias' => 'mobilesiteapp',
            'link' => '%ADMINPATH%/mobilesiteapp-appctrl/',
            'parent' => 'modules',
            'typelink' => 'link',
        ];

        return $items;
    }
    
    
    /**
    * Возвращает список из путей к шаблонам. Путь может быть отсительным с использованием %наименование модуля%. Принимается массив
    * ключ - путь шаблону
    * значение - массив со сведениями о данные
    * 
    * 
    * Например:
    * mobilesiteapp => array( //Модуль мобильного приложения
    *   'title' => 'По умолчанию', //Наименование шаблона
    *   'css'   => 'build/css/', //Относительный путь к css файлам
    *   'fonts' => 'build/fonts/', //Относительный путь к css файлам 
    *   'js'    => 'build/js/', //Относительный путь к js файлам 
    * ) 
    * 
    * @param array $templates - массив с шаблонами собраные из модулей
    * @return array
    */
    public static function mobileSiteAppGetTemplates($templates)
    {
        $templates['mobilesiteapp'] = [
            'title'          => t('По умолчанию'),                     //Наименование шаблона
            'mobile_root'    => '%MOBILEPATH%/appsource',              //Относительный путь к файлам. %MOBILEPATH% - путь к приложению
            'templates_root' => '%MOBILEPATH%/view',                   //Относительный путь к шаблонам. %MOBILEPATH% - путь к приложению
            'www_dir'        => '%MOBILEROOT%/www',                    //Относительный путь к css файлам. %MOBILEROOT% - путь к корню приложения
            'css'            => '%MOBILEROOT%/www/build',              //Относительный путь к css файлам. %MOBILEROOT% - путь к корню приложения
            'fonts'          => '%MOBILEROOT%/www/build/assets/fonts', //Относительный путь к файлам с шрифтами. %MOBILEROOT% - путь к корню приложения
            'js'             => '%MOBILEROOT%/www/build',              //Относительный путь к js файлам. %MOBILEROOT% - путь к корню приложения
            'img'            => '%MOBILEROOT%/www/images',             //Относительный путь к картинкам. %MOBILEROOT% - путь к корню приложения
        ];
        return $templates;
    }
    
    
    /**
    * Расширяем объект меню
    * 
    * @param \Menu\Model\Orm\Menu $menu - объект меню
    */
    public static function ormInitMenuMenu(\Menu\Model\Orm\Menu $menu)
    {
        $menu->getPropertyIterator()->append([
            t('Мобильное приложение'),
                'mobile_public' => new Type\Integer([
                    'maxLength' => '1',
                    'default' => 0,
                    'description' => t('Показывать в мобильном приложении'),
                    'hint' => t('Необходимо наличие мобильного приложения. См. документацию.'),
                    'CheckboxView' => [1,0],
                    'meVisible' => false,
                    'specVisible' => false,
                ]),
                'mobile_image' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Идентификатор картинки Ionic 2'),
                    'template' => '%mobilesiteapp%/form/menu/mobile_image.tpl',
                    'hint' => t('Укажите в данном поле идентификтор картинки из справочника'),
                    'meVisible' => false,
                    'appVisible' => true,
                ]),
        ]);
    }

    /**
     * Расширяем объект статьи
     *
     * @param \Article\Model\Orm\Category $category - объект меню
     */
    public static function ormInitArticleCategory(\Article\Model\Orm\Category $category)
    {
        $category->getPropertyIterator()->append([
            t('Мобильное приложение'),
            'mobile_public' => new Type\Integer([
                'maxLength' => '1',
                'default' => 0,
                'description' => t('Показывать в мобильном приложении'),
                'hint' => t('Необходимо наличие мобильного приложения. См. документацию.'),
                'CheckboxView' => [1,0],
                'meVisible' => false,
                'specVisible' => false,
            ]),
            'mobile_image' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Идентификатор картинки Ionic 2'),
                'template' => '%mobilesiteapp%/form/menu/mobile_image.tpl',
                'hint' => t('Укажите в данном поле идентификтор картинки из справочника'),
                'meVisible' => false,
                'specVisible' => false,
            ]),
        ]);
    }
    
    
    /**
    * Расширяем объект категории
    * 
    * @param \Catalog\Model\Orm\Dir $dir - объект категории
    */
    public static function ormInitCatalogDir(\Catalog\Model\Orm\Dir $dir)
    {
        $dir->getPropertyIterator()->append([
            t('Мобильное приложение'),
                'mobile_background_color' => new Type\Color([
                    'description' => t('Цвет фона для планшета'),
                    'maxLength' => '11',
                    'default' => '#E0E0E0',
                    'appVisible' => true,
                    'rootVisible' => false,
                ]),
                'mobile_tablet_background_image' => new Type\Image([
                    'description' => t('Картинка для планшета'),
                    'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'], //Допустимы форматы jpg, png, gif
                    'appVisible' => true,
                    'rootVisible' => false,
                ]),
                'mobile_tablet_icon' => new Type\Image([
                    'description' => t('Картинка для мобильной версии'),
                    'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'], //Допустимы форматы jpg, png, gif
                    'appVisible' => true,
                    'rootVisible' => false,
                ]),
        ]);
    }
    
    /**
    * Расширяем объект доставки
    * 
    * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
    */
    public static function ormInitShopDelivery(\Shop\Model\Orm\Delivery $delivery)
    {
        $delivery->getPropertyIterator()->append([
            'mobilesiteapp_additional_html' => new Type\MixedType([
                'description' => t('Дополнительный функционал для приложения на Ionic'),
                'visible' => false,
                'appVisible' => true,
            ]),
        ]);
    }
    
    
    /**
    * Возвращает присок доступных маршрутов
    * 
    * @param \RS\Router\Route[] $routes
    * @return \RS\Router\Route[]
    */
    public static function getRoute($routes) 
    {
        //Мобильный сайт
        $routes[] = new \RS\Router\Route('mobilesiteapp-front-gate', [
            '/mobilesiteapp/{Act}/',
            '/mobilesiteapp/'
        ], null, t('Мобильный сайт'));
        
        return $routes;
    }  
    
    /**
    * Обработка события создания или обновления заказа, отсылка PUSH уведомления об изменениях
    * 
    * @param array $data - массив данных
    */
    public static function ormAfterwriteShopOrder($data)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->push_enable 
            && $data['flag'] == \RS\Orm\AbstractObject::UPDATE_FLAG && $data['orm']['notify_user']) //Если заказ обновился и нужно уведомить пользователя 
        {
            $push = new \MobileSiteApp\Model\Push\OrderChangeToUser();
            $push->init($data['orm']);
            $push->send();            
        }
    }

    /**
     * Проверяет если вдруг истек срок подписки, то на спец странице будет специальное сообщение
     *
     * @param array $params - параметры
     */
    public static function internalAlertsGet($params)
    {
        $internal_alerts = $params['internal_alerts'];
        $app_api = new AppApi();

        /**
         * @var $sites \Site\Model\Orm\Site[]
         */
        $sites = \RS\Site\Manager::getSiteList();
        foreach($sites as $site) {
            if (method_exists($site, 'getMainDomain')) {  //Для совместимости со старыми версиями RS
                $domain = $site->getMainDomain().$site->getRootUrl();
                if ($text = $app_api->getExpireText($domain)) {
                    $href = $app_api->getControlUrl($domain);
                    $internal_alerts->addMessage($text, $href, null, InternalAlerts::STATUS_WARNING);
                }
            }
        }

    }
}
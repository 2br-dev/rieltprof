<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Config;

use Designer\Model\BlocksApi;
use RS\Router\Route;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getroute')
            ->bind('render.beforeoutput');
    }

    /**
     * Возвращает массив маршрутов для системы
     *
     * @param Route[] $routes - массив установленных ранее маршрутов
     * @return Route[]
     */
    public static function getRoute($routes)
    {
        //Карточка товара
        $routes[] = new Route('designer-front-form', '/designer-form/', null, t('Обработка формы обратной связи для блока дизайнера'), true);
        $routes[] = new Route('designer-front-productslist', '/designer-productslist/{category}/', null, t('Обработка списка товаров для блока дизайнера'), true);
        $routes[] = new \RS\Router\Route('designer.css', [
            \Setup::$MODULE_FOLDER.'/designer/cache/css/{type}-{id}\.{ext}$',
            \Setup::$MODULE_FOLDER.'/designer/cache/css/{type}\.{ext}$',
        ], [
            'controller' => 'designer-renderresource'
        ], t('CSS блока дизайнера'), true);
        return $routes;
    }

    /**
     * Добавляем меню слева для взаимодействия с блоками дизайна
     *
     * @param string $html - текущий HTML подготовленный для вывода
     * @return string
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    public static function renderBeforeOutput($html)
    {
        //Если режим отладки включен
        if (\RS\Debug\Mode::isEnabled() && !\RS\Router\Manager::obj()->isAdminZone()) {
            $config = \RS\Config\Loader::byModule('designer');

            $designer_settings = $config['designer_settings'];

            if (empty($designer_settings)){
                $designer_settings_json = json_encode(BlocksApi::getMMenuDefaultSettings());
            }else{
                $designer_settings_json = $designer_settings;
            }

            $view = new \RS\View\Engine();
            $view->assign([
                'settings_json' => $designer_settings_json
            ]);
            $html .= $view->fetch('%designer%/sidebar/leftmenu.tpl');
        }

        return $html;
    }
}

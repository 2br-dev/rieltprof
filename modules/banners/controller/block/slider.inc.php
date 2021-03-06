<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Controller\Block;
use \RS\Orm\Type;

class Slider extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Слайдер',
        $controller_description = 'Отображает зону, в которой последовательно отображаются баннеры';
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/slider/slider.tpl', //Должен быть задан у наследника
            'zone' => null,
            'autoplay_delay' => 10,
            'cache_html_lifetime' => 300
    ];
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'zone' => new Type\Integer([
                'description' => t('Зона баннеров'),
                'list' => [['\Banners\Model\ZoneApi', 'staticSelectList']]
            ]),
            'autoplay_delay' => new Type\Integer([
                'description' => t('Время показа одного слайда, сек'),
                'hint' => t('0 - отключить автопроигрывание слайдов')
            ]),
            'cache_html_lifetime' => new Type\Integer([
                'description' => t('Время кэширования HTML блока, секунд?'),
                'hint' => t('0 - кэширование выключено. Значение больше нуля ускоряет работу сайта, но допускает неактуальность данных на срок кэширования. Работает только если в настройках системы включено кэширование данных.'),
            ]),
        ]);
    }
    
    function actionIndex()
    {
        $cache_id = json_encode($this->getParam());
        $template = $this->getParam('indexTemplate');

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {
            $zone_id = $this->getParam('zone');

            if ($debug_group = $this->getDebugGroup()) {
                $create_href = $this->router->getAdminUrl('add', ['zone' => $zone_id], 'banners-ctrl');
                $debug_group->addTool('create', new \RS\Debug\Tool\Create($create_href));
            }

            $zone_api = new \Banners\Model\ZoneApi();
            $zone = $zone_api->getById($zone_id);
            $this->view->assign([
                'zone' => $zone,
                'banners' => $zone ? $zone->getBanners() : []
            ]);
        }
        
        return $this->result->setTemplate( $template );
    }

}

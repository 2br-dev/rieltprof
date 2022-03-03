<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Controller\Block;
use \RS\Orm\Type;

/**
* Блок контроллер - баннерная зона
*/
class BannerZone extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Зона для баннера',
        $controller_description = 'Отображает банеры, связанные с выбранной зоной';
        
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/bannerzone/zone.tpl', //Должен быть задан у наследника
            'zone' => null,
            'cache_html_lifetime' => 300
    ];
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'zone' => new Type\Integer([
                'description' => t('Зона баннеров'),
                'list' => [['\Banners\Model\ZoneApi', 'staticSelectList']]
            ]),
            'rotate' => new Type\Integer([
                'description' => t('Отображать один случайный баннер'),
                'checkboxView' => [1,0]
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
                $debug_group->addDebugAction(new \RS\Debug\Action\Create($create_href));
                $debug_group->addTool('create', new \RS\Debug\Tool\Create($create_href));
            }

            $zone_api = new \Banners\Model\ZoneApi();
            $zone = $zone_api->getById($zone_id);
            $this->view->assign([
                'zone' => $zone
            ]);
        }
        
        return $this->result->setTemplate( $template );
    }
}
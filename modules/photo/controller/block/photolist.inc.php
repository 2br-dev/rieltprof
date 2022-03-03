<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Controller\Block;
use \RS\Orm\Type;
/**
* Блок контроллер - список фотографий
*/
class Photolist extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Список прикрепленных фотографий',
        $controller_description = 'Отображает полосу прикрепленных фотографий с возможностью их просмотреть в увеличенном виде';
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/photolist/photolist.tpl',
            'order_by' => 'sortn desc'
    ];
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'type' => new Type\Varchar([
                'description' => t('Тип объектов, к которым привязаны картинки')
            ]),
            'route_id_param' => new Type\Varchar([
                'description' => t('Параметр, в котором получать id объекта, к которому привязаны фото')
            ]),
            'link_id' => new Type\Integer([
                'description' => t('ID объекта(необязательно)')
            ]),
            'order_by' => new Type\Varchar([
                'description' => t('Сортировка'),
                'listFromArray' => [[
                    'sortn desc' => t('По порядку'),
                    'id' => t('По возрастанию ID'),
                    'id desc' => t('По убыванию ID'),
                ]]
            ])
        ]);
    }
    
    function actionIndex()
    {
        $route = $this->router->getCurrentRoute();
        $param = $this->getParam('route_id_param');
        $type = $this->getParam('type');
        $id = $route->getExtra($param);
        if ($this->getParam('link_id')) {
            $link_id = $this->getParam('link_id');
        } else {
            $link_id = isset($id) ? $id : $param;
        }
        $api = new \Photo\Model\PhotoApi();
        $api->setFilter('type', $type);
        $api->setFilter('linkid', $link_id);
        $api->setOrder($this->getParam('order_by'));

        $photos = $api->getList();
        $this->view->assign('photos', $photos);

        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}

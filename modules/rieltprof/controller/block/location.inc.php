<?php
namespace Rieltprof\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Rieltprof\Model\LocationApi;
use RS\Controller\StandartBlock;
use RS\Http\Request as HttpRequest;
use RS\Orm\ControllerParamObject;
use RS\Orm\Type\Varchar;


/**
 * Контроллер - топ товаров из указанных категорий одним списком
 */
class Location extends StandartBlock
{
    protected static $controller_title = 'Выбор города';
    protected static $controller_description = 'Отображает текущий регион и город и позволяет выбрать';

    protected $default_params = [
        'indexTemplate' => '%rieltprof%/block/location/location.tpl' //Должен быть задан у наследника
    ];

    /** @var LocationApi $api */
    public $api;

    function init()
    {
        $this->api = new LocationApi();
        $this->api->setFilter('public', 1);
    }

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return ControllerParamObject | false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'referrer' => (new Varchar())
                ->setDescription(t('Адрес текущей страницы')),
        ]);
    }

    /**
     * Отображение регионов и городов
     */
    function actionIndex()
    {
        $referrer = $this->getParam('referrer', HttpRequest::commonInstance()->selfUri());
        $current_city = $this->api->getCurrentCity();
        if($current_city){
            $current_region = $this->api->getParentLocation($current_city['parent_id']);
        }else{
            $current_region = $this->api->getCurrentRegion();
        }

        $this->view->assign([
            'current_city' => $current_city,
            'current_region' => $current_region,
            'referrer' => $referrer,
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}

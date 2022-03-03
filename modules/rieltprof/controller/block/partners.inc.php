<?php
/**
 * ReadyScript (http://readyscript.ru)
 *
 * @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
 * @license http://readyscript.ru/licenseAgreement/
 */

namespace Rieltprof\Controller\Block;

use rieltprof\Model\PartnersApi;
use RS\Controller\StandartBlock;

/**
 * Контроллер - топ товаров из указанных категорий одним списком
 */
class Partners extends StandartBlock
{
    protected static $controller_title = 'Партнеры';
    protected static $controller_description = 'Отображает партнеров';

    protected $default_params = [
        'indexTemplate' => '%rieltprof%/block/partners.tpl', //Должен быть задан у наследника
        'pageSize' => 100,
    ];
    protected $page;

    /** @var PartnersApi $api */
    public $api;

    function init()
    {
        $this->api = new PartnersApi();
    }

    function actionIndex()
    {
        $pageSize = $this->getParam('pageSize', null);
        $this->api->setFilter('public', 1);
        $partners = $this->api->getList();
        $this->view->assign([
            'partners' => $partners,
            'block_title' => $this->getParam('block_title'),
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}

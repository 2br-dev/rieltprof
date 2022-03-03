<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin\Block;

use Main\Model\NoticeSystem\Meter;
use Main\Model\RsNewsApi;

/**
 * Класс отвечает за отображение новостей в административной панели
 */
class RsNews extends \RS\Controller\Admin\Block
{
    const
        PAGE_SIZE = 10;

    protected
        $action_var = 'rsnews_do';

    public
        $api;

    function init()
    {
        $this->api = new RsNewsApi();
    }

    function actionIndex()
    {
        return $this->result->setTemplate('%main%/adminblocks/rsnews/rsnews_item.tpl');
    }

    function actionAjaxGetNews()
    {
        $page = $this->url->get('p', TYPE_INTEGER, 1);
        $news_data = $this->api->getNewsList($page, self::PAGE_SIZE);

        $paginator = new \RS\Helper\Paginator($page, $news_data ? $news_data['summary']['total'] : 0, self::PAGE_SIZE);

        $this->view->assign([
            'news_data' => $news_data,
            'error' => $this->api->getLastError(),
            'paginator' => $paginator,
            'unviewed' => Meter::getInstance()->getNumber(RsNewsApi::METER_KEY)
        ]);

        return $this->result
            ->addSection('title', t('Новости'))
            ->addSection('html_head', $this->view->fetch('%main%/adminblocks/rsnews/rsnews_head.tpl'))
            ->setTemplate('%main%/adminblocks/rsnews/rsnews_list.tpl');
    }

    function actionAjaxMarkAsViewed()
    {
        $id = $this->url->get('id', TYPE_INTEGER);

        return $this->result->setSuccess(true)->addSection('meters', [
            RsNewsApi::METER_KEY => $this->api->markAsViewed($id)
        ]);
    }

    function actionAjaxMarkAllAsViewed()
    {
        return $this->result->setSuccess(true)->addSection('meters', [
            RsNewsApi::METER_KEY => $this->api->markAllAsViewed()
        ]);
    }
}
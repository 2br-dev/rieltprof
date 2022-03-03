<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Admin;

use \RS\Html\Table\Type as TableType;
use \RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

class Control extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new \Site\Model\Api());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper['beforeTableContent'] = $this->view->fetch('site_limit.tpl');
        $helper['bottomToolbar']->removeItem('multiedit');
        $helper->setTopTitle(t('Сайты'));
        $helper->setTopHelp($this->view->fetch('top_help.tpl'));
        
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC,'CurrentSort' => SORTABLE_ASC,'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH,'LinkAttr' => ['class' => 'crud-edit'], 'href' => $this->router->getAdminPattern('edit', [':id' => '@id'])]),
                new TableType\Text('domains', t('Доменные имена'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('language', t('Язык')),
                new TableType\StrYesno('default', t('По умолчанию')),
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                        new TableType\Action\DropDown([
                            [
                                'title' => t('Редактировать файл robots.txt'),
                                'attr' => [
                                    'class' => 'crud-edit',
                                    '@href' => $this->router->getAdminPattern('editRobotsTxt', [':site_id' => '@id']),
                                ]
                            ]
                        ])
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));
        return $helper;
    }

    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if (!$primaryKeyValue) {
            if (!__CAN_ADD_SITE()) {
                return $this->result->addSection('close_dialog', true)->addEMessage(t('Достигнут лимит по количеству сайтов'));
            }
            
            $this->api->getElement()->getProp('theme')->setChecker('chkEmpty', t('Выберите тему оформления для нового сайта'));
        }
        
        $this->api->getElement()->tpl_module_folders = \RS\Module\Item::getResourceFolders('templates');
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Перемещение позиций
     *
     * @return mixed
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }

    /**
     * Хелпер метода редактирования robots.txt
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    function helperEditRobotsTxt()
    {
        return new \RS\Controller\Admin\Helper\CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $this->buttons(['save', 'cancel']),
            'viewAs' => 'form',
            'formTitle' => t('Редактировать robots.txt')
        ]);
    }

    /**
     * Открытие окна редактирования robots.txt
     *
     * @return $this|\RS\Controller\Result\Standard
     */
    function actionEditRobotsTxt()
    {
        $site_id = $this->url->get('site_id', TYPE_INTEGER);
        $site = new \Site\Model\Orm\Site($site_id);
        if (!$site['id']) $this->e404(t('Сайт не найден'));
        
        $robots_txt_api = new \Site\Model\RobotsTxtApi($site);
        $robots_content = $this->url->post('robots_content', TYPE_STRING, $robots_txt_api->getRobotsTxtContent(), false);
        
        $helper = $this->getHelper();
        
        if ($this->url->isPost()) {
            if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                return $this->result->addEMessage($access_error);
            }
            $robots_txt_api->writeRobotsTxt(htmlspecialchars_decode( $robots_content, ENT_QUOTES ));
            return $this->result
                ->setSuccess( true )
                ->setSuccessText(t('Изменения успешно сохранены'));
        }

        $this->view->assign([
            'elements' => $helper->active(),
            'robots_content' => $robots_content
        ]);
        $helper['form'] = $this->view->fetch('form/site/robots.tpl');
                
        return $this->result->setTemplate( $helper['template'] );
    }

}



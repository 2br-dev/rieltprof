<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

class Ctrl extends \RS\Controller\Admin\Crud
{
    protected
        $route_id;
        
    function __construct()
    {
        parent::__construct(new \PageSeo\Model\PageSeoApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Раздел позволяет задать каждой странице заголовок, который будет отображаться в браузере. Вы можете также составить краткое описание для конкретной страницы, а если считаете нужным, то указать и ключевые слова, по которой оптимизируете конкретную страницу сайта. Обязательно воспользуйтесь данным разделом, чтобы указать мета данные для главной страницы вашего сайта.'));
        $helper->setTopTitle(t('Заголовки, мета-тэги страниц'));
        $helper->addCsvButton('pageseo-pageseo');
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->setListFunction('pageSeoList');
        
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),            
                new TableType\Text('description', t('Страница')),
                new TableType\Usertpl('routeview', t('Маршрут'), $this->mod_tpl.'pageseo_column_route.tpl', ['hidden' => true]),
                new TableType\Usertpl('', t('Мета-теги'), $this->mod_tpl.'pageseo_column_meta.tpl', ['TdAttr' => ['class' => 'cell-small']]),
                new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']),null, [
                                'attr' => [
                                    '@data-id' => '@id'
                                ]
                            ]),
                ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));
        return $helper;
    }
    
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $create = $this->url->get('create', TYPE_INTEGER);
        
        if ($create) {
            $this->api->getElement()->route_id = $this->route_id;
            $this->api->getElement()->getProp('route_id')->setReadOnly(true);
        }
        $this->api->getElement()->replaceOn(true);        

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
    
    function actionEdit()
    {
        $id = $this->url->request('id', TYPE_STRING);
        $route_id = null;
        if (!is_numeric($id)) {
            $route_id = $id;
            $id = null;
            $this->api->setFilter('route_id', $route_id);            
            $element = $this->api->getFirst();
            if ($element) {
                $this->api->setElement( $element );
                $id = $element['id'];
            }
        } else {
            $this->api->getElement()->load($id);
        }
        $this->route_id = $route_id;
        return $this->actionAdd($id);
    }

}



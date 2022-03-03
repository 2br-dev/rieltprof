<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\Links\Type\AbstractType;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Toolbar\Button\Cancel;
use RS\Html\Toolbar\Button\SaveForm;
use RS\Html\Toolbar\Element;

/**
 * Контроллер, отвечающий за отображение диалога выбота связываемых объектов
 * в формах объектов
 */
class LinkCtrl extends \RS\Controller\Admin\Front
{
    /**
     * Отображает диалог добавления связи
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAddLink()
    {
        $link_types = $this->url->get('link_types', TYPE_ARRAY);
        $link_type = $this->url->post('link_type', TYPE_STRING, $link_types[0]);

        $links_type_objects = [];
        foreach($link_types as $type) {
            $links_type_objects[$type] = AbstractType::makeById($type);
        }

        if ($this->url->isPost()) {
            if (!isset($links_type_objects[$link_type])) {
                $this->e404(t('Не найден тип связи'));
            }

            $link_type_object = $links_type_objects[$link_type];
            $form_object = $link_type_object->getTabForm();

            if ($form_object->checkData()) {

                $link_id = $link_type_object->getLinkIdByTabFormObject($form_object);
                $link_type_object->init($link_id);

                return $this->result
                    ->addSection([
                        'noUpdateTarget' => true,
                        'link_type' => $link_type,
                        'link_id' => $link_id,
                        'link_view' => $link_type_object->getLinkView()
                    ])
                    ->setSuccess(true);

            } else {
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($form_object->getDisplayErrors());
            }
        }


        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Выберите связываемый объект'));
        $helper->setBottomToolbar(new Element([
            'Items' => [
                new SaveForm(),
                new Cancel(null)
            ]
        ]));
        $helper->viewAsForm();
        $this->view->assign([
            'links_type_objects' => $links_type_objects,
            'link_type' => $link_type
        ]);
        $helper['form'] = $this->view->fetch('admin/links/dialog.tpl');

        return $this->result->setTemplate( $helper['template'] );
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;
use RS\Controller\Admin\Front;
use \RS\Html\Toolbar\Button as ToolbarButton;
use \RS\Html\Toolbar;

/**
 * Контроллер отвечает за отображение диалога выбора или создания пользователя
 */
class UserDialog extends Front
{
    function actionIndex()
    {
        $helper  = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper
            ->setTopTitle(t('Добавление пользователя'))
            ->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(null, t('применить')),
                    'cancel' => new ToolbarButton\Cancel(null, t('отмена')),
                ]
            ]))
            ->viewAsForm();

        $user    = new \Users\Model\Orm\User();

        //Если нужно обновить блок
        if ($this->url->isPost()) {
            $is_reg_user = $this->request('is_reg_user', TYPE_INTEGER); //Смотрим, нужно ли регистривать или указать существующего пользователя

            if ($is_reg_user) { //Если нужно регистрировать

                $user->save(null, ['changepass' => 1]);

            } else { //Если не нужно регистрировать, а указать конкретного пользователя
                $user_id = $this->request('user_id', TYPE_INTEGER);

                if (!$user_id || !$user->load($user_id)) {
                    $user->addError(t('Не выбран пользователь'));
                }
            }

            if ($user->hasError()) {
                return $this->result->setSuccess(false)
                    ->setErrors($user->getDisplayErrors());
            } else {
                return $this->result->setSuccess(true)
                    ->addSection('noUpdateTarget', true)
                    ->addSection('user_fio', $user->getFio()."({$user['id']})")
                    ->addSection('user_link', $this->router->getAdminUrl('edit', ['id' => $user['id']], 'users-ctrl'))
                    ->addSection('user_id', $user['id']);
            }
        } else {
            $user['openpass'] = \RS\Helper\Tools::generatePassword(10);
        }

        //Стандартное поле выбора пользователя
        $field = new \RS\Orm\Type\User([
            'name' => 'user_id'
        ]);

        $this->view->assign([
            'field' => $field,
            'user' => $user,
            'conf_userfields' => $user->getUserFieldsManager()
        ]);

        $helper['form'] = $this->view->fetch('%users%/admin/user_dialog_body.tpl');
        return $this->result->setTemplate($helper['template']);
    }
}
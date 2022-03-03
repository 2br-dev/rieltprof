<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

/**
 * Тип поля - зарегистрированный пользователь.
 * В данном случае пользователь может быть либо выбран среди существующих, либо создан с помощью диалогового окна
 */
class UserDialog extends User
{
    protected $select_user_template = '%system%/coreobject/type/form/user.tpl';
    protected $form_template = '%system%/coreobject/type/form/user_dialog.tpl';
    protected $create_user_url;
    protected $dialog_url;

    /**
     * Устанавливает URL, на который будет отправляться POST запрос на создание пользователя
     *
     * @param string $url
     * @return void
     */
    function setCreateUserUrl($url)
    {
        $this->create_user_url = $url;
    }

    /**
     * Возвращает URL, на который будет отправляться POST запрос на создание пользователя
     *
     * @return string
     * @return void
     */
    function getCreateUserUrl()
    {
        return $this->create_user_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxCreateUser', null, 'users-ajaxlist');
    }

    /**
     * Устанавливает URL, на который будет отправляться GET Запрос для загрузки диалога выбора пользователя
     *
     * @param string $url
     * @return void
     */
    function setDialogUrl($url)
    {
        $this->dialog_url = $url;
    }

    /**
     * Возвращает URL, на который будет отправляться GET Запрос для загрузки диалога выбора пользователя
     *
     * @return string
     */
    function getDialogUrl()
    {
        return $this->dialog_url ?: \RS\Router\Manager::obj()->getAdminUrl(false, null, 'users-userdialog');
    }

    /**
     * Устанавливает шаблон, обычного выбора зарегистрированного пользователя
     *
     * @param string $template
     * @return void
     */
    function setSelectUserTemplate($template)
    {
        $this->select_user_template = $template;
    }

    /**
     * Возвращает шаблон, обычного выбора зарегистрированного пользователя
     *
     * @return string
     */
    function getSelectUserTemplate()
    {
        return $this->select_user_template;
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

/**
 * Тип колонки - пользователь. Отображает ФИО пользователя и его ID
 */
class User extends AbstractType
{
    protected
        $body_template = 'system/admin/html_elements/table/coltype/user.tpl';

    /**
     * Загружает и возвращает объект пользователя
     *
     * @return \Users\Model\Orm\User
     */
    function getUser()
    {
        $user_id = $this->getValue();

        $user = new \Users\Model\Orm\User();
        if ($user_id > 0) {
            $user->load($user_id);
        }

        return $user;
    }
}
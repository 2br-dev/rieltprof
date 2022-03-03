<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Controller\Admin;

use RS\Orm\Request;
use Users\Model\Api;
use Users\Model\Orm\UserInGroup;

/**
 * Контроллер отдает список пользователей для компонента JQuery AutoComplete
 * @ingroup Users
 */
class AjaxList extends \RS\Controller\Admin\Front
{
    /** @var \Users\Model\Api */
    public $api;

    function init()
    {
        $this->api = new Api();
    }

    function actionAjaxEmail()
    {
        $term = $this->url->request('term', TYPE_STRING);
        $groups = $this->url->request('groups', TYPE_ARRAY, []);

        $list = $this->api->getLike($term, ['login', 'e_mail', 'surname', 'name', 'company', 'company_inn', 'phone'], [
            Api::USER_LIKE_FILTER_GROUPS => $groups
        ]);

        $json = [];
        foreach ($list as $user) {
            $json[] = [
                'label' => $user['surname'] . ' ' . $user['name'] . ' ' . $user['midname'],
                'id' => $user['id'],
                'email' => $user['e_mail'],
                'desc' => t('E-mail') . ':' . $user['e_mail'] . ($user['company'] ? t(" ; {$user['company']}(ИНН:{$user['company_inn']})") : '')
            ];

        }

        return json_encode($json);
    }
}

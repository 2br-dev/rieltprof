<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;
use Catalog\Model\Orm\OneClickItem;
use RS\Http\Request;

/**
 * Push уведомление администратору о покупке в 1 клик
 */
class NewOneClickToAdmin extends AbstractPushToAdmin
{
    public
        $one_click;

    public function init(OneClickItem $one_click)
    {
        $this->one_click = $one_click;
    }

    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Новая покупка в 1 клик(администратору)');
    }


    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        $request = Request::commonInstance();
        return t('Новая покупка в 1 клик на сайте %site', [
            'site' => $request->getDomainStr(),
        ]);
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        $user = $this->one_click->getUser();

        if ($user['phone']) {
            $contact = "({$user['phone']})";
        } elseif ($user['e_mail']) {
            $contact = "({$user['e_mail']})";
        } else {
            $contact = '';
        }

        return t('Покупатель: %fio %contact', [
            'fio' => $user->getFio(),
            'contact' => $contact
        ]);
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        $site = new \Site\Model\Orm\Site($this->one_click['__site_id']->get());
        return [
            'site_uid' => $site->getSiteHash()
        ];
    }

}
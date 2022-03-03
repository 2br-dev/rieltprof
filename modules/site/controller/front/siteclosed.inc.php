<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Front;

/**
 * Класс отвечает за отображение страницы "Сайт закрыт"
 */
class SiteClosed extends \RS\Controller\AbstractController
{
    /**
     * Отображает страницу о том, что доступ к сайту запрещен и останавливает
     * дальнейшее выполнение скрипта.
     *
     * @param \Site\Model\Orm\Site|\Partnership\Model\Orm\Partner $site_or_partner - Объект сайта или партнерского сайта, на который доступ воспрещен
     * @throws \RS\Exception
     * @return string
     */
    function renderClosePage($site_or_partner)
    {
        if ( !($site_or_partner instanceof \Site\Model\Orm\Site)
            && !($site_or_partner instanceof \Partnership\Model\Orm\Partner))
        {
            throw new \RS\Exception(t('Ожидался объект класса \Site\Model\Orm\Site или \Partnership\Model\Orm\Partner'));
        }

        $this->view->assign([
            'close_object' => $site_or_partner,
            'message' => $site_or_partner['close_message'],
        ]);

        $body = $this->view->fetch('%site%/siteclosed/closed.tpl');
        return $this->wrapHtml($body);
    }
}

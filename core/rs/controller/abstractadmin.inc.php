<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller;
use RS\Application\Auth;
use RS\Http\Request;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\AbstractObject;
use Site\Model\Orm\Site;
use RS\Site\Manager as SiteManager;

/**
* Базовый класс блочных и фронтальных контроллеров администраторской части
*/
abstract class AbstractAdmin extends AbstractModule
{
    protected
        /**
         * @var string Зарезервированное имя для принудительной установки ID сайта
         */
        $change_site_var = 'site_id_context',
        $before_site_id;

    function __construct($param = [])
    {
        // Устанавливает принудительно заданный ID сайта в качестве текущего,
        // если у пользователя есть права к данному сайту
        if ($site_id_context = Request::commonInstance()->get($this->change_site_var, TYPE_INTEGER)) {
            $this->changeSiteIdIfNeed($site_id_context);
        }

        parent::__construct($param);
    }

    /**
    * Возвращает false, если нет ограничений на запуск контроллера, иначе вызывает исключение 404.
    * Вызывается при запуске метода exec() у контроллера, перед исполнением действия(action).
    * В методе можно проверять права доступа ко всему контроллеру или к конкретному действию.
    * 
    * @return bool(false)
    */
    function checkAccessRight()
    {
        //Для доступа к контроллеру пользователь должен быть администратором
        if (!\RS\Application\Auth::getCurrentUser()->isAdmin()) {
            return t('Недостаточно прав. Необходимы права администратора.');
        } 
        return parent::checkAccessRight();
    }

    /**
     * Устанавливает необходимый сайт на время выполнения одного жизненного цикта текущего скрипта
     *
     * @param
     * @return bool
     */
    protected function changeSiteIdIfNeed($new_site_id)
    {
        $site_id = SiteManager::getSiteId();
        if ($new_site_id > 0 && $new_site_id != $site_id) {

            //Проверим права на другой сайт
            $user = Auth::getCurrentUser();
            if (!$user->checkSiteRights($new_site_id)) {
                $this->e404(t('Недостаточно прав для доступа к сайту'));
            }

            $site = new Site($new_site_id);
            if ($site['id']) {
                $this->before_site_id = $site_id;
                SiteManager::setCurrentSite($site);
                return true;
            }
        }

        return false;
    }

    /**
     * Изменяет текущий сайт на время выполнения одного действия контроллера в случае,
     * если редактируемый объект с другого мультисайта
     *
     * @param EntityList $api Класс API для ORM объекта $orm_object
     * @param AbstractObject $orm_object ORM объект, из которого необходимо узнать ID сайта, который нужно выставить
     * @return
     */
    protected function setSiteIdByOrmObject($api, $orm_object = null)
    {
        if (!$api->isMultisite()) return false;

        if (!$orm_object) {
            $orm_object = $api->getElement();
        }

        $site_id = SiteManager::getSiteId();
        $new_site_id = $orm_object[$api->getSiteIdField()];

        if ($new_site_id != $site_id) {
            return $this->changeSiteIdIfNeed($new_site_id);
        }
        return false;
    }

    /**
     * Восстанавливает исходный текущий сайт
     *
     * @return bool
     */
    protected function restoreSiteId()
    {
        if ($this->before_site_id) {

            $site = new Site($this->before_site_id);
            SiteManager::setCurrentSite($site);
            $this->before_site_id = null;
            return true;
        }

        return false;
    }
}

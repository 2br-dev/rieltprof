<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Model\Orm;

use RS\Config\Loader as ConfigLoader;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;

/**
 * Класс ORM-объектов "E-mail для рассылки".
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $email E-mail
 * @property string $dateof Дата подписки
 * @property integer $confirm Подтверждён?
 * @property string $signature Подпись для E-mail
 * --\--
 */
class Email extends OrmObject
{
    protected static
        $table = 'subscribe_email'; //Имя таблицы в БД

    /**
     * Инициализирует свойства ORM объекта
     *
     * @return void
     */
    public function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'email' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('E-mail'),
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата подписки'),
            ]),
            'confirm' => new Type\Integer([
                'maxLength' => 1,
                'default' => 0,
                'description' => t('Подтверждён?'),
                'checkboxView' => [1, 0]
            ]),
            'signature' => new Type\Varchar([
                'maxLength' => '250',
                'index' => true,
                'description' => t('Подпись для E-mail'),
            ]),
        ]);
        $this->addIndex(['email', 'confirm'], self::INDEX_KEY);
    }


    /**
     * Действия перед записью объекта в БД
     *
     * @param string $flag - insert или update
     * @return void
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $config = ConfigLoader::byModule($this);
            if (!$config['send_confirm_email']) {
                $this['confirm'] = 1;
            }
            //Получим подпись для E-mail
            $this['signature'] = md5("signEmail" . $this['dateof'] . $this['email'] . SiteManager::getSiteId());
        }
    }

    /**
     * Получает url активации подписки
     *
     */
    public function getSubscribeActiveUrl()
    {
        return RouterManager::obj()->getUrl('emailsubscribe-front-window', [
            'Act' => 'activateEmail',
            'signature' => $this['signature'],
        ], true);
    }

    /**
     * Получает url деактивации подписки
     *
     */
    public function getSubscribeDeActivateUrl()
    {
        return RouterManager::obj()->getUrl('emailsubscribe-front-window', [
            'Act' => 'deActivateEmail',
            'email' => $this['email'],
        ], true);
    }
}

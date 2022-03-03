<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Orm;

use \RS\Orm\AbstractObject;
use \RS\Orm\Type;

/**
 * Класс описывает заблокированные для Desktop приложения уведомления в рамках пользователя
 * --/--
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $notice_type Тип Desktop уведомления
 * --\--
 */
class NoticeLock extends AbstractObject
{
    protected static
        $table = 'notice_lock';

    public function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\Integer([
                'description' => t('Пользователь')
            ]),
            'notice_type' => new Type\Varchar([
                'description' => t('Тип Desktop уведомления'),
                'maxLength' => 100
            ])
        ]);

        $this->addIndex(['site_id', 'user_id', 'notice_type'], self::INDEX_UNIQUE);
    }
}
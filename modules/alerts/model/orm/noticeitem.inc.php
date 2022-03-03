<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Orm;
use RS\Orm\Type;

/**
 * Уведомление, которое после сможет получить Desktop программа
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $dateofcreate Дата создания
 * @property string $title Заголовок уведомления
 * @property string $short_message Короткий текст уведомления
 * @property string $full_message Полный текст уведомления
 * @property string $link Ссылка
 * @property string $link_title Подпись к ссылке
 * @property string $notice_type Тип уведомления
 * @property integer $destination_user_id Пользователь-адресат уведомления
 * --\--
 */
class NoticeItem extends \RS\Orm\OrmObject
{
    protected static
        $table = 'notice_item';
    
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'dateofcreate' => new Type\Datetime([
                'description' => t('Дата создания')
            ]),
            'title' => new Type\Varchar([
                'description' => t('Заголовок уведомления')
            ]),
            'short_message' => new Type\Varchar([
                'maxLength' => 400,
                'description' => t('Короткий текст уведомления')
            ]),
            'full_message' => new Type\Text([
                'description' => t('Полный текст уведомления')
            ]),
            'link' => new Type\Varchar([
                'description' => t('Ссылка')
            ]),
            'link_title' => new Type\Varchar([
                'description' => t('Подпись к ссылке')
            ]),
            'notice_type' => new Type\Varchar([
                'description' => t('Тип уведомления')
            ]),
            'destination_user_id' => new Type\Integer([
                'description' => t('Пользователь-адресат уведомления'),
                'hint' => t('0 - означает, что уведомление адресовано всем пользователям'),
                'allowEmpty' => false,
                'visibleApp' => false
            ])
        ]);
        
        $this->addIndex(['destination_user_id', 'notice_type']);
    }
    
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['dateofcreate'] = date('c');
        }
    }
}


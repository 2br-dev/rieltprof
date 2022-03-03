<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект, отвечающий за хранение событий для подсистемы long polling
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $user_id Пользователь-адресат
 * @property string $date_create Дата создания события
 * @property string $event_name Имя события
 * @property string $json_data JSON данные, которые следует передать с событием
 * @property string $expire Время потери актуальности
 * --\--
 */
class LongPollingEvent extends OrmObject
{
    protected static $table = 'long_polling_event';

    protected function _init()
    {
        parent::_init()->append([
            'user_id' => new Type\User([
                'description' => t('Пользователь-адресат')
            ]),
            'date_create' => new Type\Datetime([
                'description' => t('Дата создания события')
            ]),
            'event_name' => new Type\Varchar([
                'description' => t('Имя события')
            ]),
            'json_data' => new Type\Text([
                'description' => t('JSON данные, которые следует передать с событием')
            ]),
            'expire' => new Type\Datetime([
                'description' => t('Время потери актуальности'),
                'hint' => t('Сообщения, утратившие актуальность будут удаляться')
            ])
        ]);

        $this->addIndex(['expire', 'user_id'], self::INDEX_KEY);
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Model\Orm;
use \RS\Orm\Type;
use \Users\Model\Orm\User;

/**
 * ORM объект - Заметка
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Краткий текст заметки
 * @property string $status Статус
 * @property string $message Сообщение
 * @property string $date_of_create Дата создания заметки
 * @property string $date_of_update Дата последнего обновления
 * @property integer $creator_user_id Создатель заметки
 * @property integer $is_private Видна только мне
 * --\--
 */
class Note extends \RS\Orm\OrmObject
{
    const
        STATUS_OPEN = 'open',
        STATUS_INWORK = 'inwork',
        STATUS_CLOSE = 'close';

    protected static
        $table = 'notes_note';
    
    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'description' => t('Краткий текст заметки'),
                'meVisible' => false,
            ]),
            'status' => new Type\Enum(array_keys(self::getStatusList()), [
                'description' => t('Статус'),
                'listFromArray' => [self::getStatusList()],
                'default' => self::STATUS_OPEN,
                'index' => true,
                'radioListView' => true
            ]),
            'message' => new Type\Richtext([
                'description' => t('Сообщение'),
                'meVisible' => false,
            ]),
            'date_of_create' => new Type\Datetime([
                'description' => t('Дата создания заметки'),
                'visible' => false,
                'meVisible' => false,
            ]),
            'date_of_update' => new Type\Datetime([
                'description' => t('Дата последнего обновления'),
                'visible' => false,
                'meVisible' => false,
            ]),
            'creator_user_id' => new Type\User([
                'description' => t('Создатель заметки'),
                'readOnly' => true,
                'index' => true,
                'meVisible' => false,
                'widgetVisible' => false
            ]),
            'is_private' => new Type\Integer([
                'description' => t('Видна только мне'),
                'allowEmpty' => false,
                'hint' => t('Данная заметка будет видна только вам как в виджете, так и в отдельном разделе Заметки'),
                'checkboxView' => [1,0]
            ])
        ]);
    }

    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['date_of_create'] = date('c');
            $this['creator_user_id'] = \RS\Application\Auth::getCurrentUser()->id;
        }

        $this['date_of_update'] = date('c');
    }

    /**
     * Возвращает список возможных статусов заметки
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_OPEN => t('Открыт'),
            self::STATUS_INWORK => t('В работе'),
            self::STATUS_CLOSE => t('Закрыт')
        ];
    }

    /**
     * Возвращает объект пользователя
     *
     * @return User
     */
    function getCreatorUser()
    {
        return new User($this['creator_user_id']);
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Model;

/**
* Класс для организации выборок ORM объекта.
* В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
*/
class NoteApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Note);
    }

    /**
     * Скрывает чужие приватные заметки
     *
     * @param null $user_id
     */
    function initPrivateFilter($user_id = null)
    {
        $user_id = $user_id ?: \RS\Application\Auth::getCurrentUser()->id;

        $this->setFilter([
            'creator_user_id' => $user_id,
            '|is_private' => 0
        ]);
    }
    
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;
use \RS\Orm\Type;

/**
 * ORM объект содержит сведения о прочитанных объектах пользователя
 * --/--
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $entity Тип прочитанного объекта
 * @property integer $entity_id ID прочитанного объекта
 * @property integer $last_id ID последнего прочитанного объекта
 * --\--
 */
class ReadedItem extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'readed_item';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\User([
                'description' => t('Пользователь')
            ]),
            'entity' => new Type\Varchar([
                'description' => t('Тип прочитанного объекта'),
                'maxLength' => 50
            ]),
            'entity_id' => new Type\Integer([
                'description' => t('ID прочитанного объекта')
            ]),
            'last_id' => new Type\Integer([
                'description' => t('ID последнего прочитанного объекта')
            ])
        ]);

        $this->addIndex(['site_id', 'user_id', 'entity', 'entity_id'], self::INDEX_UNIQUE);
    }
}

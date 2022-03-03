<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Comments\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект - голос за комментарий
 * --/--
 * @property string $ip IP пользователя, который оставил комментарий
 * @property integer $comment_id ID комментария
 * @property integer $help Оценка полезности комментария
 * --\--
 */
class Vote extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'comments_votes';
    
    function _init()
    {        
        $this->getPropertyIterator()->append([
            'ip' => new Type\Varchar([
                'description' => t('IP пользователя, который оставил комментарий')
            ]),
            'comment_id' => new Type\Integer([
                'description' => t('ID комментария')
            ]),
            'help' => new Type\Integer([
                'description' => t('Оценка полезности комментария')
            ])
        ]);
        
        $this->addIndex(['ip', 'comment_id'], self::INDEX_UNIQUE);
    }
    
    
}


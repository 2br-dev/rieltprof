<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model;

use Comments\Model\Orm\Comment;
use Main\Model\NoticeSystem\HasMeterInterface;
use RS\Orm\Request;

class Api extends \RS\Module\AbstractModel\EntityList
            implements HasMeterInterface
{
    const
        METER_COMMENT = 'rs-admin-menu-comments',
        COMMENT_TYPE_FOLDER = '/model/commenttype'; //Путь к папке с классами типов комментариев. Относительно корня модуля
        
    protected
        $config, //Конфиг комментариев
        $replace_by_ip = false; //только 1 комментарий от одного ip на один aid

    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule('comments');
        parent::__construct(new \Comments\Model\Orm\Comment,
        [
            'loadOnDelete' => true,
            'multisite' => true,
            'defaultOrder' => 'dateof DESC'
        ]);
    }

    /**
     * Возвращает количество различных оценок у товаров
     *
     * @param string $aid ID объекта связанного с комментарием
     * @param string $comment_type_id
     * @return array
     */
    function getMarkMatrix($aid, $comment_type_id, $max_mark = 5)
    {
        $matrix = Request::make()
            ->select('COUNT(*) as cnt, rate')
            ->from(Comment::_getTable())
            ->where([
                'aid' => $aid,
                'type' => $comment_type_id
            ])
            ->groupby('rate')
            ->exec()->fetchSelected('rate', 'cnt');

        $default = array_fill(1, $max_mark, '0');
        
        $result = $matrix + $default;
        krsort($result, SORT_NUMERIC);
        return $result;
    }

    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi($user_id = null)
    {
        return new \Main\Model\NoticeSystem\MeterApi($this->obj_instance,
            self::METER_COMMENT,
            $this->getSiteContext(),
            $user_id);
    }

    /**
     * Устанавливает, нужно ли заменять комментарии, если они оставлены с одного IP
     * @param bool $bool - Если true, то заменять
     * @return void
     */
    function replaceByIp($bool)
    {
        $this->replace_by_ip = $bool;
        if ($this->config['allow_more_comments']){
            $this->replace_by_ip = false;
        }
    }
    
    /**
     * Возвращает Все существующие типы комментариев
     *
     * @return array|null
     * @throws \RS\Exception вызывается, если присутствует неверный тип комментария
     */
    public static function getTypeList()
    {
        static 
            $result;
        
        if ($result === null) {
            $result = [];
            $event_result = \RS\Event\Manager::fire('comments.gettypes', []);
            foreach($event_result->getResult() as $type_object) {
                if ($type_object instanceof \Comments\Model\IType) {
                    $result['\\'.get_class($type_object)] = $type_object->getTitle();
                } else {
                    throw new \RS\Exception(t('Тип комментариев должен имплементировать интерфейс \Comments\Model\IType'));
                }
            }
        }
        return $result;
	}
    
    
    /**
     * Сохраняет POST с комментарием от текущего пользователя
     * @deprecated Следует использовать метод save, вместо данного метода. Текущий метод будет удален в следующих версиях
     *
     * @param integer $aid - ID объекта, к которому привязывается комментарий
     * @param string $type - Класс комментария
     * @return bool
     */
    function addComment($aid, $type)
    {        
        //Если пользователь не авторизован и модуль каптчи включён, то проверяем капчу
        $captcha_config = \RS\Config\Loader::byModule('kaptcha');
        if (!\RS\Application\Auth::isAuthorize() && $captcha_config['enabled']) {
            $this->getElement()->__captcha->setEnable(true);
        }
        
        $data = [
            'aid' => $aid,
            'type' => $type,
            'moderated' => 0,
            'dateof' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'useful' => 0,
            'help_yes' => 0,
            'help_no' => 0
        ];

        if (\RS\Application\Auth::isAuthorize()) {
            $user = \RS\Application\Auth::getCurrentUser();
            $data['user_id'] = $user['id'];
        }        
        
        if ($this->getElement()->checkData($data)) {
            if ($this->replace_by_ip) {
                //Удаляем коммент с голосами
                \RS\Orm\Request::make()
                    ->delete('A, B')
                    ->from($this->obj_instance)->asAlias('A')
                    ->leftjoin(new Orm\Vote(), 'A.id = B.comment_id', 'B')
                    ->where("A.aid = '#aid' AND A.ip = '#ip'", [
                        'aid' => $data['aid'],
                        'ip' => $data['ip']
                    ])
                    ->exec();
            }
            
            $this->getElement()->__captcha->setEnable(false);
            return $this->save(null, $data);
        } else {
            return false;
        }
    }
    
    /**
    * Добавляет или убавляет рейтинг комментарию
    * 
    * @param integer $comment_id
    * @param mixed $help - yes или no
    */
    function markHelpful($comment_id, $help)
    {
        $help = ($help == 'yes') ? 1 : -1;
        
        $vote = new Orm\Vote();
        $vote['ip'] = $_SERVER['REMOTE_ADDR'];
        $vote['comment_id'] = $comment_id;
        $vote['help'] = $help;
        $vote->replace();
        
        //Пересчитываем количество положительных и отрицательных оценок у комментария
        \RS\Orm\Request::make()
            ->update($this->obj_instance)
            ->set("help_yes = (SELECT COUNT(*) FROM ".$vote->_getTable()." WHERE comment_id = id AND help=1)")
            ->set("help_no = (SELECT COUNT(*) FROM ".$vote->_getTable()." WHERE comment_id = id AND help='-1')")
            ->set("useful = (SELECT SUM(help) FROM ".$vote->_getTable()." WHERE comment_id = id)")
            ->where(['id' => $comment_id])
            ->exec();
    }
    
    /**
    * Подсчитывает рейтинг элемента или товара на основе количества комментариев
    * Подсчёт на основе комментария
    * 
    * @param \RS\Orm\OrmObject $object - объект элемента к которому делали комментарий
    * @param \Comments\Model\Orm\Comment $comment - объект комментария
    */
    function recountItemRatingByComment(\RS\Orm\OrmObject $object, \Comments\Model\Orm\Comment $comment)
    {
        if(\RS\Config\Loader::byModule($this)->need_moderate == 'Y') {
            $moderate = ['moderated' => 1];
        } else {
            $moderate = [];
        }

        $q = \RS\Orm\Request::make()
            ->select('SUM(rate) sum, COUNT(*) cnt')
            ->from($comment)
            ->where(['aid' => $comment['aid'], 'type' => $comment['type']] + $moderate);

        $result = $q->exec()->fetchRow();

        $cnt = isset($result['cnt']) ? $result['cnt'] : 0;
        $rating = round( ($cnt>0) ? (isset($result['sum']) ? $result['sum'] : 0) / $cnt : 0 , 1);
        
        \RS\Orm\Request::make()
            ->update($object)
            ->set([
                'rating' => $rating,
                'comments' => $cnt,
            ])
            ->where(['id' => $comment['aid']])
            ->exec();
    }
    
    
    /**
    * Устанавливаем условие, чтобы загружалась информация о голосованиях.
    */
    function joinVoteInfo()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->queryObj()->leftjoin(new Orm\Vote(), "V.comment_id = {$this->def_table_alias}.id AND V.ip='$ip'", 'V');
    }
    
    /**
    * Устанавливает фильтр по типу комментария
    * 
    * @param Abstracttype $type_object
    * @return Api
    */
    function setCommentTypeFilter(Abstracttype $type_object)
    {
        $this->setFilter('type', '\\'.ltrim(strtolower(get_class($type_object)),'\\'));
        return $this;
    }

    /**
     * Возвращает true, если комментарий с $ip уже присутствует для данного объекта
     *
     * @param integer $aid ID объекта, к которому привязан комментарий
     * @param string|null $ip IP-адрес. Если не задан, то берется текущий
     * @return bool
     */
    function alreadyWrite($aid, $ip = null)
    {   
        if ($ip === null) $ip = $_SERVER['REMOTE_ADDR'];
        
        $count = \RS\Orm\Request::make()->from($this->obj_instance)->where(['aid' => $aid, 'ip' => $ip])->count();
        return ($count > 0);
    }
    
}
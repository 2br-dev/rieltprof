<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model;
use Alerts\Model\Orm\NoticeConfig;
use Alerts\Model\Orm\NoticeLock;

/**
* Функции по работе с уведомлениями
*/
class Api extends \RS\Module\AbstractModel\EntityList
{
    private static $senders;
    
    
    function __construct()
    {
        parent::__construct(new \Alerts\Model\Orm\NoticeConfig(), 
            [
                'multisite' => true
            ]);
    }
    
    /**
    * Возвращает список объектов, согласно заданным раннее условиям
    * 
    * @param integer $page - номер страницы
    * @param integer $page_size - количество элементов на страницу
    * @param string $order - условие сортировки
    * @return array of objects
    */
    public function getList($page = null, $page_size = null, $order = null)
    {
        $classes = self::getAllNoticeClasses();
        //Очистим конфиги для несуществующих классов уведомлений
        if ($classes) {
            \RS\Orm\Request::make()
                ->delete()
                ->from(new NoticeConfig())
                ->where('class NOT IN(' . implode(",", \RS\Helper\Tools::arrayQuote($classes)) . ')')
                ->exec();

            // Создаем все конфиги, если их нет
            foreach ($classes as $one) {
                $obj = self::getNoticeConfig($one);
            }
        }
        
        $list = (array)parent::getList($page, $page_size, $order);
        
        return $list;
    }

    
    /**
    * Возвращает список типов уведомлений, которые возможно отправить в Desktop Приложение
    * 
    * @return array
    */
    public function getDesktopNoticeTypes()
    {
        $result = [];
        foreach(self::getAllNoticeClasses() as $one) {
            if (class_exists($one)) {
                $notice = new $one();
                if ($notice instanceof Types\InterfaceDesktopApp) {
                    $type = $notice->getSelfType();
                    $result[$type] = [
                        'title' => $notice->getDescription(),
                        'type' => $type
                    ];
                }
            }
        }
        
        return $result;
    }

    /**
     * Исключает из списка уведомления, которые запрещены у пользователя
     *
     * @param $notice_list список уведомлений
     * @param $user_id ID пользователя
     * @param $site_id ID сайта
     * @return array Возвращает те уведомления, которые не запрещены у пользователя
     */
    public function excludeLockedUserNotices($notice_list, $user_id, $site_id = null)
    {
        $locks = $this->getUserLockedNotices($user_id, $site_id);
        return array_diff_key($notice_list, array_flip($locks));
    }


    /**
     * @param $user_id
     * @param null $site_id
     * @return array
     * @throws \RS\Exception
     */
    public function getUserLockedNotices($user_id, $site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }

        if (!$site_id) {
            throw new \RS\Exception(t('Не определен текущий сайт'));
        }

        return \RS\Orm\Request::make()
            ->from(new NoticeLock())
            ->where([
                'site_id' => $site_id,
                'user_id' => $user_id
            ])->exec()->fetchSelected(null, 'notice_type');
    }

    /**
     * Возвращает список заблокированных Desktop уведомлений для пользователя в рамках сайта
     *
     * @param integer $user_id ID пользователя
     * @return array()
     */
    public function getAllLockedUserDesktopNotices($user_id)
    {
        return \RS\Orm\Request::make()
            ->from(new Orm\NoticeLock)
            ->where([
                'user_id' => $user_id
            ])
            ->exec()
            ->fetchSelected('site_id', 'notice_type', true);
    }
    
    /**
    * Возвращает список SMS-провайдеров.
    * 
    * @return array
    */
    public function getSenders()
    {
        if (self::$senders === null) {
            $event_result = \RS\Event\Manager::fire('alerts.getsmssenders', []);
            $list = $event_result->getResult();
            self::$senders = [];
            foreach($list as $sender_object) {
                if (!($sender_object instanceof Sms\AbstractSender)) {
                    throw new \Exception(t('Тип отправки SMS должен быть наследником \Alerts\Model\Sms\AbstractSender'));
                }
                self::$senders[$sender_object->getShortName()] = $sender_object;
            }
        }
        
        return self::$senders;
    }
    
    /**
    * Возвращает список сервисов-отправителей SMS
    * 
    * @return array
    */
    public static function selectSendersList()
    {
        $_this = new self();
        $result = [];
        foreach($_this->getSenders() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }
    
    /**
    * Возвращает объект провайдера по его короткому строковому идентификатору
    * 
    * @param string $name
    * @return Sms\AbstractSender
    */
    public static function getSenderByShortName($name)
    {
        $_this = new self();
        $list = $_this->getSenders();
        return isset($list[$name]) ? $list[$name] : null;
    }    
    
    /**
    * Получить полный список всех уведомлений системы
    *     
    * @return array
    */
    static public function getAllNoticeClasses()
    {
        static
            $classes;

        if ($classes === null) {
            $classes = [];
            foreach(glob(\Setup::$PATH.'/modules/*/model/notice/*.inc.php') as $one){
                $class_name = self::getClassNameByPath($one);
                $classes[] = $class_name;
            }
        }

        return $classes;
    }
    
    /**
    * Возвращает имя класса уведомления по пути к файлу
    * 
    * @param string $file_path
    * @return string
    */
    static public function getClassNameByPath($file_path)
    {
        $file_path = str_replace('\\', '/', $file_path);
        $file_path = str_replace(['.'.\Setup::$CUSTOM_CLASS_EXT, '.'.\Setup::$CLASS_EXT], '', $file_path);
        $parts = explode('/', $file_path);
        $parts = array_slice($parts, count($parts) - 4, 4);
        return '\\'.join('\\', $parts);
    }
    
    /**
    * Возвращает объект NoticeConfig по имени класса уведомления
    * 
    * @param string $class_name
    */
    static public function getNoticeConfig($class_name){
        if($class_name[0] != '\\') $class_name = '\\'.$class_name;
        if(!class_exists($class_name)) throw new \Exception(t('Класс %0 не найден', [$class_name]));
        $notice_config = \Alerts\Model\Orm\NoticeConfig::loadByWhere([
            'site_id' => \RS\Site\Manager::getSiteId(),
            'class' => $class_name
        ]);
        if(!$notice_config['id']){
            $notice_config['enable_email'] = 1;
            $notice_config['enable_sms'] = 1;
            $notice_config['enable_desktop'] = 1;
            $notice_config['class'] = $class_name;
            $notice_config->insert();
        }
        return $notice_config;
    }
    
}
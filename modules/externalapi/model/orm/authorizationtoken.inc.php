<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Orm;
use RS\Orm\Type;
use ExternalApi\Model\Exception as ApiException;
use RS\RemoteApp\AbstractAppType;

/**
 * Таблица содержит авторизационные token'ы
 * --/--
 * @property string $token Авторизационный токен
 * @property integer $user_id ID Пользователя
 * @property string $app_type Класс приложения
 * @property string $ip IP-адрес
 * @property string $dateofcreate Дата создания
 * @property integer $expire Срок истечения авторизационного токена
 * --\--
 */
class AuthorizationToken extends \RS\Orm\OrmObject
{
    protected static 
        $table = 'external_api_token';
        
    protected 
        $app_cache;
        
    function _init()
    {
        $this->getPropertyIterator()->append([
            'token' => new Type\Varchar([
                'description' => t('Авторизационный токен'),
                'checker' => ['ChkEmpty', t('Укажите авторизационный токен')]
            ]),
            'user_id' => new Type\User([
                'description' => t('ID Пользователя'),
                'checker' => ['ChkEmpty', t('Укажите пользователя')]
            ]),
            'app_type' => new Type\Varchar([
                'description' => t('Класс приложения'),
                'checker' => ['ChkEmpty', t('Выберите приложение')],
                'list' => [['\RS\RemoteApp\Manager', 'getAppTypesTitles']]
            ]),
            'ip' => new Type\Varchar([
                'description' => t('IP-адрес')
            ]),
            'dateofcreate' => new Type\Datetime([
                'description' => t('Дата создания')
            ]),
            'expire' => new Type\Integer([
                'description' => t('Срок истечения авторизационного токена'),
                'checker' => ['ChkEmpty', t('Укажите timestamp времени истечения токена')]
            ]),
        ]);
    }
    
    /**
    * Выполняет действие перед записью объекта
    * 
    * @param string $flag
    */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['token'] = $this->generateToken();
        }
    }

    /**
     * Возвращает новый token
     *
     * @return string
     */
    public function generateToken()
    {
        return sha1(uniqid(rand(), true));
    }
    
    /**
    * Возвращает первичный ключ 
    * 
    * @return string
    */
    public function getPrimaryKeyProperty()
    {
        return 'token';
    }

    /**
     * Возвращает объект приложения, для которого выдан token
     *
     * @return AbstractAppType
     * @throws ApiException
     */
    public function getApp()
    {
        if ($this->app_cache === null) {
            $this->app_cache = \RS\RemoteApp\Manager::getAppByType($this['app_type']);
            if (!$this->app_cache) {
                throw new ApiException(t('Приложение %0 не найдено', [$this['app_type']]), ApiException::ERROR_INSIDE);
            }
            
            if (!($this->app_cache instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
                throw new ApiException(t('Приложение %0 не поддерживает работу с API', [$this['app_type']]), ApiException::ERROR_INSIDE);
            }
            
            $this->app_cache->setToken($this);
        }
        
        return $this->app_cache;
    }
    
    /**
    * Возвращает пользователя, для которого выдан token
    * 
    * @return \Users\Model\Orm\User
    */
    public function getUser()
    {
        return new \Users\Model\Orm\User($this['user_id']);
    }
}

<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

/**
* Общие исключения, связанные с внешним API
*/
class Exception extends AbstractException
{
    const
        /**
        * Внутренняя ошибка
        */
        ERROR_INSIDE = 'inside',
                
        /**
        * Метод API не найден
        */
        ERROR_METHOD_NOT_FOUND = 'method_not_found',
        
        /**
        * Недостаточно прав для вызова метода.
        * Возможно в приложении client_id не установлены права на вызов данного метода
        */
        ERROR_METHOD_ACCESS_DENIED = 'method_access_denied',        
        
        /**
        * Неверный логин или пароль 
        * или превышен лимит попыток авторизации с одного IP
        * или пользователь заблокирован по IP
        */
        ERROR_BAD_AUTHORIZATION = 'bad_authorization',        
        
        /**
        * Неизвестный client_id или client_secret.
        * client_id и client_secret создаются в классе приложения, 
        * потомке от ExternalApi\Model\App\AbstractAppType
        */        
        ERROR_BAD_CLIENT_SECRET_OR_ID = 'bad_client_secret_or_id',

        /**
        * Доступ к приложению запрещен. Проверьте, состоит ли пользователь 
        * в группе, которая требуется приложению.
        */
        ERROR_APP_ACCESS_DENIED = 'app_access_denied',

        /**
         * Неверные параметры, переданные в метод.
         * Почитайте справку к методу /api/help
         */
        ERROR_WRITE_ERROR = 'write_error',
        
        /**
        * Неверные параметры, переданные в метод.
        * Почитайте справку к методу /api/help
        */
        ERROR_WRONG_PARAMS = 'wrong_params',
        
        /**
        * Неверное значение параметра, переданного для вызова метода API.
        * Почитайте справку к методу /api/help
        */
        ERROR_WRONG_PARAM_VALUE = 'wrong_param_value',
                
        /**
        * Запрашиваемый объект не найден
        */
        ERROR_OBJECT_NOT_FOUND = 'object_not_found';
}

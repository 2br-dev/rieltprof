<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Model;

use RS\Module\AbstractModel\BaseModel;

/**
 * Класс обеспечивает методы для сверки подписи в момент внешнего запроса на установку дополнения
 */
class SignApi extends BaseModel
{

    /**
     * Валидирует запрос от сервера ReadyScript. Проверяет подпись входящих данных
     *
     * @param $_files - Если false, то не будет проверяться
     * @param $_post
     * @return bool
     */
    public function checkUploadRequest($_files, $_post)
    {
        $public_key  = file_get_contents(__DIR__.'/rs_public.pem');

        if(!isset($_post['signature'])){
            return $this->addError(t('Отсутствует подпись запроса'));
        }
        
        if (!function_exists('openssl_verify')) {
            return $this->addError(t('Внешняя установка невозможна. Отсутствует поддержка openSSL на сервере магазина'));
        }

        if ($_files !== false) {
            if (!isset($_files['file'])) {
                return $this->addError(t('Файл не отправлен'));
            }

            $file_contents = file_get_contents($_files['file']['tmp_name']);
        } else {
            $file_contents = '';
        }

        $signature = base64_decode($_post['signature']);
        unset($_post['signature']);

        //Проверяем подпись сервера ReadyScript
        $sign_result = openssl_verify($_SERVER['HTTP_HOST'].$file_contents.http_build_query($_post), $signature, $public_key);

        if($sign_result === 0){
            return $this->addError(t('Неверная подпись запроса'));
        }

        if($sign_result === -1){
            return $this->addError(t('Ошибка проверки подписи')); //echo openssl_error_string();
        }
        
        //Сверяем ключи установленные в системе и ключи инициатора запроса на установку
        if (defined('CLOUD_UNIQ')) {
            $check_string = CLOUD_UNIQ;
        } else {
            $main_license = '';
            __GET_LICENSE_LIST($main_license);
            if ($main_license) $check_string = sha1(str_replace('-', '', $main_license['license_key']));
        }
        
        if (empty($check_string) || strpos($_post['license_check_str'], $check_string) === false ) {
            return $this->addError(t('Ошибка проверки лицензии'));
        }

        if($sign_result === 1){
            // Подпись верна
            return true;
        }
        return false;
    }
}
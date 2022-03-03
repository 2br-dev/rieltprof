<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Model;
 
/**
* API функции для работы с магазинами сети
*/
class Api extends \RS\Module\AbstractModel\EntityList
{
    const EASY_CAPTCHA_SESSION_KEY = 'easy_captcha_key';

    function __construct()
    {
        parent::__construct(new Orm\Email(), [
            'name_field' => 'email',
            'id_field' => 'id',
            'multisite' => true,
            'defaultOrder' => 'dateof'
        ]);
    }
    
    /**
    * Проверяет присутствует ли E-mail в списке на подписку
    * 
    * @param string $email - E-mail для подписки 
    * @param integer $confirmed - 1 - подтверждённые 0 - не подтверждённые
    */ 
    function checkEmailPresent($email, $confirmed = 1)
    {
       $this->clearFilter(); 
       $this->setFilter('email', $email);
       $this->setFilter('confirm', $confirmed);
       $this->setFilter('site_id', \RS\Site\Manager::getSiteId());
       $emailuser = $this->getFirst(); 
       
       return $emailuser;
    }
    
    
    /**
    * Отправляет уведомление на E-mail о подписке на новости
    * 
    * @param string $email - E-mail для подписки
    */
    function sendSubscribeToEmail($email)
    {
        //Проверим существует ли такой E-mail, если он не подтверждён
        if (($emailuser = $this->checkEmailPresent($email, 0))==null) { //Создадим такого пользователя
           $emailuser = new \EmailSubscribe\Model\Orm\Email();
           $emailuser['email']  = $email;
           $emailuser['dateof'] = date('Y-m-d H:i:s');
           $emailuser->insert();
        }
        
        $notice = new \EmailSubscribe\Model\Notice\UserSubscribe();
        $notice->init([
            'email' => $email,
            'user' => $emailuser,
            'signature' => $emailuser['signature'],
        ]);
        //Отправляем уведомление
        \Alerts\Model\Manager::send($notice);
    }
    
    
    /**
    * Активирует E-mail по подписке в письме
    * 
    * @param string $signature - подпись к E-mail
    */
    function activateEmailBySignature($signature)
    {
        $this->setFilter('signature', $signature);
        $this->setFilter('confirm', 0);
        
        $emailuser = $this->getFirst();
        
        if ($emailuser){
           $emailuser['confirm'] = 1;
           $emailuser->update();
           return true; 
        }
        return false; 
    }
    
    /**
    * Деактивирует E-mail по подписке в письме
    * 
    * @param string $signature - подпись к E-mail
    */
    function deactivateEmailBySignature($signature)
    {
        $this->setFilter('signature', $signature);
        $this->setFilter('confirm', 1);
        
        $emailuser = $this->getFirst();
        
        if ($emailuser){
           $emailuser->delete(); 
           return true; 
        }
        return false; 
    }
    
    /**
    * Деактивирует E-mail по подписке в письме
    * 
    * @param string $email - E-mail
    */
    function deactivateEmailByEmail($email)
    {
        $this->setFilter('email', $email);
        
        $emailuser = $this->getFirst();
        
        if ($emailuser){
           $emailuser->delete(); 
           return true; 
        }
        return false; 
    }

    /**
     * Возвращает HTML код для скрытого поля проверки капчи.
     * Элементарная капча, защищает только от самых простых ботов.
     *
     * @param null $code Код, который будет сгенерирован
     * @return string
     */
    function getEasyCaptchaInput(&$code = null)
    {
        $code = rand(1, 50000);
        $_SESSION[self::EASY_CAPTCHA_SESSION_KEY] = $code;
        $pow_code = pow($code,2);
        $uniq_id = uniqid();

        return "<input type=\"hidden\" name=\"code\" value=\"\" id=\"{$uniq_id}\"/>".
                "<script>document.getElementById('{$uniq_id}').value = Math.sqrt({$pow_code});</script>";
    }

    /**
     * Возвращает true, если $code корректный
     *
     * @return bool
     */
    function checkEasyCaptcha($code)
    {
        return isset($_SESSION[self::EASY_CAPTCHA_SESSION_KEY])
            && $_SESSION[self::EASY_CAPTCHA_SESSION_KEY] === $code;
    }
}
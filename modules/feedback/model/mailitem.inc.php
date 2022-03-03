<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Model;

use RS\Event\Exception as EventException;

/**
* Класс отправки на почту письма с формой
*/
class MailItem
{
    private
        $table_data,  //Значения для таблицы уже преобразованные
        $php_mailer,  //Объект класса отправки PHPMailer
        $form,        //Объект формы
        $mail,        //Массив значений формы
        $result_id;   //ID результата заполнения формы
    
    public 
        $host_title,  //Название сайта
        $host;        //Хост сайта

    /**
     * Конструктор класса отправки письма формы
     *
     * @param \Feedback\Model\Orm\FormItem $form - Объект формы которую посылаем
     * @param array $mail - массив с значениями полей (ключи - field, value)
     * @param $result_id
     * @throws EventException
     */
    function __construct($form, $mail, $result_id)
    {
        $event_result = \RS\Event\Manager::fire('feedback.mailitem.create', [
            'values' => $mail
        ]);

        list($mail) = $event_result->extract();

        $this->form       = $form;
        $this->mail       = $mail;
        $this->result_id  = $result_id;
        
        $this->host_title = \RS\Site\Manager::getSite()->full_title; //Название сайта полное
        $this->host       = "http://".\RS\Http\Request::commonInstance()->server('HTTP_HOST', TYPE_STRING);  //Хост сайта
        $this->template   = $form['template']; //Установим шаблон
        $system_config    = \RS\Config\Loader::getSystemConfig();
        
        $this->php_mailer           = new \RS\Helper\Mailer();
        $this->php_mailer->Subject  = $form['subject']." ({$this->result_id})";                     //Тема письма
        $adress                     = $form->getAddressArray();              //Массив адресатов для получения
        foreach($adress as $email_adress){
            $this->php_mailer->AddAddress(trim($email_adress));
        }
        
        $this->php_mailer->IsHTML(true);
        
        foreach($mail as $field)  {
            $method = 'field'.$field['field']['show_type'];
            if (!method_exists($this, $method)) {
                $method = 'fieldDefault';
            }
            $this->$method($field);
        }
    }
    
    /**
    * Обработка текстового поля перед отправкой
    * 
    * @param array $field_data - массив с объектом поля и его значением
    */
    function fieldDefault($field_data)
    {
        $this->table_data[] = $field_data;
    }
    
    
    /**
    * Обработка поля у которого тип Email  перед отправкой
    * 
    * @param array $field_data - массив с объектом поля и его значением
    */
    function fieldEmail($field_data)
    {
        $this->php_mailer->AddReplyTo($field_data['value']);
        $this->table_data[] = $field_data;
    }

    /**
    * Обработка поля у которого тип ФАЙЛ перед отправкой
    *
    * @param array $field_data - массив с объектом поля и его значением
    */
    function fieldFile($field_data)
    {
        if ($field_data['value']['name']){
           $fileinfo = pathinfo($field_data['value']['name']); //Получим информацию о файле
           $this->php_mailer->AddAttachment($field_data['value']['tmp_name'],$field_data['field']['title'].".".$fileinfo['extension']); 
        }
    }
    /**
    * Возвращает поля уже подготовленные
    * 
    * @return array
    */ 
    function getFields()
    {
       return $this->table_data; 
    }
    
    /**
    * Возвращает объект формы
    * 
    * @return \Feedback\Model\Orm\FormItem
    */ 
    function getForm()
    {
       return $this->form; 
    }

    /**
    * Непосредственно сама отправка
    * 
    * @return boolean
    */
    function send()
    {
        $view = new \RS\View\Engine();
        $view->assign([
            'mail'   => $this,
        ]);
        $body = $view->fetch($this->template);
        
        $this->php_mailer->Body = $body;
        return $this->php_mailer->send();
    }
}

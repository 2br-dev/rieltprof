<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Front;

use Feedback\Model\FormApi;
use RS\Controller\Front;

class Form extends Front
{
    function actionIndex()
    {
        $form_id = $this->url->request('form_id', TYPE_INTEGER, 0);
        $form    = new \Feedback\Model\Orm\FormItem($form_id);

        if ($form['id']) {//Если результат пришёл и форма такая существует
            if ($form['use_csrf_protection'] && !$this->url->checkCsrf()){
                return $this->result->setSuccess(false)->addEMessage(t('Ошибка CSRF'));
            }

            $api = new FormApi();
            $form['use_captcha'] = 0; //Отменим проверки, т.к. каптчу не возможно стилизовать
            if ($api->send($form, $this->url)) { //OK
                $success_text = $form['successMessage'] ? $form['successMessage'] : t('Благодарим Вас за обращение к нам. Мы ответим вам при первой же возможности.');
                return $this->result->setSuccess(true)->addSection(['success_text' => $success_text]);
            } else { //Если есть ошибки
                $errors  = $api->getErrors();
                if (!empty($errors)){
                    foreach ($errors as $error){
                        $this->result->addEMessage($error);
                    }
                }
                return $this->result->setSuccess(false);
            }
        }

        return $this->result->setSuccess(false)->addEMessage(t('Форма не найдена'));
    }
}


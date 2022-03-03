<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Model\Source;

/**
* Источник получателей рассылки - зарегистрированные пользователи
*/
class EmailSubscribe extends \MailSender\Model\Source\AbstractSource
{
    /**
    * Возвращает название источника получателей
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Подписавшиеся на новости пользователи');
    }
    
    /**
    * Возвращает описание источника получателей
    * 
    * @return string
    */
    public function getDescription()
    {
        return t('Возвращает список Email адресов, подписавшихся пользователей. Кроме Email никаких данных о пользователе больше нет.');
    }
    
    /**
    * Возвращает список объектов получателей
    * 
    * @return MailSender\Model\Orm\MailRecipient[]
    */
    public function getRecipients()
    {
        $offset = 0;
        $page_size = 100;
        $q = \RS\Orm\Request::make()
            ->from(new \EmailSubscribe\Model\Orm\Email())
            ->where([
                'site_id' => $this->template['site_id'],
                'confirm' => 1
            ])
            ->limit($page_size);

        $recipients = [];
        while($users = $q->offset($offset)->objects()) {
            foreach($users as $user) {
                $recipient = new \MailSender\Model\Orm\MailRecipient();
                $recipient->source_class = get_class($this);
                $recipient->email = $user['email'];

                $recipients[$recipient->email] = $recipient;
            }
            $offset += $page_size;
        }
        return $recipients;
    }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Model\Orm;

use Alerts\Model\Manager as AlertsManager;
use Feedback\Model\FormApi;
use Feedback\Model\Notice\NewResultAdmin;
use RS\Orm\Exception as OrmException;
use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * Класс ORM-объектов "Формы отправки". Объект результата отправки формы
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $form_id Форма
 * @property string $title Название
 * @property string $dateof Дата отправки
 * @property string $status Статус
 * @property string $ip IP Пользователя
 * @property string $sending_url URL с которого отравлена форма
 * @property string $stext Содержимое результата формы
 * @property string $answer Ответ
 * @property integer $send_answer Отправить ответ на E-mail пользователя
 * --\--
 */
class ResultItem extends OrmObject
{
    protected static $table = 'connect_form_result'; //Имя таблицы в БД

    /** @var FormItem */
    protected $form;  //Текущая форма

    /**
     * Инициализирует свойства ORM объекта
     *
     * @return void
     */
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(), //Создаем поле, которое будет содержать id текущего сайта
            'form_id' => new Type\Integer([
                'description' => t('Форма'),
                'List' => [['\Feedback\Model\FormApi', 'staticSelectList']],
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Название'),
                'meVisible' => false,
            ]),
            'dateof' => new Type\Datetime([
                'maxLength' => '150',
                'description' => t('Дата отправки'),
            ]),
            'status' => new Type\Enum(['new', 'viewed'], [
                'maxLength' => '1',
                'allowEmpty' => false,
                'default' => 'new',
                'listFromArray' => [[
                    'new' => t('Новое'),
                    'viewed' => t('Просморен'),
                ]],
                'description' => t('Статус'),
            ]),
            'ip' => new Type\Varchar([
                'description' => t('IP Пользователя'),
                'maxLength' => '150',
                'meVisible' => false,
            ]),
            'sending_url' => new Type\Varchar([
                'description' => t('URL с которого отравлена форма'),
                'meVisible' => false,
            ]),
            'stext' => new Type\Text([
                'description' => t('Содержимое результата формы'),
                'template' => 'form/field/stext.tpl',
                'meVisible' => false,
            ]),
            'answer' => new Type\Text([
                'description' => t('Ответ'),
                'meVisible' => false,
            ]),
            'send_answer' => new Type\Integer([
                'maxLength' => '1',
                'meVisible' => false,
                'checkBoxView' => [1, 0],
                'runtime' => true,
                'description' => t('Отправить ответ на E-mail пользователя')
            ]),
        ]);
    }

    /**
     * Возращает масстив сохранённых данных рассериализованными
     *
     * @return array|mixed
     */
    function tableDataUnserialized()
    {
        return unserialize($this['stext']);
    }

    /**
     * Событие срабатывает перед записью объекта в БД
     *
     * @param mixed $flag - Флаг вставки обновления, либо удаления
     * @return void
     * @throws OrmException
     */
    function beforeWrite($flag)
    {
        if ($flag == self::UPDATE_FLAG) { //Флаг обновления
            //Проверим можем ли мы отослать ответ пользователю
            if ($this->hasEmail() && $this['send_answer']) { //Если есть поле с E-mail и стоит галка отсылать на E-mail
                $email = $this->getFirstEmail();
                if (!empty($email) && !empty($this['answer'])) {  //Если значение существует, то отправим на E-mail этому пользователю уведомление с ответом
                    $form_api = new FormApi();
                    $form_api->sendAnswer($email, $this['answer']);
                }
            }
        }
    }

    /**
     * Событие срабатывает после записи объекта в БД
     *
     * @param mixed $flag - Флаг вставки обновления, либо удаления
     * @return void
     */
    function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) { //Флаг вставки
            $this['title'] = t("Сообщение №") . $this['id']; //Обновим название
            $this->update();

            $notice = new NewResultAdmin();
            $notice->init($this);
            AlertsManager::send($notice);
        }
    }

    /**
     * Получает значение первого поля E-mail
     *
     * @return string
     */
    function getFirstEmail()
    {
        $data_values = $this->tableDataUnserialized();
        foreach ($data_values as $data) {
            if ($data['field']['show_type'] == $data['field']::SHOW_TYPE_EMAIL) {
                return $data['value'];
            }
        }
        return '';
    }

    /**
     * Возращает true, если есть хоть одно поле с E-mail
     *
     * @return boolean
     * @throws OrmException
     */
    function hasEmail()
    {
        if (!isset($this->form)) { //Если форма ещё не загружена
            $this->getFormObject();
        }
        $fields = $this->form->getFields();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                /** @var FormFieldItem */
                if ($field['show_type'] == $field::SHOW_TYPE_EMAIL) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Получает объект формы для текущего результата и возвращает себя
     *
     * @return FormItem
     */
    function getFormObject()
    {
        if ($this->form === null) {
            $this->form = new FormItem($this['form_id']);
        }
        return $this->form;
    }
}

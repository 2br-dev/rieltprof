<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Notice as CatalogNotice;
use Catalog\Model\OneClickItemApi;
use Feedback\Model\Orm\FormItem;
use RS\Application\Auth;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Users\Model\Api as UserApi;
use Users\Model\Orm\User;

/**
 * Класс ORM-объектов "Добавить в 1 клик". Объект добавить в 1 клик
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $user_fio Ф.И.О. пользователя
 * @property string $user_phone Телефон пользователя
 * @property string $title Номер сообщения
 * @property string $dateof Дата отправки
 * @property string $status Статус
 * @property string $ip IP Пользователя
 * @property string $currency Трехсимвольный идентификатор валюты на момент покупки
 * @property string $sext_fields Дополнительными сведения
 * @property string $stext Cведения о товарах
 * --\--
 */
class OneClickItem extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_VIEWED = 'viewed';
    const STATUS_CANCELLED = 'cancelled';

    protected static $table = 'one_click'; //Имя таблицы в БД

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
            'user_id' => new Type\Bigint([
                'description' => t('Пользователь'),
                'visible' => false
            ]),
            'user_fio' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Ф.И.О. пользователя')
            ]),
            'user_phone' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Телефон пользователя')
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Номер сообщения')
            ]),
            'dateof' => new Type\Datetime([
                'maxLength' => '150',
                'description' => t('Дата отправки')
            ]),
            'status' => new Type\Enum([
                self::STATUS_NEW,
                self::STATUS_VIEWED,
                self::STATUS_CANCELLED
            ], [
                'maxLength' => '1',
                'allowEmpty' => false,
                'default' => self::STATUS_NEW,
                'listFromArray' => [[
                    self::STATUS_NEW => t('Новое'),
                    self::STATUS_VIEWED => t('Закрыт'),
                    self::STATUS_CANCELLED => t('Отменен')
                ]],
                'description' => t('Статус')
            ]),
            'ip' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('IP Пользователя')
            ]),
            'currency' => new Type\Varchar([
                'maxLength' => '5',
                'description' => t('Трехсимвольный идентификатор валюты на момент покупки')
            ]),
            'sext_fields' => new Type\Text([
                'description' => t('Дополнительными сведения'),
                'Template' => 'form/field/sext_fields.tpl'
            ]),
            'stext' => new Type\Text([
                'description' => t('Cведения о товарах'),
                'Template' => 'form/field/stext.tpl'
            ]),
            'kaptcha' => new Type\Captcha([
                'enable' => false,
                'context' => '',
            ]),
        ]);
    }

    /**
     * Возращает масстив сохранённых данных рассериализованными
     *
     * @param string $field - поле для десеарелизации
     * @return mixed
     */
    function tableDataUnserialized($field = 'stext')
    {
        return @unserialize($this[$field]);
    }

    /**
     * Возвращает пользователя, оформившего заказ
     *
     * @return User
     */
    function getUser()
    {
        if ($this['user_id'] > 0) {
            return new User($this['user_id']);
        }
        $user = new User();
        $fio = explode(" ", $this['user_fio']);
        if (isset($fio[0])) {
            $user['surname'] = $fio[0];
        }
        if (isset($fio[1])) {
            $user['name'] = $fio[1];
        }
        if (isset($fio[2])) {
            $user['midname'] = $fio[2];
        }
        $user['phone'] = $this['user_phone'];
        return $user;
    }

    /**
     * Событие срабатывает перед записью объекта в БД
     *
     * @param string $flag - Флаг вставки обновления, либо удаления insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) { //Флаг вставки
            if (empty($this['stext']) && !empty($this['products'])) {
                $api = new OneClickItemApi();
                $this['stext'] = $api->prepareSerializeTextFromProducts($this['products']);
            }
            $this['user_id'] = Auth::getCurrentUser()->id;
        }

        if (empty($this['currency'])) {
            //Если Валюта не задана, то укажем базовую
            $default_currency = CurrencyApi::getDefaultCurrency();
            $this['currency'] = $default_currency['title'];
        }

        $this['user_phone'] = UserApi::normalizePhoneNumber($this['user_phone']);
    }

    /**
     * Событие срабатывает после записи объекта в БД
     *
     * @param string $flag - Флаг вставки обновления, либо удаления
     * @return void
     */
    function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) { //Флаг вставки

            $notice = new CatalogNotice\OneClickAdmin();
            $notice->init($this);
            //Отсылаем письмо администратору
            AlertsManager::send($notice);

            $this['title'] = t("Покупка №") . $this['id'] . " " . $this['user_fio'] . " (" . $this['user_phone'] . ")"; //Обновим название
            $this->update();
        }
    }
}

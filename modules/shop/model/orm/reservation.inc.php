<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use Users\Model\Api as UserApi;
use \RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * Предварительный заказ товара
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $product_id ID товара
 * @property string $product_barcode Артикул товара
 * @property string $product_title Название товара
 * @property string $offer Название комплектации товара
 * @property integer $offer_id Комплектация товара
 * @property string $currency Валюта на момент оформления заявки
 * @property string $multioffer Многомерная комплектация товара
 * @property array $multioffers 
 * @property float $amount Количество
 * @property string $phone Телефон пользователя
 * @property string $email E-mail пользователя
 * @property string $is_notify Уведомлять о поступлении на склад
 * @property string $dateof Дата заказа
 * @property integer $user_id ID пользователя
 * @property string $status Статус
 * @property string $comment Комментарий администратора
 * --\--
 */
class Reservation extends \RS\Orm\OrmObject
{
    const
        STATUS_OPEN = 'open',
        STATUS_CLOSE = 'close';
    
    protected static
        $table = 'product_reservation';
        
    protected
        $product;
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'product_id' => new Type\Integer([
                'description' => t('ID товара'),
                'checker' => ['chkEmpty', t('Не задан товар')],
                'meVisible' => false
            ]),
            'product_barcode' => new Type\Varchar([
                'description' => t('Артикул товара'),
                'meVisible' => false
            ]),
            'product_title' => new Type\Varchar([
                'description' => t('Название товара'),
                'meVisible' => false
            ]),
            'offer' => new Type\Varchar([
                'description' => t('Название комплектации товара'), 
                'visible' => false
            ]),
            'offer_id' => new Type\Integer([
                'description' => t('Комплектация товара'), 
                'Template' => 'form/reservationfield/offer_id.tpl',
                'meVisible' => false
            ]),
            'currency' => new Type\Varchar([
                'description' => t('Валюта на момент оформления заявки')
            ]),
            'multioffer' => new Type\Varchar([
                'description' => t('Многомерная комплектация товара'),
                'Template' => 'form/reservationfield/multioffer.tpl',
                'meVisible' => false
            ]),
            'multioffers' => new Type\ArrayList([
                'descirption' => t('Многомерная комплектация товара - массив'),
                'visible' => false
            ]),
            'amount' => new Type\Decimal([
                'description' => t('Количество'),
                'maxLength' => 11,
                'decimal' => 3,
                'meVisible' => false
            ]),
            'phone' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Телефон пользователя')
            ]),
            'email' => new Type\Varchar([
                'description' => t('E-mail пользователя'),
                'checker' => [[__CLASS__, 'checkContacts'], t('Укажите телефон или E-mail')]
            ]),
            'is_notify' => new Type\Enum(['1', '0'], [
                'allowempty' => false,
                'default' => '0',
                'description' => t('Уведомлять о поступлении на склад'),
                'listFromArray' => [[
                    '1' => t('Уведомлять'),
                    '0' => t('Не уведомлять')
                ]]
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата заказа')
            ]),
            'user_id' => new Type\User([
                'description' => t('ID пользователя'),
                'meVisible' => false
            ]),
            'kaptcha' => new Type\Captcha([
                'description' => t('Защитный код'),
                'enable' => false,
                'context' => '',
            ]),
            'status' => new Type\Enum([self::STATUS_OPEN, self::STATUS_CLOSE], [
                'allowempty' => false,
                'description' => t('Статус'),
                'listFromArray' => [[
                    self::STATUS_OPEN => t('открыт'),
                    self::STATUS_CLOSE => t('закрыт')
                ]]
            ]),
            'comment' => new Type\Text([
                'description' => t('Комментарий администратора')
            ])
        ]);
    }    
    
    public static function checkContacts($_this, $value, $error_text)
    {
        if ($_this['phone'] || $_this['email']) return true;
        return $error_text;
    }

    /**
     * Действия перед записью в БД
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['dateof'] = date('c');
            $this['user_id'] = \RS\Application\Auth::getCurrentUser()->id;
        }
        
        $this['multioffer'] = serialize($this['multioffers']);
        $this['product_title'] = $this->getProduct()->title;

        $this['phone'] = UserApi::normalizePhoneNumber($this['phone']);
    }

    /**
     * Действия после записи в БД
     *
     * @param string $flag - insert или update
     * @return void
     */
    function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $notice = new \Shop\Model\Notice\Reservation;
            $notice->init($this);
            \Alerts\Model\Manager::send($notice); 
        }        
    }
    
    function afterObjectLoad()
    {
        $this['multioffers'] = @unserialize($this['multioffer']);
        
        // Приведение типов
        $this['amount'] = (float)$this['amount'];
    }
    
    function getProduct()
    {
        if (empty($this->product)){
          $this->product =  new \Catalog\Model\Orm\Product($this['product_id']);
        }   
        return $this->product;
    }
    
    /**
    * Возращает массив мульти комплектаций
    * 
    */
    function getArrayMultiOffer()
    {
       $arr = [];
       if (!empty($this['multioffers'])){
          foreach ($this['multioffers'] as $offer){
              $parse = explode(":",$offer);
              $arr[trim($parse[0])] = trim($parse[1]);
          } 
       }
       elseif ($this['offer_id'] > 0) {
           $offer = $this->getOffer();
           $arr = $offer['propsdata_arr'];
       }
       
       return $arr;
    }

    /**
     * Возвращает комплектацию товара
     *
     * @return \Catalog\Model\Orm\Offer
     */
    function getOffer()
    {
        $offer = new \Catalog\Model\Orm\Offer($this['offer_id']);
        return $offer;
    }

    /**
     * Возвращает объект пользователя, для которого оформлен предзаказ
     *
     * @return User
     */
    function getUser()
    {
        return new User($this['user_id']);
    }
}

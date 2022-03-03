<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\FilesType;

/**
* Тип файлов - "Файлы для товаров"
*/
class CatalogProduct extends AbstractType
{
    const
        ACCESS_TYPE_AFTERPAY = 'afterpay',
        ACCESS_TYPE_AUTHORIZED = 'authorized';
    
    /**
    * Возвращает название типа 
    * @return string
    */
    function getTitle()
    {
        return t('Файлы для товаров');
    }
    
    /**
    * Возвращает массив с возможными уровнями доступа
    * [id => пояснение, id => пояснение, ...]
    * 
    * @return []
    */
    public static function getAccessTypes()
    {
        $access_list = parent::getAccessTypes();
        
        $access_list += [
            static::ACCESS_TYPE_AUTHORIZED => [
                'title' => t('требует авторизации'),
                'hint' => t('Виден и доступен для скачивания только авторизованным пользователям')
            ]
        ];
        //В Витрине недоступен данный тип доступа к файлу
        if (\Setup::$SCRIPT_TYPE != 'Shop.Base') {
            $access_list += [
                static::ACCESS_TYPE_AFTERPAY => [
                    'title' => t('доступен после оплаты'),
                    'hint' => t('Ссылка на скачивание данного файла будет отправлена покупателю по почте, а также будет доступна в разделе личного кабинета - Мои заказы')
                ]
            ];
        }
        return $access_list;
    }    
    
    /**
    * Проверяет права на скачивание файла
    * Возвращает текст ошибки или false - в случае отсутствия ошибки
    * 
    * @param \Files\Model\Orm\File $file
    * @return string | false
    */
    function checkDownloadRightErrors(\Files\Model\Orm\File $file)
    {
        //Если файл имеет уровень доступа - "доступен после оплаты", требуем авторизацию и проверяем права
        if ($file['access'] == static::ACCESS_TYPE_AFTERPAY) {
            //Проверяем, купил ли пользователь файл
            $user = \RS\Application\Auth::getCurrentUser();
            $is_purchased = \RS\Orm\Request::make()
                ->from(new \Shop\Model\Orm\Order(), 'O')
                ->join(new \Shop\Model\Orm\OrderItem(), 'I.order_id = O.id', 'I')
                ->where([
                    'O.user_id' => $user['id'],
                    'O.is_payed' => 1,
                    'I.type' => \Shop\Model\Cart::TYPE_PRODUCT,
                    'I.entity_id' => $file['link_id']
                ])->count();
            
            if (!$is_purchased)
                return t('Доступ к файлу запрещен');
        }
        return false;
    }            
    
    /**
    * Возвращает идентификатор группы, если для скачивания $access требуется авторизация
    * 
    * @param \Files\Model\Orm\File $file - объект файла
    * @return string|false
    */
    function getNeedGroupForDownload(\Files\Model\Orm\File $file)
    {
        if (in_array($file['access'], [static::ACCESS_TYPE_AFTERPAY, static::ACCESS_TYPE_AUTHORIZED])) {
            return \Users\Model\Orm\UserGroup::GROUP_CLIENT;
        }
        return false;
    }
}

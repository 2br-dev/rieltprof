<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\FilesType;

/**
* Тип файлов - "Файлы для заказов"
*/
class ShopOrder extends AbstractType
{
    const
        ACCESS_TYPE_AFTERPAY = 'afterpay';
    
    /**
    * Возвращает название типа 
    * @return string
    */
    function getTitle()
    {
        return t('Файлы для заказов');
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
        
        //В Витрине недоступен данный тип доступа к файлу
        if (\Setup::$SCRIPT_TYPE != 'Shop.Base') {
            $access_list += [
                'afterpay' => [
                    'title' => t('доступен после оплаты'),
                    'hint' => t('Ссылка на скачивание данного файла будет доступна в разделе личного кабинета - Мои заказы')
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
        if ($file['access'] == self::ACCESS_TYPE_AFTERPAY) {
            //Проверяем, купил ли пользователь файл
            $user = \RS\Application\Auth::getCurrentUser();
            $is_purchased = \RS\Orm\Request::make()
                ->from(new \Shop\Model\Orm\Order())
                ->where([
                    'id' => $file['link_id'],
                    'user_id' => $user['id'],
                    'is_payed' => 1,
                ])->count();
            
            if (!$is_purchased)
                return t('Доступ к файлу запрещен');
        }
        return false;
    }            
    
    /**
    * Возвращает true, если для скачивания $access требуется авторизация
    * 
    * @param string $access - уровень доступа
    * @return bool
    */
    function getNeedGroupForDownload(\Files\Model\Orm\File $file)
    {
        return $file['access'] == self::ACCESS_TYPE_AFTERPAY ? \Users\Model\Orm\UserGroup::GROUP_CLIENT : false;
    }
}

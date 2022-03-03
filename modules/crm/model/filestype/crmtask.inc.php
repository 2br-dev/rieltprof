<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilesType;
use Crm\Config\ModuleRights;
use RS\AccessControl\Rights;

/**
 * Тип файлов - "Файлы для сделок"
 */
class CrmTask extends \Files\Model\FilesType\AbstractType
{
    /**
     * Возвращает название типа
     *
     * @return string
     */
    function getTitle()
    {
        return t('Файлы для задач');
    }

    /**
     * Возвращает массив с возможными уровнями доступа
     * [id => пояснение, id => пояснение, ...]
     *
     * @return []
     */
    public static function getAccessTypes()
    {
        return [];
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
        return false;
    }

    /**
     * Проверяет права на загрузку файла в систему
     * Возвращает текст ошибки или false - в случае отсутствия ошибки
     *
     * @return string | false
     */
    function checkUploadRightErrors($file_arr)
    {
        //Стандартный механизм проверки прав - проверяются права на запись у модуля,
        //к которому принадлежит текущий класс связи
        return Rights::CheckRightError($this, ModuleRights::TASK_CREATE);
    }
}
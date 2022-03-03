<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Front;

use Main\Model\DaDataApi;

/**
 * Фронт контроллер. Позволяющий получить отпечаток CMS.
 * URL по умолчанию для данного фронт контроллера /cms-sign/
 */
class DaData extends \RS\Controller\Front
{
    function actionAddressSuggestion()
    {
        $result = [];
        $this->wrapOutput(false);
        $query = $this->request('query', TYPE_STRING);
        if ($query) {
            $result = DaDataApi::getInstance()->getAddressSuggestion($query);
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}

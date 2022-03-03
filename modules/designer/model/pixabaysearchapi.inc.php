<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

class PixabaySearchApi extends \RS\Module\AbstractModel\BaseModel
{
    const PROXY_URL = "https://readyscript.ru/pixabay/proxy/";

    /**
     * Возвращает URL для proxy pixabay
     *
     * @return string
     */
    function getProxyUrl()
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/pixabay/proxy/';
    }

    /**
     * Ищет картинки по запросу и параметрам через прокси сервер
     *
     * @param string $term - запрос
     * @param string $category - категория
     * @param integer $page - страница
     * @return array
     */
    function makeSearch($term, $category, $page = 1)
    {
        $url = $this->getProxyUrl();

        $params = [
            'term' => $term,
            'category' => $category,
            'p' => $page,
            'ajax' => 1
        ];

        $params = array_merge($params, \RS\Helper\RSApi::getAuthParams());
        $url = $url."?".http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result){
            $result = @json_decode($result, true);
        }

        return $result;
    }
}


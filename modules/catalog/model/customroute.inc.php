<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
 * Created by PhpStorm.
 * User: Пользователь
 * Date: 23.01.2019
 * Time: 13:06
 */

namespace Catalog\Model;


use RS\Router\Route;

class CustomRoute extends Route
{
    /**
     * Возвращает Uri с нужными параметрами
     *
     * @param array $params параметры для uri
     * @param bool $absolute если true, то вернет абсолютный путь
     * @param mixed $mask_key индекс маски по которой будет строиться url, если не задан, то будет определен автоматически
     */
    public function buildUrl($params = [], $absolute=false, $mask_key = null)
    {
        $uri = parent::buildUrl($params, $absolute, $mask_key);

        if(strpos($uri,'?') === false)
        {
            $uri = str_replace('%2F','/',$uri);
        } else {
            $str1 = str_replace('%2F','/',stristr($uri,'?',true));
            $str2 = str_replace('%2F','/',stristr($uri,'?'));
            $uri = $str1.$str2;
        }

        $uri = preg_replace('/^\/catalog\/$/', '/catalog/all/', $uri);
        $uri = str_replace('/catalog/?','/catalog/all/?',$uri);
        $uri = str_replace('/catalog//','/catalog/all/',$uri);

        return $uri;
    }
}
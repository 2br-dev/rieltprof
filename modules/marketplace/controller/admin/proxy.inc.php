<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Admin;
use Marketplace\Model\ProxyApi;
use Marketplace\Model\ProxyCommands;
use RS\Exception;
use \RS\Router\Manager as RouterManager,
    \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

class Proxy extends \RS\Controller\Admin\Front
{
    private static 
        $allowed_domains = [],
        $protocol,
        $start_url;

    /** @var ProxyApi */
    private 
        $proxyApi;
    

    static public function staticInit()
    {
        self::$allowed_domains = [
            \Setup::$MARKETPLACE_DOMAIN,
            'readyscript.ru',
            'readyscript.local',
        ];
        self::$protocol = \RS\Http\Request::commonInstance()->getProtocol();
        self::$start_url = self::$protocol.'://'.\Setup::$MARKETPLACE_DOMAIN;
    }

    public function init()
    {
        parent::init();
        $this->proxyApi = new ProxyApi();
    }


    /**
     * Страница прокси. Принимает GET-аргумент "url"
     * Выводит обработанный html
     * Добавляет в конец документа вставку JavaScript
     * @throws \Exception
     */
    public function actionIndex()
    {
        $url = $this->url->get('url', TYPE_MIXED, self::$start_url);
        
        $url_info = parse_url($url);

        if(!in_array($url_info['host'], self::$allowed_domains)){
            throw new \Exception(t('Проксирование возможно только для разрешенного домена (code 1)'));
        }

        // Добавляем остальные get параметры, как параметры к url (для того чтобы работали GET-формы)
        $get_arr = $this->url->getSource(GET);
        unset($get_arr['url']);
        unset($get_arr['controller']);
        $url = $this->addParamsToUrl($url, $get_arr);

        if($this->url->isPost())
        {
            // Есле передна команда на исполнение
            if($url_info['path'] == '/proxyCommand')
            {
                $proxy_commands = new ProxyCommands();
                $proxy_commands->executeCommand($_POST);
            }
            else{
                // Стандартная обработка POST-запроса
                $html = $this->proxyApi->requestPost($url, $_POST);
            }
        }
        else
        {
            // Стандартная обработка GET-запроса
            $html = $this->proxyApi->requestGet($url);
        }

        // Обновляем url_info (вдруг был редирект и на саамом деле мы уже на другом URL)
        $url_info = parse_url($this->proxyApi->effective_url);

        // Если это Ajax запрос в формате {html:"...."}
        if(($decoded = json_decode($html)) && isset($decoded->html)){
            $decoded->html = $this->makeUrlsAbsolute($decoded->html, $url_info['host']);
            $decoded->html = $this->wrapUrlsWithProxy($decoded->html);
            $html = json_encode($decoded);
        }
        else{
            $html = $this->makeUrlsAbsolute($html, $url_info['host']);
            $html = $this->wrapUrlsWithProxy($html);
        }

        if(!in_array($url_info['host'], self::$allowed_domains)){
            throw new \Exception(t('Проксирование возможно только для разрешенного домена (code 2)'));
        }

        $script = self::getScript($this->proxyApi->effective_url);
        $html = str_ireplace('</body>', $script.'</body>', $html);

        header("Content-Type: ".$this->proxyApi->content_type);
        echo $html;
        die;
    }

    /**
     * Добавляет дополнительные GET параметры к URL адресу
     *
     * @param $url
     * @param array $params
     * @return string
     */
    private function addParamsToUrl($url, array $params)
    {
        $url_info = parse_url($url);
        // Значения по умолчанию
        $url_info['query'] = isset($url_info['query']) ? $url_info['query'] : '';
        $url_info['path'] = isset($url_info['path']) ? $url_info['path'] : '';
        // Получение списка существующих параметров
        parse_str($url_info['query'], $url_query_arr);
        // Объединение параметров
        $merged_params = array_merge($url_query_arr, $params);
        // Склеивание URL обратно
        $url = $url_info['scheme'].'://'.$url_info['host'].$url_info['path'];
        $url .= empty($merged_params) ? '' : '?'.http_build_query($merged_params);

        return $url;
    }


    private function makeUrlsAbsolute($html, $host)
    {
        $protocol = self::$protocol;
        $html = preg_replace_callback(
            '~((href|src|action)\s*=\s*[\"\'])([^\"\']+)~i',
            function ($x) use($host, $protocol) {                
                if (strpos($x[0], ':') !== false) return $x[0];
                $url = $x[3];
                if(strpos($url, '//') !== 0 && strpos($url, 'http') !== 0){
                    $url = $protocol.'://'.$host.$url;
                }
                return $x[1] . $url;
            },
            $html);
        return $html;
    }

    private function wrapUrlsWithProxy($html)
    {
        $router = RouterManager::obj();
        $html = preg_replace_callback('/(<a.*?\shref=["\'])([^"\']+?)(["\'].*?>)/', function($match) use ($router) {
            if (strpos($match[0], 'data-no-proxy') !== false || strpos($match[0], 'target=') !== false ) return $match[0];
            return $match[1].$router->getAdminUrl(false, ['url' => $match[2]], 'marketplace-proxy').$match[3];
        }, $html);
        $html = preg_replace_callback('/(<.*?data\-href=["\'])([^"\']+?)(["\'].*?>)/', function($match) use ($router) {
            if (strpos($match[0], 'data-no-proxy') !== false || strpos($match[0], 'target=') !== false) return $match[0];
            return $match[1].$router->getAdminUrl(false, ['url' => $match[2]], 'marketplace-proxy').$match[3];
        }, $html);
        $html = preg_replace_callback('/(<form.*?action=["\'])([^"\']+?)(["\'].*?>)/', function($match) use ($router) {
            if (strpos($match[0], 'data-no-proxy') !== false || strpos($match[0], 'target=') !== false) return $match[0];
            return $match[1].$router->getAdminUrl(false, ['url' => $match[2]], 'marketplace-proxy').$match[3];
        }, $html);
        return $html;
    }

    private function getScript($real_url)
    {
        return "<script>
        $(window).load(function(){

            var body = document.body;
            var html = document.documentElement;
            var iFrame = parent.document.getElementById('frame');

            // Подправка высоты IFrame
            if(iFrame){
                iFrame.style.height = '0px';

                var height = Math.max( body.scrollHeight, body.offsetHeight,
                                       html.clientHeight, html.scrollHeight, html.offsetHeight );
                iFrame.style.height = height + 'px';

                parent.postMessage({url:'{$real_url}'}, '*');
            }
        });
        </script>";
    }

}

Proxy::staticInit();
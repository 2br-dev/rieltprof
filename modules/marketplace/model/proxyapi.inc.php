<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Model;

use RS\Http\Request;
use RS\Module\AbstractModel\BaseModel;
use RS\Router\Manager;
use \RS\Config\Loader as ConfigLoader;

/**
 * Класс содержит методы для проксирования HTTP запросов на сервер Marketplace
 */
class ProxyApi extends BaseModel
{
    public $effective_url;
    public $content_type;

    public function request($url, $post_data = false)
    {
        $config = ConfigLoader::byModule($this);
        $tmp_dir = \RS\File\Tools::makePrivateDir( $config->getModuleStorageDir() );
        
        $referer_file = $tmp_dir.'/marketplace_iframe_referer.txt';
        $referer = file_exists($referer_file) ? file_get_contents($referer_file, $url) : null;

        $ch = curl_init();
        $cookies_file = $tmp_dir.'/marketplace_iframe_cookies.txt';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        
        
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if(is_array($post_data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }
        
        $module_manager = new \RS\Module\Manager();
        $installed_addons = implode(',', array_keys($module_manager->getList()));
        
        $host = \RS\Http\Request::commonInstance()->getSelfAbsoluteHost();
        
        $headers_to_send = [
            'X-Readyscript-Iframe: True',
            'X-ReadyScript-Lang: '.\RS\Language\Core::getCurrentLang(),
            'X-ReadyScript-ClientVersion: '.$config['version'],
            'X-ReadyScript-ProxyUrl: '.$host.\RS\Router\Manager::obj()->getAdminUrl(false, [], 'marketplace-proxy'),
            'X-ReadyScript-Addons: '.$installed_addons
        ];

        if(defined('CLOUD_UNIQ'))
        {
            $headers_to_send[] = 'X-Readyscript-CloudUniq: '.CLOUD_UNIQ;
        }
        else
        {
            $licenses_info = $this->getLicensesInfo();
            $headers_to_send[] = 'X-Readyscript-Licenses: '.join(',', $licenses_info['keys']);
            $headers_to_send[] = 'X-Readyscript-Signs: '.join(',', $licenses_info['signs']);
            $headers_to_send[] = 'X-Readyscript-Package: '.\Setup::$SCRIPT_TYPE;
        }

        if(Request::commonInstance()->isAjax()){
            $headers_to_send[] = 'X-Requested-With: XMLHttpRequest';
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_to_send);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);

        $header = substr($data, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        $body = substr($data, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        $header_arr = self::httpParseHeaders($header);

        // Запоминаем Текущий URL
        $this->effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        // Запоминаем Content-Type
        $this->content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        // Сохраняем URL как Referer для следующего запроса
        file_put_contents($referer_file, $this->effective_url);

        // Если обнаружено перенаправление на другой URL
        if(isset($header_arr['Location'])){
            $abs_url = $this->makeUrlAbsolute($header_arr['Location'], $url);
            return $this->request($abs_url/*, $post_data*/);
        }

        return $body;

    }

    public function requestGet($url)
    {
        return $this->request($url);
    }

    public function requestPost($url, $post)
    {
        return $this->request($url, $post);
    }

    public function getLicensesInfo()
    {
        $lApi = new \Main\Model\LicenseApi;
        $lic_keys = [];
        $signs = [];
        $licenses = $lApi->getList();
        foreach($licenses as $one){
            $license_arr = ['license' => $one['license'], 'data' => $one['data'], 'crypt_type' => $one['crypt_type']];
            $row = __GET_LICENSE_INFO($license_arr);

            $lic_keys[] = $one['license'];
            $signs[] = md5(
                $row['sites'].$row['type'].$row['update_months']
                .$row['expire'].$row['expire_month'].$row['update_expire'].$row['date_of_activation']
                .$row['product'].$row['domain'].$row['upgrade_to_product']
            );
        }

        return [
            'keys'  => $lic_keys,
            'signs' => $signs,
        ];
    }

    private function makeUrlAbsolute($url, $current_url)
    {
        $url_info = parse_url($current_url);
        if(strpos($url, '//') !== 0 && strpos($url, 'http') !== 0){
            $url = 'http://'.$url_info['host'].$url;
        }
        return $url;
    }

    static function httpParseHeaders($header)
    {
        $retVal = [];
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/^([^:]+): (.+)$/m', $field, $match) ) {
                $retVal[ucfirst($match[1])] = trim($match[2]);
            }
        }
        return $retVal;
    }

}
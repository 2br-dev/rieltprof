<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Model;

use Marketplace\Controller\Admin\Proxy;
use RS\Config\Loader;
use RS\AccessControl\Rights;
use RS\Module\AbstractModel\BaseModel;
use RS\AccessControl\DefaultModuleRights;

/**
 * Класс обеспечивает backend для команд, которые вызываются в iframe маркетплейса
 */
class ProxyCommands extends BaseModel
{
    static private function getDownloadUrl()
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$MARKETPLACE_DOMAIN.'/downloadAddon/';
    }

    public function executeCommand($params)
    {
        $method = 'command'.$params['command'];

        if(method_exists($this, $method)){
            $response = $this->{$method}($params);
            exit(json_encode($response));
        }
        exit(json_encode([
            'success'=>false,
            'message'=>'Wrong Command: '.$params['command']
        ]));
    }

    private function commandDownloadAddon($params)
    {
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE)) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => t('Недостаточно прав на установку дополнений'),
            ];
        }
        
        $proxy_api = new ProxyApi();
        $get_params['name'] = $params['name'];
        $get_params['demo'] = $params['demo'] ? 1 : 0;


        $config = Loader::byModule($this);
        $tmp_filename = $config->getModuleStorageDir().'/'.$params['name'].($params['demo']?'-demo':'').'.part';
        $dst_filename = $config->getModuleStorageDir().'/'.$params['name'].($params['demo']?'-demo':'').'.zip';

        if(file_exists($tmp_filename)){
            $get_params['offset'] = filesize($tmp_filename);
        }
        else{
            $get_params['offset'] = 0;
        }

        $res = $proxy_api->requestGet(self::getDownloadUrl().'?'.http_build_query($get_params));

        if( !$res ){
            return [
                'success' => false,
                'status' => 'error',
                'message' => t('Не удалость соединиться с сервером Marketplace'),
            ];
        }

        // Если от сервера получены бинарные данные
        if($proxy_api->content_type == 'application/octet-stream')
        {
            file_put_contents($tmp_filename, $res , FILE_APPEND);
            return [
                'success' => true,
                'status' => 'process',
            ];
        }

        // Если от сервера получен JSON
        if($proxy_api->content_type == 'application/json')
        {
            $decoded = json_decode($res);

            // Если не удалось распознать JSON
            if(!$decoded){
                return [
                    'success' => false,
                    'status' => 'error',
                    'message' => t('Сервер Marketplace вернул неверный JSON'),
                ];
            }

            // Если загрузка завершена
            if($decoded->success && $decoded->status == 'done'){
                // Переименовывание файла ( .part => .zip )
                rename($tmp_filename, $dst_filename);
                return [
                    'success' => true,
                    'status' => 'done',
                ];
            }

            // Все прочие типы ответов являются сообщениями об ошибках
            return [
                'success' => false,
                'status' => 'error',
                'message' => $decoded->message,
            ];

        }

        // Не удалось распознать тип содержимого
        return [
            'success' => false,
            'status'  => 'error',
            'message' => t('Недопустимый Content Type'),
            'server_response' => $res,
        ];

    }

    private function commandInstallAddon($params)
    {
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => t('Недостаточно прав на установку дополнений'),
            ];
        }
        
        $name = $params['name'];
        $demo = $params['demo'];

        $config = Loader::byModule($this);
        $file = $config->getModuleStorageDir().'/'.$name.($demo?'-demo':'').'.zip';

        $install_api = new InstallApi();
        $install_api->installAddon($file);

        rename($file, $file.'.installed');

        if($install_api->hasError()){
            return [
                'success' => false,
                'status' => 'error',
                'message' => join(', ', $install_api->getErrors()),
            ];
        }
        return [
            'success' => true,
            'status' => 'done',
        ];
    }
}
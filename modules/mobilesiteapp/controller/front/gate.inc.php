<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Controller\Front;

/**
* Контроллер отвечает за просмотр брэнда
*/
class Gate extends \RS\Controller\Front
{
    /**
     * @var \MobileSiteApp\Model\ExtendApi $extend_api
     */
    public $extend_api;
    /**
     * @var \MobileSiteApp\Model\TemplateManager $template_api
     */
    public $template_api;
    public $config;
    public $client_version;
    public $client_name;

    function __construct($param = [])
    {
        parent::__construct($param);
    }

    /**
     * Инициализация
     *
     * @throws \RS\Exception
     */
    function init()
    {
        $this->extend_api   = new \MobileSiteApp\Model\ExtendApi();   
        $this->template_api = new \MobileSiteApp\Model\TemplateManager();   
        $this->config       = \RS\Config\Loader::byModule($this);
        $this->view->assign($this->template_api->getResourseVariables());

        //Получим заголовки приложения
        if (isset($_GET['client_version'])){ //Если это в параметрах
            $this->client_version = $this->url->request('client_version', TYPE_FLOAT, 0);
            $this->client_name    = $this->url->request('client_name', TYPE_STRING, '');
        }else{ //Если нет, то смотрим в заголовках
            $this->client_version = $this->url->server('X-Client-Version', TYPE_FLOAT, 0);
            $this->client_name    = $this->url->server('X-Client-Name', TYPE_STRING, '');
        }

        $this->view->assign([
            'client_version' => $this->client_version,
            'client_name' => $this->client_name
        ]);
    }
    
    
    /**
    * Добавляет необходимые заголовки
    * 
    * @param boolean $json - Добавить заголовок для json
    */
    private function addHeaders($json = true)
    {
        $origin = \ExternalApi\Model\ApiRouter::getOriginForRequest($this->client_name, $this->client_version);

        $this->app->headers
                        ->addHeader('Access-Control-Allow-Origin', $origin)
                        ->addHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                        ->addHeader('Access-Control-Allow-Headers', '*, x-client-name, x-client-version');
        if ($json){
            $this->app->headers->addHeader('Content-type', 'application/json;charset=UTF-8');
        }
    }
    
    /**
    * Устанавливает текущего пользователя, если токен взапросе передан
    */
    private function setUserByToken()
    {
        //Если есть id токена, то загрузим токен, и установим из него пользователя
        $token_id = $this->url->request('token', TYPE_STRING, false);
        if ($token_id){
            $token = new \ExternalApi\Model\Orm\AuthorizationToken();
            
            if ($token->load($token_id) && $token['expire'] > time()) {
                \RS\Application\Auth::setCurrentUser($token->getUser());
            }
        }
    }
    
    
    
    /**
    * Получение необходимых шаблонов tpl из переданного массива
    *
    * @return string
    */
    /*function actionTemplates()
    {
        $this->addHeaders();  //Установим заголовки      
        $this->setUserByToken(); //Установим пользователя      
        
        $templates = $this->url->request('templates', TYPE_ARRAY, array());
        
        $params = array();
        $this->wrapOutput(false);
        if (!empty($templates)){
            $arr = array(); //Массив отрендеренных шаблонов    
            foreach ($templates as $template){
                $wrapped_content = $this->view->fetch($this->template_api->getCurrentThemeTemplatesFolder().'/'.$template.".tpl"); //Предварительный рендер содержимого
                $this->view->assign(array(
                    'wrapped_content' => $wrapped_content
                ));
                $arr[$template] = $this->view->fetch('wrapper.tpl');
            }
            $params['success']   = true;
            $params['templates'] = $arr;
        }else{
            $params['success'] = false;
            $params['error']   = t('Не передан список шаблонов для рендера');
        }
        $params['check_token'] = true;
        
        return json_encode($params);
    }*/

    /**
     * Возвращает javascript который инициализирует и возращает шаблоны и javascript нужный для расширения объектов приложения
     *
     * @return string
     * @throws \Exception
     */
    function actionGetTemplatesJS()
    {
        $this->wrapOutput(false);

        //Получим шаблоны
        $loaded_templates = $this->template_api->getTemplatesJSONPrepared($this->view);

        //Получим javascript для расширения объектов
        $extend_api   = new \MobileSiteApp\Model\ExtendApi();
        $extends_json = $extend_api->getExtendsJSON();

        echo "//Установим все наши шаблоны
        localStorage.setItem('loaded_templates', JSON.stringify(".json_encode($loaded_templates, JSON_UNESCAPED_UNICODE)."));".
        "localStorage.setItem('extends_json', JSON.stringify(".json_encode($extends_json, JSON_UNESCAPED_UNICODE)."));";
    }


    /**
     * Получение необходимого шаблона tpl
     *
     * @return \RS\Controller\Result\Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionTemplate()
    {
        $template = $this->template_api->checkTemplatePath($this->request('path', TYPE_STRING, null)); //Путь к шаблону
        $this->addHeaders(false); //Установим заголовки
        $this->app->headers->addHeader('Content-type', 'text/plain'); //Добавим дополнительный заголовок

        $this->setUserByToken(); //Установим пользователя
        $this->wrapOutput(false);

        //Проверим, если есть шаблон, то отрендерим его
        $template_full_path = $this->template_api->getCurrentThemeTemplatesFolder().'/'.$template.".tpl";
        if ($this->template_api->checkTemplateExists($template_full_path)){
            $wrapped_content = $this->view->fetch($template_full_path); //Предварительный рендер содержимого
            $this->view->assign([
                'wrapped_content' => $wrapped_content
            ]);
            $this->result->setTemplate('wrapper.tpl');
        }else{
            $this->result->setHtml('');
        }
        return $this->result;
    }

    /**
     * Функция для проверки доступности функционала(для проверки извне)
     *
     * @return string
     */
    function actionCheckOnline()
    {
        $this->addHeaders();  
        $this->wrapOutput(false);        
        $token = $this->request('token', TYPE_STRING, null); //Авторизационный токен      
        $params['success'] = true;   
        $params['check_token'] = !empty($token) ? true : false;
        if (!empty($token) && !$this->extend_api->checkToken($token)){
            $params['check_token'] = false;    
            $params['error'][] = t('Передан неправильный токен или уже не действительный.');
        }    
        //Проверим доступно ли внешнее API
        if (!\RS\Module\Manager::staticModuleExists('externalapi') || !\RS\Module\Manager::staticModuleEnabled('externalapi')){
            $params['success']   = false; 
            $params['error'][] = t('Внешнее API недоступно. Включите внешнее API.');
        }
        
        return json_encode($params);  
    }


    /**
     * Возвращает построенный маршрутом URL с учетом параметров для мобильного приложения вырезая секцию api
     * В случае если маршрут не найден возвращает пустую строку. Генерирует warning
     *
     * @param string $route_id - идентификатор маршрута
     * @param array $params - параметры для формирования адреса
     * @param bool $absolute - абсолютный адрес?
     * @param null $mask_key - клюя для маски
     *
     * @return string
     * @throws \RS\Exception
     */
    function getUrl($route_id, $params = [], $absolute = false, $mask_key = null)
    {
        $config  = \RS\Config\Loader::byModule('externalapi');
        $api_key = $config->api_key ? '-'.$config->api_key : '';
        return str_replace("/api{$api_key}", "", $this->router->getUrl($route_id, $params, $absolute, $mask_key));
    }
}
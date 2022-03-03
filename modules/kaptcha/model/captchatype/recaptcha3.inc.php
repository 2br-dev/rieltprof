<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Model\CaptchaType;

use RS\Application\Application;
use RS\Captcha\AbstractCaptcha;
use RS\Config\Loader;
use RS\View\Engine;

/**
 * Класс для работы с Google RecptchaV3
 * Капча невидимая
 */
class ReCaptcha3 extends AbstractCaptcha
{
    protected $config;

    function __construct()
    {
        $this->config = Loader::byModule($this);
    }

    /**
     * Возвращает идентификатор класса капчи
     *
     * @return string
     */
    public function getShortName()
    {
        return 'google-recaptcha-v3';
    }

    /**
     * Возвращает название класса капчи
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Google reCAPTCHA v3');
    }

    /**
     * Возвращает название поля для клиентских форм
     *
     * @return string
     */
    public function getFieldTitle()
    {
        return '';
    }

    /**
     * Возвращает HTML капчи
     *
     * @param string $name - атрибут name для шаблона отображения
     * @param string $context - контекст капчи
     * @param array $attributes - дополнительные атрибуты для Dom элемента капчи
     * @param array|null $view_options - параметры отображения формы. если null, то отображать все
     *     Возможные элементы массива:
     *         'form' - форма,
     *         'error' - блок с ошибками,
     *         'hint' - ярлык с подсказкой,
     * @param string $template - используемый шаблон
     *
     * @return string
     */
    public function getView($name, $context = null, $attributes = [], $view_options = null, $template = null)
    {
        $view = new Engine();
        $view->assign([
            'unique_id' => substr(md5(uniqid()), 0,10),
            'name' => $name,
            'context' => $context,
            'attributes' => $this->getReadyAttributes($attributes),
            'view_options' => $view_options,
        ]);
        return $view->fetch('%kaptcha%/recaptcha3/recaptcha3.tpl');
    }

    /**
     * Возвращает приватный ключ reCaptcha
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->config->recaptcha_v3_secret_key;
    }

    /**
     * Возвращает публичный ключ ReCaptcha
     *
     * @return string
     */
    public function getPublicSiteKey()
    {
        return $this->config->recaptcha_v3_site_key;
    }

    /**
     * Возвращает минимальную оценку для прохождения теста
     *
     * @return float
     */
    public function getMinSuccessScore()
    {
        return $this->config->recaptcha_v3_success_score;
    }

    /**
     * Проверяет правильность заполнения капчи
     *
     * @param mixed $data - данные для проверки
     * @param string $context - контекст капчи
     * @return bool
     */
    public function check($data, $context = null)
    {
        if ($data == '') return false;

        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret' => $this->getPrivateKey(), 'response' => $data];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'timeout' => 5,
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        $responseKeys = @json_decode($response,true);

        if($responseKeys
            && $responseKeys["success"]
            && $responseKeys['score'] > $this->getMinSuccessScore())
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Возвращает текст ошибки
     *
     * @return string
     */
    public function errorText()
    {
        return t('Ошибка капчи, попробуйте еще раз');
    }

    /**
     * Запускается при старте системы
     */
    public function onStart()
    {
        $config = Loader::byModule($this);
        $app = Application::getInstance();
        $app->addJs('https://www.google.com/recaptcha/api.js?render='.$this->getPublicSiteKey(), 'recaptcha3', BP_ROOT)
            ->addJs('%kaptcha%/recaptcha3.js')
            ->addJsVar('reCaptchaV3SiteKey', $this->getPublicSiteKey());

        if ($this->config->recaptcha_v3_hide_sticker) {
            $app->addCss('%kaptcha%/recaptcha3.css');
        }
    }
}
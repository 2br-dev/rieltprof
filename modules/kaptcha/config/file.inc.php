<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Config;
use RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Стандартная капча'),
                'rs_captcha_allowed_symbols' => new Type\Varchar([
                    'description' => t('Допустимые символы для капчи')
                ]),
                'rs_captcha_length' => new Type\Integer([
                    'description' => t('Количество символов на картинке')
                ]),
                'rs_captcha_width' => new Type\Integer([
                    'description' => t('Ширина генерируемой картинки с капчей, в px'),
                    'hint' => t('В теме оформления может использоваться другой размер отображения картинки')
                ]),
                'rs_captcha_height' => new Type\Integer([
                    'description' => t('Высота генерируемой картинки с капчей, в px'),
                    'hint' => t('В теме оформления может использоваться другой размер отображения картинки')
                ]),
            t('Google ReCaptcha V3'),
                'recaptcha_v3_site_key' => new Type\Varchar([
                    'description' => t('Ключ сайта')
                ]),
                'recaptcha_v3_secret_key' => new Type\Varchar([
                    'description' => t('Секретный ключ')
                ]),
                'recaptcha_v3_success_score' => new Type\Real([
                    'description' => t('Пропускная оценка. Число от 0.1 до 1, ниже которого пользователь считается роботом'),
                    'hint' => t('Стандартное значение 0.5'),
                    'checker' => [function($_this, $value) {
                        if ($value <0 || $value > 1) {
                            return t('Пропускная оценка должна быть в диапазоне от 0 до 1');
                        }
                        return true;
                    }]
                ]),
                'recaptcha_v3_hide_sticker' => new Type\Integer([
                    'description' => t('Скрывать стикер от Google с политикой конфиденциальности'),
                    'checkboxView' => [1,0]
                ])
        ]);
    }

}
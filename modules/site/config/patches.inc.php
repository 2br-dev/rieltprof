<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Config;
use \RS\Orm\Type;
/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает список имен существующих патчей
    */
    function init()
    {
        return [
            '20027',
            '20021',
            '20025',
            '20034',
            '20037',
            '20038',
            '309',
            '3012',
            '3014',
            '3015',
            '409',
            '4010',
        ];
    }

    function afterUpdate3012()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Организация'),
            'firm_legal_address' => new Type\Varchar([
                'description' => t('Юридический адрес компании'),
            ]),
        ]);
        $site_config->dbUpdate();
    }

    function afterUpdate309()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Организация'),
            'firm_email' => new Type\Varchar([
                'description' => t('Официальный Email компании'),
            ]),
        ]);
        $site_config->dbUpdate();
    }

    function afterUpdate20038()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Организация'),
                'firm_address' => new Type\Varchar([
                    'description' => t('Физический адрес компании'),
                ]),
            t('Персональные данные'),
                'policy_personal_data' => new Type\Richtext([
                    'description' => t('Политика обработки персональных данных (ссылка /policy/)'),
                ]),
                'agreement_personal_data' => new Type\Richtext([
                    'description' => t('Соглашение на обработку персональных данных (ссылка /policy-agreement/)'),
                ]),
                'enable_agreement_personal_data' => new Type\Integer([
                    'description' => t('Включить отображение соглашения на обработку персональных данных в формах'),
                ])
        ]);
        $site_config->dbUpdate();
    }

    function beforeUpdate20037()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Основные'),
            'is_closed' => new Type\Integer([
                'description' => t('Закрыть доступ к сайту'),
                'hint' => t('Используйте данный флаг, чтобы закрыть доступ к сайту на время его разработки'),
                'template' => '%site%/form/site/is_closed.tpl',
                'checkboxView' => [1,0]
            ]),
            'close_message' => new Type\Varchar([
                'description' => t('Причина закрытия сайта'),
                'hint' => t('Будет отображена пользователям'),
                'attr' => [[
                    'placeholder' => t('Причина закрытия сайта')
                ]],
                'visible' => false
            ])
        ]);
        $site_config->dbUpdate();
    }
    
    function beforeUpdate20034()
    {
        $site = new \Site\Model\Orm\Site();
        $site->getPropertyIterator()->append([
            'redirect_to_https' => new Type\Integer([
                'description' => t('Перенаправлять на Https'),
                'allowEmpty' => false,
                'checkboxView' => [1,0]
            ]),
        ]);
        $site->dbUpdate();
    }    
    
    function beforeUpdate20027()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Основные'),
            'favicon' => new Type\File([
                'description' => t('Иконка сайта 16x16 (PNG, ICO)'),
                'hint' => t('Отображается возле заголовка страницы в браузерах'),
                'max_file_size' => 100000, //100 Кб
                'allow_file_types' => ['image/png', 'image/x-icon'],
                'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
            ])
        ]);
        $site_config->dbUpdate();
    }
    
    function beforeUpdate20021()
    {
        $site = new \Site\Model\Orm\Site();
        $site->getPropertyIterator()->append([
            'redirect_to_main_domain' => new Type\Integer([
                'description' => t('Перенаправлять на основной домен'),
                'allowEmpty' => false,
                'checkboxView' => [1,0],
                'hint' => t('Если включено, то при обращении к НЕ основному домену будт происходить 301 редирект на основной домен')
            ]),
        ]);
        $site->dbUpdate();
    }
    
    function beforeUpdate20025()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Социальные ссылки'),
            'instagram_group' => new Type\Varchar([
                'description' => t('Ссылка на страницу в Instagram')
            ])
        ]);
        $site_config->dbUpdate();
    }

    function beforeUpdate409()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Социальные ссылки'),
            'viber_group' => new Type\Varchar([
                'description' => t('Ссылка на Viber')
            ]),
            'telegram_group' => new Type\Varchar([
                'description' => t('Ссылка на Telegram')
            ]),
            'whatsapp_group' => new Type\Varchar([
                'description' => t('Ссылка на WhatsApp')
            ])
        ]);
        $site_config->dbUpdate();
    }
    
    function beforeUpdate3014()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Уведомления'),
                'smtp_is_use' => new Type\Integer([
                    'description' => t('Использовать SMTP для отправки писем'),
                    'checkboxView' => [1,0],
                    'template' => '%main%/form/sysoptions/smtp_is_use.tpl',
                    'hint' => t('Если включена - все поля из настроек сайта перекроют соответствующие поля настроек системы'),
                ]),
                'smtp_host' => new Type\Varchar([
                    'description' => t('SMTP сервер')
                ]),
                'smtp_port' => new Type\Varchar([
                    'description' => t('SMTP порт'),
                    'maxLength' => 10
                ]),
                'smtp_secure' => new Type\Varchar([
                    'description' => t('Тип шифрования'),
                    'listFromArray' => [[
                        '' => t('Нет шифрования'),
                        'ssl' => 'SSL',
                        'tls' => 'TLS'
                    ]]
                ]),
                'smtp_auth' => new Type\Integer([
                    'description' => t('Требуется авторизация на SMTP сервере'),
                    'checkboxView' => [1,0]
                ]),
                'smtp_username' => new Type\Varchar([
                    'description' => t('Имя пользователя SMTP'),
                    'maxLength' => 100
                ]),
                'smtp_password' => new Type\Varchar([
                    'description' => t('Пароль SMTP'),
                    'maxLength' => 100
                ]),
            t('Социальные ссылки'),
                'youtube_group' => new Type\Varchar([
                    'description' => t('Ссылка на страницу в YouTube'),
                ])
        ]);
        $site_config->dbUpdate();
    }
    
    function beforeUpdate3015()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Организация'),
                'firm_v_lice' => new Type\Varchar([
                    'description' => t('Компания представлена в лице ....'),
                    'hint' => t('например: директора Иванова Ивана Ивановича. Пустое поле означает - "в собственном лице"')
                ]),
                'firm_deistvuet' => new Type\Varchar([
                    'description' => t('действует на основании ...'),
                    'hint' => t('например: устава или свидетельства о регистрации физ.лица в качестве ИП, ОГРНИП:0000000000')
                ]),
        ]);
        $site_config->dbUpdate();
    }

    function beforeUpdate4010()
    {
        $site_config = new \Site\Model\Orm\Config();
        $site_config->getPropertyIterator()->append([
            t('Основные'),
            'favicon_svg' => new Type\File([
                'description' => t('Иконка сайта в формате SVG'),
                'hint' => t('Отображается возле заголовка страницы в браузерах'),
                'max_file_size' => 100000, //100 Кб
                'allow_file_types' => ['image/svg+xml'],
                'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
            ]),
        ]);
        $site_config->dbUpdate();
    }
}

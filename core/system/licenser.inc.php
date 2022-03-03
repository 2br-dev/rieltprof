<?php
/*
* Список функций, запрещенных к обфускации
__LIC_TEST
__CHECK_LICENSE
__GET_LICENSE_LIST
__GET_LICENSE_DATA
__SET_LICENSE
__GET_LICENSE_TABLE_LIST
__GET_ADMIN_NOTICE
__CAN_ADD_SITE
__GET_MAX_SITE_LIMIT
__CDI
__LICENSE_CHECK
__SET_INSTALL_ID
__GET_LICENSE_INFO
__CHECK_PACKAGE_ERROR
__GET_PACKAGE_NOTICE
__MODULE_LICENSE_GENERAL_CHECK
__MODULE_LICENSE_CHECK
__MODULE_LICENSE_GET_DATA
__MODULE_LICENSE_DECODE_LICENSE_DATA
__MODULE_LICENSE_GET_ALL
__MODULE_LICENSE_LOAD
__MODULE_LICENSE_GET_MODULES
__MODULE_LICENSE_DELETE_DATA
__MODULE_LICENSE_IS_SYSTEM_MODULE
__ON_MODULE_INSTALL
__MODULE_LICENSE_GET_ALL_LIST
__MODULE_LICENSE_LOG

Переменные:
$MODULE_LICENSE_LOG_ENABLE
$INSTALLED
$SCRIPT_TYPE
$FOLDER
$PATH
$VERSION
$LOGS_DIR
$error
$type
$success
$license_key
$data
$error_code

http://delowap.ru/obfuscator.php

Опции:

Заменять переменные - Дать переменным короткие имена
Обфускация статических строк - Дополнительно шифровать в base64
Заменять функции - Дать функциям короткие имена
Заменять стандартные функции PHP
Шифровать их названия в base64
Обфускация INTEGER - yes
Максимально сжать скрипт - yes
Обфускация констант PHP - no
Добавить спец. комментарии - no
Добавить мусорный код - no
Вывести комментарии в отдельных строках - no
Заменять переменные и функции в eval() - no
Сжать файл (gzip+base64) - no

*/

use RS\Module\ModuleLicense;
use RS\HashStore\Api as HashStoreApi;
use RS\Orm\Request as OrmRequest;
use RS\Router\Manager as RouterManager;
use Main\Model\Orm\License as MainOrmLicense;
use Site\Model\Orm\Site as SiteOrmSite;

require('copyid.inc.php');

if (isset($_GET['check_license_request'])) die; //Если запрос был с этим параметром и этот файл выполняется, значит временный файл не найден

if (defined('__E_L')) die(t('Нарушение защиты EL')); else define('__E_L', 0);

try {
    if (defined('INSTALL_ID')) {
        die(t('Нарушение защиты(INSTALL_ID)'));
    } else {
        define('INSTALL_ID', HashStoreApi::get('__INSTALL_DATA'));
    }
} catch (Exception $e) {
    if (INSTALLED) throw $e;
}

if (!\Setup::$INSTALLED) {
    /**
     * Устанавливает INSTALL ID в Hash Store, который определяет связь копии файлов с БД
     */
    function __SET_INSTALL_ID()
    {
        $__error_reporting = error_reporting(__E_L);

        $idata = HashStoreApi::get('__INSTALL_DATA');
        if (!$idata) {
            $key = '';
            $key_length = 4;
            for ($i = 0; $i < $key_length; $i++) {
                $key .= rand(0, 9);
            }

            $t0 = time() + 60 * 60 * 24 * 31;
            $t1 = $t0 + sprintf('%u', crc32($key));
            $t2 = $t1 . '-' . $key;
            $expire = $t2 . (sprintf('%u', crc32($t2 . COPY_ID)) - $key);
            HashStoreApi::set('__INSTALL_DATA', $expire);
        }
        error_reporting($__error_reporting);
    }
}

function __LIC_TEST()
{
}

/**
 * Проверяет корректность лицензионного ключа
 *
 * @param string $license_key
 * @return bool | string Возвращает true или текст ошибки
 */
function _CHECK_LICENSE_CRC($license_key)
{
    $__error_reporting = error_reporting(__E_L);
    //Проверяем целостность ключа
    $line = trim(str_replace('-', '', $license_key));

    //Проверяем целостность ключа
    if (!strlen($line)) {
        $result['error'] = t('Номер лицензии не указан');
    }

    $sign = substr($line, -4);
    $key = substr($line, 0, -4);
    $real_sign = strtoupper(str_replace([0, 'i', 'o'], 'a', substr(dechex(crc32($key)), 0, 4)));

    error_reporting($__error_reporting);
    if ($sign !== $real_sign) {
        return t('Неверный номер лицензии');
    }
    return true;
}


/**
 * Проверяет, существует ли такой лицензионный ключ
 *
 * @param string $license_key ключ
 * @param mixed $response
 */
function __CHECK_LICENSE($license_key, &$response = null, $license_type = 'script')
{
    $__error_reporting = error_reporting(__E_L);
    $result = true;
    if (($check_crc = _CHECK_LICENSE_CRC($license_key)) !== true) {
        $result = $check_crc;
    } else {
        //Проверяем лицензию на удаленном сервере
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'timeout' => 7,
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query([
                    'license_key' => $license_key,
                    'copy_id' => COPY_ID,
                    'install_id' => INSTALL_ID,
                    'script_type' => \Setup::$SCRIPT_TYPE,
                ]),
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        @$response = file_get_contents(CHECK_LICENSE_SERVER, false, $context);

        if ($response !== false) {
            $response_arr = json_decode($response);
            if ($response_arr->success == true) {
                if ($license_type && $license_type !== $response_arr->type) {
                    $result = t('Укажите основную лицензию на продукт');
                }
                //Результат успешный
            } else {
                $result = $response_arr->error;
            }
        } else {
            $result = t('Не удалось соединиться с сервером ReadyScript');
        }
    }
    error_reporting($__error_reporting);
    return $result;
}

/**
 * Возвращает приватные данные ключа или false
 *
 * @param mixed $license_key
 * @param mixed $error
 * @param mixed $activation_data
 *
 */
function __GET_LICENSE_DATA($license_key, &$error_msg = null, $activation_data = [], &$license_type = null, &$license_expire_month = null, $crypt_type = null)
{
    $__error_reporting = error_reporting(__E_L);
    $crypt_type = $crypt_type ?: 'mcrypt';

    $error_msg = null;
    if (($check_crc = _CHECK_LICENSE_CRC($license_key)) !== true) {
        $error_msg = [
            'code' => -1,
            'message' => $check_crc
        ];
        $result = false;
    } else {
        $script_license = false;
        __GET_LICENSE_LIST($script_license);
        $main_license = ($script_license) ? ['main_lic' => $script_license['license_key']] : [];

        //Проверяем лицензию на удаленном сервере
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'timeout' => 7,
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query([
                        'do' => 'getLicense',
                        'version' => \Setup::$VERSION,
                        'license_key' => $license_key,
                        'copy_id' => COPY_ID,
                        'install_id' => INSTALL_ID,
                        'script_type' => SCRIPT_TYPE,
                        'activation_data' => $activation_data,
                        'check_domain_folder' => \Setup::$FOLDER,
                        'check_domain_hash' => __MAKE_CHECK_DOMAIN_HASH(),
                        'crypt_type' => $crypt_type
                    ] + $main_license),
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        @$response = file_get_contents(CHECK_LICENSE_SERVER, false, $context);

        if ($response !== false) {
            $response_arr = @json_decode($response);
            if (isset($response_arr->type)) {
                $license_type = $response_arr->type;
            }
            if (isset($response_arr->expire_month)) {
                $license_expire_month = $response_arr->expire_month;
            }
            if ($response_arr->success == true) {
                if (!isset($response_arr->license_key) || !isset($response_arr->data)) {
                    $error_msg = [
                        'code' => -2,
                        'message' => t('Ошибка ответа сервера')
                    ];
                    $result = false;
                } else {
                    $result = $response_arr->data;
                }
            } else {
                $error_msg = [
                    'code' => $response_arr->error_code,
                    'message' => $response_arr->error,
                ];
                $result = false;
            }
        } else {
            $error_msg = [
                'code' => -4,
                'message' => t('Не удалось соединиться с сервером ReadyScript')
            ];
            $result = false;
        }
    }

    error_reporting($__error_reporting);
    return $result;
}

/**
 * Генерирует произвольный ключ для текущего домена, который далее можно будет проверить
 *
 * @return string
 */
function __MAKE_CHECK_DOMAIN_HASH()
{
    $__error_reporting = error_reporting(__E_L);
    $hash = md5(uniqid(mt_rand(), true));
    HashStoreApi::set('__CHECK_DOMAIN_HASH', $hash);
    error_reporting($__error_reporting);
    return $hash;
}

/**
 * Подтверждает, что этот интернет-магазин имеет именно такой check_domain_hash.
 * Функция используется при активации лицензии, чтобы клиент не ошибся в названии домена.
 *
 * @return void
 */
function __CHECK_DOMAIN_HASH()
{
    if (isset($_GET['check_domain_hash'])) {
        $current_hash = HashStoreApi::get('__CHECK_DOMAIN_HASH');
        if ($current_hash === $_GET['check_domain_hash']) {
            echo json_encode(['result' => 'success']);
            exit;
        }
    }
}

/**
 * Сохраняет лицензионный ключ в БД
 *
 * @param $license_key
 * @return bool|string
 * @throws \RS\Event\Exception
 */
function __SET_LICENSE($license_key)
{
    $__error_reporting = error_reporting(__E_L);

    $license = new MainOrmLicense();
    $license['license'] = $license_key;
    if ($license->replace()) {
        $result = true;
    } else {
        $result = implode(',', $license->getErrors());
    }
    error_reporting($__error_reporting);
    return $result;
}

/**
 * Проверяет вероятное доменное имя, путем совершения запроса на него
 *
 * @param $probably_domain
 * @return bool возвращает true, в случае если вероятный домен успешно подтвержден. Иначе - false
 */
function __CHECK_DOMAIN_ERROR($probably_domain)
{
    static $cache = [];
    $__error_reporting = error_reporting(__E_L);

    if (!isset($cache[$probably_domain])) {
        __CHECK_PACKAGE_ERROR();
        //Проверяем домен
        $cache_folder = defined('CACHE_MAIN_FOLDER') ? CACHE_MAIN_FOLDER : '';
        $cache_license_file = \Setup::$PATH . $cache_folder . '/cachelic.tmp';
        $cache_license = md5(date('YW') . INSTALL_ID . '%K$-./*@()');

        $site_domain_error = null;

        //Проверяем месячную лицензию
        if (file_exists($cache_license_file)) {
            $monthLicense = file_get_contents($cache_license_file);

            if (!strcmp($cache_license, $monthLicense)) {
                $site_domain_error = false; //Временная лицензия в порядке
            } else {
                unlink($cache_license_file);
            }
        }

        if ($site_domain_error === null) {
            //Начинаем процедуру проверки домена. Пишем файл. И пробуем к нему обратиться через HTTP request
            $randFile = md5(uniqid(rand(), true));
            $randData = md5(uniqid(rand(), true));
            $result = file_put_contents(\Setup::$PATH . '/' . $randFile, $randData);

            if (!$result) {
                die(t('Не удалось записать файл в корневую папку. Проверьте права и доступное место на диске'));
            }

            $ctx = stream_context_create([
                'http' => ['timeout' => CHECK_DOMAIN_TIMEOUT]
            ]);

            //Проверяем возвращает ли "вероятный домен" верный отзыв
            $url = 'http://' . $probably_domain . \Setup::$FOLDER . '/' . $randFile . '?check_license_request';

            $responseValue = file_get_contents($url, 0, $ctx, 0, 40);
            unlink(\Setup::$PATH . '/' . $randFile);

            if ($responseValue === false || (strcmp($responseValue, $randData) != 0)) {
                $site_domain_error = true;
            } else {
                //Если проверка пройдена, то формируем ключ на 1 неделю
                file_put_contents($cache_license_file, $cache_license);
                $site_domain_error = false;
            }
        }
        $cache[$probably_domain] = $site_domain_error;
    }
    error_reporting($__error_reporting);
    return $cache[$probably_domain];
}

/**
 * Функция проверки наличия и выключение найденых запрещенных модулей в пакете RS.
 */
function __CHECK_PACKAGE_ERROR()
{
    $check = false;
    $package_locked_modules = [
        'shop.base' => [
            'shop',
            'support',
            'exchange',
            'export',
            'partnership',
            'antivirus',
            'affiliate',
            'cdn',
            'statistic',
            'mobilemanagerapp',
        ],
        'shop.middle' => [
            'partnership',
            'antivirus',
            'affiliate',
            'cdn',
            'statistic',
            'yandexmarketcpa',
        ],
        'shop.full' => [
            'antivirus',
            'affiliate',
            'cdn',
            'statistic',
        ],
        'shop.mega' => [],
    ];

    $package = strtolower(\Setup::$SCRIPT_TYPE);

    foreach ($package_locked_modules[$package] as $locked_module_name) {
        if ($module_config = \RS\Config\Loader::byModule($locked_module_name)) {
            $check = true;
            if ($module_config['enabled'] == true) {
                $module_config['enabled'] = false;
                $module_config->update();
            }
        }
    }

    define('SCRIPT_PACKAGE_ERROR', $check);
}

/**
 * Возвращает есть ли запрещенные модули в пакете
 *
 * @return bool|string
 */
function __GET_PACKAGE_NOTICE()
{
    $result = false;

    if (defined('SCRIPT_PACKAGE_ERROR') && SCRIPT_PACKAGE_ERROR) {
        $package = \Setup::$SCRIPT_TYPE;
        $result = t("Обнаружены модули не соответствующие вашей редакции ReadyScript (%0). Эти модули были отключены.", [$package]);
    }

    return $result;
}

/**
 * Возвращает список имеющихся лицензий в системе
 *
 * @param null $script_license здесь будет установлена "основная" лицензия на продукт, если она есть в списке
 * @return array|null
 */
function __GET_LICENSE_LIST(&$script_license = null)
{
    static
    $cached_list = null,
    $cache_script_license = false;

    $probably_domain = strtolower(getEnv('HTTP_HOST'));
    $__error_reporting = error_reporting(__E_L);
    try {
        if ($cached_list === null) {
            $cached_list = [];
            $licenses = OrmRequest::make()
                ->from(new MainOrmLicense())
                ->orderby("type != '#type'", ['type' => 'script'])
                ->exec()
                ->fetchAll();
            $update_expire = 0;

            $script_type = strtolower(\Setup::$SCRIPT_TYPE);
            $allow_products = [$script_type => $script_type];
            $lic_data = [];
            foreach ($licenses as $license) {
                $data = __GET_LICENSE_INFO($license);
                $lic_data[$license['license']] = $data;
                if (!empty($data['upgrade_to_product'])) {
                    $product_name = strtolower($data['upgrade_to_product']);
                    $allow_products[$product_name] = $product_name;

                    $product_name = strtolower($data['product']);
                    $allow_products[$product_name] = $product_name;
                }
            }

            //Лицензия у которой срок окончания наступит раньше всех остальных
            $min_expire_temporary_license = null;

            foreach ($licenses as $license) {
                $data = $lic_data[$license['license']];

                if (is_array($data)) {
                    $data['errors'] = [];
                    if ($data['expire'] > 0 && time() > $data['expire']) {
                        $data['errors'][] = t('Срок действия лицензии истек');
                    }

                    if (!isset($allow_products[strtolower($data['product'])])) {
                        $data['errors'][] = t('Лицензия не соответствует комплектации системы');
                    }

                    //Если это продакшн, то проверяем на соответствие доменного имени
                    if (!preg_match('/(.local|.test)$/', $probably_domain)) {
                        if (($pos = strpos($probably_domain, ":")) !== false) {
                            $port = ':'.(int)substr($probably_domain, $pos + 1);
                        } else {
                            $port = '';
                        }
                        $data['dev_domain'] = 'dev.' . $data['domain'];

                        if (__CHECK_DOMAIN_ERROR($data['domain'].$port)
                            && __CHECK_DOMAIN_ERROR($data['dev_domain'].$port)
                            && (empty($data['partner_dev_domain']) || __CHECK_DOMAIN_ERROR($data['partner_dev_domain'].$port))
                        ) {
                            $data['errors'][] = t('Доменное имя не соответствует лицензии');
                        }
                    };

                    $data['active'] = !count($data['errors']);
                    if ($data['active']) {
                        if ($data['update_expire'] > $update_expire) {
                            $update_expire = $data['update_expire'];
                        }
                        if ($data['type'] == 'script') {
                            if (!$data['expire_month']) {
                                $cache_script_license = $data; //Запоминаем вечную лицензию
                            } else {
                                //Запоминаем лицензию в минимальным временем действия
                                if ($min_expire_temporary_license === null || $data['expire'] < $min_expire_temporary_license['expire']) {
                                    $min_expire_temporary_license = $data;
                                }
                            }
                        }
                    }

                } else {
                    $data['errors'] = t('Ошибка расшифровки лицензии');
                    $data['domain'] = '';
                    $data['active'] = false;
                    $data['type'] = '';
                }
                $cached_list[$license['license']] = $data;
            }

            $cache_script_license_tmp = null;
            //Выключаем неактивные временные лицензии
            foreach ($cached_list as $key => $data) {
                if ($data['active']
                    && $data['type'] == 'script'
                    && $data['expire_month']) {
                    if ($cache_script_license) {
                        //Если есть вечная лицензия, то отключаем все временные
                        $cached_list[$key]['active'] = false;
                        $cached_list[$key]['errors'] = [t('Лицензия недействительна пока установлена вечная лицензия')];
                    } else {
                        if ($key == $min_expire_temporary_license['license_key']) {
                            $cache_script_license_tmp = $data;
                        } else {
                            $cached_list[$key]['active'] = false;
                            $cached_list[$key]['errors'] = [t('Лицензия пока не используется')];
                            $cached_list[$key]['unused'] = true;
                        }
                    }
                }
            }

            if ($cache_script_license_tmp) {
                //Устанавливаем одну из временных лицензий в качестве основной
                $cache_script_license = $cache_script_license_tmp;
            }

            $expire_days = ceil(($update_expire - time()) / 60 / 60 / 24);
            define('SCRIPT_UPDATE_EXPIRE', $update_expire);
            define('SCRIPT_UPDATE_EXPIRE_DAYS', $expire_days > 0 ? $expire_days : 0);
        }
    } catch (Exception $e) {
    }
    $script_license = $cache_script_license;

    error_reporting($__error_reporting);
    return $cached_list;
}


/**
 * Дешифрует информацию по лицензии. Возвращает массив с информацией по лицензии
 *
 * @param array $license_arr
 * @return array
 */
function __GET_LICENSE_INFO($license_arr)
{
    $__error_reporting = error_reporting(__E_L);
    $result = [];

    if (isset($license_arr['crypt_type']) && $license_arr['crypt_type'] == 'openssl' && function_exists('openssl_public_decrypt')) {

        //используем openssl для расшировки новых лицензий. Расшифровку ведем с помощью открытого ключа
        $public_key = '-----BEGIN PUBLIC KEY-----' . "\n" .
            'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxBAcmpd/6O4cqCYlhnPk' . "\n" .
            'WXnsIUNgnVoW2OES0cwj2Hzr6R6WysCRoHxWGvJieGJPh3EIcfhKUwucZg8Oljiz' . "\n" .
            'x6GpoyT8zGxS0gI6gMLbRJsE4LlIHZRulFbkoiuH/OEl6zupT9dZOhaYLyCN1bDm' . "\n" .
            'oElr+DyRTrlSqTzFfuUiwwGnmqiJTrPKJ4H6XPLOZXMaI/WlApz0W8TqzXzqynyM' . "\n" .
            'qn4maPSfHEHZdkOYKdeOmpfz8KlBVHPLie0MNAPnTcAA4KBQqZ0PZ/i2WX8ojBU3' . "\n" .
            'M1Kcb2LxW5G4ggpc62Vgmb32Luu4fOGI/SISlCm5TvpDKjizV6M4e+axICC0ABtZ' . "\n" .
            'gQIDAQAB' . "\n" .
            '-----END PUBLIC KEY-----';

        $decrypt = __decryptRSA(openssl_pkey_get_public($public_key), base64_decode($license_arr['data']));

    } elseif (function_exists('mcrypt_module_open')) {

        //Используем mcrypt для расшифровки старых лицензий
        $secret = 'ABYSL)(*&^(&^$y))' . chr(0) . chr(15) . chr(254);
        $key = md5(md5(INSTALL_ID . substr($secret, 0, 5)) . md5(COPY_ID . substr($secret, 6)) . $secret . $license_arr['license']);

        //Используем mcrypt для расшифровки старых лицензий
        $td = mcrypt_module_open(MCRYPT_TWOFISH, '', MCRYPT_MODE_ECB, '');
        $iv = substr(md5('installer'), 0, mcrypt_enc_get_iv_size($td));

        mcrypt_generic_init($td, $key, $iv);
        $decrypt = mdecrypt_generic($td, base64_decode($license_arr['data']));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
    }

    $result = unserialize($decrypt);

    error_reporting($__error_reporting);
    return $result;
}

//Расшифровывает зашифрованные с помощью OpenSSL данные
function __decryptRSA($publicPEMKey, $data)
{
    $decrypt_block_size = 256;

    $decrypted = '';
    $data = str_split($data, $decrypt_block_size);
    foreach ($data as $chunk) {
        $partial = '';
        $decryptionOK = openssl_public_decrypt($chunk, $partial, $publicPEMKey, OPENSSL_PKCS1_PADDING);
        if ($decryptionOK === false) {
            return false;
        }
        $decrypted .= $partial;
    }
    return $decrypted;
}


//Код проверки домена и лицензии
function __CH__($run_key, $result_key)
{

    if ($run_key !== md5('register_licenser')) return;
    $__error_reporting = error_reporting(__E_L);

    if (!\Setup::$INSTALLED && defined('INSTALLED') && INSTALLED == true) die(t('Нарушение защиты. Несоответствие INSTALLED'));
    if (!\Setup::$INSTALLED && count(RouterManager::getRoutes()) > 3) die(t('Нарушение защиты. Недопустимое число маршрутов'));
    if (defined('SCRIPT_DOMAIN_ERROR') || defined('SITE_LIMIT_ERROR') ||
        defined('SCRIPT_TRIAL_STATUS') || defined('SCRIPT_TRIAL_DAYS') ||
        defined('SCRIPT_TYPE_ERROR') || defined('MAX_SITE_LIMIT')) {
        die(t('Нарушение защиты. Определены зарезервированные константы'));
    }

    $is_admin = RouterManager::obj()->isAdminZone();
    $is_cli = php_sapi_name() == 'cli';

    if (\Setup::$INSTALLED) {
        $probably_domain = strtolower(getEnv('HTTP_HOST'));
        $total_sites = 0;
        $allow_products = [];  //Допустимые комплектации товаров
        $site_domain_error = null;  //Невозможно проверить подлинность домена
        $site_limit_error = false;  //Превышено число допустимых сайтов
        $trial_time_error = false;  //Trial завершен
        $temporary_expire = false; //Осталось дней по временным лицензиям

        $lic_request_timeout = 10;
        $script_license = false;
        $licenses = __GET_LICENSE_LIST($script_license);

        foreach ($licenses as $license) {
            if ($license['active']) {
                $total_sites += $license['sites'];
                if (!empty($license['upgrade_to_product'])) {
                    $product_name = strtolower($license['upgrade_to_product']);
                } else {
                    $product_name = strtolower($license['product']);
                }
                $allow_products[$product_name] = $product_name;
            }
            if (($license['active'] || $license['unused']) && $license['type'] == 'script' && $license['expire_month']) {
                if ($temporary_expire === false || $temporary_expire < $license['expire']) {
                    $temporary_expire = $license['expire'];
                }
            }
        }
        define('MAX_SITE_LIMIT', $total_sites);
        define('TEMPORARY_EXPIRE', $temporary_expire);

        //Если есть основная лицензия. И она активна, то триала - нет.
        if ($script_license !== false) {
            define('SCRIPT_TRIAL_STATUS', 'DISABLED');

        } else {
            //Trial период
            //Это триал период. Если это не зона .local или .test, то проверяем временной период
            if ($correct = preg_match('/^(\d+)\-(\d+)$/', INSTALL_ID, $match)) {
                $time = $match[1];
                $klength = 4;
                $key = substr($match[2], 0, $klength);
                $sign = substr($match[2], $klength);
                $correct = ((sprintf('%u', crc32($time . '-' . $key . COPY_ID)) - $key) == $sign);
            }

            if ($correct) {
                $expire = $time - sprintf('%u', crc32($key));
                define('SCRIPT_EXPIRE_TIME', $expire);

                if (preg_match('/^.*(\.local|\.test)$/i', $probably_domain)) {
                    define('SCRIPT_TRIAL_STATUS', 'LOCAL');
                    define('SCRIPT_TRIAL_DAYS', '∞');
                } else {
                    if ($expire - time() >= 0) {
                        if ($expire - time() > 60 * 60 * 24 * 31) {
                            die(t('На сервере некорректно установлено время'));
                        } else {
                            define('SCRIPT_TRIAL_STATUS', 'ACTIVE');
                            define('SCRIPT_TRIAL_DAYS', floor(($expire - time()) / (60 * 60 * 24)));
                        }
                    } else {
                        define('SCRIPT_TRIAL_STATUS', 'OVERDUE');
                        $trial_time_error = true;
                    }
                }

            } else {
                die(t('Нарушение защиты. Скрипты не соответствуют базе данных'));
            }
        }

        define('SCRIPT_TYPE_ERROR', !in_array(strtolower(\Setup::$SCRIPT_TYPE), $allow_products) && SCRIPT_TRIAL_STATUS == 'DISABLED');
        define('SITE_LIMIT_ERROR', $site_limit_error);

        if (preg_match('/(.local|.test)$/', $probably_domain)) {
            //Это локальная зона
            $site_domain_error = __CHECK_DOMAIN_ERROR($probably_domain);
            if (!$is_admin && !$is_cli && $site_domain_error) {
                die(t('Невозможно подтвердить домен .local или .test'));
            }
        } else {
            //Это production сервер
            if (!$is_admin && !$is_cli) {
                if ($trial_time_error) {
                    die(t('Тестовый период работы с ReadyScript завершен. Необходимо приобрести лицензию'));
                }

                if ($site_limit_error) {
                    die(t('Количество сайтов не соответствует лицензии'));
                }

                if (SCRIPT_TYPE_ERROR) {
                    die(t('Лицензионный ключ не соответствует продукту'));
                }
            }
        }
    }
    define('__R_' . $result_key, true);
    error_reporting($__error_reporting);
}

/**
 * Вызывается при установке/обновлении модуля
 *
 * @param \RS\Module\Item $module
 * @throws \RS\Event\Exception
 * @throws \RS\Exception
 */
function __ON_MODULE_INSTALL($module)
{
    $config = $module->getConfig();
    if (!$config['is_system']) {
        __MODULE_LICENSE_LOAD($module->getName());
    }
}

/**
 * Общая проверка модульных лицензий
 *
 * @return void
 * @throws \RS\Event\Exception
 * @throws \RS\Exception
 */
function __MODULE_LICENSE_GENERAL_CHECK()
{
    $licenses = __MODULE_LICENSE_GET_ALL();
    $modules_config = __MODULES_LOAD_CONFIG();

    foreach ($licenses as $module_name => $license_data) {
        if (!preg_match('/#/', $module_name)) { //Пропускаем темы оформления
            $module_config_array = isset($modules_config[$module_name])
                ? $modules_config[$module_name]
                : ['deactivated' => 0];

            $action_reason = null;
            if ($license_data['is_working']) {
                if ($module_config_array['deactivated']) {
                    //Нужно включить
                    $module_config_array['deactivated'] = 0;
                    $action_reason = t('Активирован модуль %0 (reason = %1)', [$module_name, $license_data['working_reason']]);
                }

            } else {
                if (!$module_config_array['deactivated']) {
                    //Нужно выключить
                    $module_config_array['deactivated'] = 0;
                    $action_reason = t('Деактивирован модуль %0 (reason = %1)', [$module_name, $license_data['working_reason']]);
                }
            }

            if ($action_reason !== null) {
                if (__MODULE_UPDATE_CONFIG($module_name, $module_config_array)) {
                    __MODULE_LICENSE_LOG($action_reason);
                }
            }
        }
    }
}

/**
 * Загружает конфигурацию модулей напрямую из БД
 * Быстрая альтернатива полной загрзки модулей
 * Возвращает
 *
 * @return array
 */
function __MODULES_LOAD_CONFIG()
{
    $site_id = \RS\Site\Manager::getSiteId();
    $rows = \RS\Orm\Request::make()
        ->from(\RS\Module\ModuleConfig::_getTable())
        ->where(['site_id' => $site_id])
        ->exec();

    $result = [];
    while($row = $rows->fetchRow()) {
        $result[$row['module']] = (@unserialize($row['data']) ?: []) +
            [
                'deactivated' => 0 //Значение на случай отсутствия конфига в БД
            ];
    }
    return $result;
}

/**
 * Обновляет конфигурацию модуля напрямую в БД
 *
 * @param $module_name
 * @param $config_data
 * @return bool
 */
function __MODULE_UPDATE_CONFIG($module_name, $config_data)
{
    if ($config = \RS\Config\Loader::byModule($module_name)) {
        $config['deactivated'] = $config_data['deactivated'];
        return $config->update();
    }
    return false;
}


/**
 * Возвращает true, если лицензия на модуль позволяет модулю быть активным
 *
 * @param string $module_name - имя модуля
 * @param array $data - данные по лицензии
 * @param string $reason - причина результата
 * @return bool
 */
function __MODULE_LICENSE_CHECK($module_name, $data, &$reason = '')
{
    static $is_local;
    if ($is_local === null) {
        $is_local = preg_match('/(.local|.test)$/', strtolower(getenv('HTTP_HOST')));
    }
    if ($is_local) {
        $reason = 'Site in .local zone';
        return true; //В зоне .local всегда модули не отключаются
    }
    if (empty($data)) { //Пустые данные
        $reason = 'Empty license data';
        return false;
    }
    if (isset($data['error'])) { //Если есть ошибки
        $reason = 'Error ' . $data['error'];
        return false;
    }
    if ($data['type'] == 'market') { //Для модуля нет купленной лицензии
        $reason = 'License not found for paid addon';
        return false;
    }
    if ($data['type'] == 'none') { //Модуля нет в маркетплейсе
        $reason = 'Addon don`t need license';
        return true;
    }
    if ($data['type'] == 'free') { //Модуль бесплатный
        $reason = 'Addon is free';
        return true;
    }

    $expire_compare = ($data['expire'] == 0 || $data['expire'] > time());
    $reason = 'License expire = ' . $expire_compare;

    return $expire_compare;
}

/**
 * Возвращает данные по лицензии для модуля
 *
 * @param ModuleLicense $module_license
 * @return array|false
 */
function __MODULE_LICENSE_GET_DATA($module_license)
{
    if ($module_license['data']) {
        return __MODULE_LICENSE_DECODE_LICENSE_DATA($module_license['data']);
    }
    return false;
}

/**
 * Расшифровывает данные по модульной лицензии
 *
 * @param string $data
 * @return array|false
 */
function __MODULE_LICENSE_DECODE_LICENSE_DATA($data)
{
    $public_key = '-----BEGIN PUBLIC KEY-----' . "\n" .
        'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAysEfpmUOxkK2GRx9wEsv' . "\n" .
        'lN+7pVYNadDpv99KYM4LjaEVFQBuz+U55MtJxMkjGRCvNmCmZGQQKMC5GNpBL/o5' . "\n" .
        'k1glUpf5nfNhkv/whZZgkoDfzz2xFWDavMdUMSHXKD48tvicNljAu/ivJXxvXFMS' . "\n" .
        'Sm2vvQ5IEDAHJpb6DqHcka8iuL97nIGpMDryKCADw02v6cyTkTgoB7FeTkOThL1Z' . "\n" .
        'SOcoLFPWgEfnKmWrkLlSnisPih1VyFxvTJOHfWjklk1NXBskGoSFTlM9LsYuyp84' . "\n" .
        'awt0nol0s+ic9R4+Umf2qwKBgBZ0Q2sNkxCLeqDU0Rbj2C3LqyP0HLCIbKg18I4+' . "\n" .
        'iQIDAQAB' . "\n" .
        '-----END PUBLIC KEY-----';

    return @unserialize(__decryptRSA($public_key, hex2bin($data)));
}

/**
 * Возвращает данные по лицензиям для всех модулей, подгружает недостающие данные с сервера RS
 * Лицензия будет возвращаться, только если она полностью соответствует основной лицензии текущего магазина или аккаунту облака
 *
 * @return array
 * @throws \RS\Event\Exception
 * @throws \RS\Exception
 * @throws \Exception
 */
function __MODULE_LICENSE_GET_ALL()
{
    static $licenses = null;

    if ($licenses === null) {
        $list = __MODULE_LICENSE_GET_ALL_LIST();

        try {
            /** @var ModuleLicense[] $loaded_licenses */
            $loaded_licenses = (new \RS\Orm\Request())
                ->from(new ModuleLicense())
                ->objects(null, 'module');
        } catch(\Exception $e) {
            if ($e->getCode() == 1146) {
                return [];
            }
            throw $e;
        }


        $tmp_licenses = [];
        $not_loaded = [];

        //Формируем список существующих и отсутствующих лицензий
        foreach ($list as $module_name) {
            if (isset($loaded_licenses[$module_name])) {
                $data = __MODULE_LICENSE_DECODE_LICENSE_DATA($loaded_licenses[$module_name]['data']);
                if ($data) {
                    $tmp_licenses[$module_name] = $data;
                } else {
                    $not_loaded[] = $module_name;
                }
            } else {
                $not_loaded[] = $module_name;
            }
        }

        //Догружаем недостающие лицензии
        if (!empty($not_loaded)) {
            $loaded_license_data = __MODULE_LICENSE_LOAD($not_loaded);
            if (is_array($loaded_license_data)) {
                $tmp_licenses = array_merge($tmp_licenses, $loaded_license_data);
            }
        }

        //Проверяем корректность лицензий

        if (defined('CLOUD_UNIQ')) {
            $shop_type = 'cloud';
            $shop_uniq = CLOUD_UNIQ;
        } else {
            $shop_type = 'box';
            $script_license = false;
            __GET_LICENSE_LIST($script_license);
            $shop_uniq = $script_license ? $script_license['domain'] : false;
        }

        $licenses = []; //Формируем итоговый список
        foreach ($list as $module_name) {

            if (!isset($tmp_licenses[$module_name])) {
                $licenses[$module_name] = [
                    'error' => t('Не удалось загрузить данные по лицензии'),
                    'type' => 'market',
                    'is_working' => false,
                    'working_reason' => 'License not loaded',
                ];
            } else {
                $data = $tmp_licenses[$module_name];

                if (!empty($data['type']) && $data['type'] == 'exist') {
                    $shop_uniq_dev = $shop_uniq !== false ? preg_replace('#^dev\.#', '', $shop_uniq) : $shop_uniq;

                    if ($shop_uniq === false) {
                        $data['error'] = t('Лицензия не активна, так как отсутствует основная лицензия для магазина');
                    } elseif (($shop_type != $data['shop_type']) || ($shop_uniq != $data['shop_uniq'] && $shop_uniq_dev != $data['shop_uniq'])) {
                        $data['error'] = t('Домен лицензии модуля не соответствует основному домену лицензии для магазина');
                    }
                }

                $licenses[$module_name] = $data;
                $licenses[$module_name]['is_working'] = __MODULE_LICENSE_CHECK($module_name, $data, $reason);
                $licenses[$module_name]['working_reason'] = $reason;
            }
        }
    }

    return $licenses;
}

/**
 * Возвращает общий список названий модулей и тем оформления
 *
 * @return string[]
 */
function __MODULE_LICENSE_GET_ALL_LIST()
{
    $theme_manager = new \RS\Theme\Manager();

    $modules_list = __MODULE_LICENSE_GET_MODULES();
    $theme_list = array_map(function ($i) {
        return "#$i";
    }, array_keys($theme_manager->getList()));

    return array_merge($modules_list, $theme_list);
}

/**
 * Загружает данные по модульным лицензиям с сервера RS
 *
 * @param string|string[] $modules
 * @return array | string Возвращает строку в случае ошибки, иначе - массив
 * @throws \RS\Event\Exception
 * @throws \RS\Exception
 */
function __MODULE_LICENSE_LOAD($modules)
{
    if (!is_array($modules)) {
        $modules = [$modules];
    }

    if (defined('CLOUD_UNIQ')) {
        $cloud_uniq = CLOUD_UNIQ;
    } else {
        $licenses = (new \RS\Orm\Request())
            ->from(\Main\Model\Orm\License::_getTable())
            ->exec()->fetchSelected(null, 'license');
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'timeout' => 7,
            'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
            'content' => http_build_query([
                'Act' => 'getLicenseData',
                'modules' => $modules,
                'shop_type' => defined('CLOUD_UNIQ') ? 'cloud' : 'box',
                'licenses' => $licenses ?? null,
                'cloud_uniq' => $cloud_uniq ?? null,
                'product' => SCRIPT_TYPE,
                'domain' => getenv('HTTP_HOST')
            ]),
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    $response = @file_get_contents(MODULE_LICENSE_URL, false, $context);
    if ($response === false) {
        return t('Не удалось выполнить запрос к серверу ReadyScript');
    }

    $response_data = @json_decode($response, true);

    if (is_array($response_data)) {
        if (!empty($response_data['success'])) {

            $result = [];
            foreach ($response_data['licenses'] as $module => $license_data) {
                $decoded_data = __MODULE_LICENSE_DECODE_LICENSE_DATA($license_data);
                if ($decoded_data) {
                    $result[$module] = $decoded_data;
                }

                $module_license = new ModuleLicense();
                $module_license['module'] = $module;
                $module_license['data'] = $license_data;
                $module_config = \RS\Config\Loader::byModule($module);
                if ($module_config && $module_config['deactivated'] && __MODULE_LICENSE_CHECK($module, $decoded_data, $reason)) {
                    $module_config['deactivated'] = 0;
                    if ($module_config->update()) {
                        __MODULE_LICENSE_LOG(t('Активирован модуль %0 (reason = %1)', [$module, $reason]));
                    }
                }
                if ($module_license->replace()) {
                    $text = t('Загружена лицензия для модуля %0', [$module]);
                    if (!empty($decoded_data['type'])) {
                        $text .= " (type = {$decoded_data['type']})";
                    }
                    if (!empty($decoded_data['error'])) {
                        $text .= " (error = {$decoded_data['error']})";
                    }
                    __MODULE_LICENSE_LOG($text);
                }
            }
            return $result;
        } elseif (!empty($response_data['error'])) {
            return t('Ошибка: %0', [$response_data['error']]);
        }
    }

    return t('Получены некорректные данные от сервера ReadyScript');
}

/**
 * Возвращает конфиги модулей у которых должны быть лицензии
 *
 * @return \RS\Orm\ConfigObject[]
 */
function __MODULE_LICENSE_GET_MODULES()
{
    $module_manager = new \RS\Module\Manager();
    $list = $module_manager->getList();

    $result = [];
    foreach ($list as $module_name => $module_item) {
        if (!__MODULE_LICENSE_IS_SYSTEM_MODULE($module_name)) {
            $result[] = $module_name;
        }
    }

    return $result;
}

/**
 * Удаляет сохранённые данные по лицензии модуля
 *
 * @param string|string[] $modules
 */
function __MODULE_LICENSE_DELETE_DATA($modules)
{
    if (!is_array($modules)) {
        $modules = [$modules];
    }

    (new \RS\Orm\Request())
        ->delete()
        ->from(ModuleLicense::_getTable())
        ->whereIn('module', $modules)
        ->exec();
}

/**
 * Возвращает является ли модуль системным
 *
 * @param string $module_name - имя модуля
 * @return bool
 */
function __MODULE_LICENSE_IS_SYSTEM_MODULE($module_name)
{
    static $system_modules;
    if ($system_modules === null) { //Фикс, чтобы обфускация проходила корректно
        $system_modules = [
            'affiliate', 'alerts', 'antivirus', 'article', 'atolonline', 'banners', 'catalog', 'cdn', 'comments', 'crm', 'emailsubscribe', 'exchange', 'export', 'extcsv',
            'externalapi', 'feedback', 'files', 'install', 'kaptcha', 'main', 'marketplace', 'menu', 'mobilemanagerapp', 'mobilesiteapp', 'modcontrol', 'notes', 'pageseo',
            'partnership', 'photo', 'pushsender', 'search', 'shop', 'site', 'sitemap', 'siteupdate', 'statistic', 'support', 'tags', 'templates', 'users', 'yandexmarketcpa',
            'modcloud', 'photogalleries', 'faq', '#default', "#fashion", "#flatlines", "#perfume", "#young",
        ];
    }

    return in_array($module_name, $system_modules);
}

/**
 * Записывает лог по модульной лицензии
 *
 * @param string $text
 */
function __MODULE_LICENSE_LOG($text)
{
    if (\Setup::$MODULE_LICENSE_LOG_ENABLE) {
        static $log_file;
        if ($log_file === null) {
            $log_file = \Setup::$PATH . \Setup::$LOGS_DIR . '/module_license.log';
        }

        file_put_contents($log_file, date('[d.m.Y H:i:s] ') . $text . "\n", FILE_APPEND);
        clearstatcache();
        $size = filesize($log_file);
        $max_size = 1048576;

        if ($size > $max_size) {
            $content        = file_get_contents($log_file);
            $begin          = $size - (int)(0.75 * $max_size);
            $new_content    = substr($content, $begin);
            file_put_contents($log_file, $new_content);
        }
    }
}

$__CHR = rand(0, 999999);
__CHECK_DOMAIN_HASH();
__CH__(md5('register_licenser'), $__CHR);


__MODULE_LICENSE_GENERAL_CHECK();


if (defined('__R_' . $__CHR) && constant('__R_' . $__CHR) === true) {
    //Регистрируем функции в глобальной области видимости

    function __LICENSE_CHECK()
    {
        return true;
    }

    //Can do it :)
    function __CDI()
    {
        return true;
    }

    function __GET_MAX_SITE_LIMIT()
    {
        return MAX_SITE_LIMIT;
    }

    function __CAN_ADD_SITE()
    {
        $sites = OrmRequest::make()->from(new SiteOrmSite())->count();
        return __GET_MAX_SITE_LIMIT() == 0 || $sites < __GET_MAX_SITE_LIMIT();
    }

    function __GET_ADMIN_NOTICE()
    {
        $__error_reporting = error_reporting(__E_L);
        $result = false;
        switch (SCRIPT_TRIAL_STATUS) {
            case 'LOCAL':
            case 'ACTIVE':
                $result = t('Осталось %0 дней пробного периода. <u>Приобрести лицензию</u>...', [SCRIPT_TRIAL_DAYS]);
                break;
            case 'OVERDUE':
                $result = t('Пробный период истек. <u>Приобрести лицензию</u>...');
                break;
        }
        if (TEMPORARY_EXPIRE !== false) {
            $days = ceil((TEMPORARY_EXPIRE - time()) / (60 * 60 * 24));
            if ($days <= 7) {
                $result = t('Осталось %0 дней до истечения лицензии', [$days]);
            }
        }
        if (SITE_LIMIT_ERROR || SCRIPT_TYPE_ERROR) {
            $result = t('Имеются нарушения лицензии. <u>Подробнее</u>...');
        }

        error_reporting($__error_reporting);
        return $result;
    }


    function __GET_LICENSE_TABLE_LIST()
    {
        $__error_reporting = error_reporting(__E_L);

        $script_license = null;
        $list = __GET_LICENSE_LIST($script_license);

        $types = [
            'script' => t('Основная лицензия'),
            'additional' => t('Дополнительная лицензия'),
            'update' => t('Лицензия на обновление'),
            'upgrade' => t('Лицензия на комплектацию')
        ];

        $result = [];
        foreach ($list as $key => $license) {
            $result[$key] = $license + [
                    'license_key' => $key,
                    'license_type_str' => (!empty($license['expire_month']) && $license['type'] == 'script') ? t('Временная лицензия') : $types[$license['type']],
                ];
        }

        error_reporting($__error_reporting);
        return $result;
    }
}
unset($__CHR);
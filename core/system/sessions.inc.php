<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
session_name( md5(\Setup::$SECRET_KEY.\Setup::$SECRET_SALT) );
session_set_cookie_params(0, '/', null, false, true);
session_start();
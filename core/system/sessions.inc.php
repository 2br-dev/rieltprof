<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
session_name( \Setup::getSessionName() );
session_set_cookie_params(0, '/', null, false, true);
session_start(['use_only_cookies' => 0]);
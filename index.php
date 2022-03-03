<?php
/**
* Единая точка входа в ReadyScript.CMS
*/
require('setup.inc.php');
use \RS\Language\Core as LangCore;

//Добавляем языковые функции во front-end
if (!\RS\Http\Request::commonInstance()->isAjax()) {
    $app = \RS\Application\Application::getInstance();
    $app->addJs('corelang.js', null, BP_COMMON);
    $app->addJsVar(array(
        'baseLang' => LangCore::getBaseLang(),
        'lang' => LangCore::getCurrentLang()
    ));

    if (LangCore::issetJsMessages()) {
        $app->addJs(LangCore::getScriptFilename(), null, BP_ROOT, true);
    }
}

//Запускаем диспетчер маршрутов
\RS\Event\Manager::fire('start');
\RS\Router\Manager::obj()->dispatch();
\RS\Event\Manager::fire('stop');
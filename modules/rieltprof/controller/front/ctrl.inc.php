<?php

namespace rieltprof\Controller\Front;

use RS\Controller\Front;

/**
 * Фронт контроллер
 */
class Ctrl extends Front
{
    function actionIndex()
    {
        return $this->result->setTemplate('test.tpl');
    }
}

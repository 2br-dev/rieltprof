<?php

namespace rieltprof\Controller\Front;

use RS\Controller\Front;

/**
 * Фронт контроллер
 */
class Objects extends Front
{
    function actionIndex()
    {
        return $this->result->setTemplate('test.tpl');
    }

    public function actionRepublish()
    {
        $id = $this->request('id', TYPE_INTEGER);
        $type_object = $this->request('type', TYPE_STRING);
        $config = \RS\Config\Loader::byModule('rieltprof');
        /**
         * @var \Catalog\Model\Orm\Product $object
         */
        $object = $config->getObjectByType($type_object, $id);
        $object['public'] = 1;
        $object['actual_on_date'] = date('Y-m-d');
        $success = $object->update();
        $this->result->setSuccess($success);
        $this->result->addSection('reloadPage', $success);
        return $this->result;
    }
}

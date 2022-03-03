<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Front;

use Catalog\Model\DirApi;
use Designer\Model\BlocksApi;
use Designer\Model\RenderApi;
use RS\Controller\Front;

class ProductsList extends Front
{
    /**
     * Получение контента для списка товаров
     *
     * @return \RS\Controller\Result\Standard|void
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    function actionIndex()
    {
        $alias = $this->url->request('category', TYPE_STRING);
        $id   = $this->url->request('id', TYPE_STRING);
        $page = $this->url->request('p', TYPE_INTEGER, 1);

        if (empty($alias)){
            return $this->e404(t('Категория не передана'));
        }
        if (empty($id)){
            return $this->e404(t('Id атома не передано'));
        }
        $api = new DirApi();
        if ($alias != 'all'){
            $category = $api->getById($alias);

            if (!$category['id']){
                return $this->e404(t('Категория не найдена'));
            }
        }

        $blockApi = new BlocksApi();
        $atom = $blockApi->getAtomById($id);

        if (empty($atom)){
            return $this->e404(t('Атома не переден'));
        }

        $renderApi = new RenderApi();
        $atom['page'] = $page;
        $html = $renderApi->getElementHTML($atom);

        return $this->result->setSuccess(true)->addSection('html', $html);
    }
}


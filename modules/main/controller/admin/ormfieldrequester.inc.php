<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Front;
use RS\Controller\Exception as ControllerException;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Exception as RSException;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Module\AbstractModel\TreeList\TreeListOrmIterator;
use RS\Orm\AbstractObject;
use RS\Orm\Type\AbstractType;
use RS\View\Engine as ViewEngine;

/**
 * Класс, позвляющий взаимодействовать полями orm объектов
 */
class OrmFieldRequester extends Front
{
    /**
     * Возвращает html для указанных ветвей древовидного списка
     * в запросе необходимо передать параметр - int[] $ids - список id
     * в запросе можно передать параметр - bool $recursive -
     *
     * @return ResultStandard
     * @throws ControllerException
     * @throws \SmartyException
     * @throws RSException
     */
    public function actionGetTreeChilds()
    {
        $field = $this->getFieldFromRequest();
        $ids = $this->url->request('ids', TYPE_ARRAY);
        $load_recursive = $this->url->request('recursive', TYPE_BOOLEAN);

        $branches = $this->recursiveGetTreeBranchesHtml($field->getTreeList(), $ids, $load_recursive);

        return $this->result->setSuccess(true)->addSection('branches', $branches);
    }

    /**
     * Рекурсивно обходит дерево, собирая html искомых ветвей
     *
     * @param AbstractTreeListIterator $tree - дерево элементов
     * @param int[] $ids - список id искомых ветвей
     * @param bool $load_recursive - рендерить дочерние ветви
     * @param string $id_field - название поля идентификатора
     * @param string[] $result - результат работы функции
     * @return string[]
     * @throws \SmartyException
     */
    protected function recursiveGetTreeBranchesHtml(AbstractTreeListIterator $tree, array $ids, $load_recursive = false, $id_field = null, array $result = [])
    {
        if (!$id_field && $tree instanceof TreeListOrmIterator) {
            $id_field = $tree->getApi()->getIdField();
        }

        foreach ($tree as $node) {
            if (in_array($node->getID(), $ids)) {
                $view = new ViewEngine();
                $view->assign([
                    'iterator' => $node->getChilds(),
                    'load_recursive' => $load_recursive,
                ]);
                $result[$node->getID()] = $view->fetch('%system%/coreobject/type/form/treelistbox_branch.tpl');
            }
            if ($node->getChildsCount()) {
                $result = $this->recursiveGetTreeBranchesHtml($node->getChilds(), $ids, $load_recursive, $id_field, $result);
            }
        }

        return $result;
    }

    /**
     * Возвращает поле orm объекта из параметров запроса
     *
     * @return AbstractType
     * @throws ControllerException
     * @throws RSException
     */
    protected function getFieldFromRequest()
    {
        $class = $this->url->request('class', TYPE_STRING);
        $method_name = $this->url->request('method_name', TYPE_STRING);
        $property = $this->url->request('property', TYPE_STRING);

        if (!$class) {
            throw new ControllerException(t('Не указан класс orm объекта'));
        }
        if (!$property) {
            throw new ControllerException(t('Не указано свойство orm объекта'));
        }

        $object = false;
        if (class_exists($class)) {
            $object = new $class();
        }

        if ($object === false) {
            throw new ControllerException(t('Указанный класс не существует'));
        }
        $right = ($object instanceof AbstractObject) ? $object->getRightRead() : DefaultModuleRights::RIGHT_READ ;
        if (Rights::CheckRightError($object, $right, true)) {
            throw new ControllerException(t('Недостаточно прав для просмотра свойств указанного класса'));
        }

        if ($method_name) {
            $form_method_name = 'get' . $method_name . 'Object';
            if (method_exists($object, $form_method_name)) {
                $properties = $object->$form_method_name()->getPropertyIterator();
            } else {
                throw new RSException(t('Указанный метод не существует в классе orm объекта'));
            }
        } else {
            $properties = $object->getPropertyIterator();
        }
        if (!isset($properties[$property])) {
            throw new ControllerException(t('Указанное свойство не существует в классе orm объекта'));
        }

        return $properties[$property];
    }
}

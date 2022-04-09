<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;
use RS\Orm\FormObject;
use RS\View\Engine;

/**
 * Класс описывает базовые методы для типов взаимосвязи объектов
 */
abstract class AbstractType
{
    protected $last_objects_template;

    /**
     * Возвращает внутренний идентификатор типа связи
     *
     * @return string
     */
    public static function getId()
    {
        $class_name = strtolower(trim(str_replace('\\', '-', get_called_class()),'-'));
        $id = str_replace('-model-links-type', '', $class_name);

        if ($id == $class_name) {
            throw new \RS\Exception(t('Класс типа связи должен находиться в пространстве имен: ИМЯ_МОДУЛЯ\Model\Links\Type '));
        }

        return $id;
    }

    /**
     * Возвращает имя закладки, характеризующей данную связь
     *
     * @return string
     */
    abstract public function getTabName();

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    abstract public function getTabForm();

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    abstract public function getLinkText();

    /**
     * Возвращает ссылку, которую нужно установить к тексту, при визуализации связи
     *
     * @return mixed
     */
    abstract public function getLinkUrl();

    /**
     * Возвращает объект необходимого класса по идентификатору типа связи
     *
     * @param string $id ID типа связи
     * @return AbstractType
     * @throws \RS\Exception
     */
    public static function makeById($id)
    {
        $class_name = preg_replace('/-/', '-model-links-type-', $id, 1, $count);
        if ($count) {
            $class_name = str_replace('-', '\\', $class_name);
            if (class_exists($class_name)) {
                return new $class_name();
            }
        }

        throw new \RS\Exception(t('Класс типа связи `%0` не найден'), [$class_name]);
    }

    /**
     * Возвращает ID связываемого объекта, опираясь на данные заполненного объекта формы
     *
     * @param FormObject|array $tab_form
     * @return integer
     */
    abstract public function getLinkIdByTabFormObject($tab_form);

    /**
     * Возвращает HTML-код одной связи
     *
     * @return string
     */
    public function getLinkView()
    {
        $view = new Engine();
        $view->assign([
            'link' => $this
        ]);

        return $view->fetch('%crm%/admin/links/link_item.tpl');
    }

    /**
     * Возвращает true, если объект находится на другом сайте
     */
    public function isObjectOtherSite()
    {
        return false;
    }

    /**
     * Возвращает последние $limit объектов, с которыми возможно установить связь
     *
     * @param integer $limit Количество элементов
     * @return []
     */
    public function getLastObjects($limit = null)
    {
        return [];
    }

    /**
     * Возвращает готовый HTML со списком объектов, с которыми возможно установить сязь
     *
     * @param integer $limit
     * @return string
     * @throws \SmartyException
     */
    public function getLastObjectsHtml($limit = null)
    {
        if ($this->last_objects_template) {
            $last_objects = $this->getLastObjects($limit);
            if ($last_objects) {
                $view = new Engine();
                $view->assign([
                    'last_objects' => $last_objects
                ]);
                return $view->fetch($this->last_objects_template);
            }
        }

        return '';
    }
}
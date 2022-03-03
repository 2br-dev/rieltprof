<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

use RS\Http\Request;
use RS\Router\Manager;

/**
 * Фильтр по пользователю. Отображается в виде поля c autocomplete.
 */
class Product extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/product.tpl';

    protected
        $request_url;

    function __construct($key, $title, $options = [])
    {
        $this->attr = [
            'class' => 'w150'
        ];
        parent::__construct($key, $title, $options);
        @$this->attr['class'] .= ' object-select';
    }

    /**
     * Возвращает URL для поиска пользователя
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: Manager::obj()->getAdminUrl('ajaxProduct', null, 'catalog-ajaxlist');
    }

    /**
     * Устанавливает URL для поиска пользователя
     *
     * @param string $url
     * @return User
     */
    function setRequestUrl($url)
    {
        $this->request_url = $url;
        return $this;
    }

    function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];
        $product_id = $this->getNonEmptyValue();
        if ($product_id !== null) {
            $without_this = $current_filter_values;
            unset($without_this[$this->getKey()]);

            $prefilters = '';
            foreach($this->getPrefilters() as $prefilter) {
                $prefilters .= $prefilter->getTextValue().' ';
            }
            $product = new \Catalog\Model\Orm\Product($product_id);
            $exclude = array_combine($exclude_keys, array_fill(0, count($exclude_keys), null));

            $parts[] = [
                'title' => $product['title'],
                'href_clean' => Request::commonInstance()->replaceKey([$this->wrap_var => $without_this] + $exclude) //Url, для очистки данной части фильтра
            ];
        }
        return $parts;
    }
}
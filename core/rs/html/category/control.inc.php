<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Category;

use RS\Html\AbstractHtml;

class Control extends AbstractHtml
{
    protected $auto_fill = true;
    /** @var Element */
    protected $category;

    function __construct(array $options)
    {
        parent::__construct($options);
        if ($this->auto_fill) $this->fill();
    }

    function setAutoFill($autofill)
    {
        $this->auto_fill = $autofill;
    }

    function setCategory($category)
    {
        $this->category = $category;
    }

    function getCategory()
    {
        return $this->category;
    }

    function fill()
    {
    }

    function getView($local_options = [])
    {
        return $this->getCategory()->getView($local_options);
    }

    function getPathView()
    {
        return $this->getCategory()->getPathView();
    }
}

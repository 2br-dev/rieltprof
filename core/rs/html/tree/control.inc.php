<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Tree;

use RS\Html\AbstractHtml;

class Control extends AbstractHtml
{
    protected $auto_fill = true;
    /** @var Element */
    protected $tree;

    function __construct(array $options)
    {
        parent::__construct($options);
        if ($this->auto_fill) $this->fill();
    }

    function setAutoFill($autofill)
    {
        $this->auto_fill = $autofill;
    }

    function setTree($tree)
    {
        $this->tree = $tree;
    }

    function getTree()
    {
        return $this->tree;
    }

    function fill()
    {
    }

    function getView($local_options = [])
    {
        return $this->getTree()->getView($local_options);
    }

    function getPathView()
    {
        return $this->getTree()->getPathView();
    }
}

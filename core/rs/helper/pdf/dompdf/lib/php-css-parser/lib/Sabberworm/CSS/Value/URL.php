<?php

namespace Sabberworm\CSS\Value;


use Sabberworm\CSS\OutputFormat;

class URL extends PrimitiveValue {

	private $oURL;

	public function __construct(CSSString $oURL, $iLineNo = 0) {
		parent::__construct($iLineNo);
		$this->oURL = $oURL;
	}

	public function setURL(CSSString $oURL) {
		$this->oURL = $oURL;
	}

	public function getURL() {
		return $this->oURL;
	}

	public function __toString() {
		return $this->render(new OutputFormat());
	}

	public function render(OutputFormat $oOutputFormat) {
		return "url({$this->oURL->render($oOutputFormat)})";
	}

}
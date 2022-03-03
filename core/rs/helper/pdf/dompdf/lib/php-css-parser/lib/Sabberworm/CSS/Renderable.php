<?php

namespace Sabberworm\CSS;

interface Renderable {
	public function __toString();
	public function render(OutputFormat $oOutputFormat);
	public function getLineNo();
}
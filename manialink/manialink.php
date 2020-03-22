<?php

include('element.php');

class Manialink {
	
	private $elements;
	private $header;
	
	public function __construct($manialinkId) {
		$this->header = '<manialink id="' . $manialinkId . '">' . PHP_EOL;
	}

	public function append($element) {
		$this->elements[sizeof($this->elements)] = $element;
	}
	
	public function buildManialink() {
		$manialink = $this->header;
		
		for($i = 0; $i < sizeof($this->elements); $i++) {
			$manialink .= $this->elements[$i]->buildElement();
		}
		
		$manialink .= '</manialink>';
		
		echo $manialink;
	}
	
}


?>
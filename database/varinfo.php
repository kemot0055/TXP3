<?php

class VarInfo {
	
	private $varName;
	private $varValue;
	
	public function __construct($varName, $varValue) {
		$this->varName = $varName;
		$this->varValue = $varValue;
	}
	
	public function getName() {
		return $this->varName;
	}
	
	public function getValue() {
		return $this->varValue;
	}
	
	public function setValue($varValue) {
		$this->varValue = $varValue;
	}
	
	public function isString() {
		return is_string($this->varValue);
	}
	
}

?>
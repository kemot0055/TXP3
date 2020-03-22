<?php

class Modification {
	
	private $modId;
	
	private $modName;
	private $modRarity;
	
	private $effectId;
	private $effectValue;
	
	public function __construct($modId, $modValue, Database $database) {
		$this->modId = $modId;
		
		$this->modName = $database->getVarInfoFromModification($modId, 'mod_name');
		$this->modRarity = $database->getVarInfoFromModification($modId, 'mod_rarity');
		
		$this->effectId = $database->getVarInfoFromModification($modId, 'effect_id');
		$this->effectValue = $modValue;
	}
	
	public function getId() {
		return $this->modId;
	}
	
	public function getName() {
		return $this->modName->getValue();
	}
	
	public function getRarity() {
		return $this->modRarity->getValue();
	}
	
	public function getEffectId() {
		return $this->effectId->getValue();
	}
	
	public function getEffectValue() {
		return $this->effectValue;	
	}
	
	public function getRanges() {
		return TXP::getModificationRanges( $this->modId );
	}
	
}

class ModificationRanges {
		
	const RANGE_LOW = 0;
	const RANGE_HIGH = 1;
	
	private $lowRange;
	private $highRange;
	
	public function __construct( $modificationId, Database $database ) {
		$this->lowRange = $database->getModificationRange(ModificationRanges::RANGE_LOW, $modificationId);
		$this->highRange = $database->getModificationRange(ModificationRanges::RANGE_HIGH, $modificationId);
	}
	
	public function getLower() {
		return $this->lowRange;
	}
	
	public function getHigher() {
		return $this->highRange;	
	}
	
}

?>
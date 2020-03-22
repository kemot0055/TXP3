<?php

interface PaintableNeon {
	
	public function paintNick($nickname);
	
	public function isStrange();
	
}

class Neon extends CarPart implements PaintableNeon {
	
	private $neonType;
	
	public function __construct( $itemId, Database $database ) {
		parent::__construct($itemId, $database);
		$this->neonType = intval($itemId[3]);
	}
	
	public function getPartTier() {}
	
	public function getPartValues() {
		return array();
	}
	
	public function getPartType() {
		return CarPart::TYPE_NEON;
	}
	
	public function getPartManufacturer() {}
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack) {
		return true;
	}
	
	public function paintNick($nickname) {
		$paintCode = self::getPaintCode();
		return ($paintCode . $nickname);
	}
	
	public function isStrange() {
		return false;
	}
	
	public function getPaintCode() {
		switch ( $this->neonType ) {
			case 0: return '$000'; break;
			case 1: return '$0ff'; break;
			case 2: return '$0f0'; break;
			case 3: return '$00f'; break;
			case 4: return '$f00'; break;
		}
	}
	
}

class NeonStrange extends Neon {

	public function isStrange() {
		return true;
	}
	
}

class NeonGradient extends NeonStrange {
	
	public function paintNick($nickname) {
		return 'gradient';
	}
	
	public function getPaintCode() {
		return '$000';
	}
	
}

?>

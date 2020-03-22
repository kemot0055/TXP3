<?php

class FirstAdditional extends CarPart {
	
	public function getPartTier() {
		
	}
	
	public function getPartValues() {
		
	}
	
	public function getPartType() {
		return CarPart::TYPE_FIRST_ADDITIONAL;
	}
	
	public function getPartManufacturer() {
		
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack) {
		
	}
	
}

class SecondAdditional extends CarPart {
	
	public function getPartTier() {
		
	}
	
	public function getPartValues() {
		
	}
	
	public function getPartType() {
		return CarPart::TYPE_SECOND_ADDITIONAL;
	}
	
	public function getPartManufacturer() {
	
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack) {
		
	}
	
}

?>
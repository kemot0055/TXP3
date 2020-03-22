<?php

class Gearbox extends CarPart {
	
	private $manufacturerId;
	private $gearboxBonusId;
	private $gearboxTier;
	
	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);
		
		$this->manufacturerId = intval($itemId[1]);
		$this->gearboxBonusId = intval($itemId[2]);
		$this->gearboxTier = intval($itemId[3]);
	}
	
	public function getPartManufacturer() {
		return $this->manufacturerId;
	}
	
	public function getPartType() {
		return CarPart::TYPE_GEARBOX;
	}
	
	public function getPartTier() {
		return ($this->gearboxTier + 1);
	}
	
	public function getPartValues() {
		if ( $this->manufacturerId == 0 ) {
			if ( $this->gearboxTier == 0 ) { 
				$values = array(
					Effects::PART_ACCELERATION => 18,
					Effects::PART_MAX_SPEED => 16
				);
			} else if ( $this->gearboxTier == 1 ) { 
				$values = array(
					Effects::PART_ACCELERATION => 32,
					Effects::PART_MAX_SPEED => 30,
					Effects::PART_STEERING => 2
				);
			} else if ( $this->gearboxTier == 2 ) { 
				$values = array(
					Effects::PART_ACCELERATION => 43,
					Effects::PART_MAX_SPEED => 45,
					Effects::PART_STEERING => 4
				);
			} else if ( $this->gearboxTier == 3 ) { 
				$values = array(
					Effects::PART_ACCELERATION => 51,
					Effects::PART_MAX_SPEED => 53,
					Effects::PART_STEERING => 6
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->gearboxTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 26,
					Effects::PART_MAX_SPEED => 25,
					Effects::PART_STEERING => 2
				);
			} else if ( $this->gearboxTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 40,
					Effects::PART_MAX_SPEED => 42,
					Effects::PART_STEERING => 4
				);
			} else if ( $this->gearboxTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 62,
					Effects::PART_MAX_SPEED => 60,
					Effects::PART_STEERING => 8
				);
			} else if ( $this->gearboxTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 73,
					Effects::PART_MAX_SPEED => 71,
					Effects::PART_STEERING => 12
				);
			}
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->gearboxTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 14,
					Effects::PART_MAX_SPEED => 12
				);
			} else if ( $this->gearboxTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 27,
					Effects::PART_MAX_SPEED => 25,
					Effects::PART_STEERING => 1
				);
			} else if ( $this->gearboxTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 38,
					Effects::PART_MAX_SPEED => 41,
					Effects::PART_STEERING => 2
				);
			} else if ( $this->gearboxTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 46,
					Effects::PART_MAX_SPEED => 48,
					Effects::PART_STEERING => 3
				);
			}
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->gearboxTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 23,
					Effects::PART_MAX_SPEED => 21,
					Effects::PART_STEERING => 1
				);
			} else if ( $this->gearboxTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 36,
					Effects::PART_MAX_SPEED => 35,
					Effects::PART_STEERING => 3
				);
			} else if ( $this->gearboxTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 52,
					Effects::PART_MAX_SPEED => 54,
					Effects::PART_STEERING => 6
				);
			} else if ( $this->gearboxTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 62,
					Effects::PART_MAX_SPEED => 58,
					Effects::PART_STEERING => 9
				);
			}
		}
		
		return $values;
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack) {
		$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
		
		if ( $playerGarage == null ) {
			return false;
		}
		
		$car = $playerGarage->getCar();
		
		if ( $car == null ) {
			return false;
		}
		
		if ( $car->getGearboxMaxTier() != 0 ? $car->getGearboxMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}
	
}

?>
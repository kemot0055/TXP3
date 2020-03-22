<?php

class Engine extends CarPart {
	
	private $isElectricEngine;
	private $manufacturerId;
	private $engineBonusId;
	private $engineTier;
	
	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);
		
		$this->isElectricEngine = (intval($itemId[0]) == 4);
		$this->manufacturerId = intval($itemId[1]);
		$this->engineBonusId = intval($itemId[2]);
		$this->engineTier = intval($itemId[3]);
	}
	
	public function getPartType() {
		return CarPart::TYPE_ENGINE;
	}
	
	public function getPartTier() {
		return ($this->engineTier + 1);
	}
	
	public function getEngineBonusId() {
		return $this->engineBonusId;
	}
	
	public function getPartValues() {	
		if ( $this->manufacturerId == 0 ) {
			if ( $this->engineTier == 0 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 24 : 17 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 14 : 16 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 6 : 4 )	
				);
			} else if ( $this->engineTier == 1 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 38 : 26 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 19 : 28 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 13 : 5 )
				);				
			} else if ( $this->engineTier == 2 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 57 : 43 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 28 : 42 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 17 : 9 )
				);				
			} else if ( $this->engineTier == 3 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 80 : 55 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 35 : 51 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 22 : 12 )
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->engineTier == 0 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 34 : 26 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 22 : 30 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 11 : 8 )
				);
			} else if ( $this->engineTier == 1 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 46 : 39 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 29 : 41 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 20 : 10 )
				);
			} else if ( $this->engineTier == 2 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 67 : 52 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 36 : 56 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 22 : 13 )
				);
			} else if ( $this->engineTier == 3 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 92 : 81 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 43 : 79 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 26 : 16 )
				);
			}
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->engineTier == 0 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 22 : 13 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 12 : 15 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 4 : 2 )
				);
			} else if ( $this->engineTier == 1 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 35 : 20 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 17 : 22 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 10 : 3 )
				);
			} else if ( $this->engineTier == 2 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 52 : 38 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 25 : 39 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 15 : 7 )
				);
			} else if ( $this->engineTier == 3 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 76 : 46 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 31 : 44 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 20 : 10 )
				);
			}
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->engineTier == 0 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 28 : 21 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 19 : 23 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 8 : 6 )
				);
			} else if ( $this->engineTier == 1 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 42 : 32 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 24 : 30 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 16 : 7 )
				);
			} else if ( $this->engineTier == 2 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 62 : 46 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 32 : 49 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 19 : 11 )
				);
			} else if ( $this->engineTier == 3 ) {
				$values = array (
					Effects::PART_ACCELERATION => ( $this->isElectricEngine ? 86 : 66 ),
					Effects::PART_MAX_SPEED => ( $this->isElectricEngine ? 39 : 62 ),
					Effects::PART_STEERING => ( $this->isElectricEngine ? 24 : 14 )
				);
			}	
		}
		
		return $values;
	}
	
	public function getPartManufacturer() {
		return $this->manufacturerId;
	}
	
	public function isElectricEngine() {
		return $this->isElectricEngine;
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
		
		if ( $this->isElectricEngine ? $car->getType() != Car::TYPE_ELECTRIC : $car->getType() != Car::TYPE_COMBUSTION ) {
			return false;
		}
		
		if ( $car->getEngineMaxTier() != 0 ? $car->getEngineMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}
	
}

?>
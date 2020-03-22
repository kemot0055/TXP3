<?php

class Tyres extends CarPart {

	private $manufacturerId;
	private $tyresTier;
	private $tyresBonusId;

	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);

		$this->manufacturerId = intval($itemId[1]);
		$this->tyresBonusId = intval($itemId[2]);
		$this->tyresTier = intval($itemId[3]);
	}

	public function getPartTier() {
		return ($this->tyresTier + 1);
	}
	
	public function getPartValues() {
		if ( $this->manufacturerId == 0 ) {
			if ( $this->tyresTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 5,
					Effects::PART_MAX_SPEED => 3,
					Effects::PART_STEERING => 27
				);
			} else if ( $this->tyresTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 9,
					Effects::PART_MAX_SPEED => 5,
					Effects::PART_STEERING => 35
				);				
			} else if ( $this->tyresTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 12,
					Effects::PART_MAX_SPEED => 8,
					Effects::PART_STEERING => 46
				);
			} else if ( $this->tyresTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 16,
					Effects::PART_MAX_SPEED => 12,
					Effects::PART_STEERING => 62
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->tyresTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 10,
					Effects::PART_MAX_SPEED => 5,
					Effects::PART_STEERING => 38
				);
			} else if ( $this->tyresTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 16,
					Effects::PART_MAX_SPEED => 9,
					Effects::PART_STEERING => 51
				);
			} else if ( $this->tyresTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 23,
					Effects::PART_MAX_SPEED => 13,
					Effects::PART_STEERING => 72
				);
			} else if ( $this->tyresTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 28,
					Effects::PART_MAX_SPEED => 18,
					Effects::PART_STEERING => 90
				);
			}	
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->tyresTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 3,
					Effects::PART_MAX_SPEED => 2,
					Effects::PART_STEERING => 22
				);
			} else if ( $this->tyresTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 7,
					Effects::PART_MAX_SPEED => 4,
					Effects::PART_STEERING => 30
				);
			} else if ( $this->tyresTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 9,
					Effects::PART_MAX_SPEED => 6,
					Effects::PART_STEERING => 41
				);
			} else if ( $this->tyresTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 12,
					Effects::PART_MAX_SPEED => 10,
					Effects::PART_STEERING => 52
				);
			}	
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->tyresTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 8,
					Effects::PART_MAX_SPEED => 4,
					Effects::PART_STEERING => 31
				);
			} else if ( $this->tyresTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 13,
					Effects::PART_MAX_SPEED => 7,
					Effects::PART_STEERING => 48
				);
			} else if ( $this->tyresTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 17,
					Effects::PART_MAX_SPEED => 11,
					Effects::PART_STEERING => 64
				);
			} else if ( $this->tyresTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 22,
					Effects::PART_MAX_SPEED => 15,
					Effects::PART_STEERING => 79
				);
			}	
		}
		
		return $values;
	}
	
	public function getPartManufacturer() {
		return $this->manufacturerId;	
	}
	
	public function getPartType() {
		return CarPart::TYPE_TYRES;
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $itemStack) {
		$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
		
		if ( $playerGarage == null ) {
			return false;
		}
		
		$car = $playerGarage->getCar();
		
		if ( $car == null ) {
			return false;
		}
		
		if ( $car->getTyresMaxTier() != 0 ? $car->getTyresMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}

}

?>
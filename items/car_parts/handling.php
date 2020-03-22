<?php

class Handling extends CarPart {
	
	private $manufacturerId;
	private $handlingTier;
	private $handlingBonusId;
	
	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);
		
		$this->manufacturerId = intval($itemId[1]);
		$this->handlingBonusId = intval($itemId[2]);
		$this->handlingTier = intval($itemId[3]);
	}
	
	public function getPartManufacturer() {
		return $this->manufacturerId;
	}
	
	public function getPartTier() {
		return ($this->handlingTier + 1);
	}
	
	public function getPartValues() {
		if ( $this->manufacturerId == 0 ) {
			if ( $this->handlingTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 2,
					Effects::PART_MAX_SPEED => 3,
					Effects::PART_STEERING => 21
				);
			} else if ( $this->handlingTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 5,
					Effects::PART_MAX_SPEED => 7,
					Effects::PART_STEERING => 28
				);
			} else if ( $this->handlingTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 8,
					Effects::PART_MAX_SPEED => 9,
					Effects::PART_STEERING => 41
				);
			} else if ( $this->handlingTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 12,
					Effects::PART_MAX_SPEED => 12,
					Effects::PART_STEERING => 49
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->handlingTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 4,
					Effects::PART_MAX_SPEED => 7,
					Effects::PART_STEERING => 30
				);
			} else if ( $this->handlingTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 9,
					Effects::PART_MAX_SPEED => 11,
					Effects::PART_STEERING => 42
				);
			} else if ( $this->handlingTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 13,
					Effects::PART_MAX_SPEED => 14,
					Effects::PART_STEERING => 51
				);
			} else if ( $this->handlingTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 16,
					Effects::PART_MAX_SPEED => 17,
					Effects::PART_STEERING => 63
				);
			}
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->handlingTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 1,
					Effects::PART_MAX_SPEED => 2,
					Effects::PART_STEERING => 17
				);
			} else if ( $this->handlingTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 3,
					Effects::PART_MAX_SPEED => 5,
					Effects::PART_STEERING => 24
				);
			} else if ( $this->handlingTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 6,
					Effects::PART_MAX_SPEED => 7,
					Effects::PART_STEERING => 37
				);
			} else if ( $this->handlingTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 10,
					Effects::PART_MAX_SPEED => 10,
					Effects::PART_STEERING => 42
				);
			}
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->handlingTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 3,
					Effects::PART_MAX_SPEED => 5,
					Effects::PART_STEERING => 26
				);
			} else if ( $this->handlingTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 7,
					Effects::PART_MAX_SPEED => 9,
					Effects::PART_STEERING => 33
				);
			} else if ( $this->handlingTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 11,
					Effects::PART_MAX_SPEED => 11,
					Effects::PART_STEERING => 46
				);
			} else if ( $this->handlingTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 14,
					Effects::PART_MAX_SPEED => 14,
					Effects::PART_STEERING => 55
				);
			}
		}
		
		return $values;
	}
	
	public function getPartType() {
		return CarPart::TYPE_HANDLING;
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
		
		if ( $car->getHandlingMaxTier() != 0 ? $car->getHandlingMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}
	
}

?>
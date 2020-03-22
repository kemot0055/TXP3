<?php

class Suspension extends CarPart {

	private $manufacturerId;
	private $suspensionTier;
	private $suspensionBonusId;

	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);

		$this->manufacturerId = intval($itemId[1]);
		$this->suspensionBonusId = intval($itemId[2]);
		$this->suspensionTier = intval($itemId[3]);
	}

	public function getPartManufacturer() {
		return $this->manufacturerId;
	}
	
	public function getPartType() {
		return CarPart::TYPE_SUSPENSION;
	}
	
	public function getPartTier() {
		return ($this->suspensionTier + 1);
	}

	public function getPartValues() {
		if ( $this->manufacturerId == 0 ) {
			if ( $this->suspensionTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 2,
					Effects::PART_MAX_SPEED => 2,
					Effects::PART_STEERING => 36
				);
			} else if ( $this->suspensionTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 2,
					Effects::PART_MAX_SPEED => 4,
					Effects::PART_STEERING => 47
				);
			} else if ( $this->suspensionTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 4,
					Effects::PART_MAX_SPEED => 6,
					Effects::PART_STEERING => 55
				);
			} else if ( $this->suspensionTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 6,
					Effects::PART_MAX_SPEED => 9,
					Effects::PART_STEERING => 63
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->suspensionTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 4,
					Effects::PART_MAX_SPEED => 5,
					Effects::PART_STEERING => 47
				);
			} else if ( $this->suspensionTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 7,
					Effects::PART_MAX_SPEED => 8,
					Effects::PART_STEERING => 59
				);
			} else if ( $this->suspensionTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 9,
					Effects::PART_MAX_SPEED => 14,
					Effects::PART_STEERING => 72
				);
			} else if ( $this->suspensionTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 12,
					Effects::PART_MAX_SPEED => 16,
					Effects::PART_STEERING => 87
				);
			}			
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->suspensionTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 1,
					Effects::PART_MAX_SPEED => 1,
					Effects::PART_STEERING => 32
				);
			} else if ( $this->suspensionTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 1,
					Effects::PART_MAX_SPEED => 2,
					Effects::PART_STEERING => 41
				);
			} else if ( $this->suspensionTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 2,
					Effects::PART_MAX_SPEED => 4,
					Effects::PART_STEERING => 47
				);
			} else if ( $this->suspensionTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 3,
					Effects::PART_MAX_SPEED => 6,
					Effects::PART_STEERING => 53
				);
			}
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->suspensionTier == 0 ) {
				$values = array(
					Effects::PART_ACCELERATION => 3,
					Effects::PART_MAX_SPEED => 3,
					Effects::PART_STEERING => 41
				);
			} else if ( $this->suspensionTier == 1 ) {
				$values = array(
					Effects::PART_ACCELERATION => 5,
					Effects::PART_MAX_SPEED => 6,
					Effects::PART_STEERING => 52
				);
			} else if ( $this->suspensionTier == 2 ) {
				$values = array(
					Effects::PART_ACCELERATION => 7,
					Effects::PART_MAX_SPEED => 8,
					Effects::PART_STEERING => 64
				);
			} else if ( $this->suspensionTier == 3 ) {
				$values = array(
					Effects::PART_ACCELERATION => 9,
					Effects::PART_MAX_SPEED => 12,
					Effects::PART_STEERING => 75
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
		
		if ( $car->getSuspensionMaxTier() != 0 ? $car->getSuspensionMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}

}

?>

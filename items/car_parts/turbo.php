<?php

class Turbo extends CarPart {

	private $manufacturerId;
	private $turboTier;
	private $turboBonusId;

	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);

		$this->manufacturerId = intval($itemId[1]);
		$this->turboBonusId = intval($itemId[2]);
		$this->turboTier = intval($itemId[3]);
	}

	public function getPartTier() {
		return ($this->turboTier + 1);
	}

	public function getPartValues() {
		if ( $this->manufacturerId == 0 ) {
			if ( $this->turboTier == 0 ) {
				$values = array(
						Effects::PART_ACCELERATION => 24,
						Effects::PART_MAX_SPEED => 18,
						Effects::PART_STEERING => 4
				);
			} else if ( $this->turboTier == 1 ) {
				$values = array(
						Effects::PART_ACCELERATION => 37,
						Effects::PART_MAX_SPEED => 29,
						Effects::PART_STEERING => 12
				);
			} else if ( $this->turboTier == 2 ) {
				$values = array(
						Effects::PART_ACCELERATION => 50,
						Effects::PART_MAX_SPEED => 41,
						Effects::PART_STEERING => 17
				);
			} else if ( $this->turboTier == 3 ) {
				$values = array(
						Effects::PART_ACCELERATION => 68,
						Effects::PART_MAX_SPEED => 62,
						Effects::PART_STEERING => 23
				);
			}
		} else if ( $this->manufacturerId == 1 ) {
			if ( $this->turboTier == 0 ) {
				$values = array(
						Effects::PART_ACCELERATION => 31,
						Effects::PART_MAX_SPEED => 26,
						Effects::PART_STEERING => 8
				);
			} else if ( $this->turboTier == 1 ) {
				$values = array(
						Effects::PART_ACCELERATION => 46,
						Effects::PART_MAX_SPEED => 40,
						Effects::PART_STEERING => 16
				);
			} else if ( $this->turboTier == 2 ) {
				$values = array(
						Effects::PART_ACCELERATION => 62,
						Effects::PART_MAX_SPEED => 53,
						Effects::PART_STEERING => 22
				);
			} else if ( $this->turboTier == 3 ) {
				$values = array(
						Effects::PART_ACCELERATION => 86,
						Effects::PART_MAX_SPEED => 72,
						Effects::PART_STEERING => 27
				);
			}
		} else if ( $this->manufacturerId == 2 ) {
			if ( $this->turboTier == 0 ) {
				$values = array(
						Effects::PART_ACCELERATION => 20,
						Effects::PART_MAX_SPEED => 15,
						Effects::PART_STEERING => 2
				);
			} else if ( $this->turboTier == 1 ) {
				$values = array(
						Effects::PART_ACCELERATION => 31,
						Effects::PART_MAX_SPEED => 24,
						Effects::PART_STEERING => 10
				);
			} else if ( $this->turboTier == 2 ) {
				$values = array(
						Effects::PART_ACCELERATION => 45,
						Effects::PART_MAX_SPEED => 37,
						Effects::PART_STEERING => 14
				);
			} else if ( $this->turboTier == 3 ) {
				$values = array(
						Effects::PART_ACCELERATION => 62,
						Effects::PART_MAX_SPEED => 56,
						Effects::PART_STEERING => 21
				);
			}
		} else if ( $this->manufacturerId == 3 ) {
			if ( $this->turboTier == 0 ) {
				$values = array(
						Effects::PART_ACCELERATION => 27,
						Effects::PART_MAX_SPEED => 23,
						Effects::PART_STEERING => 6
				);
			} else if ( $this->turboTier == 1 ) {
				$values = array(
						Effects::PART_ACCELERATION => 42,
						Effects::PART_MAX_SPEED => 36,
						Effects::PART_STEERING => 14
				);
			} else if ( $this->turboTier == 2 ) {
				$values = array(
						Effects::PART_ACCELERATION => 57,
						Effects::PART_MAX_SPEED => 49,
						Effects::PART_STEERING => 19
				);
			} else if ( $this->turboTier == 3 ) {
				$values = array(
						Effects::PART_ACCELERATION => 78,
						Effects::PART_MAX_SPEED => 68,
						Effects::PART_STEERING => 25
				);
			}
		}
		
		return $values;
	}
	
	public function getPartManufacturer() {
		return $this->manufacturerId;
	}
	
	public function getPartType() {
		return CarPart::TYPE_TURBO;
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
		
		if ( $car->getTurboMaxTier() != 0 ? $car->getTurboMaxTier() < self::getPartTier() : true ) {
			return false;
		}
		
		return true;
	}

}

?>

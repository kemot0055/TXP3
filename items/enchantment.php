<?php

class Enchantment extends Item implements Useable, Chooseable {
	
	const ADD_PREFIX = 0;
	const MOD_PREFIX = 1;
	const ADD_SUFFIX = 2;
	const MOD_SUFFIX = 3;
	const MOD_PREFIX_VALUE = 4;
	const MOD_SUFFIX_VALUE = 5;
	
	private $enchantmentType;
	
	public function __construct($itemId, Database $database) {
		parent::__construct($itemId, $database);
		$this->enchantmentType = intval($itemId[3]);
	}
	
	public function onItemChoose(Player $player, ItemStack $originStack, $originSlotId, ItemStack $targetStack, $targetSlotId) {
		$targetItem = $targetStack->getItem();
		if ( $targetItem instanceof CarPart ) {
			$enchantmentComplete = false;
			
			$item = $originStack->getItem();
			$firstMod = $targetStack->getFirstMod();
			$secondMod = $targetStack->getSecondMod();
			
			$enchantmentType = $item->getType();
			if ( $enchantmentType == Enchantment::ADD_PREFIX && $firstMod == null ) {
				$modificationId = self::generateModId($targetItem->getEnchantPower(), true);
				$modificationValue = self::generateModValue($modificationId);

				$result = TXP::getModification($modificationId, $modificationValue);
				$targetStack->setFirstMod($result);
				
				$enchantmentComplete = true;
			} else if ( $enchantmentType == Enchantment::MOD_PREFIX && $firstMod != null ) {
				$modificationId = self::generateModId($targetItem->getEnchantPower(), true);
				$modificationValue = self::generateModValue($modificationId);
				
				$result = TXP::getModification($modificationId, $modificationValue);
				$targetStack->setFirstMod($result);
				
				$enchantmentComplete = true;
			} else if ( $enchantmentType == Enchantment::ADD_SUFFIX && $secondMod == null ) {
				$modificationId = self::generateModId($targetItem->getEnchantPower(), false);
				$modificationValue = self::generateModValue($modificationId);
				
				$result = TXP::getModification($modificationId, $modificationValue);
				$targetStack->setSecondMod($result);
				
				$enchantmentComplete = true;
			} else if ( $enchantmentType == Enchantment::MOD_SUFFIX && $secondMod != null ) {
				$modificationId = self::generateModId($targetItem->getEnchantPower(), false);
				$modificationValue = self::generateModValue($modificationId);
				
				$result = TXP::getModification($modificationId, $modificationValue);
				$targetStack->setSecondMod($result);
				
				$enchantmentComplete = true;
			} else if ( $enchantmentType == Enchantment::MOD_PREFIX_VALUE && $firstMod != null ) {
				//$enchantmentComplete = true; //TODO: Dodać item - update 1.1.1
			} else if ( $enchantmentType == Enchantment::MOD_SUFFIX_VALUE && $secondMod != null ) {
				//$enchantmentComplete = true; //TODO: Dodać item - update 1.1.1
			}
			
			if ( $enchantmentComplete ) {
				$playerInventory = $player->getInventory();
				
				$playerInventory->removeItem($originSlotId); // Usunięcie itemku który powoduje zmiane modyfikatora
				$playerInventory->setItemStack($targetStack, $targetSlotId); // Danie update na item który zmieni wartości modyfikatora
			}
		}
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $itemStack) {
		return true;
	}
	
	public function needsConfirm() {
		return false;
	}
	
	public function getType() {
		return $this->enchantmentType;
	}
	
	public function onItemUse(Player $player, ItemStack $targetStack, $confirmState, $targetSlotId) {}
	
	const BONUS_TYPE_TXP = 0;
	const BONUS_TYPE_VR = 1;
	const BONUS_TYPE_PU = 2;
	
	private function generateModId( $itemEnchantability, $generatePrefix ) {
		$modType = ( $generatePrefix ? 0 : 1 );
		$bonusType = rand(0, 1);
		
		if ( $bonusType == Enchantment::BONUS_TYPE_PU ) {
			$bonusSubType = rand(0, 1);
		} else {
			$bonusSubType = rand(0, 2);
		}
		
		$bonusRarity = 1;
		if ( rand(0, ( 128 + $itemEnchantability ) ) < ( $itemEnchantability / 1.15 ) ) {
			$bonusRarity = 2;
			if ( rand(0, ( 192 + ( $itemEnchantability * 1.25 ) ) ) < ( $itemEnchantability / 1.25 ) ) {
				$bonusRarity = 3;
				if ( rand (0, ( 256 + ( $itemEnchantability * 1.5 ) ) ) < ( $itemEnchantability / 1.5 ) ) {
					$bonusRarity = 4;
				}
			}
		}
		
		return intval($modType . $bonusType . $bonusSubType . $bonusRarity);
	}
	
	private function generateModValue ( $modificationId ) {
		$modificationRanges = TXP::getModificationRanges($modificationId);
		return rand($modificationRanges->getLower(), $modificationRanges->getHigher());
	}
	
}

?>
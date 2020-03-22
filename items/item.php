<?php

require('car_parts/part.php');
require('enchantment.php');
require('powerup.php');
require('pin.php');

class Item {
	
	private $itemId;
	
	private $itemName;
	private $itemDesc;
	private $itemPrice;
	private $itemImage;
	private $itemRarity;
	private $itemMaxDamage;
	private $isTradeable;
	private $enchantPower;
	
	public function __construct($itemId, Database $database) {
		$this->itemId = $itemId;
		
		$this->itemName = $database->getVarInfoFromItem($itemId, 'item_name');
		$this->itemDesc = $database->getVarInfoFromItem($itemId, 'item_desc');
		$this->itemPrice = $database->getVarInfoFromItem($itemId, 'item_price');
		$this->itemImage = $database->getVarInfoFromItem($itemId, 'item_image');
		$this->itemRarity = $database->getVarInfoFromItem($itemId, 'item_rarity');
		$this->itemMaxDamage = $database->getVarInfoFromItem($itemId, 'item_damage');
		$this->isTradeable = $database->getVarInfoFromItem($itemId, 'item_tradeable');
		$this->enchantPower = $database->getVarInfoFromItem($itemId, 'item_enchant_power');
	}
	
	public function getItemId() {
		return $this->itemId;
	}
	
	public function getName() {
		return $this->itemName->getValue();
	}
	
	public function getDescription() {
		return $this->itemDesc->getValue();
	}
	
	public function getSellPrice() {
		return $this->itemPrice->getValue();
	}
	
	public function isTradeable() {
		return $this->isTradeable->getValue();
	}
	
	public function getMaxDamage() {
		return $this->itemMaxDamage->getValue();
	}
	
	public function getEnchantPower() {
		return $this->enchantPower->getValue();
	}
	
	public function getImage() {
		return TXP::HOME_URL . $this->itemImage->getValue();
	}
	
	public function getRarityId() {
		return $this->itemRarity->getValue();
	}
	
	public static function createItem($itemId, Database $database) {
		$itemClass = $database->getItemClass($itemId);

		switch( $itemClass ) {
			case "CAR_ENGINE" : return new Engine($itemId, $database); break;
			case "CAR_GEARBOX" : return new Gearbox($itemId, $database); break;
			case "CAR_SUSPENSION" : return new Suspension($itemId, $database); break;
			case "CAR_HANDLING" : return new Handling($itemId, $database); break;
			case "CAR_TYRES" : return new Tyres($itemId, $database); break;
			case "CAR_TURBO" : return new Turbo($itemId, $database); break;
			case "CAR_FIRST_ADDON" : return new FirstAdditional($itemId, $database); break;
			case "CAR_SECOND_ADDON" : return new SecondAdditional($itemId, $database); break;
			case "CAR_NEON" : return new Neon($itemId, $database); break;
			case "CAR_NEON_STRANGE" : return new NeonStrange($itemId, $database); break;
			case "CAR_NEON_GRADIENT" : return new NeonGradient($itemId, $database); break;
			case "PLAYER_PIN" : return new PlayerPin($itemId, $database); break;
			case "POWERUP" : return new Powerup($itemId, $database); break;
			case "ENCHANTMENT" : return new Enchantment($itemId, $database); break;
			case "VOID" : return null; break;
			default : return new Item($itemId, $database); break;
		}
	}
	
}

class ItemStack {
	
	private $item;
	private $itemFirstMod;
	private $itemSecondMod;
	private $itemDurbality;
	
	public function __construct(Item $item = null, Modification $itemFirstMod = null, Modification $itemSecondMod = null, $itemDurbality = 1) {
		$this->item = $item;
		$this->itemFirstMod = $itemFirstMod;
		$this->itemSecondMod = $itemSecondMod;
		$this->itemDurbality = $itemDurbality;
	}
	
	public function getItem() {
		return $this->item;
	}
	
	public function getFirstMod() {
		return $this->itemFirstMod;
	}
	
	public function getSecondMod() {
		return $this->itemSecondMod;
	}
	
	public function getDurbality() {
		return $this->itemDurbality;
	}
	
	public function getItemRarityId() {
		$rarityId = $this->item->getRarityId();
		if ( $this->item->getEnchantPower() != 0 && ($this->itemFirstMod != null || $this->itemSecondMod != null) ) {
			$rarityId += ( $this->itemFirstMod != null && $this->itemSecondMod != null ? 2 : 1 );
		}
		return $rarityId;
	}
	
	public function setFirstMod(Modification $firstMod = null) {
		$this->itemFirstMod = $firstMod;
		return $this;
	}
	
	public function setSecondMod(Modification $secondMod = null) {
		$this->itemSecondMod = $secondMod;
		return $this;
	}
	
	public function setItemDurbality($itemDurbality) {
		$this->itemDurbality = $itemDurbality;
		return $this;
	}
	
}

interface PreUseable {
	
	public function onItemPreUse(Frame $displayFrame, Player $player, ItemStack $targetStack, $buttonActionIds);
	
}

interface Useable {
	
	public function onItemUse(Player $player, ItemStack $targetStack, $confirmState, $targetSlotId);
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack);
	
	public function needsConfirm();
	
}

interface Chooseable {
	
	public function onItemChoose(Player $player, ItemStack $originStack, $originSlotId, ItemStack $targetStack, $targetSlotId);
	
}

?>
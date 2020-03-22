<?php

class Powerup extends Item implements Useable {
	
	private $powerupId;
	
	public function __construct($itemId, Database $database) {
		$this->powerupId = intval($itemId[3]);
	}
	
	public function getPowerupId() {
		return $this->powerupId;
	}
	
	public function getEffects() {
		//TODO: Efekty powerupów!
		//...do obmyślenia najpierw...
	}
	
	public function itemCanBeUsed(Player $player, ItemStack $targetStack) {
		return true;
	}
	
	public function needsConfirm() {
		return true;
	}
	
	public function onItemUse(Player $player, ItemStack $targetStack, $confirmState, $targetSlotId) {
		$playerInventory = $player->getInventory();
		$playerInventory->removeItem($targetSlotId);
		$player->updateCurrentPowerup($targetStack);
	}
	
}

?>
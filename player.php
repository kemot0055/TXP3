<?php

class Player {
	
	private $database;
	private $playerLogin;
	
	private $playerId;
	
	private $playerNickname;
	private $playerPinId;
	
	private $currentGarageId;
	
	private $playerTxp;
	private $playerCtxp;
	private $playerCash;
	
	private $playerPreviousLevelTxp;
	private $playerCurrentLevelTxp;
	
	private $playerLevel;
	
	private $playerCompetitiveWins;
	private $playerCompetitiveLosses;
	
	private $playerCasualWins;
	private $playerCasualLosses;
	
	private $playerCurrentPowerupId;
	private $playerCurrentPowerupDurbality;
	
	public function __construct($playerLogin, Database $database) {
		$this->playerLogin = $playerLogin;
		$this->database = $database;

		$this->playerId = $database->getVarInfoFromPlayer($playerLogin, 'player_id');
		
		$this->playerNickname = $database->getVarInfoFromPlayer($playerLogin, 'player_name');
		$this->playerPinId = $database->getVarInfoFromPlayer($playerLogin, 'player_pin');
		
		$this->currentGarageId = $database->getVarInfoFromPlayer($playerLogin, 'player_garage');
		
		$this->playerTxp = $database->getVarInfoFromPlayer($playerLogin, 'player_txp');
		$this->playerCtxp = $database->getVarInfoFromPlayer($playerLogin, 'player_ctxp');
		$this->playerCash = $database->getVarInfoFromPlayer($playerLogin, 'player_cash');
		
		$this->playerLevel = self::countPlayerLevel($this->playerTxp->getValue());
		
		$this->playerCompetitiveWins = $database->getVarInfoFromPlayer($playerLogin, 'player_competitive_wins');
		$this->playerCompetitiveLosses = $database->getVarInfoFromPlayer($playerLogin, 'player_competitive_losses');
		
		$this->playerCasualWins = $database->getVarInfoFromPlayer($playerLogin, 'player_casual_wins');
		$this->playerCasualLosses = $database->getVarInfoFromPlayer($playerLogin, 'player_casual_losses');
		
		$this->playerCurrentPowerupId = $database->getVarInfoFromPlayer($playerLogin, 'player_current_powerup');
		$this->playerCurrentPowerupDurbality = $database->getVarInfoFromPlayer($playerLogin, 'player_current_powerup_durbality');
	}
	
	public function addTxp($txp) {
		$value = $this->playerTxp->getValue() + $txp;
		$this->playerTxp->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerTxp);
		self::countPlayerLevel($this->playerTxp->getValue());
	}
	
	public function addCtxp($ctxp) {
		$value = $this->playerCtxp->getValue() + $ctxp;
		$this->playerCtxp->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCtxp);		
	}
	
	public function addCash($cash) {
		$value = $this->playerCash->getValue() + $cash;
		$this->playerCash->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCash);
	}
	
	public function addCasualWin() {
		$value = $this->playerCasualWins->getValue() + 1;
		$this->playerCasualWins->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCasualWins);
	}
	
	public function addCompetitiveWin() {
		$value = $this->playerCompetitiveWins->getValue() + 1;
		$this->playerCompetitiveWins->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCompetitiveWins);		
	}
	
	public function addCasualLoss() {
		$value = $this->playerCasualLosses->getValue() + 1;
		$this->playerCasualLosses->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCasualLosses);
	}
	
	public function addCompetitiveLoss() {
		$value = $this->playerCompetitiveWins->getValue() + 1;
		$this->playerCompetitiveWins->setValue($value);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCompetitiveWins);
	}
	
	public function setPlayerNickname($nickname) {
		$this->playerNickname->setValue($nickname);
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerNickname);
	}
	
	public function changeGarage($newGarageId) {
		if( $newGarageId < 0 || $newGarageId > self::getGarageSize() ) {
			return false;
		}

		$this->currentGarageId->setValue($newGarageId);
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->currentGarageId);
		
		return true;
	}
	
	public function buyGarage() {
		if( self::getGarageSize() < 3 ) {
			
			self::addCash(self::getGarageSize() == 1 ? -25000 : -75000);
			$this->database->registerGarage($this->playerId->getValue(), self::getGarageSize());
		}
	}
	
	public function getPinId() {
		return $this->playerPinId->getValue();
	}
	
	public function getWins() {
		return $this->playerWins->getValue();
	}
	
	public function getLosses() {
		return $this->playerLosses->getValue();	
	}
	
	public function getTxp() {
		return $this->playerTxp->getValue();
	}
	
	public function getCtxp() {
		return $this->playerCtxp->getValue();
	}
	
	public function getCash() {
		return $this->playerCash->getValue();
	}
	
	public function getLevel() {
		return $this->playerLevel->getValue();
	}
	
	public function getPlayerNick() {
		return $this->playerNickname->getValue();
	}
	
	public function getCurrentGarageId() {
		return $this->currentGarageId->getValue();
	}
	
	public function getPlayerId() {
		return $this->playerId->getValue();
	}
	
	public function getInventory() {
		return new PlayerInventory($this->playerId->getValue(), $this->database);
	}

	public function getPlayerGarage($garageId) {
		if($garageId < 0 || $garageId >= self::getGarageSize()) {
			return null;
		}
		
		return new PlayerGarage($this->playerId->getValue(), $garageId, $this->database);
	}
	
	public function getGarageSize() {
		return $this->database->getPlayerGaragesCount($this->playerId->getValue());
	}
	
	private function countPlayerLevel($playerTxp) {
		$levelVarInfo = new VarInfo(null, 0);
		
		$currentLevel = 1;
		
		$baseTxp = 30;
		$prevBaseTxp = 0;
		
		$txp = $baseTxp;
		
		while ( $playerTxp >= $txp ) {
			
			$prevBaseTxp += $baseTxp;
			$baseTxp = floor($baseTxp * 1.1425);
			$txp = floor($baseTxp + $txp);
				
			$currentLevel++;
			
		}

		$this->playerPreviousLevelTxp = $prevBaseTxp;
		$this->playerCurrentLevelTxp = $baseTxp;
		
		$levelVarInfo->setValue($currentLevel);	
		return $levelVarInfo;
	}
	
	public function getPreviousLevelTxp() {
		return $this->playerPreviousLevelTxp;
	}
	
	public function getCurrentLevelTxp() {
		return $this->playerCurrentLevelTxp;
	}
	
	public function getPlayerCarsCount() {
		return $this->database->getPlayerCarsCount($this->playerId->getValue());
	}
	
	public function getEmptyGarage() {
		return $this->database->getPlayerEmptyGarages($this->playerId->getValue());	
	}
	
	public function getCasualWins() {
		return $this->playerCasualWins->getValue();
	}
	
	public function getCompetitiveWins() {
		return $this->playerCompetitiveWins->getValue();
	}
	
	public function getCasualLosses() {
		return $this->playerCasualLosses->getValue();
	}
	
	public function getCompetitiveLosses() {
		return $this->playerCompetitiveLosses->getValue();
	}
	
	public function getCurrentPowerup() {
		$powerup = TXP::getItem( $this->playerCurrentPowerupId->getValue() );
		
		if ( $powerup != null ) {
			return new ItemStack($powerup, null, null, $this->playerCurrentPowerupDurbality->getValue());
		}
		
		return null;
	}
	
	public function updateCurrentPowerup(ItemStack $itemStack = null) {
		$powerupId = 0;
		$powerupDurbality = 0;
		
		if ( $itemStack != null ) {
			$powerupId = $itemStack->getItem()->getItemId();
			$powerupDurbality = $itemStack->getDurbality();
		}
		
		$this->playerCurrentPowerupId->setValue($powerupId);
		$this->playerCurrentPowerupDurbality->setValue($powerupDurbality);
		
		$this->database->updatePlayerVarInfo($this->playerId->getValue(), $this->playerCurrentPowerupId);
		$this->database->updateGarageVarInfo($this->playerId->getValue(), $this->playerCurrentPowerupDurbality);
	}
	
	public function getLogin() {
		return $this->playerLogin;
	}
	
}

class PlayerInventory {
	
	private $database;
	private $playerId;
	
	public function __construct($playerId, Database $database) {
		$this->playerId = $playerId;
		$this->database = $database;
	}
	
	private function getItem($slotId) {
		$itemId = $this->database->getItemIdFromPlayerInventory($this->playerId, $slotId);
		
		if( $itemId != 0 ) {
			return TXP::getItem($itemId);
		}
		
		return null;
	}
	
	private function getItemDamage($slotId) {
		return $this->database->getItemDamageFromPlayerInventory($this->playerId, $slotId);
	}
	
	private function getItemModification($slotId, $itemModSlot) {
		$modId = $this->database->getItemModFromPlayerInventory($this->playerId, $slotId, $itemModSlot);
		$modValue = $this->database->getItemModValueFromPlayerInventory($this->playerId, $slotId, $itemModSlot);
		
		if( $modId != 0 ) {
			return TXP::getModification( $modId, $modValue );
		}
		
		return null;
	}
	
	private function setItem($slotId, $itemId) {
		$this->database->setItemInPlayerInventory($this->playerId, $slotId, $itemId);
	}
	
	private function setItemDamage($slotId, $itemDamage) {
		$this->database->setItemDamageInPlayerInventory($this->playerId, $slotId, $itemDamage);
	}
	
	private function setItemModification($slotId, $itemModSlot, $modificationId, $modificationValue) {
		$this->database->setItemModInPlayerInventory($this->playerId, $slotId, $itemModSlot, $modificationId, $modificationValue);
	}
	
	/**
	 * Adds a item from ItemStack class
	 * 
	 * @param ItemStack $itemStack - The ItemStack class
	 */
	public function addItemStack(ItemStack $itemStack) {	
		$freeSlotId = $this->database->getFreeSlotInPlayerInventory($this->playerId);
		
		if ( $freeSlotId == -1 ) {
			return false;
		}
		
		self::setItemStack($itemStack, $freeSlotId);
		return true;
	}
	
	/**
	 * 
	 * Sets or forces update on item in slotId
	 * 
	 * @param ItemStack $itemStack - ItemStack class to be updated or setted on slot ID
	 * @param int $slotId - slot ID
	 * 
	 */
	public function setItemStack(ItemStack $itemStack = null, $slotId) {
		if ( $itemStack == null ) {
			self::removeItem($slotId);
		} else {
			self::setItem($slotId, $itemStack->getItem()->getItemId());
			if ( $itemStack->getFirstMod() != null ) {
				self::setItemModification($slotId, 0, $itemStack->getFirstMod()->getId(), $itemStack->getFirstMod()->getEffectValue());
			}
			if ( $itemStack->getSecondMod() != null ) {
				self::setItemModification($slotId, 1, $itemStack->getSecondMod()->getId(), $itemStack->getSecondMod()->getEffectValue());
			}
			self::setItemDamage($slotId, $itemStack->getDurbality());
		}
	}
	
	/**
	 * Removes item from player inventory
	 *
	 * @param int $slotId - The slot ID in inventory
	 */
	public function removeItem($slotId) {
		self::setItem($slotId, 0);
		self::setItemModification($slotId, 0, 0, 0);
		self::setItemModification($slotId, 1, 0, 0);
		self::setItemDamage($slotId, 0);
	}
	
	/**
	 * Moves item or swaps it when two item stacks aren't null
	 * 
	 * @param ItemStack originItem - origin ItemStack class 
	 * @param int originSlotId - origin slot ID
	 * @param ItemStack targetItem - target ItemStack class
	 * @param int targetSlotId - target slot ID
	 * 
	 */
	public function swapItems(ItemStack $originStack = null, $originSlotId, ItemStack $targetStack = null, $targetSlotId) {
		self::removeItem($originSlotId);
		self::removeItem($targetSlotId);
		
		if ( $targetStack != null ) {
			self::setItemStack($targetStack, $originSlotId);
		}
		self::setItemStack($originStack, $targetSlotId);
	}
	
	public function getItemStack($slotId) {
		$item = self::getItem($slotId);
		
		if ( $item == null ) {
			return null;
		}
		
		return new ItemStack($item, self::getItemModification($slotId, 0), self::getItemModification($slotId, 1), self::getItemDamage($slotId));
	}
	
	public function isInventoryFree() {
		$slotId = intval($this->database->getFreeSlotInPlayerInventory($this->playerId));
		return ( $slotId > 0 );
	}
	
}

class PlayerGarage {
	
	private $playerId;
	private $garageId;
	private $database;
	
	private $carId;
	
	private $carEngineId;
	private $carEngineFirstModId;
	private $carEngineFirstModValue;
	private $carEngineSecondModId;
	private $carEngineSecondModValue;
	private $carEngineDurbality;
	
	private $carGearboxId;
	private $carGearboxFirstModId;
	private $carGearboxFirstModValue;
	private $carGearboxSecondModId;
	private $carGearboxSecondModValue;
	private $carGearboxDurbality;
	
	private $carSuspensionId;
	private $carSuspensionFirstModId;
	private $carSuspensionFirstModValue;
	private $carSuspensionSecondModId;
	private $carSuspensionSecondModValue;
	private $carSuspensionDurbality;
	
	private $carHandlingId;
	private $carHandlingFirstModId;
	private $carHandlingFirstModValue;
	private $carHandlingSecondModId;
	private $carHandlingSecondModValue;
	private $carHandlingDurbality;
	
	private $carTyresId;
	private $carTyresFirstModId;
	private $carTyresFirstModValue;
	private $carTyresSecondModId;
	private $carTyresSecondModValue;
	private $carTyresDurbality;
	
	private $carTurboId;
	private $carTurboFirstModId;
	private $carTurboFirstModValue;
	private $carTurboSecondModId;
	private $carTurboSecondModValue;
	private $carTurboDurbality;
	
	private $carNeonId;
	private $carFirstAdditionalId;
	private $carSecondAdditionalId;
	
	public function __construct($playerId, $garageId, Database $database) {
		$this->playerId = $playerId;
		$this->garageId = $garageId;
		$this->database = $database;
		
		$this->carId = $database->getVarInfoFromGarage($playerId, $garageId, 'car_id');
		
		$this->carEngineId = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_id');
		$this->carEngineFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_mod1');
		$this->carEngineFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_mod1_value');
		$this->carEngineSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_mod2');
		$this->carEngineSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_mod2_value');
		$this->carEngineDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'engine_durbality');
		
		$this->carGearboxId = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_id');
		$this->carGearboxFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_mod1');
		$this->carGearboxFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_mod1_value');
		$this->carGearboxSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_mod2');
		$this->carGearboxSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_mod2_value');
		$this->carGearboxDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'gearbox_durbality');
		
		$this->carSuspensionId = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_id');
		$this->carSuspensionFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_mod1');
		$this->carSuspensionFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_mod1_value');
		$this->carSuspensionSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_mod2');
		$this->carSuspensionSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_mod2_value');
		$this->carSuspensionDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'suspension_durbality');
		
		$this->carHandlingId = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_id');
		$this->carHandlingFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_mod1');
		$this->carHandlingFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_mod1_value');
		$this->carHandlingSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_mod2');
		$this->carHandlingSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_mod2_value');
		$this->carHandlingDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'handling_durbality');
		
		$this->carTyresId = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_id');
		$this->carTyresFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_mod1');
		$this->carTyresFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_mod1_value');
		$this->carTyresSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_mod2');
		$this->carTyresSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_mod2_value');
		$this->carTyresDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'tyres_durbality');
		
		$this->carTurboId = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_id');
		$this->carTurboFirstModId = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_mod1');
		$this->carTurboFirstModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_mod1_value');
		$this->carTurboSecondModId = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_mod2');
		$this->carTurboSecondModValue = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_mod2_value');
		$this->carTurboDurbality = $database->getVarInfoFromGarage($playerId, $garageId, 'turbo_durbality');
		
		$this->carNeonId = $database->getVarInfoFromGarage($playerId, $garageId, 'neon_id');
		
		$this->carFirstAdditionalId = $database->getVarInfoFromGarage($playerId, $garageId, 'additional1_id');
		$this->carSecondAdditionalId = $database->getVarInfoFromGarage($playerId, $garageId, 'additional2_id');
	}
	
	public function getCar() {
		return TXP::getCar(self::getCarId());
	}
	
	public function getCarId() {
		return $this->carId->getValue();
	}
	
	private function getEngine() {
		return TXP::getItem($this->carEngineId->getValue());
	}
	
	private function getEngineFirstMod() {
		return TXP::getModification($this->carEngineFirstModId->getValue(), $this->carEngineFirstModValue->getValue());
	}
	
	private function getEngineSecondMod() {
		return TXP::getModification($this->carEngineSecondModId->getValue(), $this->carEngineSecondModValue->getValue());
	}
	
	private function getEngineDurbality() {
		return $this->carEngineDurbality->getValue();
	}
	
	private function getGearbox() {
		return TXP::getItem($this->carGearboxId->getValue());
	}
	
	private function getGearboxFirstMod() {
		return TXP::getModification($this->carGearboxFirstModId->getValue(), $this->carGearboxFirstModValue->getValue());
	}
	
	private function getGearboxSecondMod() {
		return TXP::getModification($this->carGearboxSecondModId->getValue(), $this->carGearboxSecondModValue->getValue());
	}
	
	private function getGearboxDurbality() {
		return $this->carGearboxDurbality->getValue();
	}
	
	private function getSuspension() {
		return TXP::getItem($this->carSuspensionId->getValue());
	}
	
	private function getSuspensionFirstMod() {
		return TXP::getModification($this->carSuspensionFirstModId->getValue(), $this->carSuspensionFirstModValue->getValue());
	}
	
	private function getSuspensionSecondMod() {
		return TXP::getModification($this->carSuspensionSecondModId->getValue(), $this->carSuspensionSecondModValue->getValue());
	}
	
	private function getSuspensionDurbality() {
		return $this->carSuspensionDurbality->getValue();
	}
	
	private function getHandling() {
		return TXP::getItem($this->carHandlingId->getValue());
	}
	
	private function getHandlingFirstMod() {
		return TXP::getModification($this->carHandlingFirstModId->getValue(), $this->carHandlingFirstModValue->getValue());
	}
	
	private function getHandlingSecondMod() {
		return TXP::getModification($this->carHandlingSecondModId->getValue(), $this->carHandlingSecondModValue->getValue());
	}
	
	private function getHandlingDurbality() {
		return $this->carHandlingDurbality->getValue();
	}
	
	private function getTyres() {
		return TXP::getItem($this->carTyresId->getValue());
	}
	
	private function getTyresFirstMod() {
		return TXP::getModification($this->carTyresFirstModId->getValue(), $this->carTyresFirstModValue->getValue());
	}
	
	private function getTyresSecondMod() {
		return TXP::getModification($this->carTyresSecondModId->getValue(), $this->carTyresSecondModValue->getValue());
	}
	
	private function getTyresDurbality() {
		return $this->carTyresDurbality->getValue();
	}
	
	private function getTurbo() {
		return TXP::getItem($this->carTurboId->getValue());
	}
	
	private function getTurboFirstMod() {
		return TXP::getModification($this->carTurboFirstModId->getValue(), $this->carTurboFirstModValue->getValue());
	}
	
	private function getTurboSecondMod() {
		return TXP::getModification($this->carTurboSecondModId->getValue(), $this->carTurboSecondModValue->getValue());
	}
	
	private function getTurboDurbality() {
		return $this->carTurboDurbality->getValue();
	}
	
	private function getNeon() {
		return TXP::getItem($this->carNeonId->getValue());
	}
	
	private function getFirstAdditional() {
		return TXP::getItem($this->carFirstAdditionalId->getValue());
	}
	
	private function getSecondAdditional() {
		return TXP::getItem($this->carSecondAdditionalId->getValue());
	}
	
	public function getEngineItemStack() {
		$engine = self::getEngine();
		
		if ( $engine == null ) {
			return null;
		}
		
		return new ItemStack($engine, self::getEngineFirstMod(), self::getEngineSecondMod(), self::getEngineDurbality());
	}
	
	public function getGearboxItemStack() {
		$gearbox = self::getGearbox();
		
		if ( $gearbox == null ) {
			return null;
		}
		
		return new ItemStack($gearbox, self::getGearboxFirstMod(), self::getGearboxSecondMod(), self::getGearboxDurbality());
	}
	
	public function getSuspensionItemStack() {
		$suspension = self::getSuspension();
		
		if ( $suspension == null ) {
			return null;
		}
		
		return new ItemStack($suspension, self::getSuspensionFirstMod(), self::getSuspensionSecondMod(), self::getSuspensionDurbality());
	}
	
	public function getHandlingItemStack() {
		$handling = self::getHandling();
		
		if ( $handling == null ) {
			return null;
		}
		
		return new ItemStack($handling, self::getHandlingFirstMod(), self::getHandlingSecondMod(), self::getHandlingDurbality());
	}
	
	public function getTyresItemStack() {
		$tyres = self::getTyres();
		
		if ( $tyres == null ) {
			return null;
		}
		
		return new ItemStack($tyres, self::getTyresFirstMod(), self::getTyresSecondMod(), self::getTyresDurbality());
	}
	
	public function getTurboItemStack() {
		$turbo = self::getTurbo();
		
		if ( $turbo == null ) {
			return null;
		}
		
		return new ItemStack($turbo, self::getTurboFirstMod(), self::getTurboSecondMod(), self::getTurboDurbality());
	}
	
	public function getFirstAddonItemStack() {
		$firstAddon = self::getFirstAdditional();
		
		if ( $firstAddon == null ) {
			return null;
		}
		
		return new ItemStack($firstAddon);
	}
	
	public function getSecondAddonItemStack() {
		$secondAddon = self::getSecondAdditional();
		
		if ( $secondAddon == null ) {
			return null;	
		}
		
		return new ItemStack($secondAddon);
	}
	
	public function getNeonItemStack() {
		$neon = self::getNeon();
		
		if ( $neon == null ) {
			return null;
		}
		
		return new ItemStack($neon);
	}
	
	public function sellCar() {
		if ( self::getCarId() != 0 ) {
			self::attachEngine();
			self::attachGearbox();
			self::attachSuspension();
			self::attachHandling();
			self::attachTyres();
			self::attachTurbo();
			self::attachFirstAdditional();
			self::attachSecondAdditional();
			self::attachNeon();
			
			$this->carId->setValue(0);
			$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carId);
			
			return true;
		}
		return false;
	}
	
	public function attachCar($carId) {
		if( self::getCarId() == 0 && $carId != 0 ) {
			$this->carId->setValue($carId);
			$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carId);
					
			return true;
		}
		return false;
	}
	
	public function attachEngine(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carEngineId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carEngineFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carEngineFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carEngineSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carEngineSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carEngineDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineId);	
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carEngineDurbality);
	}
	
	public function attachGearbox(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carGearboxId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carGearboxFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carGearboxFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carGearboxSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carGearboxSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carGearboxDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carGearboxDurbality);	
	}
	
	public function attachSuspension(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carSuspensionId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carSuspensionFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carSuspensionFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carSuspensionSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carSuspensionSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carSuspensionDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSuspensionDurbality);
	}
	
	public function attachHandling(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carHandlingId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carHandlingFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carHandlingFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carHandlingSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carHandlingSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carHandlingDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carHandlingDurbality);
	}
	
	public function attachTyres(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carTyresId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carTyresFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carTyresFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carTyresSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carTyresSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carTyresDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTyresDurbality);
	}
	
	public function attachTurbo(ItemStack $itemStack = null) {
		$firstMod = ($itemStack != null ? $itemStack->getFirstMod() : null);
		$secondMod = ($itemStack != null ? $itemStack->getSecondMod() : null);
		
		$this->carTurboId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->carTurboFirstModId->setValue($firstMod != null ? $firstMod->getId() : 0);
		$this->carTurboFirstModValue->setValue($firstMod != null ? $firstMod->getEffectValue() : 0);
		$this->carTurboSecondModId->setValue($secondMod != null ? $secondMod->getId() : 0);
		$this->carTurboSecondModValue->setValue($secondMod != null ? $secondMod->getEffectValue() : 0);
		$this->carTurboDurbality->setValue($itemStack != null ? $itemStack->getDurbality() : 0);
		
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboFirstModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboFirstModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboSecondModId);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboSecondModValue);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carTurboDurbality);
	}
	
	public function attachFirstAdditional(ItemStack $itemStack = null) {
		$this->carFirstAdditionalId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carFirstAdditionalId);
	}
	
	public function attachSecondAdditional(ItemStack $itemStack = null) {
		$this->carSecondAdditionalId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carSecondAdditionalId);		
	}
	
	public function attachNeon($itemStack = null) {
		$this->carNeonId->setValue($itemStack != null ? $itemStack->getItem()->getItemId() : 0);
		$this->database->updateGarageVarInfo($this->playerId, $this->garageId, $this->carNeonId);
	}
	
}

class PlayerCapabilites {
	
	/** Zwiększone przyspieszenie pojazdu */
	public $partAcceleration;

	/** Zwiększona maksymalna prędkość pojazdu */
	public $partMaxSpeed;
	
	/** Zwiększona sterowność pojazdu */
	public $partSteering;
	
	/** Dodatkowy mnożnik TXP */
	public $txpBoost;
	
	/** Dodatkowe TXP */
	public $txpBonus;
	
	/** Dodatkowe TXP liczone procentowo */
	public $txpPercentageBonus;
	
	/** Dodatkowe TXP liczone od dolnych pozycji */
	public $txpPositionDescBonus;
	
	/** Dodatkowy mnożnik VR */
	public $vrBoost;
	
	/** Dodatkowe VR */
	public $vrBonus;
	
	/** Dodatkowe VR liczone procentowo */
	public $vrPercentageBonus;
	
	/** Dodatkowe VR liczone od dolnych pozycji */
	public $vrPositionDescBonus;
	
	/** Szansa na dropnięcie rzeczy pod koniec mapy */
	public $itemFinishDropChance;
	
	/** Szansa na dropnięcie rzeczy o lepszej jakości pod koniec mapy */
	public $itemFinishBetterQualityChance;
	
	/** Szansa na dropnięcie rzeczy o lepszej jakości podczas rozdawania przedmiotu */
	public $itemDropBetterQualityChance;
	
	/** Zwiększona siła powerupa */
	public $puStrength;
	
	/** Zmniejszone użycie powerupa */
	public $puDurbality;
	
	public function sortEffects( $effectValue, $effectId ) {
		switch ( $effectId ) {
			case Effects::PART_ACCELERATION : $this->partAcceleration += $effectValue; break;
			case Effects::PART_MAX_SPEED : $this->partMaxSpeed += $effectValue; break;
			case Effects::PART_STEERING : $this->partSteering += $effectValue; break;
			
			case Effects::TXP_BOOST : $this->txpBoost += $effectValue; break;
			case Effects::VR_BOOST : $this->vrBoost += $effectValue; break;
			
			case Effects::TXP_BONUS : $this->txpBonus += $effectValue; break;
			case Effects::TXP_PERCENTAGE_BONUS : $this->txpPercentageBonus += $effectValue; break;
			case Effects::TXP_DESC_POSITION_BONUS : $this->txpPositionDescBonus += $effectValue; break;
			
			case Effects::VR_BONUS : $this->vrBonus += $effectValue; break;
			case Effects::VR_PERCENTAGE_BONUS : $this->vrPercentageBonus += $effectValue; break;
			case Effects::VR_DESC_POSITION_BONUS : $this->vrPositionDescBonus += $effectValue; break;
			
			case Effects::ITEM_DROP_BETTER_QUALITY_CHANCE : $this->itemDropBetterQualityChance += $effectValue; break;
			case Effects::FINISH_EVENT_ITEM_DROP_CHANCE : $this->itemFinishDropChance += $effectValue; break;
			case Effects::FINISH_EVENT_ITEM_DROP_BETTER_QUALITY_CHANCE : $this->itemFinishBetterQualityChance += $effectValue; break;
			
			case Effects::PU_STRENGTH : $this->puStrength += $effectValue; break;
			case Effects::PU_DURBALITY : $this->puDurbality += $effectValue; break;
		}
	}
	
	public function manageItemStack( ItemStack $itemStack = null ) {
		if ( $itemStack != null ) {
			$item = $itemStack->getItem();
			$firstMod = $itemStack->getFirstMod();
			$secondMod = $itemStack->getSecondMod();
			
			if ( $item instanceof CarPart ) {
				$itemEffects = $item->getPartValues();
				array_walk($itemEffects, array('PlayerCapabilites', 'sortEffects'));	
			}
			
			if ( $firstMod != null ) {
				self::sortEffects($firstMod->getEffectValue(), $firstMod->getEffectId());
			}
			
			if ( $secondMod != null ) {
				self::sortEffects($secondMod->getEffectValue(), $secondMod->getEffectId());
			}
		}
	}
	
}

?>
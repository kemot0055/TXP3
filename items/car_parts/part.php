<?php

abstract class CarPart extends Item implements PreUseable, Useable {
	
	const TYPE_ENGINE = 0;
	const TYPE_GEARBOX = 1;
	const TYPE_SUSPENSION = 2;
	const TYPE_HANDLING = 3;
	const TYPE_TYRES = 4;
	const TYPE_TURBO = 5;
	const TYPE_FIRST_ADDITIONAL = 6;
	const TYPE_SECOND_ADDITIONAL = 7;
	const TYPE_NEON = 8;
	
	/** Jakie parametry ma ta czesc? */
	public abstract function getPartValues();
	
	/** Jaki poziom ma ta czesc? */
	public abstract function getPartTier();
	
	/** Jaki typem części jest? */
	public abstract function getPartType();
	
	/** Jakiego producenta jest ta czesc? */
	public abstract function getPartManufacturer();
	
	public function onItemPreUse(Frame $displayFrame, Player $player, ItemStack $targetStack, $buttonActionIds) {
		$buttonActionIds[2] = $buttonActionIds[1];
		$buttonActionIds[2][6] = '2';
		
		$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
		$currentCar = $playerGarage->getCar();
		
		$partType = $targetStack->getItem()->getPartType();
		$part = self::getPartFromCar($playerGarage, $partType);
		
		if ( $part != null ) {
			BetterDialogs::createWindow($displayFrame, 56, 44);
			
			$label = new LabelDimensioned(64, -34.5, BetterDialogs::getElementZ(), 52, 0, '$sCzy zamienić starą część w samochodzie na nową? Zamieniając stary silnik istnieje szansa że może się zniszczyć. Możesz skorzystać z usługi mechanika który gwarantuje że silnik będzie w plecaku...', true);
			$label->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$displayFrame->append($label);
			
			$arrowLabel = new Label(64, -48, BetterDialogs::getElementZ(), '$aaa$o»', 1.75);
			$arrowLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$displayFrame->append($arrowLabel);
			
			BetterDialogs::createItemSlot($displayFrame, $targetStack, 64 - 10.5, -49.5, BetterDialogs::getElementZ());
			BetterDialogs::createItemSlot($displayFrame, $part, 64 + 10, -49.5, BetterDialogs::getElementZ());
			
			BetterDialogs::createButton($displayFrame, 64 + 13, -61.5, BetterDialogs::getElementZ(), 26, 'Nie', $buttonActionIds[0]);
			BetterDialogs::createButton($displayFrame, 64 - 13, -61.5, BetterDialogs::getElementZ(), 26, 'Tak', $buttonActionIds[1]);
			BetterDialogs::createButton($displayFrame, 64, -66, BetterDialogs::getElementZ(), 52, 'Skorzystaj z usługi mechanika (150VR)', ($player->getCash() >= 150 ? $buttonActionIds[2] : null) );
		} else {
			BetterDialogs::createWindow($displayFrame, 56, 14);
			
			$label = new LabelDimensioned(64, -45, BetterDialogs::getElementZ(), 54, 0, '$sCzy założyć część do aktualnie używanego samochodu?', true);
			$label->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$displayFrame->append($label);

			BetterDialogs::createButton($displayFrame, 64 + 12, -50.5, BetterDialogs::getElementZ(), 24, 'Nie', $buttonActionIds[0]);
			BetterDialogs::createButton($displayFrame, 64 - 12, -50.5, BetterDialogs::getElementZ(), 24, 'Tak', $buttonActionIds[1]);
		}
	}
	
	public function onItemUse(Player $player, ItemStack $targetStack, $confirmState, $targetSlotId) {
		$playerInventory = $player->getInventory();
		$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
		
		$partType = $targetStack->getItem()->getPartType();
		$carPart = self::getPartFromCar($playerGarage, $partType);
		$playerInventory->removeItem($targetSlotId);
		
		if ( $carPart != null ) {
			CarUtils::attachPart($playerGarage, null, $partType);
			
			if ( $confirmState == 1 ? (rand(0, 100) < 33) : true ) {
				if ( $confirmState == 2 ) {
					$player->addCash(-150);
				}
				
				$playerInventory->addItemStack($carPart);
			}
		}
		CarUtils::attachPart($playerGarage, $targetStack, $partType);
	}
	
	public function needsConfirm() {
		return true;
	}
	
	private function getPartFromCar(PlayerGarage $playerGarage, $partType) {
		switch ( $partType ) {
			case CarPart::TYPE_ENGINE : return $playerGarage->getEngineItemStack(); break;
			case CarPart::TYPE_GEARBOX : return $playerGarage->getGearboxItemStack(); break;
			case CarPart::TYPE_SUSPENSION : return $playerGarage->getSuspensionItemStack(); break;
			case CarPart::TYPE_HANDLING : return $playerGarage->getHandlingItemStack(); break;
			case CarPart::TYPE_TYRES : return $playerGarage->getTyresItemStack(); break;
			case CarPart::TYPE_TURBO : return $playerGarage->getTurboItemStack(); break;
			case CarPart::TYPE_FIRST_ADDITIONAL : return $playerGarage->getFirstAddonItemStack(); break;
			case CarPart::TYPE_SECOND_ADDITIONAL : return $playerGarage->getSecondAddonItemStack(); break;
			case CarPart::TYPE_NEON : return $playerGarage->getNeonItemStack(); break;
		}
	}
	
}

require('engine.php');
require('gearbox.php');
require('suspension.php');
require('handling.php');
require('tyres.php');
require('turbo.php');
require('addon.php');
require('neon.php');

?>
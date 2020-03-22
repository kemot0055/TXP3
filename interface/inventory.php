<?php

class Inventory {
	
	private $itemSlot;
	private $selectedSlot;
	
	public function __construct(Menu $menu, $actionId, Player $player) {

		$playerInventory = $player->getInventory();
		$displayFrame = $menu->getDisplayFrame();
	
		$selectedSlotId = intval($actionId[3] . $actionId[4]);
		$inventoryPage = intval($actionId[2]);
		
		$actionEvent = self::createActionEvent($actionId);
		$selectedSlot = null;
		
		if( $selectedSlotId != 0 ) {
		
			$targetSlotId = ($inventoryPage * 50) + $selectedSlotId;
			$targetStack = $playerInventory->getItemStack($targetSlotId);
			
			$actionInvoked = 0;
			
			switch ( $actionEvent->getEventId() ) {
				case ActionEvent::ACTION_MOVE:
					
					$originSlotId = ($actionEvent->getOriginSlotPage() * 50) + $actionEvent->getOriginSlotId();
					$originStack = $playerInventory->getItemStack($originSlotId);
					
					$playerInventory->swapItems($originStack, $originSlotId, $targetStack, $targetSlotId);
					$actionInvoked = 1;
					
					break;
				case ActionEvent::ACTION_REMOVE:
					
					$sellPrice = floor( $targetStack->getItem()->getSellPrice() / 4 );
					if ( $targetStack->getItem()->getMaxDamage() != 0 ) {
						$sellPrice = floor( $sellPrice * ( $targetStack->getDurbality() / $targetStack->getItem()->getMaxDamage() ) );
					}
					
					if ( $actionEvent->removeIsConfirmed() ) {
						$player->addCash($sellPrice);
						$menu->updateVrBar($player);

						$playerInventory->removeItem($targetSlotId);
						$actionInvoked = 1;
					} else {
						BetterDialogs::createBackground($displayFrame);
						BetterDialogs::createWindow($displayFrame, 64, 14);
						
						$confirmLabel = new LabelDimensioned(64, -45.5, BetterDialogs::getElementZ(), 60, 16, '$sCzy na pewno chcesz sprzedać przedmiot za $0af' . TXP::formatCash($sellPrice) . '$fff VR?', true);
						$confirmLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
						$displayFrame->append($confirmLabel);
						
						BetterDialogs::createButton($displayFrame, 64 - 14, -50.5, BetterDialogs::getElementZ(), 26, 'Tak', (Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1) . ActionEvent::ACTION_REMOVE . '1'));
						BetterDialogs::createButton($displayFrame, 64 + 14, -50.5, BetterDialogs::getElementZ(), 26, 'Nie', (Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1) . ActionEvent::ACTION_NOTHING));
					}
					
					break;
				case ActionEvent::ACTION_USE :
					
					$confirmationStatus = $actionEvent->useIsConfirmed();
					if ( !$targetStack->getItem()->needsConfirm() ) {
						$confirmationStatus = 1;
					}
					
					if ( $confirmationStatus ) {
						$targetStack->getItem()->onItemUse($player, $targetStack, $confirmationStatus, $targetSlotId);
						$menu->updateVrBar($player);
						$actionInvoked = 2;
						
						if ( $targetStack->getItem() instanceof Chooseable ) {
							$actionId = (Groups::INVENTORY . $inventoryPage . '00' . ActionEvent::ACTION_CHOOSE_USE . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1));
							$actionEvent = self::createActionEvent($actionId);
							$actionInvoked = 0;
						}
					} else {
						BetterDialogs::createBackground($displayFrame);
						
						$buttonActionIds[0] = (Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1) . ActionEvent::ACTION_NOTHING);
						$buttonActionIds[1] = (Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1) . ActionEvent::ACTION_USE . '1');
						
						if ( $targetStack->getItem() instanceof PreUseable ) {
							$targetStack->getItem()->onItemPreUse($displayFrame, $player, $targetStack, $buttonActionIds);
						} else {
							BetterDialogs::createWindow($displayFrame, 52, 12);
							
							$confirmLabel = new LabelDimensioned(64, -45.5, BetterDialogs::getElementZ(), 48, 16, '$sCzy na pewno chcesz użyć tego przedmiotu?', true);
							$confirmLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
							$displayFrame->append($confirmLabel);
							
							BetterDialogs::createButton($displayFrame, 64 + 12, -50, BetterDialogs::getElementZ(), 24, 'Nie', $buttonActionIds[0]);
							BetterDialogs::createButton($displayFrame, 64 - 12, -50, BetterDialogs::getElementZ(), 24, 'Tak', $buttonActionIds[1]);
						}
					}
					
					break;
				case ActionEvent::ACTION_CHOOSE_USE :
					
					$actionInvoked = 1;
					if ( $targetStack != null ) {
						$originSlotId = ( $actionEvent->getOriginSlotPage() * 50 ) + $actionEvent->getOriginSlotId();
						$originStack = $playerInventory->getItemStack( $originSlotId );
							
						$item = $originStack->getItem();
						$item->onItemChoose($player, $originStack, $originSlotId, $targetStack, $targetSlotId);
						
						$menu->updateVrBar($player);
					}
					
					break;
			}
			
			if( $actionInvoked == 2 ) {
			
				$actionId = Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($selectedSlotId - 1) . '0';
				$actionEvent = self::createActionEvent($actionId);
			
			} else if ( $actionInvoked == 1 ) {

				$actionId = Groups::INVENTORY . $inventoryPage . '000';
				$actionEvent = self::createActionEvent($actionId);
				$selectedSlotId = 0;
				
			}
		
		}
		
		//$label2 = new Label(64, -48, 5, $actionId);
		//$displayFrame->append($label2);

		$itemsBg = new QuadColored(4, 0, 0, 120, 88, '0008');
		$displayFrame->append($itemsBg);
		
		$iActionId = Groups::INVENTORY . '000' . $actionEvent->getEventId();
		if( $actionEvent instanceof MoveEvent ) {
			$iActionId .= $actionEvent->getOriginSlotPage();
			$iActionId .= $actionEvent->getOriginSlotId();
		}
		
		$lastPageBg = new QuadImaged(1.5, 0, 0.1, 2.5, 64 + 24, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', ( $inventoryPage == 1 ? TXP::HOME_URL . 'gfx/inventory/pageFocused.png' : '' ));
		$lastPageBg->setActionId($iActionId);
		$displayFrame->append($lastPageBg);
		
		$lastPageArrow = new Label(1.5 + (2.5 / 2), -48, 0.2, ( $inventoryPage == 0 ? '$666' : '' ) . '$s«', 1.4);
		$lastPageArrow->setVAlign(Alignment::CENTER);
		$lastPageArrow->setHAlign(Alignment::CENTER);
		$displayFrame->append($lastPageArrow);
		
		$iActionId[2] = 1;
		
		$nextPageBg = new QuadImaged(124, 0, 0.1, 2.5, 64 + 24, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', ( $inventoryPage == 0 ? TXP::HOME_URL . 'gfx/inventory/pageFocused.png' : '' ));
		$nextPageBg->setActionId($iActionId);
		$displayFrame->append($nextPageBg);
		
		$nextPageArrow = new Label(124 + (2.5 / 2), -48, 0.2, ( $inventoryPage == 1 ? '$666' : '' ) . '$s»', 1.4);
		$nextPageArrow->setVAlign(Alignment::CENTER);
		$nextPageArrow->setHAlign(Alignment::CENTER);
		$displayFrame->append($nextPageArrow);
		
		$selectedStack = null;
		for($i = 0; $i < 50; $i++) {
			$slotId = ($inventoryPage * 50) + ($i + 1);
			$isSelected = (($i + 1) == $selectedSlotId);
			
			$itemStack = $playerInventory->getItemStack($slotId);
			$itemIsUseable = false;
			
			if ( $isSelected ) {
				$selectedStack = $itemStack;
				
				if ( $itemStack != null && $itemStack->getItem() instanceof Useable ) {
					$itemIsUseable = $itemStack->getItem()->itemCanBeUsed($player, $itemStack);
				}
			}
			
			if( $actionEvent instanceof MoveEvent ? ($actionEvent->getOriginSlotPage() == $inventoryPage) : false ) {
				if( ($i + 1) == $actionEvent->getOriginSlotId() ) {
					$isSelected = 2;
				}
			}
			
			$this->itemSlot[$i] = new Slot($i, $itemStack, $isSelected, $inventoryPage, $actionEvent, $itemIsUseable);
		}
		
		$itemDescBg2 = new QuadStyled(5.8, -2, 1, 115.8, 23, 'Bgs1InRace', 'BgTitle3');
		$displayFrame->append($itemDescBg2);
		
		if( $selectedStack != null && $actionEvent->getEventId() != ActionEvent::ACTION_CHOOSE_USE ) {	
			self::drawItemProperties($displayFrame, $selectedStack);	
		} else {
			if ( $actionEvent->getEventId() == ActionEvent::ACTION_CHOOSE || $actionEvent->getEventId() == ActionEvent::ACTION_CHOOSE_USE ) {
				$msg = '$sWybierz itema...';
			} else if ( $actionEvent->getEventId() == ActionEvent::ACTION_MOVE ) {
				$msg = '$sPrzenoszenie itemu...';
			} else {
				$msg = '$sŁelkam im inwentori TXP v3';
			}
			
			$label = new Label(64, -12, 2, $msg, 1.5);
			$label->setHAlign(Alignment::CENTER);
			$label->setVAlign(Alignment::CENTER);
			
			$displayFrame->append($label);
		}
		
		$displayFrame->append($this->itemSlot);
		
	}
	
	private function createActionEvent($actionId) {
		switch( intval($actionId[5]) ) {
			case ActionEvent::ACTION_NOTHING : return new NothingEvent();
			case ActionEvent::ACTION_SELECT : return new SelectEvent();
			case ActionEvent::ACTION_MOVE : return new MoveEvent($actionId);
			case ActionEvent::ACTION_REMOVE : return new RemoveEvent($actionId);
			case ActionEvent::ACTION_USE : return new UseEvent($actionId);
			case ActionEvent::ACTION_CHOOSE : return new ChooseEvent($actionId); # Wybierasz tylko itemka
			case ActionEvent::ACTION_CHOOSE_USE : return new ChooseUseEvent($actionId); # Wybierasz tylko itemka na którym masz użyć innego itemka
		}
		return null;
	}
	
	private function drawItemProperties(Frame $displayFrame, ItemStack $itemStack) {
		$itemProps = new Frame(5.8, -2, 1);
		
		$itemImage = new QuadImaged(2, -2, 0.1, 19, 19, $itemStack->getItem()->getImage());
		$itemProps->append($itemImage);
		
		if ( $itemStack->getItem()->getMaxDamage() != 0 ) {
			$itemDamage[0] = new QuadColored(1.75, -20.5, 0.2, 19.5, 0.75, '080F');
			$itemDamage[1] = new QuadColored(1.75, -20.5, 0.3, ( ( $itemStack->getDurbality() / $itemStack->getItem()->getMaxDamage() ) * 19.5 ), 0.75, '0F0F');
			
			$itemProps->append($itemDamage);
		}
				
		$firstAddon = $itemStack->getFirstMod();
		$secondAddon = $itemStack->getSecondMod();
		$rarityCode = TXP::$RARITES[$itemStack->getItemRarityId()];
		
		$isEnchantable = ($itemStack->getItem()->getEnchantPower() != 0);
		$isAnyModsOnItem = ($firstAddon != null || $secondAddon != null);
		
		$itemTitle = '$s' . $rarityCode;
		$itemName = $itemStack->getItem()->getName();
		
		$itemDescription = '$s$ccc' . $itemStack->getItem()->getDescription();
		
		if ( $isEnchantable && $firstAddon != null ) {
			$itemTitle .= $firstAddon->getName() . ' ';
			$itemName[0] = strtolower($itemName[0]);
		}
		$itemTitle .= $itemName;
		if ( $isEnchantable && $secondAddon != null ) {
			$itemTitle .= ' ' . $secondAddon->getName(); 
		}
		
		$itemLabel[0] = new QuadStyled(22, -2, 0.1, 92, 4.5, 'Bgs1InRace', 'BgList');
		$itemLabel[1] = new LabelDimensioned(23, -3, 0.2, 92, 0, $itemTitle, false, 0.85);
		
		$itemDesc[0] = new QuadStyled(22, -7, 0.1, ( $isEnchantable && $isAnyModsOnItem ? 56 : 92 ), 14, 'Bgs1InRace', 'BgList');
		$itemDesc[1] = new LabelDimensioned(23, -8, 0.2, ( $isEnchantable && $isAnyModsOnItem ? 64 : 100 ), 11, $itemDescription, true, 0.85);
		
		if ( $isEnchantable && $isAnyModsOnItem ) {
			$modsDesc[0] = new QuadStyled(78, -7, 0.1, 36, 14, 'Bgs1InRace', 'BgList');
			
			$modDescriptions = '$s';
			if ( $firstAddon != null ) {
				$modDescriptions .= TXP::$RARITES[$firstAddon->getRarity() + 2] . Effects::getEffectDescription($firstAddon->getEffectId(), $firstAddon->getEffectValue()) . PHP_EOL;
			}
			if ( $secondAddon != null ) {
				$modDescriptions .= TXP::$RARITES[$secondAddon->getRarity() + 2] . Effects::getEffectDescription($secondAddon->getEffectId(), $secondAddon->getEffectValue());
			}
			
			$modsDesc[1] = new LabelDimensioned(79, -8, 0.2, 44, 14, $modDescriptions, true, 0.85);
			$itemProps->append($modsDesc);
		}
		
		$itemProps->append($itemLabel);
		$itemProps->append($itemDesc);
		
		$displayFrame->append($itemProps);
	}
	
}


class Slot extends Container {
	
	private $slotBackground;
	
	private $inventoryPage;
	private $actionEvent;
	private $isSelected;
	private $slotId;
	
	private $slotItem;
	
	public function __construct($slotId, ItemStack $itemStack = null, $isSelected, $inventoryPage, ActionEvent $actionEvent, $itemIsUseable) {
		parent::__construct();
		
		$this->actionEvent = $actionEvent;
		$this->slotId = $slotId;
		
		$slotItem = null;
		if ( $itemStack != null ) {
			$slotItem = $itemStack->getItem();
			$itemDamage = $itemStack->getDurbality();
		}
		
		$posX = 6 + (($slotId % 10) * 11.6);
		$posY = -28.25 + (intval($slotId / 10) * -11.6);
		$posZ = 1;
		
		$slotWidth = 11;
		$slotHeight = 11;
		
		$actionId = null;
		
		$slotImg = null;
		$slotImg2 = null;
		
		if( $slotItem !== null ) {
			
			$rarityId = $itemStack->getItemRarityId();
			
			if( $isSelected ) {
				$slotImg = TXP::HOME_URL . 'gfx/inventory/slot_t' . $rarityId . '_l3.png';
			} else {
				$slotImg = TXP::HOME_URL . 'gfx/inventory/slot_t' . $rarityId . '_l1.png';
				$slotImg2 = TXP::HOME_URL . 'gfx/inventory/slot_t' . $rarityId . '_l2.png';
			}
			
		} else if ( $isSelected ) {
			$slotImg = TXP::HOME_URL . 'gfx/inventory/slot_t0_l3.png';
		} else {
			$slotImg = TXP::HOME_URL . 'gfx/inventory/slot_t0_l1.png';
			$slotImg2 = TXP::HOME_URL . 'gfx/inventory/slot_t0_l2.png';
		}
		
		if( $isSelected ) {
			
			$posX -= 1.125;
			$posY += 1.125;
			$posZ += 1;
			
			$slotWidth += 2.25;
			$slotHeight += 2.25;
			
			$actionId = Groups::INVENTORY . $inventoryPage . '000';
			
			if( $slotItem != null && $isSelected == 1 ) {
			
				$icon[0] = new QuadImaged($posX + $slotWidth - 3.5, $posY + 1.25, $posZ + 2, 3.5, 3.5, TXP::HOME_URL . 'gfx/inventory/icons/bin.png', TXP::HOME_URL . 'gfx/inventory/icons/binf.png');
				$icon[0]->setActionId(Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($slotId) . ActionEvent::ACTION_REMOVE . 0);

				$icon[1] = new QuadImaged($posX + $slotWidth - 7, $posY + 1.25, $posZ + 2, 3.5, 3.5, TXP::HOME_URL . 'gfx/inventory/icons/move.png', TXP::HOME_URL . 'gfx/inventory/icons/movef.png');
				$icon[1]->setActionId(Groups::INVENTORY . $inventoryPage . '00' . ActionEvent::ACTION_MOVE . $inventoryPage . SlotUtils::getRawId($slotId));
				
				if ( $itemIsUseable ) {
					$icon[2] = new QuadStyled($posX - 2.5, $posY + 3, $posZ + 2, 7.5, 9, 'Icons128x128_1', 'Options');
					$icon[2]->setActionId(Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($slotId) . ActionEvent::ACTION_USE . 0);
				}
				
				self::append($icon);
				
				if ( $slotItem->getMaxDamage() != 0 ) {
				
					$itemDamageCurrent = new QuadColored($posX + 0.5, $posY - $slotHeight + 1.75, $posZ + 2.2, (( $itemDamage / $slotItem->getMaxDamage() ) * ($slotWidth - 1)), 0.75, '0F0F');
					$itemDamageBg = new QuadColored($posX + 0.5, $posY - $slotHeight + 1.75, $posZ + 2.1, ($slotWidth - 1), 0.75, '080F');
					
					self::append($itemDamageCurrent);
					self::append($itemDamageBg);
			
				}
			}
			
		} else {
			
			$actionId = Groups::INVENTORY . $inventoryPage . SlotUtils::getRawId($slotId);
			$actionId .= $actionEvent->getEventId();
			
			if( $actionEvent instanceof MoveEvent ) {
				$actionId .= $actionEvent->getOriginSlotPage();
				$actionId .= $actionEvent->getOriginSlotId();
			}
			
		}
		
		$this->slotBackground = new QuadImaged($posX, $posY, $posZ, $slotWidth, $slotHeight, $slotImg, $slotImg2);
		$this->slotBackground->setActionId($actionId);
		self::append($this->slotBackground);
		
		if ( $slotItem !== null ) {
			$this->slotItem = new QuadImaged($posX + 0.25, $posY - 0.25, $posZ + 0.5, $slotWidth - 0.5, $slotHeight - 0.5, $slotItem->getImage());
			self::append($this->slotItem);
		}
		
		$this->isSelected = $isSelected;
	}
	
	public function getBackgroundImage() {
		return $this->slotBackground;
	}
	
	public function getItemImage() {
		return $this->slotItem;
	}
	
	public function isSelected() {
		return $this->isSelected;
	}
	
	public function getActionEvent() {
		return $this->actionEvent;
	}
	
	public function getInventoryPage() {
		return $this->inventoryPage;
	}
	
}


interface ActionEvent {
	
	const ACTION_NOTHING = 0;
	const ACTION_SELECT = 1;
	const ACTION_MOVE = 2;
	const ACTION_REMOVE = 3;
	const ACTION_USE = 4;
	const ACTION_CHOOSE = 5;
	const ACTION_CHOOSE_USE = 6;
	
	function getEventId();
	
}


class NothingEvent implements ActionEvent {
	
	public function getEventId() {
		return ActionEvent::ACTION_NOTHING;
	}
	
}

class SelectEvent implements ActionEvent {
	
	public function getEventId() {
		return ActionEvent::ACTION_SELECT;
	}
	
}


class MoveEvent implements ActionEvent {
	
	private $originSlotPage;
	private $originSlotId;
	
	public function __construct($actionId) {
		$this->originSlotPage = intval($actionId[6]);
		$this->originSlotId = SlotUtils::getRawId( ($actionId[7] . $actionId[8]) - 1 );
	}
	
	public function getEventId() {
		return ActionEvent::ACTION_MOVE;
	}
	
	public function getOriginSlotPage() {
		return $this->originSlotPage;
	}
	
	public function getOriginSlotId() {
		return $this->originSlotId;
	}
	
}


class RemoveEvent implements ActionEvent {
	
	private $isConfirmed;
	
	public function __construct($actionId) {
		$this->isConfirmed = $actionId[6];
	}
	
	public function getEventId() {
		return ActionEvent::ACTION_REMOVE;
	}
	
	public function removeIsConfirmed() {
		return $this->isConfirmed;
	}
	
}

class UseEvent implements ActionEvent {
	
	private $isConfirmed;
	
	public function __construct($actionId) {
		$this->isConfirmed = $actionId[6];
	}
	
	public function getEventId() {
		return ActionEvent::ACTION_USE;
	}
	
	public function useIsConfirmed() {
		return $this->isConfirmed;
	}
	
}

class ChooseEvent extends MoveEvent {

	public function getEventId() {
		return ActionEvent::ACTION_CHOOSE;
	}
	
}

class ChooseUseEvent extends MoveEvent {
	
	public function getEventId() {
		return ActionEvent::ACTION_CHOOSE_USE;
	}
	
}

class SlotUtils {

	static function getRawId($id) {
		return ( strlen( ($id + 1) ) > 1 ? ($id + 1) : '0' . ($id + 1) );
	}
	
}

$inventory = new Inventory($menu, $actionId, $player);

?>
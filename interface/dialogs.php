<?php

final class BetterDialogs {
	
	const SLOT_BACKGROUND = 0;
	const SLOT_ITEM_IMAGE = 1;
	const SLOT_ITEM_DAMAGE_BACKGROUND = 2;
	const SLOT_ITEM_DAMAGE_FOREGROUND = 3;
	
	public static function createBackground(Frame $displayFrame) {
		$background = new QuadColored(0, 0, 6, 128, 96, '0008');
		$background->setActionId(0);
		
		$displayFrame->append($background);
		return $background;
	}
	
	public static function createWindow(Frame $displayFrame, $width, $height) {
		$window = new QuadStyled(64, -48, 6.1, $width, $height, 'Bgs1InRace', 'BgWindow2');
		$window->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		
		$displayFrame->append($window);
		return $window;
	}
	
	public static function createItemSlot(Frame $displayFrame, ItemStack $itemStack, $posX, $posY, $posZ, $slotLength = 14) {
		$slotFrame = new Frame($posX - ($slotLength / 2), $posY + ($slotLength / 2), $posZ);
		
		$item = $itemStack->getItem();
		$firstMod = $itemStack->getFirstMod();
		$secondMod = $itemStack->getSecondMod();
		$itemDamage = $itemStack->getDurbality();
		
		$slotElements[BetterDialogs::SLOT_BACKGROUND] = new QuadImaged(0, 0, 0, $slotLength, $slotLength, TXP::HOME_URL . 'gfx/inventory/slot_t' . $itemStack->getItemRarityId() . '_l3.png');
		$slotElements[BetterDialogs::SLOT_ITEM_IMAGE] = new QuadImaged(1, -1, 0.1, $slotLength - 2, $slotLength - 2, $item->getImage());
		
		if ( $item->getMaxDamage() != 0 ) {
			$slotElements[BetterDialogs::SLOT_ITEM_DAMAGE_BACKGROUND] = new QuadColored(0.5, -($slotLength - 1.5), 0.2, ($slotLength - 1), 0.75, '080F');
			$slotElements[BetterDialogs::SLOT_ITEM_DAMAGE_FOREGROUND] = new QuadColored(0.5, -($slotLength - 1.5), 0.3, ( ( $itemDamage / $item->getMaxDamage() ) * ($slotLength - 1)), 0.75, '0F0F');
		}
		
		$slotFrame->append($slotElements);
		$displayFrame->append($slotFrame);
		return $slotFrame;
	}
	
	public static function createButton(Frame $displayFrame, $posX, $posY, $posZ, $buttonWidth, $buttonLabel, $buttonActionId = null) {
		$button = new Button($posX, $posY, $posZ, $buttonWidth, $buttonLabel);
		
		if ( $buttonActionId == null ) {
			$button->setIsEnabled(false);
		} else {
			$button->setActionId($buttonActionId);
		}
		
		$displayFrame->append($button);
		return $button;
	}
	
	public static function getElementZ($zOffset = 0) {
		return (6.2 + $zOffset);
	}
	
}

?>
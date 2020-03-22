<?php

class Market {
	
	const CATEGORY_ENGINES = 0;
	const CATEGORY_GEARBOXES = 1;
	const CATEGORY_SUSPENSIONS = 2;
	const CATEGORY_HANDLING = 3;
	const CATEGORY_TYRES = 4;
	const CATEGORY_TURBOS = 5;
	const CATEGORY_OTHER = 6;
	
	const ACTION_BUY = 1;
	
	const ACCEPT_DIALOG = 0;
	const BUY_ACCEPT = 1;
	
	public function __construct(Menu $menu, Player $player, $actionId) {
		
		$displayFrame = $menu->getDisplayFrame();
		
		$currentCategory = intval($actionId[2]);
		$currentCategoryPage = intval($actionId[3]);
		$currentItemId = intval($actionId[4]);
		$currentActionId = intval($actionId[5]);
		
		$itemList = self::createCategoryItemList($currentCategory);
		$disablePages = (sizeof($itemList) > 6) ? ((($currentCategoryPage + 1) * 6) > sizeof($itemList)) : true;
		
		if ( $currentActionId == Market::ACTION_BUY ) {
			$item = $itemList[($currentCategoryPage * 6) + $currentItemId];
			$state = intval($actionId[6]);
				
			if ( $state == Market::BUY_ACCEPT) {
				$player->addCash(-$item->getSellPrice());
				
				$itemStack = new ItemStack($item, null, null, $item->getMaxDamage());
				$player->getInventory()->addItemStack($itemStack);
				
				$menu->updateVrBar($player);
			} else {
				BetterDialogs::createBackground($displayFrame);
				BetterDialogs::createWindow($displayFrame, 54, 13.5);
		
				$confirmationLabel = new LabelDimensioned(64, -44.75, BetterDialogs::getElementZ(), 50, 0, '$sCzy na pewno chcesz zakupić przedmiot za ' . TXP::formatCash($item->getSellPrice()) . ' $0afVR$fff?');
				$confirmationLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
				$displayFrame->append($confirmationLabel);
		
				BetterDialogs::createButton($displayFrame, 64 - 24 / 2, -50.25, BetterDialogs::getElementZ(), 24, 'Tak', (Groups::MARKET . $currentCategory . $currentCategoryPage . $currentItemId . Market::ACTION_BUY . '1'));
				BetterDialogs::createButton($displayFrame, 64 + 24 / 2, -50.25, BetterDialogs::getElementZ(), 24, 'Nie', (Groups::MARKET . $currentCategory . $currentCategoryPage . $currentItemId . '0'));
			}
		}
		
		$itemInfoBackground = new QuadColored(57, 0, 0, 71-5, 88-18, '0008');
		$displayFrame->append($itemInfoBackground);
		
		$pageBackground[0] = new QuadImaged(53, 0, 0, 2, 88-18, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
		if ( $disablePages ) {
			$pageBackground[0]->setImageAfterFocus(null);
		} else {
			$pageBackground[0]->setActionId(Groups::MARKET . $currentCategory . ($currentCategoryPage + 1) . '00');
		}
		$pageBackground[1] = new QuadImaged(5, 0, 0, 2, 88-18, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
		if ( (($currentCategoryPage - 1) < 0) ) {
			$pageBackground[1]->setImageAfterFocus(null);
		} else {
			$pageBackground[1]->setActionId(Groups::MARKET . $currentCategory . ($currentCategoryPage - 1) . '00');
		}
		$pageBackground[2] = new QuadColored(7, -0, 0, 46, 2, '0008');
		$pageBackground[3] = new QuadColored(7, -68, 0, 46, 2, '0008');
		$displayFrame->append($pageBackground);
		
		$pageArrow[0] = new Label(6, -70 / 2, 0.1, ((($currentCategoryPage - 1) < 0) ? '$666' : '') . '$s«');
		$pageArrow[0]->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		$pageArrow[1] = new Label(54, -70 / 2, 0.1, ($disablePages ? '$666' : '') . '$s»');
		$pageArrow[1]->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		$displayFrame->append($pageArrow);
		
		self::createCategory($displayFrame, Market::CATEGORY_ENGINES, 'Silniki', TXP::HOME_URL . 'gfx/market/icon-engine.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_GEARBOXES, 'Skrzynie biegów', TXP::HOME_URL . 'gfx/market/icon-gearbox.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_SUSPENSIONS, 'Zawieszenie', TXP::HOME_URL . 'gfx/market/icon-suspension.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_HANDLING, 'Hamulce', TXP::HOME_URL . 'gfx/market/icon-brake.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_TYRES, 'Opony', TXP::HOME_URL . 'gfx/market/icon-tyre.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_TURBOS, 'Turbosprężarki', TXP::HOME_URL . 'gfx/market/icon-turbo.png', $currentCategory);
		self::createCategory($displayFrame, Market::CATEGORY_OTHER, 'Inne', TXP::HOME_URL . 'gfx/market/icon-other.png', $currentCategory);
		
		self::createItemList($displayFrame, $currentCategory, $currentCategoryPage, $currentItemId, $itemList);
		if ( $itemList != null ) {
			self::createItemInfo($displayFrame, $itemList[($currentCategoryPage * 6) + $currentItemId], $currentCategory, $currentCategoryPage, $currentItemId, $player);
		}
		
	}
	
	private function createCategoryItemList($categoryId) {
		$itemList = array();
		
		if ( $categoryId == Market::CATEGORY_ENGINES ) {
			$itemList[0] = TXP::getItem(3000);
			$itemList[1] = TXP::getItem(3100);
			$itemList[2] = TXP::getItem(3200);
			$itemList[3] = TXP::getItem(3300);
			$itemList[4] = TXP::getItem(4000);
			$itemList[5] = TXP::getItem(4100);
			$itemList[6] = TXP::getItem(4200);
			$itemList[7] = TXP::getItem(4300);
		} else if ( $categoryId == Market::CATEGORY_GEARBOXES ) {
			$itemList[0] = TXP::getItem(5000);
			$itemList[1] = TXP::getItem(5100);
			$itemList[2] = TXP::getItem(5200);
			$itemList[3] = TXP::getItem(5300);
		} else if ( $categoryId == Market::CATEGORY_SUSPENSIONS ) {
			$itemList[0] = TXP::getItem(6000);
			$itemList[1] = TXP::getItem(6100);
			$itemList[2] = TXP::getItem(6200);
			$itemList[3] = TXP::getItem(6300);
		} else if ( $categoryId == Market::CATEGORY_HANDLING ) {
			$itemList[0] = TXP::getItem(7000);
			$itemList[1] = TXP::getItem(7100);
			$itemList[2] = TXP::getItem(7200);
			$itemList[3] = TXP::getItem(7300);
		} else if ( $categoryId == Market::CATEGORY_TYRES ) {
			$itemList[0] = TXP::getItem(8000);
			$itemList[1] = TXP::getItem(8100);
			$itemList[2] = TXP::getItem(8200);
			$itemList[3] = TXP::getItem(8300);
		} else if ( $categoryId == Market::CATEGORY_TURBOS ) {
			$itemList[0] = TXP::getItem(9000);
			$itemList[1] = TXP::getItem(9100);
			$itemList[2] = TXP::getItem(9200);
			$itemList[3] = TXP::getItem(9300);
		} else if ( $categoryId == Market::CATEGORY_OTHER ) {
			$itemList = null;
		}
		
		return $itemList;
	}
	
	private function createItemInfo(Frame $displayFrame, Item $currentItem, $currentCategoryId, $currentCategoryPage, $currentCategoryItemId, Player $player) {
		$itemImage = new QuadImaged(57 + (70 / 2), -7.5, 0.1, 32, 30, $currentItem->getImage());
		$itemImage->setHAlign(Alignment::CENTER);
		
		$itemTitle = new Label(57 + (70 / 2), -38.5, 0.1, '$o$s' . TXP::$RARITES[$currentItem->getRarityId()] . $currentItem->getName(), 0.95);
		$itemTitle->setHAlign(Alignment::CENTER);
		
		$buyButton = new Button(57 + (70 / 2), -62.5, 0.1, 30, 'Kup za ' . TXP::formatCash($currentItem->getSellPrice()) . ' $0afVR');
		
		$playerHaveSpaceInInventory = $player->getInventory()->isInventoryFree();
		$playerHaveCash = ($player->getCash() >= $currentItem->getSellPrice());
		$playerHaveCar = ($player->getPlayerGarage($player->getCurrentGarageId())->getCar() != null);
		
		if ( ($playerHaveCar && $playerHaveSpaceInInventory && $playerHaveCash) ) {
			$buyButton->setActionId(Groups::MARKET . $currentCategoryId . $currentCategoryPage . $currentCategoryItemId . Market::ACTION_BUY . Market::ACCEPT_DIALOG);
		} else {
			if ( !$playerHaveCar ) {
				$buyReason = 'Brak samochodu w garażu!';
			}
			if ( !$playerHaveSpaceInInventory ) {
				$buyReason = 'Brak miejsca w plecaku';
			}
			if ( !$playerHaveCash ) {
				$buyReason = 'Brak funduszy';
			}
			$buyButton->setText($buyReason);
			$buyButton->setIsEnabled(false);
		}
		
		$displayFrame->append($buyButton);
		
		$displayFrame->append($itemImage);
		$displayFrame->append($itemTitle);
	}
	
	private function createItemList(Frame $displayFrame, $currentCategoryId, $currentCategoryPage, $currentSelectedItemId, $itemList = null) {
		if ( $itemList != null ) {
			for ( $i = 0; $i < 6; $i++ ) {
				$itemBackgrounds[$i] = new QuadImaged(7, -(11 * $i) - 2, 0, 46, 11, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
					
				$arrayItemId = ($currentCategoryPage * 6) + $i;
					
				$item = $itemList[$arrayItemId];
					
				if ( $i == $currentSelectedItemId ) {
					$itemBackgrounds[$i]->setImage($itemBackgrounds[$i]->getImageAfterFocus());
					$itemBackgrounds[$i]->setImageAfterFocus(null);
				}
					
				if ( $item != null ) {
					$itemPictures[$i] = new QuadImaged(8, -(11 * $i) - 2.5, 0.1, 10, 10, $item->getImage());
			
					$itemPrices[$i] = new Label(8+11, -(11 * $i) - 5.5, 0.1, '$sCena: ' . TXP::formatCash($item->getSellPrice()) . '$0af VR', 0.8);
					$itemNames[$i] = new LabelDimensioned(8+11, -(11 * $i) - 2.5, 0.1, (46 - 4), 0, '$s' . TXP::$RARITES[$item->getRarityId()] . $item->getName(), false, 0.8);
			
					$itemBackgrounds[$i]->setActionId(Groups::MARKET . $currentCategoryId . $currentCategoryPage . $i . '0');
				} else {
					$itemBackgrounds[$i]->setImageAfterFocus(null);
				}
			}
			$displayFrame->append($itemBackgrounds);
			$displayFrame->append($itemPictures);
			$displayFrame->append($itemPrices);
			$displayFrame->append($itemNames);
		} else {
			$background = new QuadColored(7, -2, 0, 46, 66, '0008');
			$displayFrame->append($background);
			
			$label = new Label(7 + 46 / 2, -2 - 66 / 2, 0.1, '$s$888Brak przedmiotów w tym katalogu!');
			$label->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$displayFrame->append($label);
		}
	}
	
	private function createCategory(Frame $displayFrame, $categoryId, $categoryName, $categoryIcon, $currentCategory) {
		$categoryBackground = new QuadImaged(5 + (((128 - 7.75) / 7) * $categoryId), -73, 0, 15, 15, TXP::HOME_URL . 'gfx/inventory/pageNormal.png', TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
		$categoryBackground->setActionId(Groups::MARKET . $categoryId . '000');
		
		$categoryImage = new QuadImaged(8 + (((128 - 7.75) / 7) * $categoryId), -73.5, 0.1, 9, 11, $categoryIcon);
		$categoryTitle = new LabelDimensioned(5 + (15 / 2) + (((128 - 7.75) / 7) * $categoryId), -84.75, 0.1, 15, 0, '$888' . $categoryName, false, 0.8);
		$categoryTitle->setHAlign(Alignment::CENTER);
		
		if ( $currentCategory == $categoryId ) {
			$categoryBackground->setImage($categoryBackground->getImageAfterFocus());
			$categoryBackground->setImageAfterFocus(null);
		}
		
		$displayFrame->append($categoryBackground);
		$displayFrame->append($categoryImage);
		$displayFrame->append($categoryTitle);
	}
	
	private function createIcon(Frame $frame, $image, $actionId) {
		$icon = new QuadImaged(0, 0, 0.1, 8, 10, $image);
		$icon->setActionId($actionId);
		$frame->append($icon);
	}
	
}

$market = new Market($menu, $player, $actionId);

?>
<?php

class Garage {
	
	const ACTION_SELL_CAR = 1;
	const ACTION_USE_CAR = 2;
	const ACTION_UNMOUNT_PART = 3;
	const ACTION_BUY_GARAGE = 4;
	const ACTION_PART_INFO = 5;
	
	public function __construct(Menu $menu, $actionId, Player $player) {
		$displayFrame = $menu->getDisplayFrame();
		
		$currentGarageId = intval($actionId[2]);
		$garageActionId = intval($actionId[3]);
		
		$playerGarage = $player->getPlayerGarage($currentGarageId);
		
		//$labeleg = new Label(96, -48, 3.5, $actionId);
		//$displayFrame->append($labeleg);
		
		if ( $garageActionId == Garage::ACTION_SELL_CAR ) { # Sell car action
			$confirmed = intval($actionId[4]);
			
			{ # Obliczanie wartości samochodu
				$carValue = floor($playerGarage->getCar()->getPrice() / 3);
				$carEngine = $playerGarage->getEngineItemStack();
				$carGearbox = $playerGarage->getGearboxItemStack();
				$carSuspension = $playerGarage->getSuspensionItemStack();
				$carHandling = $playerGarage->getHandlingItemStack();
				$carTyres = $playerGarage->getTyresItemStack();
				$carTurbo = $playerGarage->getTurboItemStack();
				$carFirstAdditional = $playerGarage->getFirstAddonItemStack();
				$carSecondAdditional = $playerGarage->getSecondAddonItemStack();
				$carNeon = $playerGarage->getNeonItemStack();
				
				if ( $carEngine != null ) {
					$carValue += floor( ($carEngine->getItem()->getSellPrice() / 4) * $carEngine->getDurbality() );
				}
				if ( $carGearbox != null ) {
					$carValue += floor( ($carGearbox->getItem()->getSellPrice() / 4)  * $carGearbox->getDurbality() );
				}
				if ( $carSuspension != null ) {
					$carValue += floor( ($carSuspension->getItem()->getSellPrice() / 4)  * $carSuspension->getDurbality() );
				}
				if ( $carHandling != null ) {
					$carValue += floor( ($carHandling->getItem()->getSellPrice() / 4)  * $carHandling->getDurbality() );
				}
				if ( $carTyres != null ) {
					$carValue += floor( ($carTyres->getItem()->getSellPrice() / 4)  * $carTyres->getDurbality() );
				}
				if ( $carTurbo != null ) {
					$carValue += floor( ($carTurbo->getItem()->getSellPrice() / 4)  * $carTurbo->getDurbality() );
				}
 				if ( $carFirstAdditional != null ) {
					$carValue += floor($carFirstAdditional->getItem()->getSellPrice() / 4);
				}
				if ( $carSecondAdditional != null ) {
					$carValue += floor($carSecondAdditional->getItem()->getSellPrice() / 4);
				}
				if ( $carNeon != null ) {
					$carValue += floor($carNeon->getItem()->getSellPrice() / 4);
				}
			}
			
			if ( $confirmed ) {
				$player->addCash($carValue);
				$menu->updateVrBar($player);
				
				$playerGarage->sellCar($player);
			} else {
				BetterDialogs::createBackground($displayFrame);
				BetterDialogs::createWindow($displayFrame, 52, 16);
				
				$confirmLabel = new LabelDimensioned(64, -45.5, BetterDialogs::getElementZ(), 48, 16, '$sCzy na pewno chcesz sprzedać pojazd o wartości $0af' . TXP::formatCash($carValue) . ' $fffVR?', true);
				$confirmLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
				
				$confirmButton[0] = new Button(64 - 12, -52, BetterDialogs::getElementZ(), 24, 'Tak');
				$confirmButton[0]->setActionId(Groups::GARAGE . $currentGarageId . '11');
				
				$confirmButton[1] = new Button(64 + 12, -52, BetterDialogs::getElementZ(), 24, 'Nie');
				$confirmButton[1]->setActionId(Groups::GARAGE . $currentGarageId . '00');
				
				$displayFrame->append($confirmLabel);
				$displayFrame->append($confirmButton);
			}
		} else if ( $garageActionId == Garage::ACTION_USE_CAR ) {
			$player->changeGarage($currentGarageId);
			$menu->updateGarageShortcut($player);
		} else if ( $garageActionId == Garage::ACTION_UNMOUNT_PART ) {
			$partId = intval($actionId[4]);
			$confirmationId = intval($actionId[5]);
			
			$itemStack = null;
			switch ( $partId ) {
				case 0 : $itemStack = $playerGarage->getEngineItemStack(); break;
				case 1 : $itemStack = $playerGarage->getGearboxItemStack(); break;
				case 2 : $itemStack = $playerGarage->getSuspensionItemStack(); break;
				case 3 : $itemStack = $playerGarage->getHandlingItemStack(); break;
				case 4 : $itemStack = $playerGarage->getTyresItemStack(); break;
				case 5 : $itemStack = $playerGarage->getTurboItemStack(); break;
				case 6 : $itemStack = $playerGarage->getFirstAddonItemStack(); break;
				case 7 : $itemStack = $playerGarage->getSecondAddonItemStack(); break;
				case 8 : $itemStack = $playerGarage->getNeonItemStack(); break;
			}
			
			if ( $confirmationId == 0 ) { # Brak polecenia
				BetterDialogs::createBackground($displayFrame);
				BetterDialogs::createWindow($displayFrame, 54, 48);
				
				$label = new LabelDimensioned(64, -33, BetterDialogs::getElementZ(), 49.5, 0, 'Czy jesteś pewien że chcesz zdemontować część od pojazdu? Usuwając ją samemu istnieje szansa że część może się całkowicie zepsuć, można skorzystać z usługi mechanika który kosztuje 150VR', true);
				$label->setVAlign(Alignment::CENTER);
				$label->setHAlign(Alignment::CENTER);
				$displayFrame->append($label);
				
				$customActionId = Groups::GARAGE . $currentGarageId . '00';
				BetterDialogs::createButton($displayFrame, 64 + 24 / 2, -63, BetterDialogs::getElementZ(), 24, 'Nie', $customActionId);
				
				$customActionId = Groups::GARAGE . $currentGarageId . Garage::ACTION_UNMOUNT_PART . $partId . '1';
				BetterDialogs::createButton($displayFrame, 64 - 24 / 2, -63, BetterDialogs::getElementZ(), 24, 'Tak', $customActionId);
				
				$customActionId = ($player->getCash() >= 150 ? (Groups::GARAGE . $currentGarageId . Garage::ACTION_UNMOUNT_PART . $partId . '2') : null);
				BetterDialogs::createButton($displayFrame, 64, -68, BetterDialogs::getElementZ(), 48, 'Skorzystaj z usługi mechanika', $customActionId);

				BetterDialogs::createItemSlot($displayFrame, $itemStack, 64, -49.5, BetterDialogs::getElementZ());
			} else {
				$playerInventory = $player->getInventory();
				$item = $itemStack->getItem();
				
				if ( $item instanceof Engine ) {
					$playerGarage->attachEngine();
				} else if ( $item instanceof Gearbox ) {
					$playerGarage->attachGearbox();
				} else if ( $item instanceof Suspension ) {
					$playerGarage->attachSuspension();
				} else if ( $item instanceof Handling ) {
					$playerGarage->attachHandling();
				} else if ( $item instanceof Tyres ) {
					$playerGarage->attachTyres();
				} else if ( $item instanceof Turbo ) {
					$playerGarage->attachTurbo();
				} else if ( $item instanceof FirstAdditional ) {
					$playerGarage->attachFirstAdditional();
				} else if ( $item instanceof SecondAdditional ) {
					$playerGarage->attachSecondAdditional();
				} else if ( $item instanceof Neon ) {
					$playerGarage->attachNeon();
				}
				
				if ( $confirmationId == 1 ? rand(0, 100) < 33 : true ) {
					if ( $confirmationId == 2 ) {
						$player->addCash(-150);
						$menu->updateVrBar($player);
					}
					$playerInventory->addItemStack($itemStack);
				}
			}
		} else if ( $garageActionId == Garage::ACTION_BUY_GARAGE ) {
			$buyConfirmed = intval($actionId[4]);
			
			if( $buyConfirmed ) {
				$player->buyGarage();
				$menu->updateVrBar($player);
				
				$playerGarage = $player->getPlayerGarage($currentGarageId);
			} else {
				BetterDialogs::createBackground($displayFrame);
				
				$canBuyGarage = false;
				if ( $currentGarageId == 2 ? ( $player->getGarageSize() == 2 && $player->getCash() >= 75000 && $player->getLevel() >= 40 ) : false ) {
					$canBuyGarage = true;
				} else if ( $currentGarageId == 1 ? ( $player->getGarageSize() == 1 && $player->getCash() >= 25000 && $player->getLevel() >= 15 ) : false ) {
					$canBuyGarage = true;
				}
				
				BetterDialogs::createWindow($displayFrame, 52, ($canBuyGarage ? 14 : 18));
				
				if ( $canBuyGarage ) {
					$confirmationLabel = new LabelDimensioned(64, -43.5, BetterDialogs::getElementZ(), 48, 0, '$sCzy na pewno chcesz kupić garaż za $0af' . ( $player->getGarageSize() == 2 ? 75000 : 25000 ) . '$fffVR?');
					$confirmationLabel->setHAlign(Alignment::CENTER);
					$displayFrame->append($confirmationLabel);
					
					BetterDialogs::createButton($displayFrame, 64 - 24 / 2, -50.5, BetterDialogs::getElementZ(), 24, 'Tak', (Groups::GARAGE . $currentGarageId . Garage::ACTION_BUY_GARAGE . '1'));
					BetterDialogs::createButton($displayFrame, 64 + 24 / 2, -50.5, BetterDialogs::getElementZ(), 24, 'Nie', (Groups::GARAGE . $currentGarageId . '00'));
				} else {
					$requireLabel = new LabelDimensioned(64, -42, 6.1, 32, 0, 'Aby kupić ten garaż musisz mieć: ' . ($currentGarageId == 1 ? '25.000VR oraz 15 poziom' : '75.000VR oraz 40 poziom'), true);
					$requireLabel->setHAlign(Alignment::CENTER);
					$displayFrame->append($requireLabel);
					
					BetterDialogs::createButton($displayFrame, 64, -52, BetterDialogs::getElementZ(), 32, 'OK', (Groups::GARAGE . $currentGarageId . '00'));
				}
			}
		} else if ( $garageActionId == Garage::ACTION_PART_INFO ) {
			$partId = intval($actionId[4]);
			
			switch ( $partId ) {
				case 0 : $itemStack = $playerGarage->getEngineItemStack(); break;
				case 1 : $itemStack = $playerGarage->getGearboxItemStack(); break;
				case 2 : $itemStack = $playerGarage->getSuspensionItemStack(); break;
				case 3 : $itemStack = $playerGarage->getHandlingItemStack(); break;
				case 4 : $itemStack = $playerGarage->getTyresItemStack(); break;
				case 5 : $itemStack = $playerGarage->getTurboItemStack(); break;
				case 6 : $itemStack = $playerGarage->getFirstAddonItemStack(); break;
				case 7 : $itemStack = $playerGarage->getSecondAddonItemStack(); break;
				case 8 : $itemStack = $playerGarage->getNeonItemStack(); break;
			}
			
			BetterDialogs::createBackground($displayFrame);
			BetterDialogs::createItemSlot($displayFrame, $itemStack, 37.5, -31.9 - 4.75, BetterDialogs::getElementZ(), 18);
			
			$firstAddon = $itemStack->getFirstMod();
			$secondAddon = $itemStack->getSecondMod();
			$rarityCode = TXP::$RARITES[$itemStack->getItemRarityId()];
			
			$isAnyModsOnItem = ($firstAddon != null || $secondAddon != null);
			
			$itemTitle = '$s' . $rarityCode;
			$itemName = $itemStack->getItem()->getName();
			
			$itemDescription = '$s$ccc' . $itemStack->getItem()->getDescription();
			
			if ( $firstAddon != null ) {
				$itemTitle .= $firstAddon->getName() . ' ';
				$itemName[0] = strtolower($itemName[0]);
			}
			$itemTitle .= $itemName;
			if ( $secondAddon != null ) {
				$itemTitle .= ' ' . $secondAddon->getName();
			}
			
			$title[0] = new QuadStyled(38 + 9.5, -22.5 - 4.75, BetterDialogs::getElementZ(), 52, 4, 'Bgs1InRace', 'BgTitle3');
			$title[1] = new LabelDimensioned(39 + 9.5, -23.25 - 4.75, BetterDialogs::getElementZ(0.1), 49.75, 0, $itemTitle);
			$displayFrame->append($title);
			
			$desc[0] = new QuadStyled(38 + 9.5, -27 - 4.75, BetterDialogs::getElementZ(), 52, 14, 'Bgs1InRace', 'BgTitle3');
			$desc[1] = new LabelDimensioned(39 + 9.5, -27.75 - 4.75, BetterDialogs::getElementZ(0.1), 50.5, 0, $itemDescription, true);
			$displayFrame->append($desc);
			
			$effects[0] = new Label(28.25, -42 - 4.75, BetterDialogs::getElementZ(), '$s$i$cccModyfikatory założone na itemie:');
			$displayFrame->append($effects);
			$addonComments = null;
			
			if ( $isAnyModsOnItem ) {
				if ( $firstAddon != null ) {
					$addonComments .= '$s' . TXP::$RARITES[$firstAddon->getRarity() + 2] . Effects::getEffectDescription($firstAddon->getEffectId(), $firstAddon->getEffectValue()) . PHP_EOL;
				}
				if ( $secondAddon != null ) {
					$addonComments .= TXP::$RARITES[$secondAddon->getRarity() + 2] . Effects::getEffectDescription($secondAddon->getEffectId(), $secondAddon->getEffectValue());
				}
			}
			
			if ( $addonComments == null ) {
				$addonComments = '$s$cccNie ma żadnych modyfikatorów założonych na itemie...';
			}
			
			$effects[1] = new QuadStyled(28.25, -45 - 4.75, BetterDialogs::getElementZ(), 71.25, 14, 'Bgs1InRace', 'BgTitle3');
			$effects[2] = new LabelDimensioned(29.25, -45.75 - 4.75, BetterDialogs::getElementZ(0.1), 64, 0, $addonComments);
			$displayFrame->append($effects);
			
			BetterDialogs::createWindow($displayFrame, 75, 46);
			BetterDialogs::createButton($displayFrame, 64, -62 - 4.75, BetterDialogs::getElementZ(), 24, 'OK', (Groups::GARAGE . $currentGarageId . '000'));
		}
		
		$background = new QuadColored(7, 0, 0, 128 - 14, 88, '0008');
		$displayFrame->append($background);
		
		$garageIdLabel = new Label(64, -3.5, 0.1, '$sGaraż (' . ($currentGarageId + 1) . '/3)');
		$garageIdLabel->setVAlign(Alignment::CENTER);
		$garageIdLabel->setHAlign(Alignment::CENTER);
		$displayFrame->append($garageIdLabel);
		
		$lastPageBg = new QuadImaged(4.5, 0, 1, 2.5, 64 + 24, TXP::HOME_URL . 'gfx/inventory/pageNormal.png');
		if( $currentGarageId - 1 >= 0 ) {
			$lastPageBg->setImageAfterFocus(TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
			$lastPageBg->setActionId(Groups::GARAGE . ($currentGarageId - 1) . '00');
		}
		$displayFrame->append($lastPageBg);
		
		$lastPageArrow = new Label(4.5 + (2.5 / 2), -48, 2, ( $currentGarageId == 0 ? '$666' : '' ) . '$s«', 1.4);
		$lastPageArrow->setVAlign(Alignment::CENTER);
		$lastPageArrow->setHAlign(Alignment::CENTER);
		$displayFrame->append($lastPageArrow);
		
		$nextPageBg = new QuadImaged(121, 0, 1, 2.5, 64 + 24, TXP::HOME_URL . 'gfx/inventory/pageNormal.png');
		if( $currentGarageId + 1 < 3 ) {
			$nextPageBg->setImageAfterFocus(TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
			$nextPageBg->setActionId(Groups::GARAGE . ($currentGarageId + 1) . '00');
		}
		$displayFrame->append($nextPageBg);
		
		$nextPageArrow = new Label(121 + (2.5 / 2), -48, 2, ( ($currentGarageId + 1) > 2 ? '$666' : '' ) . '$s»', 1.4);
		$nextPageArrow->setVAlign(Alignment::CENTER);
		$nextPageArrow->setHAlign(Alignment::CENTER);
		$displayFrame->append($nextPageArrow);
		
		if ( $playerGarage == null ) {
			
			$emptyGarageInfo = new Label(64, -42, 0.2, '$s$888Garaż do kupienia', 2);
			$emptyGarageInfo->setVAlign(Alignment::CENTER);
			$emptyGarageInfo->setHAlign(Alignment::CENTER);
			$displayFrame->append($emptyGarageInfo);
			
			$buyButton = new Button(64, -52, 0.2, 32, 'Kup garaż');
			if ( $currentGarageId == 2 && $player->getGarageSize() != 2 ) {
				$buyButton->setIsEnabled(false);
			} else {
				$buyButton->setActionId(Groups::GARAGE . $currentGarageId . Garage::ACTION_BUY_GARAGE . '0');
			}
			$displayFrame->append($buyButton);
			
		} else {
			$car = $playerGarage->getCar();
			
			if ( $car == null ) {
			
				$emptyCarInfo = new Label(64, -42, 0.2, '$s$888Nie ma pojazdu w tym garażu', 2);
				$emptyCarInfo->setVAlign(Alignment::CENTER);
				$emptyCarInfo->setHAlign(Alignment::CENTER);
				$displayFrame->append($emptyCarInfo);
				
				$dealerButton = new Button(64, -52, 0.2, 32, 'Dealer samochodowy');
				$dealerButton->setActionId(Groups::CAR_DEALER . '000');
				$displayFrame->append($dealerButton);
			
			} else {
				$garageIdLabel->setText($garageIdLabel->getText() . ": " . $car->getName());
				
				$acceleration = $car->getBaseAcceleration();
				$maxSpeed = $car->getBaseMaxSpeed();
				$steering = $car->getBaseSteering();
				
				$partItemStack[0] = $playerGarage->getEngineItemStack();
				$partItemStack[1] = $playerGarage->getGearboxItemStack();
				$partItemStack[2] = $playerGarage->getSuspensionItemStack();
				$partItemStack[3] = $playerGarage->getHandlingItemStack();
				$partItemStack[4] = $playerGarage->getTyresItemStack();
				$partItemStack[5] = $playerGarage->getTurboItemStack();
				$partItemStack[6] = $playerGarage->getFirstAddonItemStack();
				$partItemStack[7] = $playerGarage->getSecondAddonItemStack();
				$partItemStack[8] = $playerGarage->getNeonItemStack();
				
				$playerCapabilities = new PlayerCapabilites();
				$playerCapabilities->manageItemStack($partItemStack[0]);
				$playerCapabilities->manageItemStack($partItemStack[1]);
				$playerCapabilities->manageItemStack($partItemStack[2]);
				$playerCapabilities->manageItemStack($partItemStack[3]);
				$playerCapabilities->manageItemStack($partItemStack[4]);
				$playerCapabilities->manageItemStack($partItemStack[5]);
				$playerCapabilities->manageItemStack($partItemStack[6]);
				$playerCapabilities->manageItemStack($partItemStack[7]);
				
				$acceleration += $playerCapabilities->partAcceleration;
				$maxSpeed += $playerCapabilities->partMaxSpeed;
				$steering += $playerCapabilities->partSteering;
				
				$average = floor( ( $acceleration + $maxSpeed + $steering ) / 3 );

				if ( $average > 1000 ) {
					$class = TXP::$CAR_RARITIES[7] . 'SS';
				} else if ( $average > 800 ) {
					$class = TXP::$CAR_RARITIES[6] . 'S';
				} else if ( $average > 650 ) {
					$class = TXP::$CAR_RARITIES[5] . 'A';
				} else if ( $average > 500 ) {
					$class = TXP::$CAR_RARITIES[4] . 'B';
				} else if ( $average > 350 ) {
					$class = TXP::$CAR_RARITIES[3] . 'C';
				} else if ( $average > 250 ) {
					$class = TXP::$CAR_RARITIES[2] . 'D';
				} else if ( $average > 100 ) {
					$class = TXP::$CAR_RARITIES[1] . 'E';
				} else {
					$class = TXP::$CAR_RARITIES[0] . 'F';
				}
				
				$time = CarStats::getCarTime($steering, $acceleration, $maxSpeed);
				if ( $time > 0 ) {
					$timeColor = '$f00+';
				} else {
					$timeColor = '$00f';
				}
				$time = TXP::formatTime($time);
				
				$barAcceleration = ($acceleration / 1000) * 30;
				if ( $barAcceleration > 30 ) {
					$barAcceleration = 30;
				}
				
				$barMaxSpeed = ($maxSpeed / 1000) * 30;
				if ( $barMaxSpeed > 30 ) {
					$barMaxSpeed = 30;
				}
				
				$barSteering = ($steering / 1000) * 30;
				if ( $barSteering > 30 ) {
					$barSteering = 30;
				}
				
				$acceleration = floor($acceleration);
				$maxSpeed = floor($maxSpeed);
				$steering = floor($steering);
				
				$carDesc = new Label(12.5, -41.5, 0.1, '$sOpis pojazdu: ' . $car->getDescription());
				$displayFrame->append($carDesc);
				
				$timeLabel = new Label(12.5, -45.25, 0.1, '$sCzas: ' . $timeColor . $time . 's$fff, średnia: ' . $average . ', klasa pojazdu: ' . $class);
				$displayFrame->append($timeLabel);
				
				$carType = new Label(12.5, -48.75, 0.1, '$sTyp samochodu: ' . ($car->getType() == 1 ? 'Elektryczny' : 'Spalinowy'));
				$displayFrame->append($carType);
				
				$carAccelerationLabel = new Label(12.5, -53, 0.1, '$sPrzyspieszenie: ' . $acceleration);
				$displayFrame->append($carAccelerationLabel);
				
				$carAccelerationBg = new QuadColored(12.5, -56, 0.1, 30, 1.25, '888F');
				$displayFrame->append($carAccelerationBg);
				
				$carAcceleration = new QuadColored(12.5, -56, 0.2, $barAcceleration, 1.25, 'BBBF');
				$displayFrame->append($carAcceleration);
				
				$carMaxSpeedLabel = new Label(12.5, -58, 0.1, '$sMaksymalna prędkość: ' . $maxSpeed);
				$displayFrame->append($carMaxSpeedLabel);
				
				$carMaxSpeedBg = new QuadColored(12.5, -61, 0.1, 30, 1.25, '888F');
				$displayFrame->append($carMaxSpeedBg);
				
				$carMaxSpeed = new QuadColored(12.5, -61, 0.2, $barMaxSpeed, 1.25, 'BBBF');
				$displayFrame->append($carMaxSpeed);
				
				$carSteeringLabel = new Label(12.5, -63, 0.1, '$sPrzyczepność: ' . $steering);
				$displayFrame->append($carSteeringLabel);
				
				$carSteeringBg = new QuadColored(12.5, -66, 0.1, 30, 1.25, '888F');
				$displayFrame->append($carSteeringBg);
				
				$carSteering = new QuadColored(12.5, -66, 0.2, $barSteering, 1.25, 'BBBF');
				$displayFrame->append($carSteering);
				
				$carImage = new QuadImaged(12.5, -10, 0.1, 30, 30, $car->getImage());
				$displayFrame->append($carImage);
				
				$useButton = new Button(12.5 + 15, -72, 0.2, 30, 'Użyj pojazdu');
				$displayFrame->append($useButton);
				
				$sellButton = new Button(12.5 + 15, -77, 0.2, 30, 'Sprzedaj pojazd');
				$sellButton->setActionId(Groups::GARAGE . $currentGarageId . '10');
				if ( $player->getCurrentGarageId() != $currentGarageId ) {
					$useButton->setActionId(Groups::GARAGE . $currentGarageId . '20');
				} else {
					$useButton->setIsEnabled(false);
				}
				$displayFrame->append($sellButton);
				
				$carDealerButton = new Button(12.5 + 15, -82, 0.2, 30, 'Dealer samochodowy');
				$carDealerButton->setActionId(Groups::CAR_DEALER . '000');
				$displayFrame->append($carDealerButton);
				
				$carPartRequirementsBackground[0] = new QuadColored(77, -57, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[0]->setVAlign(Alignment::CENTER);			
				$carPartRequirementsBackground[1] = new QuadColored(77, -62, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[1]->setVAlign(Alignment::CENTER);			
				$carPartRequirementsBackground[2] = new QuadColored(77, -67, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[2]->setVAlign(Alignment::CENTER);				
				$carPartRequirementsBackground[3] = new QuadColored(77, -72, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[3]->setVAlign(Alignment::CENTER);				
				$carPartRequirementsBackground[4] = new QuadColored(77, -77, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[4]->setVAlign(Alignment::CENTER);		
				$carPartRequirementsBackground[5] = new QuadColored(77, -82, 0.1, 18, 4, '3338');
				$carPartRequirementsBackground[5]->setVAlign(Alignment::CENTER);
				
				$carPartRequirementsBackground[6] = new QuadColored(80 + 16, -57, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[6]->setVAlign(Alignment::CENTER);
				$carPartRequirementsBackground[7] = new QuadColored(80 + 16, -62, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[7]->setVAlign(Alignment::CENTER);				
				$carPartRequirementsBackground[8] = new QuadColored(80 + 16, -67, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[8]->setVAlign(Alignment::CENTER);				
				$carPartRequirementsBackground[9] = new QuadColored(80 + 16, -72, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[9]->setVAlign(Alignment::CENTER);				
				$carPartRequirementsBackground[10] = new QuadColored(80 + 16, -77, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[10]->setVAlign(Alignment::CENTER);
				$carPartRequirementsBackground[11] = new QuadColored(80 + 16, -82, 0.1, 20, 4, '3338');
				$carPartRequirementsBackground[11]->setVAlign(Alignment::CENTER);
				
				$displayFrame->append($carPartRequirementsBackground);
				
				$carPartRequirements[0] = new Label(78 + 16, -55.75, 0.2, '$sSlinik: ');
				$carPartRequirements[0]->setHAlign(Alignment::RIGHT);
				$carPartRequirements[1] = new Label(78 + 16, -60.75, 0.2, '$sSkrzynia biegów: ');
				$carPartRequirements[1]->setHAlign(Alignment::RIGHT);
				$carPartRequirements[2] = new Label(78 + 16, -65.75, 0.2, '$sZawieszenie: ');
				$carPartRequirements[2]->setHAlign(Alignment::RIGHT);
				$carPartRequirements[3] = new Label(78 + 16, -70.75, 0.2, '$sHamulce: ');
				$carPartRequirements[3]->setHAlign(Alignment::RIGHT);
				$carPartRequirements[4] = new Label(78 + 16, -75.75, 0.2, '$sOpony: ');
				$carPartRequirements[4]->setHAlign(Alignment::RIGHT);
				$carPartRequirements[5] = new Label(78 + 16, -80.75, 0.2, '$sTurbosprężarka: ');
				$carPartRequirements[5]->setHAlign(Alignment::RIGHT);
				
				$carPartRequirements[6] = new Label(78 + 19, -55.75, 0.2, ($car->getEngineMaxTier() > 0 ? '$smax. MK' . $car->getEngineMaxTier() : '$s$f00nie można założyć!'));
				$carPartRequirements[7] = new Label(78 + 19, -60.75, 0.2, ($car->getGearboxMaxTier() > 0 ? '$smax. MK' . $car->getGearboxMaxTier() : '$s$f00nie można założyć!'));
				$carPartRequirements[8] = new Label(78 + 19, -65.75, 0.2, ($car->getSuspensionMaxTier() > 0 ? '$smax. MK' . $car->getSuspensionMaxTier() : '$s$f00nie można założyć!'));
				$carPartRequirements[9] = new Label(78 + 19, -70.75, 0.2, ($car->getHandlingMaxTier() > 0 ? '$smax. MK' . $car->getHandlingMaxTier() : '$s$f00nie można założyć!'));
				$carPartRequirements[10] = new Label(78 + 19, -75.75, 0.2, ($car->getTyresMaxTier() > 0 ? '$smax. MK' . $car->getTyresMaxTier() : '$s$f00nie można założyć!'));
				$carPartRequirements[11] = new Label(78 + 19, -80.75, 0.2, ($car->getTurboMaxTier() > 0 ? '$smax. MK' . $car->getTurboMaxTier() : '$s$f00nie można założyć!'));
				$displayFrame->append($carPartRequirements);
				
				$partSlot[0] = new GarageSlot(0, 0, $partItemStack[0], $player, 'Silnik', $currentGarageId);
				$partSlot[1] = new GarageSlot(1, 0, $partItemStack[1], $player, 'Skrzynia biegów', $currentGarageId);
				$partSlot[2] = new GarageSlot(2, 0, $partItemStack[2], $player, 'Zawieszenie', $currentGarageId);
				$partSlot[3] = new GarageSlot(3, 0, $partItemStack[3], $player, 'Hamulce', $currentGarageId);
				$partSlot[4] = new GarageSlot(4, 0, $partItemStack[4], $player, 'Opony', $currentGarageId);
				$partSlot[5] = new GarageSlot(5, 0, $partItemStack[5], $player, 'Turbosprężarka', $currentGarageId);	
				$partSlot[6] = new GarageSlot(0, 1, $partItemStack[6], $player, 'Pierwszy dodatek', $currentGarageId);
				$partSlot[7] = new GarageSlot(1, 1, $partItemStack[7], $player, 'Drugi dodatek', $currentGarageId);
				$partSlot[8] = new GarageSlot(2, 1, $partItemStack[8], $player, 'Neon', $currentGarageId);
				
				$displayFrame->append($partSlot);
			}
		}
		
	}
	
}

class GarageSlot extends Container {

	public function __construct($x, $y, ItemStack $itemStack = null, Player $player, $slotName, $currentGarageId) {		
		$posX = 46 + ( $x * 12 );
		$posX_Label = $posX + 5;
			
		$posY = $y > 0 ? -28 : -14;
		$posY_Label = $y > 0 ? -26 : -12;
		
		$item = ( $itemStack != null ? $itemStack->getItem() : null );
		$itemDamage = ( $itemStack != null ? $itemStack->getDurbality() : 0 );
			
		$slotLabel = new LabelDimensioned($posX_Label, $posY_Label, 0.2, 13, 0, '$s$888' . $slotName, false, 0.8);
		$slotLabel->setVAlign(Alignment::CENTER);
		$slotLabel->setHAlign(Alignment::CENTER);
		self::append($slotLabel);
			
		$slotBackground = null;
		$slotBackgroundFocus = null;
			
		if ( $item != null ) {
			
			$rarityId = $itemStack->getItemRarityId();
			
			$slotBackground = TXP::HOME_URL . 'gfx/inventory/slot_t' . $rarityId . '_l1.png';
			$slotBackgroundFocus = TXP::HOME_URL . 'gfx/inventory/slot_t' . $rarityId . '_l2.png';
			
		} else {
			$slotBackground = TXP::HOME_URL . 'gfx/inventory/slot_t0_l1.png';
		}
		$slotImage = new QuadImaged($posX, $posY, 0.2, 10, 10, $slotBackground, $slotBackgroundFocus);

		if ( $item != null ) {
			if ( $item instanceof Engine ) {
				$partId = 0;
			} else if ( $item instanceof Gearbox ) {
				$partId = 1;
			} else if ( $item instanceof Suspension ) {
				$partId = 2;
			} else if ( $item instanceof Handling ) {
				$partId = 3;
			} else if ( $item instanceof Tyres ) {
				$partId = 4;
			} else if ( $item instanceof Turbo ) {
				$partId = 5;
			} else if ( $item instanceof FirstAdditional ) {
				$partId = 6;
			} else if ( $item instanceof SecondAdditional ) {
				$partId = 7;
			} else if ( $item instanceof Neon ) {
				$partId = 8;
			}
			
			if ( $player->getInventory()->isInventoryFree() ) {
				$slotImage->setActionId(Groups::GARAGE . $currentGarageId . Garage::ACTION_UNMOUNT_PART . $partId . '0');
			}
			
			$icon = new QuadImaged($posX + 9.5, $posY - 9, 0.6, 3, 3.5, TXP::HOME_URL . 'gfx/inventory/icons/info.png', TXP::HOME_URL . 'gfx/inventory/icons/infof.png');
			$icon->setActionId(Groups::GARAGE . $currentGarageId . Garage::ACTION_PART_INFO . $partId);
			$icon->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			self::append($icon);
		}
		self::append($slotImage);
			
		if ( $item != null ) {
			$itemImage = new QuadImaged($posX + 0.5, $posY - 0.5, 0.3, 9, 9, $item->getImage());
			self::append($itemImage);

			if ( $item->getMaxDamage() != 0 ) {
				$damageBackground = new QuadColored($posX + 0.25, $posY - 9, 0.4, 9.5, 0.5, '080F');
				self::append($damageBackground);
				
				$damageForeground = new QuadColored($posX + 0.25, $posY - 9, 0.5, ( ( $itemDamage / $item->getMaxDamage() ) * 9.5), 0.5, '0F0F');
				self::append($damageForeground);
			}
		}
	}

}

$garage = new Garage($menu, $actionId, $player);

?>
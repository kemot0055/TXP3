<?php

class CarDealer {
	
	const ACTION_BUY_CAR = 1;
	const DIALOG_DISPLAY = 0;
	
	public function __construct(Menu $menu, Player $player, $actionId) {
		
		$displayFrame = $menu->getDisplayFrame();
		
		$carId = intval($actionId[2] . $actionId[3]);
		$dealerActionId = intval($actionId[4]);
		
		$car = TXP::getCar( ($carId + 1) );
		$carsSize = 26;
		
		if ( $dealerActionId == CarDealer::ACTION_BUY_CAR ) {
			$actionState = intval($actionId[5]);
		
			if ( $actionState == CarDealer::DIALOG_DISPLAY ) {
				BetterDialogs::createBackground($displayFrame);
				BetterDialogs::createWindow($displayFrame, 54, 13.5);
		
				$confirmationLabel = new LabelDimensioned(64, -44.75, BetterDialogs::getElementZ(), 51, 0, '$sCzy na pewno chcesz kupić pojazd za ' . TXP::formatCash($car->getPrice()) . ' $0afVR$fff?');
				$confirmationLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
				$displayFrame->append($confirmationLabel);
		
				BetterDialogs::createButton($displayFrame, 64 - 24 / 2, -50, BetterDialogs::getElementZ(), 24, 'Tak', (Groups::CAR_DEALER . self::createActionCarID($carId) . CarDealer::ACTION_BUY_CAR . '1'));
				BetterDialogs::createButton($displayFrame, 64 + 24 / 2, -50, BetterDialogs::getElementZ(), 24, 'Nie', (Groups::CAR_DEALER . self::createActionCarID($carId) . '0'));
			} else {
				$garageId = $player->getEmptyGarage();
				if ( $garageId == -1 ) {
					BetterDialogs::createBackground($displayFrame);
					BetterDialogs::createWindow($displayFrame, 52, 14);
		
					$errorLabel = new LabelDimensioned(64, -44.5, BetterDialogs::getElementZ(), 51, 0, '$s$f00Wystąpił błąd podczas kupna pojazdu!', false, 1.25);
					$errorLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
					$displayFrame->append($errorLabel);
		
					BetterDialogs::createButton($displayFrame, 64, -50.5, BetterDialogs::getElementZ(), 32, 'OK', (Groups::CAR_DEALER . self::createActionCarID($carId) . '0'));
				} else {
					$player->addCash(-$car->getPrice());
					$menu->updateVrBar($player);
		
					$garage = $player->getPlayerGarage($player->getEmptyGarage());
					$garage->attachCar($car->getId());
				}
			}
		}
		
		$acceleration = $car->getBaseAcceleration();
		$maxSpeed = $car->getBaseMaxSpeed();
		$steering = $car->getBaseSteering();

		$accelerationBar = ($acceleration / 1000) * 30;
		$maxSpeedBar = ($maxSpeed / 1000) * 30;
		$steeringBar = ($steering / 1000) * 30;
		
		if ( $accelerationBar > 30 ) {
			$accelerationBar = 30;
		}
		if ( $maxSpeedBar > 30 ) {
			$maxSpeedBar = 30;
		}
		if ( $steeringBar > 30 ) {
			$steeringBar = 30;
		}
		
		$average = floor( ($acceleration + $maxSpeed + $steering) / 3 );

		if ( $average > 1000 ) {
			$class = TXP::$CAR_RARITIES[7] . 'SS';
		} else if ( $average > 800 ) {
			$class = TXP::$CAR_RARITIES[6] . 'S';
		} else if ( $average > 650 ) {
			$class = TXP::$CAR_RARITIES[5] . 'A';
			$currentTabId = 5;
		} else if ( $average > 500 ) {
			$class = TXP::$CAR_RARITIES[4] . 'B';
			$currentTabId = 4;
		} else if ( $average > 350 ) {
			$class = TXP::$CAR_RARITIES[3] . 'C';
			$currentTabId = 3;
		} else if ( $average > 250 ) {
			$class = TXP::$CAR_RARITIES[2] . 'D';
			$currentTabId = 2;
		} else if ( $average > 100 ) {
			$class = TXP::$CAR_RARITIES[1] . 'E';
			$currentTabId = 1;
		} else {
			$class = TXP::$CAR_RARITIES[0] . 'F';
			$currentTabId = 0;
		}
				
		$background = new QuadColored(7, 0, 0, 128 - 14, 72, '0008');
		$displayFrame->append($background);
		
		$carLabel = new Label(64, -3.5, 0.1, '$sDealer samochodowy (' . ($carId + 1) . '/' . $carsSize . '): ' . $car->getName());
		$carLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		$displayFrame->append($carLabel);
		
		$carImage = new QuadImaged(12.5, -10, 0.1, 30, 30, $car->getImage());
		$displayFrame->append($carImage);
		
		$carDescription = new Label(12.5 + 30 + 1, -10, 0.1, '$sOpis pojazdu: ' . $car->getDescription());
		$displayFrame->append($carDescription);
		
		$carInfo = new Label(12.5 + 30 + 1, -13, 0.1, '$sŚrednia: ' . $average . ', klasa pojazdu: ' . $class);
		$displayFrame->append($carInfo);
		
		$statLabels[0] = new Label(12.5 + 30 + 1, -19, 0.1, '$sPrzyspieszenie: ' . $acceleration);
		$statBackgrounds[0] = new QuadColored(12.5 + 30 + 1, -22, 0.1, 30, 1.25, '888F');
		$statForegrounds[0] = new QuadColored(12.5 + 30 + 1, -22, 0.2, $accelerationBar, 1.25, 'BBBF');
		
		$statLabels[1] = new Label(12.5 + 30 + 1, -24, 0.1, '$sMaksymalna prędkość: ' . $maxSpeed);
		$statBackgrounds[1] = new QuadColored(12.5 + 30 + 1, -27, 0.1, 30, 1.25, '888F');
		$statForegrounds[1] = new QuadColored(12.5 + 30 + 1, -27, 0.2, $maxSpeedBar, 1.25, 'BBBF');
		
		$statLabels[2] = new Label(12.5 + 30 + 1, -29, 0.1, '$sPrzyczepność: ' . $steering);
		$statBackgrounds[2] = new QuadColored(12.5 + 30 + 1, -32, 0.1, 30, 1.25, '888F');
		$statForegrounds[2] = new QuadColored(12.5 + 30 + 1, -32, 0.2, $steeringBar, 1.25, 'BBBF');
		
		$displayFrame->append($statLabels);
		$displayFrame->append($statBackgrounds);
		$displayFrame->append($statForegrounds);
		
		$carPrice = new Label(12.5 + 30 + 1, -37, 0.1, '$sCena samochodu: ' . TXP::formatCash($car->getPrice()) . ' $0afVR$fff, wymagany poziom: ' . $car->getLevelRequirement());
		$displayFrame->append($carPrice);
		
		$buyActionId = null;
		if ( ($player->getPlayerCarsCount() < $player->getGarageSize()) && ($player->getCash() >= $car->getPrice()) && ($player->getLevel() >= $car->getLevelRequirement())) {
			$buyActionId = (Groups::CAR_DEALER . self::createActionCarID($carId) . CarDealer::ACTION_BUY_CAR . '0');
		}
		
		BetterDialogs::createButton($displayFrame, 12.5 + 30 / 2, -44, 0.1, 30, 'Kup pojazd', $buyActionId);
		self::createArrows($carId, $carsSize, $displayFrame);
		self::createClassTabs($currentTabId, $displayFrame);
		
		$carPartRequirementsBackground[0] = new QuadColored(77, -43, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[0]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[1] = new QuadColored(77, -48, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[1]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[2] = new QuadColored(77, -53, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[2]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[3] = new QuadColored(77, -58, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[3]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[4] = new QuadColored(77, -63, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[4]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[5] = new QuadColored(77, -68, 0.1, 18, 4, '3338');
		$carPartRequirementsBackground[5]->setVAlign(Alignment::CENTER);
		
		$carPartRequirementsBackground[6] = new QuadColored(80 + 16, -43, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[6]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[7] = new QuadColored(80 + 16, -48, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[7]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[8] = new QuadColored(80 + 16, -53, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[8]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[9] = new QuadColored(80 + 16, -58, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[9]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[10] = new QuadColored(80 + 16, -63, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[10]->setVAlign(Alignment::CENTER);
		$carPartRequirementsBackground[11] = new QuadColored(80 + 16, -68, 0.1, 20, 4, '3338');
		$carPartRequirementsBackground[11]->setVAlign(Alignment::CENTER);
		
		$displayFrame->append($carPartRequirementsBackground);
		
		$carPartRequirements[0] = new Label(78 + 16, -41.75, 0.2, '$sSlinik: ');
		$carPartRequirements[0]->setHAlign(Alignment::RIGHT);
		$carPartRequirements[1] = new Label(78 + 16, -46.75, 0.2, '$sSkrzynia biegów: ');
		$carPartRequirements[1]->setHAlign(Alignment::RIGHT);
		$carPartRequirements[2] = new Label(78 + 16, -51.75, 0.2, '$sZawieszenie: ');
		$carPartRequirements[2]->setHAlign(Alignment::RIGHT);
		$carPartRequirements[3] = new Label(78 + 16, -56.75, 0.2, '$sHamulce: ');
		$carPartRequirements[3]->setHAlign(Alignment::RIGHT);
		$carPartRequirements[4] = new Label(78 + 16, -61.75, 0.2, '$sOpony: ');
		$carPartRequirements[4]->setHAlign(Alignment::RIGHT);
		$carPartRequirements[5] = new Label(78 + 16, -66.75, 0.2, '$sTurbosprężarka: ');
		$carPartRequirements[5]->setHAlign(Alignment::RIGHT);
		
		$carPartRequirements[6] = new Label(78 + 19, -41.75, 0.2, ($car->getEngineMaxTier() > 0 ? '$smax. MK' . $car->getEngineMaxTier() : '$s$f00nie można założyć!'));
		$carPartRequirements[7] = new Label(78 + 19, -46.75, 0.2, ($car->getGearboxMaxTier() > 0 ? '$smax. MK' . $car->getGearboxMaxTier() : '$s$f00nie można założyć!'));
		$carPartRequirements[8] = new Label(78 + 19, -51.75, 0.2, ($car->getSuspensionMaxTier() > 0 ? '$smax. MK' . $car->getSuspensionMaxTier() : '$s$f00nie można założyć!'));
		$carPartRequirements[9] = new Label(78 + 19, -56.75, 0.2, ($car->getHandlingMaxTier() > 0 ? '$smax. MK' . $car->getHandlingMaxTier() : '$s$f00nie można założyć!'));
		$carPartRequirements[10] = new Label(78 + 19, -61.75, 0.2, ($car->getTyresMaxTier() > 0 ? '$smax. MK' . $car->getTyresMaxTier() : '$s$f00nie można założyć!'));
		$carPartRequirements[11] = new Label(78 + 19, -66.75, 0.2, ($car->getTurboMaxTier() > 0 ? '$smax. MK' . $car->getTurboMaxTier() : '$s$f00nie można założyć!'));
		$displayFrame->append($carPartRequirements);
	}
	
	private function createArrows($carId, $carsSize, Frame $displayFrame) {
		$lastPageBg = new QuadImaged(4.5, 0, 1, 2.5, 72, TXP::HOME_URL . 'gfx/inventory/pageNormal.png');
		if( ($carId - 1) >= 0 ) {
			$lastPageBg->setImageAfterFocus(TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
			$lastPageBg->setActionId(Groups::CAR_DEALER . self::createActionCarID($carId - 1) . '00');
		}
		$displayFrame->append($lastPageBg);
		
		$lastPageArrow = new Label(4.5 + (2.5 / 2), -(72 / 2), 2, ( $carId == 0 ? '$666' : '' ) . '$s«', 1.4);
		$lastPageArrow->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		$displayFrame->append($lastPageArrow);
		
		$nextPageBg = new QuadImaged(121, 0, 1, 2.5, 72, TXP::HOME_URL . 'gfx/inventory/pageNormal.png');
		if( ($carId + 1) < $carsSize ) {
			$nextPageBg->setImageAfterFocus(TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
			$nextPageBg->setActionId(Groups::CAR_DEALER . self::createActionCarID($carId + 1) . '00');
		}
		$displayFrame->append($nextPageBg);
		
		$nextPageArrow = new Label(121 + (2.5 / 2), -(72 / 2), 2, ( ($carId + 1) == $carsSize ? '$666' : '' ) . '$s»', 1.4);
		$nextPageArrow->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
		$displayFrame->append($nextPageArrow);
	}
	
	private function createClassTabs($currentTabId, Frame $displayFrame) {
		$classCars = array(0, 3, 8, 12, 15, 21);
		$classes = array('F', 'E', 'D', 'C', 'B', 'A');
		$classesRequirements = array('0-100', '100-250', '250-350', '350-500', '500-650', '650-800');
		
		$offset = (119 - (sizeof($classes) * 12));
		$offset /= (sizeof($classes) - 1);
		$offset += 12;
		
		for ( $i = 0; $i < sizeof($classes); $i++ ) {
			$isCurrentTab = ( $i == $currentTabId );
			
			$tab = new QuadImaged(4.5 + ($offset * $i), -74, 0, 12, 12, TXP::HOME_URL . 'gfx/inventory/' . ( $isCurrentTab ? 'pageFocused.png' : 'pageNormal.png'));
			if ( !$isCurrentTab ) {
				$tab->setImageAfterFocus(TXP::HOME_URL . 'gfx/inventory/pageFocused.png');
			}
			$tab->setActionId(Groups::CAR_DEALER . self::createActionCarID($classCars[$i]) . '0');
			$displayFrame->append($tab);
			
			$label = new Label(4.5 + (12 / 2) + ($offset * $i), -75, 0.1, 'Klasa');
			$label->setHAlign(Alignment::CENTER);
			$displayFrame->append($label);
			
			$requirements = new Label(4.5 + (12 / 2) + ($offset * $i), -83, 0.1, '$888(' . $classesRequirements[$i] . ')', 0.75);
			$requirements->setHAlign(Alignment::CENTER);
			$displayFrame->append($requirements);
			
			$classLabel = new Label(4.5 + (12 / 2) + ($offset * $i), -78.55, 0.1, '$o' . TXP::$CAR_RARITIES[$i] . $classes[$i], 1.25);
			$classLabel->setHAlign(Alignment::CENTER);
			$displayFrame->append($classLabel);
		}
	}
	
	private function createActionCarID($carId) {
		$idSize = strlen($carId);
		return ( $idSize > 1 ? $carId : ('0' . $carId) );
	}
	
}

$carDealer = new CarDealer($menu, $player, $actionId);

?>
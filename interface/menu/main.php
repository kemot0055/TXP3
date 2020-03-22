<?php

// Home, Inventory, Gara�, Sklep, Ulepszenia, Profil, Achievementy, Rankingi, Exit

// Home - wraca do menu g��wnego gdzie s� newsy
// Inventory - plecak gracza gdzie znajduj� si� aktualnie posiadane przez niego przedmioty kt�re mo�e u�yc (z wy��czeniem podzespo��w do samochodu)
// Gara� - pokazuje aktualnie wybrany gara� w kt�rym znajduje si� pojazd, jego statystyki, jakie cz�ci ma zamontowane wraz z ich wytrzyma�o�ci�, ile paliwa pojazd ma, a je�eli zaznaczy konkr�tny podzesp� samochodu np. silnik to wy�wietla si� lista aktualnie posiadanych silnik�w kt�re zalegaj� w jego inventory i mo�e go wybra� oczywi�cie p�ac�c za to VR za mechanika, je�eli gracz ma unikatowy item i go u�yje kt�ry powoduje �e montowanie/wymontowanie jest za darmo to ma za darmo XDDDDD, a opr�cz tego je�eli gracz ma wi�cej gara�y to mo�e zaznaczy� inny gara� i go wybra� jako aktywny
// Sklep - wy�wietla itemy do kupienia za VR kt�re mo�na u�y� w samochodzie np. neony, lub nametagi kt�re zmieniaj� nick na naszym profilu oraz jego wy�wietlanie pod koniec trasy. W sklepie jest tak�e dealer samochodowy w kt�rym mo�na kupi� nowy samoch�d (je�eli ma wolne miejsce w gara�u) i wiele wiele innych rzeczy takie jak np. rynek spo�eczno�ciowy
// Ulepszenia - to konsola kt�ra pozwala ulepszy� og�em profil postaci np. co lvl up ma nowe ulepszenie do odblokowania i s� mo�liwe cztery drogi kt�ra ka�da ma inne efekty
// Profil - wy�wietla og�lne informacje o profilu na TXP, jego przypinka, nickname czy awatar
// Achievementy - wy�wietla osi�gni�cia zdobyte poczas ca�ej gry na TXP
// Rankingi - wy�wietla r�ne tabele rankingowe
// Exit - wychodzi do gry

class Menu extends Container {

	private $displayFrame;
	
	private $vrBar;
	private $vrLabel;
	
	private $powerUpBar;
	private $powerUpLabel;
	
	private $menuIcons;
	
	public function __construct($actionId, Player $player) {
		$groupId = intval($actionId[0] . $actionId[1]);
		
		$this->displayFrame = new Frame(-64, 48, 16); {
			//XXX: Zdecydować się apropo teł
			$background = new QuadImaged(0, 0, -1, 128, 96, TXP::HOME_URL . 'gfx/bg' . 3 . '.jpg');
			$background->setActionId(0);
			
			if( $groupId == Groups::MAIN_MENU ) {
				$window[0] = new QuadColored(5, 0, 0, 128 - 10, 15, '0008');
				$window[1] = new QuadColored(5, -16, 0, 128 - 10, 4, '0008');
				$window[2] = new QuadColored(5, -16-4.5, 0, 128 - 10, 35-5, '0008');
				$window[3] = new QuadColored(5, -52.5, 0, 128 - 10, 4, '0008');
				$window[4] = new QuadColored(5, -52-5, 0, 128 - 10, 36-5, '0008');
				$this->displayFrame->append($window);
				
				$txpLogo = new QuadImaged(3.75, 3, 0.1, 15, 20, TXP::HOME_URL . 'gfx/menu/icon_menu1.png');
				$this->displayFrame->append($txpLogo);
				
				$txpLabel = new Label(4 + 12, -4.5, 0.1, '$s$cccTXP v3', 1.5);
				$this->displayFrame->append($txpLabel);
				
				$txpVersion = new Label(8 + 12, -8.5, 0.1, '$s$cccwersja 1.1.2');
				$this->displayFrame->append($txpVersion);
				
				$sessionState = TXP::getSessionState();
				if ( $sessionState == 0 ) {
					$sessionState = '$f00wyłączona';
				} else if ( $sessionState == 1 ) {
					$sessionState = '$0f0włączona';
				} else if ( $sessionState == 2 ) {
					$sessionState = '$0f0włączona (CTXP)';
				}
				
				$txpSession = new Label(120, -8.5, 0.1, '$s$cccSesja: ' . $sessionState);
				$txpSession->setHAlign(Alignment::RIGHT);
				$this->displayFrame->append($txpSession);
				
				$firstNewsHeader = new Label(7, -16.75, 0.1, '$s$ccc' . TXP::getFirstNewsTitle());
				$this->displayFrame->append($firstNewsHeader);
				
				$firstNewsContent = new LabelDimensioned(7, -21.2, 0.1, 115, 0, '$s$ccc' . TXP::getFirstNews(), true);
				$this->displayFrame->append($firstNewsContent);
				
				$secondNewsHeader = new Label(7, -53.15, 0.1, '$s$ccc' . TXP::getSecondNewsTitle());
				$this->displayFrame->append($secondNewsHeader);
				
				$secondNewsContent = new LabelDimensioned(7, -58.15, 0.1, 115, 0, '$s$ccc' . TXP::getSecondNews(), true);
				$this->displayFrame->append($secondNewsContent);
			}
			
			$this->displayFrame->append($background);
		}
		
		$barFrame = new Frame(-64, -42, 16); {
		
			$bars[0] = new QuadStyled(0, 0, 0, 32, 9, 'BgsPlayerCard', 'BgCard');
			$bars[1] = new QuadStyled(31.85, 1, 0.1, 64.3, 9, 'BgsPlayerCard', 'BgCard');
			$bars[2] = new QuadStyled(96, 0, 0, 32, 9, 'BgsPlayerCard', 'BgCard');
			
			$txpMax = $player->getCurrentLevelTxp();
			$txpMam = $player->getTxp() - $player->getPreviousLevelTxp();
			$percent = $txpMam / $txpMax;
			
			$vrMam = $player->getCash();
			$percent2 = $vrMam / 5000;
			
			if ( $percent > 1 ) {
				$percent = 1;
			} 
			if ( $percent2 > 1 ) {
				$percent2 = 1;
			}
			
			$progressBarLabel = new Label(64, -4.25, 0.4, '$sPostęp: ' . $txpMam . '/' . $txpMax, 0.75);
			$progressBarLabel->setVAlign(Alignment::CENTER);
			$progressBarLabel->setHAlign(Alignment::CENTER);
			$barFrame->append($progressBarLabel);
			
			$txpForeground = new QuadImaged(34, -4, 0.3, $percent * 60, 1, TXP::HOME_URL . 'gfx/menu/barTXP1.png');
			$barFrame->append($txpForeground);
			
			$txpBackground = new QuadImaged(34, -4, 0.2, 60, 1, TXP::HOME_URL . 'gfx/menu/barTXP.png');
			$barFrame->append($txpBackground);
			
			$this->vrBar = new QuadImaged(34, -1, 0.3, $percent2 * 26, 1, TXP::HOME_URL . 'gfx/menu/barVR1.png');
			$barFrame->append($this->vrBar);
			
			$vrBackground = new QuadImaged(34, -1, 0.2, 26, 1, TXP::HOME_URL . 'gfx/menu/barVR.png');
			$barFrame->append($vrBackground);
			
			$this->vrLabel = new Label(34 + 26 / 2, -1.25, 0.4, '$sVR: ' . TXP::formatCash($vrMam), 0.75);
			$this->vrLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$barFrame->append($this->vrLabel);
			
			$powerStack = $player->getCurrentPowerup();
			$percent3 = 0;
			
			if ( $powerStack != null ) {
				$percent3 = $powerStack->getDurbality() * 26;
				if ( $percent3 > 1 ) {
					$percent3 = 1;
				}				
				$puState = $powerStack->getDurbality() * 100 . '%'; 
			} else {
				$puState = 'Brak powerupu';	
			}
			
			$this->powerUpBar = new QuadImaged(68, -1, 0.3, $percent3 * 26, 1, TXP::HOME_URL . 'gfx/menu/barPU1.png');
			$barFrame->append($this->powerUpBar);
			
			$powerUpBackground = new QuadImaged(68, -1, 0.2, 26, 1, TXP::HOME_URL . 'gfx/menu/barPU.png');
			$barFrame->append($powerUpBackground);
			
			$this->powerUpLabel = new Label(68 + 26 / 2, -1.25, 0.4, '$sPowerup: ' . $puState, 0.75);
			$this->powerUpLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$barFrame->append($this->powerUpLabel);
			
			$levelLabel = new Label(64, -1, 0.2, '$s$0f0' . $player->getLevel(), 0.6, 'TextRaceChrono');
			$levelLabel->setVAlign(Alignment::CENTER)->setHAlign(Alignment::CENTER);
			$barFrame->append($levelLabel);
			
			$this->menuIcons[0] = new QuadImaged(6.25, 0, 0.1, 7, 7, TXP::HOME_URL . 'gfx/menu/icon_backpack.png', TXP::HOME_URL . 'gfx/menu/icon_backpack1.png');
			$this->menuIcons[0]->setActionId(Groups::INVENTORY . '0000');
			
			$this->menuIcons[1] = new QuadImaged(-1.25, 2.5, 0.1, 9, 11, TXP::HOME_URL . 'gfx/menu/icon_menu.png', TXP::HOME_URL . 'gfx/menu/icon_menu1.png');
			$this->menuIcons[1]->setActionId(Groups::MAIN_MENU . '000');
			
			$this->menuIcons[2] = new QuadImaged(12.5, 0, 0.1, 7, 7, TXP::HOME_URL . 'gfx/menu/icon_garage.png', TXP::HOME_URL . 'gfx/menu/icon_garage1.png');
			$this->menuIcons[2]->setActionId(Groups::GARAGE . $player->getCurrentGarageId() . '00');
			
			$this->menuIcons[3] = new QuadImaged(18.5, 0, 0.1, 7, 7, TXP::HOME_URL . 'gfx/menu/icon_market.png', TXP::HOME_URL . 'gfx/menu/icon_market1.png');
			$this->menuIcons[3]->setActionId(Groups::MARKET . '000');

			$this->menuIcons[4] = new QuadImaged(24.5, 0, 0.1, 7, 7, TXP::HOME_URL . 'gfx/menu/icon_rankings.png', TXP::HOME_URL . 'gfx/menu/icon_rankings1.png');
			$this->menuIcons[4]->setActionId(Groups::RANKINGS . '000');
			
			$this->menuIcons[5] = new QuadStyled(122, 0.2, 0.1, 6, 7, 'Icons128x128_1', 'BackFocusable');
			$this->menuIcons[5]->setActionId(Groups::TOOLBAR . '000');

			if( $groupId == Groups::MAIN_MENU ) {
				$this->menuIcons[1]->setImage($this->menuIcons[1]->getImageAfterFocus());
			} else if ( $groupId == Groups::INVENTORY ) {
				$this->menuIcons[0]->setImage($this->menuIcons[0]->getImageAfterFocus());
			} else if ( $groupId == Groups::GARAGE || $groupId == Groups::CAR_DEALER ) {
				$this->menuIcons[2]->setImage($this->menuIcons[2]->getImageAfterFocus());
			} else if ( $groupId == Groups::MARKET ) {
				$this->menuIcons[3]->setImage($this->menuIcons[3]->getImageAfterFocus());
			} else if ( $groupId == Groups::RANKINGS ) {
				$this->menuIcons[4]->setImage($this->menuIcons[4]->getImageAfterFocus());
			}
			
			$barFrame->append($this->menuIcons);
			$barFrame->append($bars);
		
		}
		
		self::append($this->displayFrame);
		self::append($barFrame);
		
	}
	
	public function getDisplayFrame() {
		return $this->displayFrame;
	}
	
	public function updateVrBar(Player $player) {
		$cash = $player->getCash();
		
		$barWidth = ($cash / 5000) * 26;
		if ( $barWidth > 26 ) {
			$barWidth = 26;
		}
		
		$this->vrBar->setWidth($barWidth);
		$this->vrLabel->setText('$sVR: ' . TXP::formatCash($cash));
	}
	
	public function updatePowerupBar(Player $player) {
		$powerStack = $player->getCurrentPowerup();
		
		$barWidth = ($powerStack->getDurbality() * 26);
		if ( $barWidth > 26 ) {
			$barWidth = 26;
		}
		
		$this->powerUpBar->setWidth($barWidth);
		$this->vrBar->setText('$sPowerup: ' . ($powerStack->getDurbality() * 100) . '%');
	}
	
	public function updateGarageShortcut(Player $player) {
		$currentGarage = $player->getCurrentGarageId();
		$this->menuIcons[2]->setActionId(Groups::GARAGE . $currentGarage . '00');
	}
	
}

$menu = new Menu($actionId, $player);

?>
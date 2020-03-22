<?php

class Rankings {
	
	public function __construct(Menu $menu, $actionId, Player $player) {
		$displayFrame = $menu->getDisplayFrame();
		
		$background[0] = new QuadColored(5, 0, 0, 128 - 10, 8, '0008');
		
		$background[1] = new QuadColored(5, -9, 0, 11, 5, '0008');
		$background[2] = new QuadColored(5 + 12, -9, 0, 63, 5, '0008');
		$background[3] = new QuadColored(5 + 12 + 64, -9, 0, 30, 5, '0008');
		$background[4] = new QuadColored(5 + 12 + 64 + 31, -9, 0, 11, 5, '0008');
		
		$background[5] = new QuadColored(5, -9 - 6, 0, 11, 64, '0008');
		$background[6] = new QuadColored(5 + 12, -9 - 6, 0, 63, 64, '0008');
		$background[7] = new QuadColored(5 + 12 + 64, -9 - 6, 0, 30, 64, '0008');
		$background[8] = new QuadColored(5 + 12 + 64 + 31, -9 - 6, 0, 11, 64, '0008');
		
		$background[9] = new QuadColored(5, -9 - 71, 0, 128 - 10, 8, '0008');
		
		$displayFrame->append($background);
		
		$infoLabels[0] = new Label(64, -2.25, 0.1, '$sRankingi graczy', 1.25);
		$infoLabels[0]->setHAlign(Alignment::CENTER);
		
		$infoLabels[1] = new Label(5 + (11 / 2), -9 - 2, 0.1, '$sPozycja', 0.95);
		$infoLabels[1]->setHAlign(Alignment::CENTER)->setVAlign(Alignment::CENTER);
		
		$infoLabels[2] = new Label(5 + 12 + (63 / 2), -9 - 2, 0.1, '$sNazwa gracza', 0.95);
		$infoLabels[2]->setHAlign(Alignment::CENTER)->setVAlign(Alignment::CENTER);

		$infoLabels[3] = new Label(5 + 12 + 64 + (30 / 2), -9 - 2, 0.1, '$sIlość TXP', 0.95);
		$infoLabels[3]->setHAlign(Alignment::CENTER)->setVAlign(Alignment::CENTER);
		
		$infoLabels[4] = new Label(5 + 12 + 64 + 31 + (11 / 2), -9 - 2, 0.1, '$sPoziom', 0.95);
		$infoLabels[4]->setHAlign(Alignment::CENTER)->setVAlign(Alignment::CENTER);
		
		$displayFrame->append($infoLabels);
		
		$rankLogins = TXP::getRankings();
		if ( $rankLogins != null ) {
			for ( $i = 0; $i < sizeof ( $rankLogins ); $i++ ) {
				$player = TXP::getPlayer($rankLogins[$i][0]);
				$playerProperties[0] = new Label(5 + (11 / 2), -16 - (3.5 * $i), 0.1, '$s' . ($i + 1));
				$playerProperties[0]->setHAlign(Alignment::CENTER);
				
				$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
				
				$playerNick = '$fff' . $player->getPlayerNick();
				if ( $playerGarage->getCar() != null ) {
					$neonStack = $playerGarage->getNeonItemStack();
				
					if ( $neonStack != null ) {
						$stack = $neonStack->getItem();
						$playerNick = $stack->paintNick( $player->getPlayerNick() );
					}
				}
				
				$playerProperties[1] = new Label(5 + 12 + (63 / 2), -16 - (3.5 * $i), 0.1, '$s' . $playerNick);
				$playerProperties[1]->setHAlign(Alignment::CENTER);
				
				$playerProperties[2] = new Label(5 + 12 + 64 + (30 / 2), -16 - (3.5 * $i), 0.1, '$s' . $player->getTxp());
				$playerProperties[2]->setHAlign(Alignment::CENTER);
				
				$playerProperties[3] = new Label(5 + 12 + 64 + 31 + (11 / 2), -16 - (3.5 * $i), 0.1, '$s' . $player->getLevel());
				$playerProperties[3]->setHAlign(Alignment::CENTER);
				
				$displayFrame->append($playerProperties);
			}
		}
		
		$button[0] = new Button((128 / 2) - 8 - (32 / 2), -83.5, 0.1, 32, 'Casual');
		$button[0]->setIsEnabled(false);
		$button[1] = new Button((128 / 2) + 8 + (32 / 2), -83.5, 0.1, 32, 'Competetive');
		$button[1]->setIsEnabled(false);
		$displayFrame->append($button);
	}
	
}

$rankings = new Rankings($menu, $actionId, $player);

?>
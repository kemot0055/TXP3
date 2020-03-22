<?php

class Toolbar extends Frame {
	
	private $txpIcon;
	
	private $buttonBg;
	private $buttonLabel;
	
	public function __construct() {
		parent::__construct(50, -25, 0);
		
		$this->txpIcon = new QuadImaged(1.75, -3, 1, 3.25, 3.25, TXP::HOME_URL . 'gfx/toolbarLogo.png');
		self::append($this->txpIcon);
		
		$this->buttonBg = new QuadStyled(0, -2.25, 0, 18, 5, 'BgsPlayerCard', 'BgPlayerCardBig');
		$this->buttonBg->setActionId(Groups::MAIN_MENU . '000');
		self::append($this->buttonBg);
		
		$this->buttonLabel = new Label(6.75, -4.5, 1, '$sMenu');
		$this->buttonLabel->setVAlign(Alignment::CENTER);
		self::append($this->buttonLabel);
	}
	
}

$toolbar = new Toolbar();

?>
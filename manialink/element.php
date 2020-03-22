<?php

final class Alignment {
	
	const RIGHT = "right";
	const LEFT = "left";
	const CENTER = "center";
	const TOP = "top";
	const BOTTOM = "bottom";
	
}

abstract class Element {
	
	private $valign, $halign;
	
	public function __construct() {
		$this->valign = Alignment::TOP;
		$this->halign = Alignment::LEFT;
	}
	
	public abstract function getX();
	
	public abstract function getY();
	
	public abstract function getZ();
	
	public abstract function setX($posX);
	
	public abstract function setY($posY);
	
	public abstract function setZ($posZ);
	
	public function setVAlign($align) {
		$this->valign = $align;
		return $this;
	}
	
	public function getVAlign() {
		return $this->valign;
	}
	
	public function setHAlign($align) {
		$this->halign = $align;
		return $this;
	}
	
	public function getHAlign() {
		return $this->halign;
	}
	
	public abstract function buildElement();
	
}

class Frame extends Element {
	
	private $posX, $posY, $posZ;
	private $elementList;
	
	public function __construct($x, $y, $z) {
		$this->posX = $x;
		$this->posY = $y;
		$this->posZ = $z;
	}
	
	public function append($element) {
		if( is_array($element) ) {
			for($i = 0; $i < sizeof($element); $i++) {
				self::append($element[$i]);
			}
		} else {
			$this->elementList[sizeof($this->elementList)] = $element;
		}
	}
	
	public function getX() {
		return $this->posX;
	}
	
	public function getY() {
		return $this->posY;
	}
	
	public function getZ() {
		return $this->posZ;
	}
	
	public function setX($posX) {
		$this->posX = $posX;
	}
	
	public function setY($posY) {
		$this->posY = $posY;
	}
	
	public function setZ($posZ) {
		$this->posZ = $posZ;
	}
	
	public function buildElement() {
		$frameStart = '<frame posn="' . $this->posX . ' ' . $this->posY . ' ' . $this->posZ . '">' . PHP_EOL;
		$elementContent = '';
		
		for($i = 0; $i < sizeof($this->elementList); $i++) {
			$elementContent .= $this->elementList[$i]->buildElement();		
		}
		
		return $frameStart . $elementContent . '</frame>' . PHP_EOL;
	}
}

class Container extends Element {
	
	private $elementList;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getX() {
		return null;
	}
	
	public function getY() {
		return null;
	}
	
	public function getZ() {
		return null;
	}
	
	public function setX($posX) {}
	
	public function setY($posY) {}
	
	public function setZ($posZ) {}
	
	public function append($element) {
		if( is_array($element) ) {
			for($i = 0; $i < sizeof($element); $i++) {
				self::append($element[$i]);
			}
		} else {
			$this->elementList[sizeof($this->elementList)] = $element;
		}
	}
	
	public function buildElement() {
		$elementContent = '';
		for($i = 0; $i < sizeof($this->elementList); $i++) {
			$elementContent .= $this->elementList[$i]->buildElement();
		}
		
		return $elementContent;
	}
	
}

abstract class Quad extends Element {
	
	private $posX, $posY, $posZ;
	private $width, $height;
	
	private $actionId;
	
	public function __construct($x, $y, $z, $w, $h) {
		parent::__construct();
		
		$this->posX = $x;
		$this->posY = $y;
		$this->posZ = $z;
		
		$this->width = $w;
		$this->height = $h;
		
		$this->actionId = null;
	}
	
	public function setX($posX) {
		$this->posX = $posX;
	}
	
	public function setY($posY) {
		$this->posY = $posY;
	}
	
	public function setZ($posZ) {
		$this->posZ = $posZ;
	}
	
	public function setWidth($width) {
		$this->width = $width;
		return $this;
	}
	
	public function setHeight($height) {
		$this->height = $height;
		return $this;
	}
	
	public function setActionId($actionId) {
		$this->actionId = $actionId;	
		return $this;
	}
	
	public function getX() {
		return $this->posX;
	}
	
	public function getY() {
		return $this->posY;
	}
	
	public function getZ() {
		return $this->posZ;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function getActionId() {
		return $this->actionId;
	}
	
	public function buildElement() {
		return '<quad posn="' . $this->posX . ' ' . $this->posY . ' ' . $this->posZ . '" sizen="' . $this->width . ' ' . $this->height . '" ' .
				
 				$this->buildQuad() .

				( $this->actionId !== null ? ' action="' . $this->actionId . '"' : '') . 
		
				' valign="' . $this->getVAlign() . '" halign="' . $this->getHAlign() . '" />' . PHP_EOL;
	}
	
	public abstract function buildQuad();
	
}

class QuadColored extends Quad {
	
	private $colorCode;
	
	public function __construct($x, $y, $z, $w, $h, $colorCode) {
		parent::__construct($x, $y, $z, $w, $h);
		$this->colorCode = $colorCode;
	}

	public function buildQuad() {
		return 'bgcolor="' . $this->colorCode . '"';
	}
	
	public function setBgColor($colorCode) {
		$this->colorCode = $colorCode;
	}
	
	public function getBgColor() {
		return $this->colorCode;
	}
	
}

class QuadImaged extends Quad {

	private $image, $imageFocus;
	
	public function __construct($x, $y, $z, $w, $h, $image, $imageFocus = null) {
		parent::__construct($x, $y, $z, $w, $h);
		
		$this->image = $image;
		$this->imageFocus = $imageFocus;
	}
	
	public function buildQuad() {
		return 'image="' . $this->image . '"' . ( ($this->imageFocus != null) ? ' imagefocus="' . $this->imageFocus . '"' : '');
	}
	
	public function setImage($imagePath) {
		$this->image = $imagePath;
	}
	
	public function setImageAfterFocus($imagePath) {
		$this->imageFocus = $imagePath;
	}
	
	public function getImage() {
		return $this->image;
	}
	
	public function getImageAfterFocus() {
		return $this->imageFocus;
	}
	
}

class QuadStyled extends Quad {
	
	private $style, $substyle;
	
	public function __construct($x, $y, $z, $w, $h, $style, $substyle) {
		parent::__construct($x, $y, $z, $w, $h);
		
		$this->style = $style;
		$this->substyle = $substyle;
	}
	
	public function buildQuad() {
		return 'style="' . $this->style . '" substyle="' . $this->substyle . '"';
	}
	
	public function setStyle($style) {
		$this->style = $style;
	}
	
	public function setSubStyle($substyle) {
		$this->substyle = $substyle;
	}
	
	public function getStyle() {
		return $this->style;
	}
	
	public function getSubStyle() {
		return $this->substyle;
	}
	
}

class Label extends Element {
	
	private $posX, $posY, $posZ;
	private $textScale;
	private $textStyle;
	private $text;
	
	public function __construct($x, $y, $z, $text, $scale = 1, $textStyle = null) {
		parent::__construct();
		
		$this->posX = $x;
		$this->posY = $y;
		$this->posZ = $z;
		
		$this->text = $text;
		$this->textScale = $scale;
		$this->textStyle = $textStyle;
	}
	
	public function setX($posX) {
		$this->posX = $posX;
	}
	
	public function setY($posY) {
		$this->posY = $posY;
	}
	
	public function setZ($posZ) {
		$this->posZ = $posZ;
	}
	
	public function setText($text) {
		$this->text = $text;
	}
	
	public function setScale($textScale) {
		$this->textScale = $textScale;
	}
	
	public function setStyle($textStyle) {
		$this->textStyle = $textStyle;
	}
	
	public function getX() {
		return $this->posX;
	}
	
	public function getY() {
		return $this->posY;
	}
	
	public function getZ() {
		return $this->posZ;
	}
	
	public function getText() {
		return $this->text;
	}
	
	public function getScale() {
		return $this->textScale;
	}
	
	public function getStyle() {
		return $this->textStyle;
	}
	
	public function buildElement() {
		return '<label posn="' . $this->posX . ' ' . $this->posY . ' ' . $this->posZ . '" text="' . $this->text . '" scale="' . $this->textScale . '"' . ($this->textStyle != null ? ' style="' . $this->textStyle . '"' : '') . ' valign="' . self::getVAlign() . '" halign="' . self::getHAlign() . '" />' . PHP_EOL;
	}
	
}

class LabelDimensioned extends Label {
	
	private $width, $height;
	private $autonewline;
	
	public function __construct($x, $y, $z, $w, $h, $text, $autonewline = false, $scale = 1, $textStyle = null) {
		parent::__construct($x, $y, $z, $text, $scale, $textStyle);
		
		$this->width = $w;
		$this->height = $h;
		
		$this->autonewline = $autonewline;
	}
	
	public function setWidth($width) {
		$this->width = $width;
	}
	
	public function setHeight($height) {
		$this->height = $height;
	}
	
	public function setAutoNewLine($autonewline) {
		$this->autonewline = $autonewline;	
	}
	
	public function isAutoNewLine() {
		return $this->autonewline;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function buildElement() {
		return '<label posn="' . self::getX() . ' ' . self::getY() . ' ' . self::getZ() . '" sizen="' . $this->width . ' ' . $this->height . '" scale="' . self::getScale() . '"' . (self::getStyle() != null ? ' style="' . self::getStyle() . '"' : '') . ' autonewline="' . ( $this->autonewline ? '1' : '0' ) . '" text="' . self::getText() . '" valign="' . self::getVAlign() . '" halign="' . self::getHAlign() . '" />' . PHP_EOL;
	}
	
}

class Button extends Container {
	
	private $posX, $posY, $posZ;
	private $width;
	private $text;
	
	private $isEnabled;
	
	private $button;
	private $label;
	
	public function __construct($x, $y, $z, $w, $text) {
		parent::__construct();
		
		$this->button = new QuadStyled($x, $y, $z, $w, 4.5, 'Bgs1InRace', 'BgButton');
		$this->button->setVAlign(Alignment::CENTER);
		$this->button->setHAlign(Alignment::CENTER);
		
		$this->label = new LabelDimensioned($x, $y + 0.35, $z + 1, $w - 3.5, 0, '$o$222' . $text);
		$this->label->setVAlign(Alignment::CENTER);
		$this->label->setHAlign(Alignment::CENTER);
		
		self::append($this->button);
		self::append($this->label);
		
		$this->posX = $x;
		$this->posY = $y;
		$this->posZ = $z;
		
		$this->width = $w;
		
		$this->text = $text;
		$this->actionId = null;
		$this->isEnabled = true;
	}
	
	public function setX($posX) {
		$this->button->setX($posX);
		$this->label->setX($posX + ($this->button->getWidth() / 2));
		
		$this->posX = $posX;
	}
	
	public function setY($posY) {
		$this->button->setY($posY);
		$this->label->setY($posY + 0.35);

		$this->posY = $posY;
	}
	
	public function setZ($posZ) {
		$this->button->setZ($posZ);
		$this->label->setZ($posZ + 1);
		
		$this->posZ = $posZ;
	}
	
	public function setWidth($width) {
		$this->button->setWidth($width);
		$this->label->setX($this->button->getX() + ($width / 2));
		
		$this->width = $width;
	}
	
	public function setActionId($actionId) {
		$this->button->setActionId($actionId);
		$this->actionId = $actionId;
	}
	
	public function setIsEnabled($isEnabled) {
		$this->isEnabled = $isEnabled;
		$this->button->setSubStyle($isEnabled ? 'BgButton' : 'BgPager');
		$this->label->setText('$o$' . ($isEnabled ? '222' : '555') . $this->text);
	}
	
	public function setText($text) {
		$this->text = $text;
		$this->label->setText('$o$' . ($this->isEnabled ? '222' : '555') . $text);
	}
	
	public function getX() {
		return $this->posX;
	}
	
	public function getY() {
		return $this->posY;
	}
	
	public function getZ() {
		return $this->posZ;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getActionId() {
		return $this->actionId;
	}
	
	public function isEnabled() {
		return $this->isEnabled;
	}
	
}

?>
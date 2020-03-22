<?php

class Car {
	
	const TYPE_COMBUSTION = 0;
	const TYPE_ELECTRIC = 1;
	
	private $carId;
	private $database;
	
	private $carType;
	private $carPrice;
	private $levelRequirement;
	
	private $carName;
	private $carDesc;
	private $carImage;
	
	private $carAcceleration;
	private $carMaxSpeed;
	private $carSteering;
	
	private $carEngineMaxTier;
	private $carGearboxMaxTier;
	private $carSuspensionMaxTier;
	private $carHandlingMaxTier;
	private $carTyresMaxTier;
	private $carTurboMaxTier;
	
	public function __construct($carId, Database $database) {
		$this->carId = $carId;
		$this->database = $database;
		
		$this->carType = $database->getVarInfoFromCar($carId, 'car_type');
		$this->carPrice = $database->getVarInfoFromCar($carId, 'car_value');
		$this->levelRequirement = $database->getVarInfoFromCar($carId, 'level_requirement');
		
		$this->carName = $database->getVarInfoFromCar($carId, 'car_name');
		$this->carDesc = $database->getVarInfoFromCar($carId, 'car_desc');
		$this->carImage = $database->getVarInfoFromCar($carId, 'car_image');
		
		$this->carAcceleration = $database->getVarInfoFromCar($carId, 'car_acceleration');
		$this->carMaxSpeed = $database->getVarInfoFromCar($carId, 'car_maxspeed');
		$this->carSteering = $database->getVarInfoFromCar($carId, 'car_steering');
		
		$this->carEngineMaxTier = $database->getVarInfoFromCar($carId, 'car_max_engine_tier');
		$this->carGearboxMaxTier = $database->getVarInfoFromCar($carId, 'car_max_gearbox_tier');
		$this->carSuspensionMaxTier = $database->getVarInfoFromCar($carId, 'car_max_suspension_tier');
		$this->carHandlingMaxTier = $database->getVarInfoFromCar($carId, 'car_max_handling_tier');
		$this->carTyresMaxTier = $database->getVarInfoFromCar($carId, 'car_max_tyres_tier');
		$this->carTurboMaxTier = $database->getVarInfoFromCar($carId, 'car_max_turbo_tier');
	}
	
	public function getId() {
		return $this->carId;
	}
	
	public function getType() {
		return $this->carType->getValue();
	}
	
	public function getPrice() {
		return $this->carPrice->getValue();
	}
	
	public function getLevelRequirement() {
		return $this->levelRequirement->getValue();
	}
	
	public function getName() {
		return $this->carName->getValue();
	}
	
	public function getDescription() {
		return $this->carDesc->getValue();
	}
	
	public function getImage() {
		return $this->carImage->getValue();
	}
	
	public function getBaseAcceleration() {
		return $this->carAcceleration->getValue();
	}
	
	public function getBaseMaxSpeed() {
		return $this->carMaxSpeed->getValue();
	}
	
	public function getBaseSteering() {
		return $this->carSteering->getValue();
	}
	
	public function getEngineMaxTier() {
		return $this->carEngineMaxTier->getValue();
	}
	
	public function getGearboxMaxTier() {
		return $this->carGearboxMaxTier->getValue();
	}
	
	public function getSuspensionMaxTier() {
		return $this->carSuspensionMaxTier->getValue();
	}
	
	public function getHandlingMaxTier() {
		return $this->carHandlingMaxTier->getValue();
	}
	
	public function getTyresMaxTier() {
		return $this->carTyresMaxTier->getValue();
	}
	
	public function getTurboMaxTier() {
		return $this->carTurboMaxTier->getValue();
	}
	
}

?>
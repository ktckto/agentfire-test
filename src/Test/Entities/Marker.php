<?php

namespace AgentFire\Plugin\Test\Entities;
/**
 * @Marker
 *
 * */
class Marker {
	/**
	 * @var string
	 */
	public string $id;
	/**
	 * @var string
	 */
	public string $longitude;
	/**
	 * @var string
	 */
	public string $latitude;

	/**
	 * @param $id
	 */
	public function __construct($id){
		$this->id=$id;
	}
	/**
	 * @method load
	 * Load values into object
	 */
	public function load(){
		$this->getLongitude();
		$this->getLatitude();
	}

	/**
	 * @return string
	 */
	public function getLongitude(): string {
		if(empty($this->longitude)){
			$this->longitude=str_replace(',', '.',
				get_post_meta($this->id,'longitude',true) ?? '');
		}

		return $this->longitude;
	}

	/**
	 * @return string
	 */
	public function getLatitude(): string {
		if(empty($this->latitude)){
			$this->latitude=str_replace(',', '.',
				get_post_meta($this->id,'latitude',true) ?? '');
		}
		return $this->latitude;
	}

	/**
	 *
	 * @return array
	 */
	public function getPosition():array{
		return [
			'id'=>$this->id,
			'longitude'=>$this->getLongitude(),
			'latitude'=>$this->getLatitude()];
	}
}
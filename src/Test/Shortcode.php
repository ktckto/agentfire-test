<?php

declare( strict_types=1 );

namespace AgentFire\Plugin\Test;

use Twig\{Environment, Loader};
use AgentFire\Plugin\Test\Traits\Singleton;

/**
 * Class Shortcode
 * @package AgentFire\Plugin\Test
 *
 * Use to add shortcode and define shortcode callback function
 */
class Shortcode {
	use Singleton;

	public function __construct() {
		//register shortcode
		add_shortcode( 'agentfire_test',[self::class,'render']);

	}
	//callback
	public function render($atts,$content,$shortcode_tag){
		return Template::getInstance()->render( 'main.twig' );
	}

}
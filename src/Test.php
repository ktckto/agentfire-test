<?php

declare( strict_types=1 );

namespace AgentFire\Plugin;

use AgentFire\Plugin\Test\PostType\Marker;
use AgentFire\Plugin\Test\Shortcode;
use AgentFire\Plugin\Test\Traits\Singleton;
use AgentFire\Plugin\Test\Rest;
use AgentFire\Plugin\Test\Admin;

/**
 * Class Test
 * @package AgentFire\Plugin\Test
 */
class Test {
	use Singleton;

	public function __construct() {
		//add custom post type, taxonomy
		Marker::getInstance();
		//Setup shortcode
		Shortcode::getInstance();
		//Setup REST
		Rest::getInstance();
		//Setup admin settings page
		Admin::getInstance();
	}
}

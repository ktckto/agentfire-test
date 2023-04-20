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
		add_action('wp_enqueue_scripts', [self::class,'registerScripts']);

	}
	public function registerScripts(){
		wp_register_script('mapbox',plugin_dir_url(__DIR__).'/assets/src/js/test.js',['jquery'],'1.0',true);
		wp_localize_script('mapbox','settings',self::getJsData());
		wp_enqueue_script('mapbox');
	}
	public function getJsData(){
		return [
			'MAPBOX_API_KEY'=>get_field('metabox_key','options'),
			'endpointURL'=>get_site_url().'/wp-json/'.Rest::NAMESPACE. Rest::REST_BASE . '/markers'
		];
	}
}

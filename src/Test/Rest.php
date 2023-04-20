<?php

declare( strict_types=1 );

namespace AgentFire\Plugin\Test;

use AgentFire\Plugin\Test\Entities\Marker;
use AgentFire\Plugin\Test\Traits\Singleton;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Rest
 * @package AgentFire\Plugin\Test
 */
class Rest {
	use Singleton;

	/**
	 * @var string Endpoint namespace
	 */
	const NAMESPACE = 'agentfire/v1/';

	/**
	 * @var string Route base
	 */
	const REST_BASE = 'test';

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * Register endpoints
	 */
	public static function registerRoutes() {
		register_rest_route( self::NAMESPACE, self::REST_BASE . '/markers', [
			'show_in_index' => false,
			'methods'       => [ WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ],
			'callback'      => [ self::class, 'markers' ],
			'args'          => [],

		] );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function markers( WP_REST_Request $request ) {
		$response=[
			'status'=>'ok',
			'data'=>[]
		];
		//if(empty($request)){
			//get all markers
			$query=self::queryMarkers();
			$markerIds=$query->posts;
			$markers=array_map(function($id){
				$marker= new Marker($id);
				$marker->load();
				return $marker;
				},$markerIds);
			$response['data']=$markers;
		//}

		return new WP_REST_Response( [$response] );
	}

	private static function queryMarkers(){
		$args=[
			'post_type'=>'marker',
			'fields'=>'ids'
		];
		return new \WP_Query($args);
	}

}

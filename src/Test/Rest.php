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
		register_rest_route( self::NAMESPACE, self::REST_BASE . '/getMarkerDateTitle', [
			'show_in_index' => false,
			'methods'       => [ WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ],
			'callback'      => [ self::class, 'getMarkerDateTitle' ],
			'args'          => [],
		] );
	}
	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function getMarkerDateTitle(WP_REST_Request $request){
		$marker_id=$request->get_param('id');
		if(empty($marker_id)){
			return new WP_REST_Response([]);
		}
		$title=get_the_title($marker_id);
		if(empty($title)){
			$title="Marker";
		}
		$date=get_the_date('dS M Y',$marker_id);
		if(empty($date)){
			$date='';
		}
		return new WP_REST_Response([
			'id'=>$marker_id,
			'date'=>$date,
			'title'=>$title
		]);
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
		$user_id=get_current_user_id();
		if(!empty($user_id)){
			$response['user_id']=$user_id;
		}

		$filters=$request->get_param('tags');
		if(empty($filters)){
			$filters=[];
		}
		if(!empty($filters) and !empty($user_id)){

			$filteredQuery=self::queryMarkers($user_id,$filters);
		}
		elseif(!empty($user_id)){
			$query=self::queryMarkers($user_id,null);
		}
		elseif(!empty($filters)){
			$query=self::queryMarkers(null,$filters);
		}
		else {
			$query=self::queryMarkers();
		}



		$query=self::queryMarkers(null,$filters);
		$markerIds=$query->posts;
		$currentUserMarkerIds=[];
		if(!empty($user_id)){
			$currentUserMarkerIds=self::queryMarkers($user_id)->posts;
		}
		$markers=array_map(function($id) use ( $currentUserMarkerIds,$user_id ) {
			$marker= new Marker($id);
			$markerData=$marker->getPosition();
			if(empty($user_id)){
				return $markerData;
			}
			elseif(in_array($id,$currentUserMarkerIds)){
				$markerData['user_id']=$user_id;
			}
			return $markerData;
			},$markerIds);
		$response['data']=$markers;
		//}

		return new WP_REST_Response( [$response] );
	}

	private static function queryMarkers($user_id=null,$filters=null){
		$args=[
			'post_type'=>'marker',
			'fields'=>'ids'
		];
		if(!empty($user_id)){
			$args['author__in']=$user_id;
		}
		if(!empty($filters)){
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'tag',
					'field' => 'term_id',
					'terms' => $filters
				));
		}
		return new \WP_Query($args);
	}

}

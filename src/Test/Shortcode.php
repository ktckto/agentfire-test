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

	/**
	 * Add shortcode, specify callback
	 */
	public function __construct() {
		add_shortcode( 'agentfire_test',[self::class,'render']);

	}

	/**
	 * @param $atts
	 * @param $content
	 * @param $shortcode_tag
	 *
	 * @return string
	 * @throws Exception\Template
	 * Shortcode callback
	 */
	public function render($atts,$content,$shortcode_tag){
		$context=[
			'user_id'=>get_current_user_id(),
			'tags'=>self::getTags()
		];
		return Template::getInstance()->render( 'main.twig',$context );
	}
	public function getTags(){
		$wp_terms=get_terms([
			'taxonomy'=>'tag',
		'hide_empty'=>false
				]
		);
		$tags=array_map(function($term){
			$tag=[
				'id'=>$term->term_id,
				'name'=>$term->name,
			];
			return $tag;
		},$wp_terms);
		return $tags;
	}
}
<?php
declare( strict_types=1 );
namespace AgentFire\Plugin\Test\PostType;
use Twig\{Environment, Loader};
use AgentFire\Plugin\Test\Traits\Singleton;

/**
 * Marker custom post type
 * @package AgentFire\Plugin\Test
 *
 */
class Marker {
	use Singleton;
	public function __construct() {
		add_action('init',[self::class,'markerPostType']);
		add_action('add_meta_boxes_marker',[self::class,'addMetaBox']);
		add_action('save_post_marker',[self::class,'savePost']);
	}

	//callback
	public function markerPostType(){
		$args = array(
			'public'       => true,
			'show_in_rest' => true,
			//'rest_namespace'=>'markers',
			//'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rest_base' => 'markers',
			'label'        => 'Markers',
			'supports'=>['title','custom-fields'],
			'taxonomies'=>['marker_tag'],
			'rewrite'=>false
		);
		register_post_type( 'marker', $args );
	}
	public function addMetaBox(){
		global $wp_meta_boxes;
		add_meta_box('Coordinates',__("Coordinates"),[self::class,'metaBoxHTML'],
			'marker','normal','high');
	}
	public function metaBoxHTML(){
		global $post;
		$fields=get_post_custom($post->ID);
		$longitude= $fields["longitude"][0] ?? '';
		$latitude=$fields["latitude"][0] ?? '';
		//Todo: Either move html to a template or maybe use ACF?
		//TODO: Add validation to admin side
		?>
		<label for="longitude">
			Longitude:
			<input name="longitude" value="<?=$longitude ?>">
		</label>
		<br>
		<label for="latitude">
			Latitude:
			<input name="latitude" value="<?=$latitude ?>">
		</label>

<?php
	}
	public function savePost(){
		if(empty($_POST)) return;
		global $post;
		update_post_meta($post->ID,'longitude',$_POST['longitude']);
		update_post_meta($post->ID,'latitude',$_POST['latitude']);
	}
}
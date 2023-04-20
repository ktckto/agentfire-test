<?php
declare( strict_types=1 );
namespace AgentFire\Plugin\Test\PostType;
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
        add_action('init',[self::class,'markerTagTaxonomy']);
		add_action('add_meta_boxes_marker',[self::class,'addMetaBox']);
		add_action('save_post_marker',[self::class,'savePost']);
        //add longitude and latitude to JSON response
        add_filter('rest_prepare_marker',[self::class,'addLongLatToJson'],10,3);
	}

	//callback
	public function markerPostType(){
		$args = array(
			'public'       => true,
			'show_in_rest' => true,
			'rest_base' => 'markers',
			'label'        => 'Markers',
			'supports'=>['title','custom-fields'],
			'taxonomies'=>['marker_tag'],
			'rewrite'=>false
		);
		register_post_type( 'marker', $args );
	}
    public function markerTagTaxonomy(){
        $args=[
                'label'=>__('Tag',AGENTFIRE_I18N_SLUG),
                'public'=>true,
                'show_in_rest'=>true,

        ];
        register_taxonomy('tag','marker',$args);
    }
	public function addMetaBox(){
		global $wp_meta_boxes;
		add_meta_box('Coordinates',__("Coordinates",AGENTFIRE_I18N_SLUG),[self::class,'metaBoxHTML'],
			'marker','normal','high');
	}
	public function metaBoxHTML(){
		global $post;
        $longitude=self::getLongitude($post->ID);
		$latitude=self::getLatitude($post->ID);
		//Todo: Either move html to a template or maybe use ACF?
		//TODO: Add validation to admin side
		?>
        <label for="latitude">
            Latitude:
            <input min="-90" max="90" name="latitude" value="<?=$latitude ?>">
        </label>
        <br>
		<label for="longitude">
			Longitude:
			<input name="longitude" value="<?=$longitude ?>">
		</label>

<?php
	}
	public function savePost(){
		if(empty($_POST)) return;
		global $post;
		update_post_meta($post->ID,'longitude',$_POST['longitude']);
		update_post_meta($post->ID,'latitude',$_POST['latitude']);
	}
    public function addLongLatToJson($data,$post,$context){
        $longitude=self::getLongitude($post->ID);
        $latitude=self::getLatitude($post->ID);
        if(($latitude) and ($longitude)){

            $data->data['longitude']=$longitude;
            $data->data['latitude']=$latitude;
        }
        return $data;
    }
    public function getLongitude($post_id){
	    return get_post_meta($post_id,'longitude',true) ?? '';
    }

    public function getLatitude($post_id){
	    return get_post_meta($post_id,'latitude',true) ?? '';
    }
}
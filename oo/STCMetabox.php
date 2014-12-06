<?php

class STCMetabox{
	private $stc;

	public function __construct( $stc ){
		$this->stc = $stc;
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action ( 'publish_post', array( $this, 'save_post_data' ) );
		add_action ( 'publish_page', array( $this, 'save_post_data' ) );
	}
	public function add_meta_box() {
		add_meta_box( 
				'social_traffic_commando',
				__( 'Social Traffic Commando', "{$this->plugin_namespace}domain" ),
				array( $this, 'inner_custom_box'),
				'post' 
		);
		add_meta_box(
				'social_traffic_commando',
				__( 'Social Traffic Commando', "{$this->plugin_namespace}domain" ), 
				array( $this, 'inner_custom_box'),
				'page'
		);
	}
	public function inner_custom_box( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), "social_traffic_commando_noncename" );
		include("{$this->stc->plugin_dir}views/metabox.php");
	}
	public function save_post_data( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return;


		if ( !wp_verify_nonce( $_POST['social_traffic_commando_noncename'], plugin_basename( __FILE__ ) ) )
				return;


		// Check permissions
		if ( 'page' == $_POST['post_type'] ) 
		{
			if ( !current_user_can( 'edit_page', $post_id ) ){
					return;
			} else{
				if( $this->stc->get_option('url_shortener') ){
					$backlink = $this->stc->stcpost->url_shorten( get_permalink( $post_id ) );
				} else {
					$backlink = get_permalink( $post_id );
				}
				$title = trim( html_entity_decode( get_the_title( $post_id ), ENT_NOQUOTES, 'UTF-8' ) );
				if( TRUE ){
					if(  FALSE ){
						$this->stc->stcpost->post_to_tumblrs( $backlink,  $title, trim($_POST['tumblr']) );
						$this->stc->stcpost->post_to_plurks( $backlink,  $title, trim($_POST['plurk'])  );
						$this->stc->stcpost->post_to_ljs( $backlink,  $title, trim($_POST['lj']) );
						//$this->stc->stcpost->post_to_reddits( $backlink,  $title, trim($_POST['reddit']) );
						$this->stc->stcpost->post_to_deliciouss( $backlink,  $title, trim($_POST['delicious']) );
						$this->stc->stcpost->post_to_twitters( $backlink,  $title, trim($_POST['twitter']) );
						$this->stc->stcpost->post_to_diigos( $backlink,  $title, trim($_POST['diigo']) );
						$this->stc->stcpost->post_to_linkedins( $backlink,  $title, trim($_POST['linkedin']) );
					} else {										
						$content = get_post_field( 'post_content', $post_id );
						$content = do_shortcode( $content );	
						$meta = array( 'action' => 'post', 'post_id' => $post_id );
						if($_POST['tumblr_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'tumblr', $backlink, 
											$title, trim( $content ), $meta );
						}
						if($_POST['scribd_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'scribd', $backlink, 
											$title, $content, $meta );
						}
						$this->stc->stcpost->add_to_queue( 'tumblr', $backlink, 
											$title, trim($_POST['tumblr']), $meta );
						$this->stc->stcpost->add_to_queue( 'plurk', $backlink,  
											$title, trim($_POST['plurk']), $meta  );
						$this->stc->stcpost->add_to_queue( 'lj', $backlink,  
											$title, trim($_POST['lj']), $meta );
						//$this->stc->stcpost->add_to_queue( 'reddit', $backlink,  
						//					$title, trim($_POST['reddit']), $meta );
						$this->stc->stcpost->add_to_queue( 'twitter', $backlink,  
											$title, trim($_POST['twitter']), $meta );
						$this->stc->stcpost->add_to_queue( 'linkedin', $backlink, 
											$title, trim($_POST['linkedin']), $meta );
						$this->stc->stcpost->add_to_queue( 'friendfeed', $backlink, 
											$title, trim($_POST['friendfeed']), $meta );
						$this->stc->stcpost->add_to_queue( 'gplus', $backlink, 
											$title, trim($_POST['gplus']), $meta );
						$this->stc->stcpost->add_to_queue( 'stumbleupon', $backlink, 
											$title, trim($_POST['stumbleupon']), $meta );
							
						$meta = array( 'action' => 'bookmark', 'post_id' => $post_id );
						$this->stc->stcpost->add_to_queue( 'diigo', $backlink,  
											$title, trim($_POST['diigo']), $meta );
						$this->stc->stcpost->add_to_queue( 'delicious', $backlink,  
											$title, trim($_POST['delicious']), $meta );

						$post_images = $this->stc->get_images_from_html( $content );
						$pin_images = explode( "\r\n", trim( $_POST['pinterest'] ) );
						$pin_images = is_array($pin_images) ? array_filter( $pin_images ) : array();
						$images = array_merge( $pin_images, $post_images );
						if( count( $images  ) ){
							foreach( $images  as $image  ){
								if( strpos($image, "http://" ) === FALSE ) continue;
								$meta = array( 'action' => 'pin', 
										'post_id' => $post_id,
										'image' => $image );
								$this->stc->stcpost->add_to_queue( 'pinterest', $backlink,  
											$title, $title, $meta );

							}
							$count = count( $images  );
							$index = rand(0, $count - 1 );
							$facebook_image = $images[$index];
						}
						$meta = array( 'action' => 'post', 
								'post_id' => $post_id, 
								'image' => $facebook_image,
								'snippet' => substr( $content , 0, 150 ) );
						$this->stc->stcpost->add_to_queue( 'facebook', get_permalink( $post_id ),  
											$title, $_POST['facebook'], $meta );

						$tags = wp_get_post_tags( 122 ); 
						$categories = get_the_category( 122 );
						$tgs = array();
						foreach( $tags as $tag ){
							$tgs[] .= $tag->name;
						}
						$cts = array();				
						foreach( $categories as $category ){
							$cts[] .= $category->name;
						}
						$meta = array( 'action' => 'post', 
								'post_id' => $post_id, 
								'tags' => implode( "," , $tgs ),
								'categories' => implode( "," , $cts ),
								'image' => $facebook_image,
								'snippet' => substr( $content , 0, 150 ) );
						if($_POST['wordpress_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'wordpress', $backlink, 
											$title, $content, $meta );
						}
						$this->stc->stcpost->add_to_queue( 'wordpress', $backlink, 
											$title, trim($_POST['wordpress']), $meta );
					}
				}
			}
		}
		else
		{
			if ( !current_user_can( 'edit_post', $post_id ) ){
					return;
			} else{			
				if( $this->stc->get_option('url_shortener') ){
					$backlink = $this->stc->stcpost->url_shorten( get_permalink( $post_id ) );
				} else {
					$backlink = get_permalink( $post_id );
				}
				$title = trim( html_entity_decode( get_the_title( $post_id ), ENT_NOQUOTES, 'UTF-8' ) );
				if( TRUE ){
					if(  FALSE ){
						$this->stc->stcpost->post_to_tumblrs( $backlink,  $title, trim($_POST['tumblr']) );
						$this->stc->stcpost->post_to_plurks( $backlink,  $title, trim($_POST['plurk'])  );
						$this->stc->stcpost->post_to_ljs( $backlink,  $title, trim($_POST['lj']) );
						//$this->stc->stcpost->post_to_reddits( $backlink,  $title, trim($_POST['reddit']) );
						$this->stc->stcpost->post_to_deliciouss( $backlink,  $title, trim($_POST['delicious']) );
						$this->stc->stcpost->post_to_twitters( $backlink,  $title, trim($_POST['twitter']) );
						$this->stc->stcpost->post_to_diigos( $backlink,  $title, trim($_POST['diigo']) );
						$this->stc->stcpost->post_to_linkedins( $backlink,  $title, trim($_POST['linkedin']) );
					} else{
						$content = get_post_field( 'post_content', $post_id );
						$content = do_shortcode( $content );	
						$meta = array( 'action' => 'post', 'post_id' => $post_id );
						if($_POST['tumblr_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'tumblr', $backlink, 
											$title, trim( $content ), $meta );
						}
						if($_POST['scribd_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'scribd', $backlink, 
											$title, $content, $meta );
						}
						$this->stc->stcpost->add_to_queue( 'tumblr', $backlink, 
											$title, trim($_POST['tumblr']), $meta );
						$this->stc->stcpost->add_to_queue( 'plurk', $backlink,  
											$title, trim($_POST['plurk']), $meta  );
						$this->stc->stcpost->add_to_queue( 'lj', $backlink,  
											$title, trim($_POST['lj']), $meta );
						//$this->stc->stcpost->add_to_queue( 'reddit', $backlink,  
						//					$title, trim($_POST['reddit']), $meta );
						$this->stc->stcpost->add_to_queue( 'twitter', $backlink,  
											$title, trim($_POST['twitter']), $meta );
						$this->stc->stcpost->add_to_queue( 'linkedin', $backlink, 
											$title, trim($_POST['linkedin']), $meta );
						$this->stc->stcpost->add_to_queue( 'friendfeed', $backlink, 
											$title, trim($_POST['friendfeed']), $meta );
						$this->stc->stcpost->add_to_queue( 'gplus', $backlink, 
											$title, trim($_POST['gplus']), $meta );
						$this->stc->stcpost->add_to_queue( 'stumbleupon', $backlink, 
											$title, trim($_POST['stumbleupon']), $meta );
							
						$meta = array( 'action' => 'bookmark', 'post_id' => $post_id );
						$this->stc->stcpost->add_to_queue( 'diigo', $backlink,  
											$title, trim($_POST['diigo']), $meta );
						$this->stc->stcpost->add_to_queue( 'delicious', $backlink,  
											$title, trim($_POST['delicious']), $meta );

						$post_images = $this->stc->get_images_from_html( $content );
						$pin_images = explode( "\r\n", trim( $_POST['pinterest'] ) );
						$pin_images = is_array($pin_images) ? array_filter( $pin_images ) : array();
						$images = array_merge( $pin_images, $post_images );
						if( count( $images  ) ){
							foreach( $images  as $image  ){
								if( strpos($image, "http://" ) === FALSE ) continue;
								$meta = array( 'action' => 'pin', 
										'post_id' => $post_id,
										'image' => $image );
								$this->stc->stcpost->add_to_queue( 'pinterest', $backlink,  
											$title, $title, $meta );

							}
							$count = count( $images  );
							$index = rand(0, $count - 1 );
							$facebook_image = $images[$index];
						}
						$meta = array( 'action' => 'post', 
								'post_id' => $post_id, 
								'image' => $facebook_image,
								'snippet' => substr( $content , 0, 150 ) );
						$this->stc->stcpost->add_to_queue( 'facebook', get_permalink( $post_id ),  
											$title, $_POST['facebook'], $meta );

						$tags = wp_get_post_tags( 122 ); 
						$categories = get_the_category( 122 );
						$tgs = array();
						foreach( $tags as $tag ){
							$tgs[] .= $tag->name;
						}
						$cts = array();				
						foreach( $categories as $category ){
							$cts[] .= $category->name;
						}
						$meta = array( 'action' => 'post', 
								'post_id' => $post_id, 
								'tags' => implode( "," , $tgs ),
								'categories' => implode( "," , $cts ),
								'image' => $facebook_image,
								'snippet' => substr( $content , 0, 150 ) );
						if($_POST['wordpress_content'] == 1 ){
							$this->stc->stcpost->add_to_queue( 'wordpress', $backlink, 
											$title, $content, $meta );
						}
						$this->stc->stcpost->add_to_queue( 'wordpress', $backlink, 
											$title, trim($_POST['wordpress']), $meta );
					}
				}
			}	

		}
	}
}
?>

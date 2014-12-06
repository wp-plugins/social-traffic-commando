<?php
if(!class_exists('OAuthException')){
	require_once dirname( __FILE__ ) . '/external/OAuth.php';
}
if(!class_exists('Spinner')){
	require_once dirname( __FILE__ ) . '/external/Spinner.php';
}
if(!class_exists('Diigo')){
	require_once dirname( __FILE__ ) . '/external/diigo/Diigo.php';
}
if(!class_exists('Plurk')){
	require_once dirname( __FILE__ ) . '/external/plurk/Plurk.php';
}
if(!function_exists('nxs_decodeEntities')){
	require_once dirname( __FILE__ ) . '/external/nxs_functions.php';
}
if(!class_exists('Scribd')){
	require_once dirname( __FILE__ ) . '/external/scribd/Scribd.php';
}
if(!class_exists('Tumblr')){
	require_once dirname( __FILE__ ) . '/external/tumblr/Tumblr.php';
}
if(!class_exists('Twitter')){
	require_once dirname( __FILE__ ) . '/external/twitter/Twitter.php';
}
if(!class_exists('LinkedInException')){
	require_once dirname( __FILE__ ) . '/external/linkedin/Linkedin.php';
}
if(!class_exists('Delicious')){
	require_once dirname( __FILE__ ) . '/external/delicious/Delicious.php';
}
if(!class_exists('FriendFeed')){
	require_once dirname( __FILE__ ) . '/external/friendfeed/FriendFeed.php';
}
if(!class_exists('LiveJournal')){
	require_once dirname( __FILE__ ) . '/external/livejournal/LiveJournal.php';
}

class STCPost{
	private $stc;

	public function __construct( $stc ){
		$this->stc = $stc;
	}
	public function reddit_check( $username, $password ){
		require_once dirname( __FILE__ ) . '/external/reddit/Reddit.php';

		$rd = new nxs_class_SNAP_RD;
		return $rd->doConnectToRD($username, $password);
	}
	public function reddit_post( $username, $password, $title, $link  ){
		$reddit = new Reddit( $username, $password );
		$subreddit = "todayilearned";
		$response = $reddit->createStory($title, $link, $subreddit);
	}
	public function gplus_check( $username, $password ){
		set_time_limit( 300 );
		require_once dirname( __FILE__ ) . '/external/gplus/postToGooglePlus.php';
  		$response = doConnectToGooglePlus2( $username, $password );
		if ( !$response ){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function blogger_check( $username, $password ){
		set_time_limit( 300 );
		require_once dirname( __FILE__ ) . '/external/gplus/GPlus.php';
		require_once dirname( __FILE__ ) . '/external/blogger/Blogger.php';
		//var_dump("$username, $password");    	
		$bg = new nxs_class_SNAP_BG;
		var_dump( $bg->nsBloggerGetAuth($username, $password) );		
		//$response = doConnectToBlogger($username, $password);var_dump($response);
		if ( !$response ){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function gplus_post( $username, $password, $title, $link, $description ){
		set_time_limit( 300 );
		require_once dirname( __FILE__ ) . '/external/gplus/postToGooglePlus.php';
  		$response = doConnectToGooglePlus2( $username, $password );
		if ( !$response ){
			return doPostToGooglePlus2("$description<br><a href=\"$link\">$link</a>");
		} else {
			return FALSE;
		}
	}
	public function diigo_check( $key, $username, $password ){
		$diigo = new Diigo( $key, $username, $password );
		return $diigo->get_bookmarks( );
	}
	public function friendfeed_check( $username, $key ){    
		$friendfeed = new FriendFeed( $username, $key );
		return $friendfeed->fetch_home_feed( );
	}
	public function friendfeed_like( $username, $key, $entry_id ){    
		$friendfeed = new FriendFeed( $username, $key );
		return $friendfeed->add_like( $entry_id );
	}
	public function friendfeed_post( $username, $key,  $title, $link, $description  ){    
		$friendfeed = new FriendFeed( $username, $key );
		return $friendfeed->publish_link( $description, $link );
	}
	public function diigo_post( $key, $username, $password, $title, $link, $description ){
		$diigo = new Diigo( $key, $username, $password );
		$title = strip_tags( $title );
		$description = strip_tags( $description );
		return $diigo->submit( $link, $title, $description );
	}
	public function spinrewritter( $content ){
		$email_address 	= $this->stc->get_option('sr_email');
		$api_key 	= $this->stc->get_option('sr_api_key');
		
		if( strlen( $email_address )  && strlen( $api_key  )  ){
			require_once dirname( __FILE__ ) . '/external/SpinRewriterAPI.php';
			$spinrewriter_api = new SpinRewriterAPI($email_address, $api_key);
			$api_response = $spinrewriter_api->getQuota();
	
			$response = isset($api_response['status']) && 
					( strtolower( $api_response['status'] ) == 'ok') 
					? TRUE : FALSE;
			if( $response ){
				$spinrewriter_api->setAutoProtectedTerms(false);
				$spinrewriter_api->setConfidenceLevel("medium");
				$spinrewriter_api->setNestedSpintax(true);
				$spinrewriter_api->setAutoSentences(false);
				$spinrewriter_api->setAutoParagraphs(false);
				$spinrewriter_api->setAutoNewParagraphs(false);
				$spinrewriter_api->setAutoSentenceTrees(false);
				$api_response = $spinrewriter_api->getUniqueVariation( $content );
				return isset($api_response['status']) && 
					( strtolower( $api_response['status'] ) == 'ok') 
					? $api_response['response'] : $content;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	}
	public function facebook_curl( $url, $data ) {
	 	$ch 		= curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
	   	curl_setopt( $ch, CURLOPT_CAINFO, dirname( __FILE__ ) . '/external/facebook/fb_ca_chain_bundle.crt');
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$response 	= curl_exec( $ch );
		$err_no		= curl_errno( $ch );
		curl_close( $ch );
		return json_decode( $response );
	}
	public function bestspinner( $content ){

		$username 	= $this->stc->get_option('bs_username');
		$password 	= $this->stc->get_option('bs_password');
		
		if( strlen( $username ) && strlen( $password  )  ){
			require_once dirname( __FILE__ ) . '/external/BestSpinner.php';
			$bestspinner = new BestSpinner( $username, $password );
			return $bestspinner->get_variation( $content );
		} else {
			return $content;
		}
	}
	public function pinterest_check( $email, $password  ){
		require_once dirname( __FILE__ ) . '/external/pinterest/Pinterest.php';
		$nt = new nxsAPI_PN();
		$response = $nt->connect($email, $password);
		if( !$response ){
			return $nt->getBoards();
		} else {
			return FALSE;
		}
	}
	public function pinterest_post( $email, $password, $description, $image, $link, $board_id ){
		require_once dirname( __FILE__ ) . '/external/pinterest/Pinterest.php';
		$response =  doConnectToPinterest( $email, $password );
		if ( !$response ){
			return doPostToPinterest( $description, $image, $link, $board_id );
		} else {
			return FALSE;
		}
	}
	public function stumbleupon_check( $email, $password  ){
		require_once dirname( __FILE__ ) . '/external/stumbleupon/StumbleUpon.php';

		$su = new nxs_class_SNAP_SU;
		$loginError = $su->nxs_doConnectToSU( $email, $password );
		if (!$loginError){
			return TRUE;
		} else return FALSE; 
	}
	public function stumbleupon_post( $email, $password, $title, $link, $cat, $tags ){
		require_once dirname( __FILE__ ) . '/external/stumbleupon/StumbleUpon.php';

		$title = strip_tags( $title );
		$su = new nxs_class_SNAP_SU;
		$loginError = $su->nxs_doConnectToSU( $email, $password );
		if (!$loginError){
			return $su->nxs_doPostToSU( $title, $link, $cat, $tags );
		} else return $loginError; 
	}
	public function delicious_check( $username, $password ){
		$delicious = new Delicious( $username, $password );
    		return $delicious->getTags();
	}
	public function wordpress_post( $access_token, $blog_id, $title, $link, $description, $tags = NULL, $categories = NULL ){
		$description = $description . "<br><br>For more on this article..<br>".
					"<a href=\"$link\">$link</a><br>";
		$options  = array (
		  'http' => 
		  array (
		    'ignore_errors' => false,
		    'method' => 'POST',
		    'header' => 
		    array (
		      0 => "authorization: Bearer $access_token",
		      1 => "Content-Type: application/x-www-form-urlencoded",
		    ),
		    'content' => http_build_query(   
		      array (
			'title' => $title,
			'content' => $description,
			'tags' => $tags,
			'categories' => $categories,
		      )
		    ),
		  ),
		);
		$context  = stream_context_create( $options );
		$response = file_get_contents(
		  "https://public-api.wordpress.com/rest/v1/sites/$blog_id/posts/new/",
		  false,
		  $context
		);
		return json_decode( $response );
	}
	public function delicious_post( $username, $password, $title, $link, $description ){
		$delicious = new Delicious( $username, $password );
		$title = strip_tags( $title );
		$description = strip_tags( $description );
		return $delicious->addUrl( $link, $title, 'shares', $description );
	}
	public function lj_check( $username, $password ){
		$lj = new LiveJournal( $username, $password );
    		return $lj->check_if_correct();
	}
	public function lj_post( $username, $password, $title, $link, $description  ){
		$lj = new LiveJournal(  $username, $password );
		$lj->set_post_title( $title );
		$lj->set_post_content( $description . ' ' . $link );
		return $lj->post_submit( );
	}
	public function scribd_post( $key, $secret, $content, $title = "" ){
		$doc_type = 'pdf';
		$access = null;
		$rev_id = null;

		$file_name = $this->stc->convert_to_pdf( $content, $title );
		if( count( $file_name ) ){
			$scribd = new Scribd( $key, $secret );
			if( isset( $file_name['path'] ) ) {
				$data = $scribd->upload( $file_name['path'], $doc_type, $access, $rev_id );
			} elseif ( isset( $file_name['url'] ) ) {
				$data = $scribd->uploadFromUrl( $file_name['url'], $doc_type, $access, $rev_id);
			}
		}
		if( isset( $data['doc_id'] ) ){
			return $data['doc_id'];
		} else {
			return FALSE;
		}
	}
	public function tumblr_post( $api_key, $api_secret, $access_token, 
					$access_token_secret, $username, 
					$title, $link, $description  ){

		$tumblr = new Tumblr(   $api_key, $api_secret,
					$access_token, $access_token_secret );	
		return $tumblr->post( "blog/$username.tumblr.com/post", 
			array('type' => 'link', 
				'url' =>  $link,
				'description' =>  $description, 
				'title' => $title )
			);
	}
	public function tumblr_get_post_info( $api_key, $api_secret, $access_token, 
					$access_token_secret, $username, 
					$post_id  ){

		$tumblr = new Tumblr(   $api_key, $api_secret,
					$access_token, $access_token_secret );	
		return $tumblr->post( "blog/$username.tumblr.com/posts", 
			array(	'id' => $post_id, 'api_key' => $api_key )
			);
	}
	public function tumblr_reblog( $api_key, $api_secret, $access_token, 
					$access_token_secret, $username, 
					$post_id, $reblog_key ){

		$tumblr = new Tumblr(   $api_key, $api_secret,
					$access_token, $access_token_secret );	
		return $tumblr->post( "blog/$username.tumblr.com/post/reblog", 
			array(	'id' => $post_id, 
				'reblog_key' =>  $reblog_key )
			);
	}
	public function plurk_post( $api_key, $api_secret, $access_token, 
					$access_token_secret, $link, $description  ){
		$plurk = new Plurk( 	$api_key, $api_secret,
					$access_token, $access_token_secret );	
		return $plurk->post('APP/Timeline/plurkAdd', 
				array('content' => "$description $link", 
				'qualifier' => 'says'));
	}
	public function plurk_replurk( $api_key, $api_secret, $access_token, 
					$access_token_secret, $plurk_id  ){
		$plurk = new Plurk( 	$api_key, $api_secret,
					$access_token, $access_token_secret );	
		return $plurk->post('/APP/Timeline/replurk', 
				array('ids' => "[$plurk_id]" ) );
	}
	public function twitter_post( $api_key, $api_secret, $access_token, 
					$access_token_secret, $link, $description  ){
		if( strlen( $description ) > 125 ){
			$description = substr( $description, 0, 130 ) . '....';
		} else {
			$description = trim( $description );
		}
		$twitter = new Twitter($api_key, $api_secret,
					$access_token, $access_token_secret );
		return $twitter->post('statuses/update', 
			array('status' => "$description $link" ));
	}
	public function twitter_search( $api_key, $api_secret, $access_token, 
					$access_token_secret, $query ){
		$twitter = new Twitter($api_key, $api_secret,
					$access_token, $access_token_secret );
		return $twitter->get('search/tweets', 
			array('q' => $query ));
	}
	public function twitter_retweet( $api_key, $api_secret, $access_token, 
					$access_token_secret, $tweet_id  ){

		$twitter = new Twitter($api_key, $api_secret,
					$access_token, $access_token_secret );
		return $twitter->post( "statuses/retweet/$tweet_id" );
	}
	public function wordpress_follow( $access_token, $blog_id  ){
		$options  = array (
		  'http' => 
		  array (
		    'ignore_errors' => true,
		    'method' => 'POST',
		    'header' => 
		    array (
		      0 => "authorization: Bearer $access_token",
		    ),
		  ),
		);
		 
		$context  = stream_context_create( $options );
		$response = file_get_contents(
		  "https://public-api.wordpress.com/rest/v1/sites/$blog_id/follows/new/",
		  false,
		  $context
		);
		return json_decode( $response );
	}
	public function wordpress_get_blogs( $access_token  ){
		$options  = array (
		  'http' => 
		  array (
		    'ignore_errors' => true,
		    'method' => 'GET',
		    'header' => 
		    array (
		      0 => "authorization: Bearer $access_token",
		    ),
		  ),
		);
		$context  = stream_context_create( $options );
		$response = file_get_contents(
		  "https://public-api.wordpress.com/rest/v1/read/recommendations/mine/",
		  false,
		  $context
		);
		return json_decode( $response );
	}
	public function wordpress_like( $access_token, $blog_id, $post_id  ){
		$options  = array (
		  'http' => 
		  array (
		    'ignore_errors' => true,
		    'method' => 'POST',
		    'header' => 
		    array (
		      0 => "authorization: Bearer $access_token",
		    ),
		  ),
		);
		 
		$context  = stream_context_create( $options );
		$response = file_get_contents(
		  "https://public-api.wordpress.com/rest/v1/sites/$blog_id/posts/$post_id/likes/new/",
		  false,
		  $context
		);
		$response = json_decode( $response );
		if( is_object( $response ) && $response->success && isset($response->meta->links->post) ){
			$response = file_get_contents( $response->meta->links->post );
			return json_decode( $response );
		} else {
			return FALSE;
		}
	}
	public function wordpress_reblog( $access_token, $blog_id, $post_id, $description  ){
		$options  = array (
		  'http' => 
		  array (
		    'ignore_errors' => true,
		    'method' => 'POST',
		    'header' => 
		    array (
		      0 => "authorization: Bearer $access_token",
		      1 => 'Content-Type: application/x-www-form-urlencoded',
		    ),
		    'content' => http_build_query(   
		      array (
			'note' => $description,
		      )
		    ),
		  ),
		);
		 
		$context  = stream_context_create( $options );
		$response = file_get_contents(
		  "https://public-api.wordpress.com/rest/v1/sites/$blog_id/posts/$post_id/reblogs/new/",
		  false,
		  $context
		);
		$response = json_decode( $response );
		if( is_object( $response ) && $response->success && isset($response->meta->links->post) ){
			$response = file_get_contents( $response->meta->links->post );
			return json_decode( $response );
		} else {
			return FALSE;
		}
	}
	public function twitter_follow( $api_key, $api_secret, $access_token, 
					$access_token_secret, $screen_name  ){

		$twitter = new Twitter($api_key, $api_secret,
					$access_token, $access_token_secret );
		return $twitter->post('friendships/create', 
			array( 'screen_name' => $screen_name, 'follow' => 'true' ));
	}
	public function linkedin_post( $api_key, $api_secret, $access_token, 
					$access_token_secret, $title, $link, $description  ){

		$title = strip_tags( $title );
		$description = strip_tags( $description );

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);
		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		$OBJ_linkedin->setTokenAccess(array('oauth_token' => $access_token, 
					'oauth_token_secret' => $access_token_secret));

		$content = array();
		$content['title'] 		= $title;
		$content['submitted-url'] 	= $link;
		$content['description'] 	= $description;
		$private = FALSE;
		return $OBJ_linkedin->share('new', $content, $private);
	}
	public function linkedin_reshare( $api_key, $api_secret, $access_token, 
					$access_token_secret, $uid  ){

		$title = strip_tags( $title );
		$description = strip_tags( $description );

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);
		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		$OBJ_linkedin->setTokenAccess(array('oauth_token' => $access_token, 
					'oauth_token_secret' => $access_token_secret));

		$content = array();
		//$content['comment'] 	= "Share";
		$content['id'] 		= $uid;
		$private = FALSE;
		return $OBJ_linkedin->share('reshare', $content, $private);
	}
	public function linkedin_like( $api_key, $api_secret, $access_token, 
					$access_token_secret, $uid  ){

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);
		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		$OBJ_linkedin->setTokenAccess(array('oauth_token' => $access_token, 
					'oauth_token_secret' => $access_token_secret));

		return $OBJ_linkedin->like( $uid );
	}
	public function linkedin_share( $api_key, $api_secret, $access_token, 
					$access_token_secret, $uid, $description  ){

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);
		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		$OBJ_linkedin->setTokenAccess(array('oauth_token' => $access_token, 
					'oauth_token_secret' => $access_token_secret));

		$content = array();
		$content['title'] 		= $title;
		$content['submitted-url'] 	= $link;
		$content['description'] 	= $description;
		$private = FALSE;

		return $OBJ_linkedin->share('reshare', $content, $private);
	}
	public function url_shorten( $long_url ){
		$apiKey 	= 'AIzaSyCv7BeL0ziGIn0SYDs4_sBIBM8U6dJ4GvU';
	 
		$postData 	= array('longUrl' => $long_url, 'key' => $apiKey);
		$jsonData 	= json_encode($postData);
	 
		$ch	 	= curl_init();
	 
		curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	 
		$response = curl_exec($ch);
		$json = json_decode($response);
		curl_close($ch);
	 
		return $json->id;
	}
	public function post_to_wordpresss( $link,  $title, $description, $tags = NULL, $categories = NULL, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'wordpress';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->wordpress_post( 
					$this->stc->stcdb->decrypt($posto['access_token']), 
					$this->stc->stcdb->decrypt($posto['access_token_secret']),
					$title, $link, $spun->spin( $description ), $tags, $categories );

			if( is_object( $response ) && isset( $response->ID  ) ) {
				$status = 1;
				$url = $response->URL;
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$rebloggers = $this->stc->array_random( $postos, $num );
					if( count( $rebloggers ) ){
						foreach( $rebloggers as $reblogger ){
							$meta = array( 'action' => 'reblog', 
									'id' => $posto['id'], 
									'social_id' => $reblogger['id'],
									'tags' => $tags,
									'categories' => $categories,
									'blog_id' => $response->site_ID,
									'pid' => $response->ID );
							$this->add_to_queue( 'wordpress', $url,  $title, $description, $meta  );
						}
					}
					$num = rand( 0, count( $postos ) );
					$likers = $this->stc->array_random( $postos, $num );
					if( count( $likers ) ){
						foreach( $likers as $liker ){
							$meta = array( 'action' => 'like', 
									'id' => $posto['id'], 
									'social_id' => $liker['id'],
									'tags' => $tags,
									'categories' => $categories,
									'blog_id' => $response->site_ID,
									'pid' => $response->ID );
							$this->add_to_queue( 'wordpress', $url,  $title, $description, $meta  );
						}
					}
				}

				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );

				$post_images = $this->stc->get_images_from_html( $description );
				$count = count( $images  );
				$index = rand(0, $count - 1 );
				$facebook_image = $post_images[$index];
				$meta = array( 'action' => 'post', 
						'id' => $posto['id'],
						'post_id' => $posto['post_id'],
						'image' => $facebook_image,
						'snippet' => substr( $description, 0, 150 ) );
				$this->add_to_queue( 'facebook', $url,  $title, $description, $meta );
				if( strlen( $facebook_image ) ){

					$meta = array( 'action' => 'pin', 
							'post_id' => $posto['post_id'],
							'image' => $facebook_image );
					$this->add_to_queue( 'pinterest', $url, $title, $title, $meta );
				}
				$response_interpretation = isset( $response->ID ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response->ID, $resarray['response']['posts'][0]['reblog_key'] ) );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'tumblr',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_facebooks( $image, $link,  $title, $description, $snippet, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'facebook';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$description = strip_tags( $description );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$user_id = $this->stc->stcdb->decrypt( $posto['access_token_secret'] );
			$access_token = $this->stc->stcdb->decrypt( $posto['access_token'] );
			$action_links = array(
				'name' => 'Share Post',
				'link' => 'http://www.facebook.com/share.php?u='.urlencode( $link )
			);
 			$data = array(
		  		'name' 		=> $title,
		  		'link' 		=> $link,
		  		'picture'	=> $image,
		   		'caption' 	=> $description,
		 		'message'	=> $description,
				'actions'	=> json_encode($action_links),
				'access_token' 	=> $access_token,
		  		'description' 	=> $snippet,
			);

			if( strlen( $image ) > 7 ){
				$request_url = "https://graph.facebook.com/$user_id/feed";
			} else {
				$request_url = "https://graph.facebook.com/$user_id/links";
			}
			$response = $this->facebook_curl( $request_url, $data );

			if( is_object( $response ) && isset( $response->id ) ) {
				$status = 1;
				$post_components = explode("_", $response->id );
				$pid = isset( $post_components[1] ) ? $post_components[1] : (string)$response->id;
				$url = "https://www.facebook.com/{$user_id}/posts/{$pid}";

				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$facebooks = $this->stc->array_random( $postos, $num );
					if( count( $facebooks ) ){
						foreach( $facebooks as $facebook ){
							$meta = array( 'action' => 'post', 
									'id' => $posto['id'], 
									'social_id' => $facebook['id'],
									'image' => $image,
									'snippet' => $snippet );
							$this->add_to_queue( 'facebook', $url,  $title, $description, $meta  );
						}
					}
				}
				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
				$response_interpretation = isset( $response->id ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( $response );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'facebook',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_friendfeeds( $link,  $title, $description, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'friendfeed';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->friendfeed_post( 
					trim($posto['username'] ), 
					$this->stc->stcdb->decrypt( $posto['password'] ), 
					$title, $link, $spun->spin( $description ) );

			if( is_object( $response ) && isset( $response->id  ) ) {
				$status = 1;
				$url = "http://friendfeed.com/e/{$response->id}";
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$friendfeeds = $this->stc->array_random( $postos, $num );
					if( count( $friendfeeds ) ){
						foreach( $friendfeeds as $friendfeed ){
							$meta = array( 'action' => 'like', 
									'id' => $posto['id'], 
									'social_id' => $friendfeed['id'],
									'entry_id' => $response->id,
									'pid' => $response->id );
							$this->add_to_queue( 'friendfeed', $url,  $title, $description, $meta  );
						}
					}
				}

				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
				$response_interpretation = isset( $response->id ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( $response );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'friendfeed',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_pinterests( $link, $title, $image, $description, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'pinterest';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this-> pinterest_post( 
					trim( $posto['username'] ), 
					$this->stc->stcdb->decrypt( $posto['password'] ), 
					$spun->spin( $description ),
					 $image, $link, 
					trim( $posto['access_secret'] ) );
			//var_dump( $response );
			if(  is_array( $response ) && ( $response['code'] == 'OK' ) ) {
				$status = 1;
				$url = "http://www.pinterest.com{$response['post_id']}";
				$response_interpretation = $response ? "Successfull" : "Unsuccessfull";
				$response = serialize( $response );

				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'pinterest',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_gpluss( $link,  $title, $description, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'gplus';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->gplus_post( 					
					trim( $posto['username'] ),
					$this->stc->stcdb->decrypt( $posto['password'] ),
					$title,
					$link,
					$spun->spin( $description )  );

			if(  isset( $response['post_url'] )  ) {
				$status = 1;
				$url = $response['post_url'];
				$response_interpretation = $response ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response ) );
				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 
						'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
			} else {
				$status = 2;
				$url = '';
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'gplus',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_tumblrs( $link,  $title, $description, $queue_id = NULL  ){
	   if( strlen( $description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'tumblr';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->tumblr_post(
					trim( $posto['access_key'] ),
					trim( $posto['access_secret'] ),
					$this->stc->stcdb->decrypt($posto['access_token']),
					$this->stc->stcdb->decrypt($posto['access_token_secret']),
					$posto['username'],
					$title,
					$link,
					$spun->spin( $description )  );

			if( is_object( $response ) && isset( $response->response->id  ) ) {
				$status = 1;

				$resarray =  json_decode( file_get_contents( "http://api.tumblr.com/v2/blog/". 
						$posto['username'] .".tumblr.com/posts/?id=". 
						$response->response->id ."&reblog_info=true&api_key=" . 
						trim( $posto['access_key'] ) ), true );
				$url = "http://{$posto['username']}.tumblr.com/post/{$response->response->id}";
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$rebloggers = $this->stc->array_random( $postos, $num );
					if( count( $rebloggers ) ){
						foreach( $rebloggers as $reblogger ){
							$meta = array( 'action' => 'reblog', 
									'id' => $posto['id'], 
									'social_id' => $reblogger['id'],
									'reblog_key' => $resarray['response']['posts'][0]['reblog_key'],
									'pid' => $response->response->id );
							$this->add_to_queue( 'tumblr', $url,  $title, $description, $meta  );
						}
					}
				}

				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );

				$post_images = $this->stc->get_images_from_html( $description );
				$count = count( $images  );
				$index = rand(0, $count - 1 );
				$facebook_image = $post_images[$index];
				$meta = array( 'action' => 'post', 
						'id' => $posto['id'],
						'post_id' => $posto['post_id'],
						'image' => $facebook_image,
						'snippet' => substr( $description, 0, 150 ) );
				$this->add_to_queue( 'facebook', $url,  $title, $description, $meta );
				if( strlen( $facebook_image ) ){

					$meta = array( 'action' => 'pin', 
							'post_id' => $posto['post_id'],
							'image' => $facebook_image );
					$this->add_to_queue( 'pinterest', $url, $title, $title, $meta );
				}
				$response_interpretation = isset( $response->response->id ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response->response->id, $resarray['response']['posts'][0]['reblog_key'] ) );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'tumblr',
				'status' 	=> $status,
				'post_id' 	=> $posto['post_id'],
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_scribd(  $link,  $title, $description, $queue_id = NULL  ){
	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'scribd';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){		
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->scribd_post( 	
					trim( $posto['access_key'] ),
					trim( $posto['access_secret'] ),
					$description,
					$title );

			if( $response ) {
				$status = 1;
				$url = "http://www.scribd.com/doc/$response/";
				$response_interpretation = $response ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response ) );

				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );

				$post_images = $this->stc->get_images_from_html( $description );
				$count = count( $images  );
				$index = rand(0, $count - 1 );
				$facebook_image = $post_images[$index];
				$meta = array( 'action' => 'post', 
						'id' => $posto['id'],
						'post_id' => $posto['post_id'],
						'image' => $facebook_image,
						'snippet' => substr( $description, 0, 150 ) );
				$this->add_to_queue( 'facebook', $url,  $title, $description, $meta );
				if( strlen( $facebook_image ) ){

					$meta = array( 'action' => 'pin', 
							'post_id' => $posto['post_id'],
							'image' => $facebook_image );
					$this->add_to_queue( 'pinterest', $url, $title, $title, $meta );
				}
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'scribd',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_plurks(  $link,  $title, $description, $queue_id = NULL  ){

	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'plurk';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->plurk_post( 	
					trim( $posto['access_key'] ),
					trim( $posto['access_secret'] ),
					$this->stc->stcdb->decrypt($posto['access_token']), 
					$this->stc->stcdb->decrypt($posto['access_token_secret']), 
					$link, 
					$spun->spin( $description )  );

			if( is_object( $response ) && isset( $response->plurk_id ) ) {
				$status = 1;
				$url = "http://www.plurk.com/{$posto['username']}";
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$plurks = $this->stc->array_random( $postos, $num );
					if( count( $plurks ) ){
						foreach( $plurks as $plurk ){
							$meta = array( 'action' => 'replurk', 
									'id' => $posto['id'], 
									'social_id' => $plurk['id'],
									'plurk_id' => $response->plurk_id  );
							$this->add_to_queue( 'plurk', $url,  $title, $description, $meta  );
						}
					}
				}
				$response_interpretation = isset( $response->plurk_id ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response->plurk_id ) );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'plurk',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_stumbleupons( $link,  $title, $description, $queue_id = NULL, $tags = NULL ){

	   if( strlen($link ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$token['social'] = 'stumbleupon';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$count = count( $postos );
		if( $count ){		
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->stumbleupon_post( 
				trim($posto['username'] ), 
				$this->stc->stcdb->decrypt( $posto['password'] ),
				$title, $link, 'Alternative News', $tags );

			if( is_array( $response ) && isset( $response['post_id'] ) ) {
				$url = "http://www.stumbleupon.com/su/{$response['post_id']}";
				$status = 1;
				$response = serialize( $response );
				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$response_interpretation = isset( $response['post_id'] ) ? "Successfull" : "Unsuccessfull";
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'stumbleupon',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_ljs( $link,  $title, $description, $queue_id = NULL ){

	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'lj';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){		
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->lj_post( 
				trim($posto['username'] ), 
				$this->stc->stcdb->decrypt( $posto['password']), 
				$title, 
				$link, 
				$spun->spin( $description )  );

			if( is_array( $response ) && isset( $response[20] ) && ( strlen( $response[20] ) > 7 ) ) {
				$url = $response[20];
				$status = 1;
				$response = serialize( $response );
				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
				$response_interpretation = isset( $response[20] ) ? "Successfull" : "Unsuccessfull";
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'lj',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_reddits(  $link,  $title, $description, $queue_id = NULL ){
	   if( strlen($description ) > 1 ){

		$token = array();
		$spun = new Spinner();

		$token['social'] = 'reddit';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		if( count( $postos ) ){
			foreach( $postos as $posto ){	
				$this->reddit_post( 
					trim($posto['username'] ), 
					$this->stc->stcdb->decrypt( $posto['password']),  
					$title, 
					$link  );
			}
		}
	  }
	}
	public function post_to_diigos(  $link,  $title, $description, $queue_id = NULL ){
	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'diigo';
		$postos = $this->stc->stcdb->fetch_all_users( $token );
		$description = substr( $description, 0, 150 ) . "...";
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){		
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->diigo_post(
				trim( $posto['access_key'] ),
				trim( $posto['username'] ),
				$this->stc->stcdb->decrypt( $posto['password'] ),
				$title,
				$link,
				$spun->spin( $description ) );

			if( is_array( $response ) ) {
				$status = 1;
				$response = serialize( $response );
				$response_interpretation = "Successfull";
			} else {
				$response = "";
				$status = 2;
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> "https://www.diigo.com/user/{$posto['username']}",
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'bookmark' ),
				'social' 	=> 'diigo',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_deliciouss(  $link,  $title, $description, $queue_id = NULL ){
	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();

		$token['social'] = 'delicious';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$description = substr( $description, 0, 150 ) . "...";
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){			
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}		
			$response = $this->delicious_post( 
					trim($posto['username'] ), 
					$this->stc->stcdb->decrypt( $posto['password']), 
					$title, 
					$link, 
					$spun->spin( $description )  );
			
			if( $response != false ) {
				$status = 1;
				$response = (string) $response;
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$deliciouss = $this->stc->array_random( $postos, $num );
					if( count( $deliciouss ) ){
						foreach( $deliciouss as $delicious ){
							$meta = array( 'action' => 'share', 
									'id' => $posto['id'], 
									'social_id' => $delicious['id'] );
							$this->add_to_queue( 'delicious', $link,  $title, $description, $meta  );
						}
					}
				}
				$response_interpretation = "Successfull";
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> "https://delicious.com/{$posto['username']}",
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'bookmark' ),
				'social' 	=> 'delicious',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function twitter_searcher( $query ){

	   if( strlen($query ) > 1 ){
		$token = array();

		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';
		$token['social'] = 'twitter';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$count = count( $postos );
		if( $count ){
			$index = rand(0, $count - 1 );
			$posto = $postos[$index];
			$response = $this->twitter_search(
						trim( $posto['access_key'] ),
						trim( $posto['access_secret'] ),
						$this->stc->stcdb->decrypt($posto['access_token']), 
						$this->stc->stcdb->decrypt($posto['access_token_secret']),
						$query );
			if( is_object( $response ) ) {
				return $response;
			} else {
				return FALSE;
			}
		}
	  }
	}
	public function twitter_follower( $screen_name ){

	   if( strlen( $screen_name ) > 1 ){
		$token = array();

		$token['social'] = 'twitter';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$count = count( $postos );
		if( $count ){
			$index = rand(0, $count - 1 );
			$posto = $postos[$index];
			$response = $this->twitter_follow(
						trim( $posto['access_key'] ),
						trim( $posto['access_secret'] ),
						$this->stc->stcdb->decrypt($posto['access_token']), 
						$this->stc->stcdb->decrypt($posto['access_token_secret']),
						$screen_name );
			//var_dump($response);
			if( is_object( $response ) ) {
				return $response;
			} else {
				return FALSE;
			}
		}
	  }
	}
	public function post_to_twitters(  $link,  $title, $description, $queue_id = NULL ){

	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();


		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';
		$token['social'] = 'twitter';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->twitter_post(
						trim( $posto['access_key'] ),
						trim( $posto['access_secret'] ),
						$this->stc->stcdb->decrypt($posto['access_token']), 
						$this->stc->stcdb->decrypt($posto['access_token_secret']),
						$link, 
						$spun->spin( $description )  );	
			if( is_object( $response ) && isset($response->id_str) ) {
				$url = "https://twitter.com/{$posto['username']}/statuses/{$response->id_str}";
				$status = 1;
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$retweeters = $this->stc->array_random( $postos, $num );
					if( count( $retweeters ) ){
						foreach( $retweeters as $retweeter ){
							$meta = array( 'action' => 'retweet', 
									'id' => $posto['id'], 
									'social_id' => $retweeter['id'],
									'tweet_id' => $response->id_str );
							$this->add_to_queue( 'twitter', $url,  $title, $description, $meta  );
						}
					}
				}
				$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
				$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
				$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
				$response_interpretation = isset( $response->id_str ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $response->id_str ) );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'twitter',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_linkedins(  $link,  $title, $description, $queue_id = NULL ){

	   if( strlen($description ) > 1 ){
		$token = array();
		$spun = new Spinner();


		$token['social'] 	= 'linkedin';
		$postos = $this->stc->stcdb->fetch_all_users( $token );

		
		$new_description = $this->spinrewritter( $description );
		if( trim( $description ) == trim( $new_description ) ){
			$new_description = $this->bestspinner( $description );
		}
		$description = $new_description;

		$new_title = $this->spinrewritter( $title );
		if( trim( $title ) == trim( $new_title ) ){
			$new_title = $this->bestspinner( $title );
		}
		$title = $new_title;

		$count = count( $postos );
		if( $count ){
			$posto = $this->stc->stcdb->fetch_authority_user($token);
			if( !count( $posto ) ){
				$index = rand(0, $count - 1 );
				$posto = $postos[$index];
			}
			$response = $this->linkedin_post( 
						trim( $posto['access_key'] ),
						trim( $posto['access_secret'] ),
						$this->stc->stcdb->decrypt($posto['access_token']), 
						$this->stc->stcdb->decrypt($posto['access_token_secret']),
						$title, 
						$link, 
						$spun->spin( $description )  );
			//var_dump($response);

			if( is_array( $response ) ) {
				$rxml = @simplexml_load_string( $response['linkedin'] );
				$url = (string)$rxml->{'update-url'};
				unset( $postos[$index] );
				if( count( $postos ) ){
					$num = rand( 0, count( $postos ) );
					$linkedins = $this->stc->array_random( $postos, $num );
					if( count( $linkedins ) ){
						foreach( $linkedins as $linkedin ){
							$meta = array( 'action' => 'like', 
									'id' => $posto['id'], 
									'social_id' => $linkedin['id'],
									'uid' => (string) $rxml->{'update-key'} );
							$this->add_to_queue( 'linkedin', $url,  $title, $description, $meta  );
						}
					}
				}
				if( strlen( $url ) > 7 ){
					$status = 1;
					$meta = array( 'action' => 'bookmark', 'id' => $posto['id'], 'post_id' => $posto['post_id'] );
					$this->add_to_queue( 'diigo', $url,  $title, $description, $meta  );
					$this->add_to_queue( 'delicious', $url,  $title, $description, $meta  );
					$this->add_to_queue( 'stumbleupon', $url,  $title, $description, $meta  );
				}
				$response_interpretation = strlen( $url ) ? "Successfull" : "Unsuccessfull";
				$response = serialize( array( $url ) );
			} else {
				$status = 2;
				$response = "";
				$response_interpretation = "Unsuccessfull";
			}
			$token = array(
				'id'		=> $queue_id,
				'url'		=> $url,
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'action' 	=> trim( 'post' ),
				'social' 	=> 'linkedin',
				'status' 	=> $status,
				'response' 	=> $response,
				'social_id' 	=> $posto['id'],
				'post_id' 	=> $posto['post_id'],
				'description' 	=> $description,
				'posted_timestamp' => time(),
				'created_timestamp' => time(),
				'response_interpretation' => $response_interpretation
			);
			return $this->stc->stcdb->insert_queue_data($token);
		}
	  }
	}
	public function post_to_sites( $link,  $title, $description ){
		$this->post_to_tumblrs( $link,  $title, $description );
		$this->post_to_plurks( $link,  $title, $description  );
		$this->post_to_ljs( $link,  $title, $description );
		//$this->post_to_reddits( $link,  $title, $description );
		$this->post_to_deliciouss( $link,  $title, $description );
		$this->post_to_twitters( $link,  $title, $description );
		$this->post_to_diigos( $link,  $title, $description );
		$this->post_to_linkedins( $link,  $title, $description );
	}
	public function add_to_queue( $social, $link,  $title, $description, $meta = array() ){
		if( strlen($description) > 1 ){
			$token = array(
				'link' 		=> trim( $link ),
				'title' 	=> trim( $title ),
				'meta' 		=> serialize( $meta ),
				'post_id' 	=> $meta['post_id'],
				'action' 	=> $meta['action'],
				'social' 	=> $social,
				'status' 	=> 0,
				'social_id' 	=> $meta['social_id'],
				'description' 	=> $description,
				'created_timestamp' => time()
			);
			return $this->stc->stcdb->insert_queue_data($token);
		} else {
			return FALSE;
		}
	}
	public function process_twitter_queue( $id = NULL ){
		if( isset( $id ) ){
			$queue = $this->stc->stcdb->fetch_twitter_item_by_id( $id  );
		} else {
			$queue = $this->stc->stcdb->fetch_twitter_item( );
		}
		if( count( $queue ) ){
			$this->twitter_follower( $queue[0]['username'] );
			$this->stc->stcdb->insert_twitter_data( array( 'id' => $queue[0]['id'], 
									'is_follower' => 1,
									'status' => 1 ) );	
		}
	}
	public function process_queue( $id = NULL ){
		if( isset( $id ) ){
			$queue = $this->stc->stcdb->fetch_queue_item_by_id( $id  );
		} else {
			$queue = $this->stc->stcdb->fetch_queue_item( );
		}
		if( count( $queue ) ){
			switch ( $queue[0]['social'] ){
				case 'diigo':
					$this->post_to_diigos(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
				break;
				case 'facebook':
					$fb_meta = unserialize( $queue[0]['meta'] );
					$this->post_to_facebooks( 
								$fb_meta['image'],
								$queue[0]['link'],
								$queue[0]['title'],
								$queue[0]['description'],
								$fb_meta['snippet'],
								$queue[0]['id'] );
				break;
				case 'pinterest':
					$pin_meta = unserialize( $queue[0]['meta'] );
					$this->post_to_pinterests(    
								$queue[0]['link'], 
								$queue[0]['title'], 
								$pin_meta['image'],
								$queue[0]['description'], 
								$queue[0]['id'] );
				break;
				case 'gplus':
					$this->post_to_gpluss(   
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
				break;
				case 'delicious':
					if( $queue[0]['action'] == 'bookmark' ){
						$this->post_to_deliciouss(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
					} elseif( $queue[0]['action'] == 'share' ) {
						$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
						$response = $this->delicious_post( 
								trim($account[0]['username'] ), 
								$this->stc->stcdb->decrypt( $account[0]['password']), 
								$queue[0]['title'],  
								$queue[0]['link'], 
								$queue[0]['description'] );

						if( $response != false ) {
							$status = 1;
							$response = (string) $response;
							$response_interpretation = "Successfull";
						} else {
							$status = 2;
							$response = "";
							$response_interpretation = "Unsuccessfull";
						}
						$token = array(
							'id'		=> $queue[0]['id'],
							'url' 		=> "https://delicious.com/{$account[0]['username']}",
							'status' 	=> $status,
							'response' 	=> $response,
							'response_interpretation' => $response_interpretation
						);
						$this->stc->stcdb->insert_queue_data($token);
					}
					
				break;

				case 'lj':
					$this->post_to_ljs(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
				break;
				case 'wordpress':
					if( $queue[0]['action'] == 'post' ){
						$wp_meta = unserialize( $queue[0]['meta'] );
						$this->post_to_wordpresss( 
								$queue[0]['link'],
								$queue[0]['title'],
								$queue[0]['description'],
								$tags = $wp_meta['tags'],
								$categories = $wp_meta['categories'], 
								$queue[0]['id']  );

					} elseif( $queue[0]['action'] == 'reblog' ) {
						if( isset( $queue[0]['social_id'] ) ){
							$reblog_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->wordpress_reblog( 
							$this->stc->stcdb->decrypt($account[0]['access_token']),
							$reblog_meta['blog_id'], $reblog_meta['pid'], 
							$queue[0]['title']  );

							if( is_object( $response ) && isset( $response->ID ) ){
								$status = 1;
								$url = $response->URL;
								$response = serialize( array( $response->ID ) );
								$response_interpretation = "Successfull";
							} else {
								$status = 2;
								$url = "";
								$response = serialize( array(  ) );
								$response_interpretation = "Unsuccessfull";
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}elseif( $queue[0]['action'] == 'like' ) {
						$like_meta = unserialize( $queue[0]['meta'] );
						$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
						$response = $this->wordpress_like( 
						$this->stc->stcdb->decrypt($account[0]['access_token']),
						$like_meta['blog_id'], $like_meta['pid'] );

						if( is_object( $response ) && isset( $response->ID ) ){
							$status = 1;
							$url = $response->URL;
							$response = serialize( array( $response->ID ) );
							$response_interpretation = "Successfull";
						} else {
							$status = 2;
							$url = "";
							$response = serialize( array(  ) );
							$response_interpretation = "Unsuccessfull";
						}
						$token = array(
							'id'		=> $queue[0]['id'],
							'url' 		=> $url,
							'status' 	=> $status,
							'response' 	=> $response,
							'response_interpretation' => $response_interpretation
						);
						$this->stc->stcdb->insert_queue_data($token);
					}
				break;
				case 'tumblr':
					if( $queue[0]['action'] == 'post' ){
						$this->post_to_tumblrs(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );

					} elseif( $queue[0]['action'] == 'reblog' ) {
						if( isset( $queue[0]['social_id'] ) ){
							$tweet_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->tumblr_reblog(
							trim( $account[0]['access_key'] ),
							trim( $account[0]['access_secret'] ),
							$this->stc->stcdb->decrypt($account[0]['access_token']), 
							$this->stc->stcdb->decrypt($account[0]['access_token_secret']),
							trim( $account[0]['username'] ), 
							$tweet_meta['pid'], $tweet_meta['reblog_key'] );

							if( is_object( $response ) ){
								if( isset( $response->response->id )){
									$status = 1;
									$url = "http://{$account[0]['username']}.".
										"tumblr.com/post/{$response->response->id}";
									$response = serialize( array( $response->response->id ) );
								} else {
									$status = 2;
									$url = "";
									$response = serialize( array(  ) );
								}
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}
				break;
				case 'friendfeed':
					if( $queue[0]['action'] == 'post' ){
						$this->post_to_friendfeeds(
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
					} elseif( $queue[0]['action'] == 'like' ) {
						if( isset( $queue[0]['social_id'] ) ){
							$like_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->friendfeed_like( 
								trim($account[0]['username'] ), 
								$this->stc->stcdb->decrypt( $account[0]['password'] ), 
								$like_meta['entry_id'] );

							if( is_object( $response ) ){
								$status = 1;
								$url = $queue[0]['link'];
								$response = serialize( array( $response->response->id ) );
							} else {
								$status = 2;
								$url = "";
								$response = serialize( array(  ) );
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}
				break;
				case 'plurk':
					if( $queue[0]['action'] == 'post' ){
						$this->post_to_plurks(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
					} elseif( $queue[0]['action'] == 'replurk' ) {
						if( isset( $queue[0]['social_id'] ) ){
							$replurk_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->plurk_replurk(  
							trim( $account[0]['access_key'] ),
							trim( $account[0]['access_secret'] ),
							$this->stc->stcdb->decrypt($account[0]['access_token']), 
							$this->stc->stcdb->decrypt($account[0]['access_token_secret']),
							$replurk_meta['plurk_id'] );

							if( is_object( $response ) && $response->success ){
								$status = 1;
								$url = $queue[0]['link'];
								$response = serialize( array( $response ) );
							} else {
								$status = 2;
								$url = "";
								$response = serialize( array(  ) );
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}
				break;
				case 'stumbleupon':
					$this->post_to_stumbleupons(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
				break;

				case 'twitter':
					if( $queue[0]['action'] == 'post' ){
						$this->post_to_twitters(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
					} elseif( $queue[0]['action'] == 'retweet' ) {
						if( isset( $queue[0]['social_id']) ){
							$tweet_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->twitter_retweet( 
							trim( $account[0]['access_key'] ),
							trim( $account[0]['access_secret'] ),
							$this->stc->stcdb->decrypt($account[0]['access_token']), 
							$this->stc->stcdb->decrypt($account[0]['access_token_secret']),
							$tweet_meta['tweet_id']  );
							if( is_object( $response ) ){
								if( isset( $response->id_str )){
									$status = 1;
									$url = "https://twitter.com/{$account[0]['username']}".
										"/statuses/{$response->id_str}";
									$response = serialize( array( $response->id_str ) );
									$response_interpretation = "Successfull";
								} else {
									$status = 2;
									$url = "";
									$response = serialize( array(  ) );
									$response_interpretation = "Unsuccessfull";
								}
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}
				break;

				case 'linkedin':
					if( $queue[0]['action'] == 'post' ){
						$this->post_to_linkedins(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$queue[0]['description'], 
								$queue[0]['id'] );
					} elseif( $queue[0]['action'] == 'like' ) {
						if( isset( $queue[0]['social_id']) ){
							$like_meta = unserialize( $queue[0]['meta'] );
							$account = $this->stc->stcdb->fetch_account_data( $queue[0]['social_id'] );
							$response = $this->linkedin_like( 
							trim( $account[0]['access_key'] ),
							trim( $account[0]['access_secret'] ),
							$this->stc->stcdb->decrypt($account[0]['access_token']), 
							$this->stc->stcdb->decrypt($account[0]['access_token_secret']),
							$like_meta['uid']  );
							if( is_array( $response ) ){
								if( isset( $response['success'] )){
									$status = 1;
									$url = $queue[0]['link'];
									$response = serialize( array( $response['info'] ) );
									$response_interpretation = "Successfull";
								} else {
									$status = 2;
									$url = "";
									$response = serialize( array(  ) );
									$response_interpretation = "Unsuccessfull";
								}
							}
							$token = array(
								'id'		=> $queue[0]['id'],
								'url' 		=> $url,
								'status' 	=> $status,
								'response' 	=> $response,
								'response_interpretation' => $response_interpretation
							);
							$this->stc->stcdb->insert_queue_data($token);
						}
					}
				break;
				
				default:
				break;
			}
			if( !count( $this->stc->stcdb->check_for_posting( $queue[0]['post_id'] ) )){
				$queue = $this->stc->stcdb->fetch_scribd_queue( $queue[0]['post_id'] );
				if( count( $queue  )){
					$links = $this->stc->stcdb->check_for_posting( $queue[0]['post_id'], 1 );
					$content = $queue[0]['description'];
					if( count( $links ) ){
						$content .= "<h3>Other Links Related to this Post</h3>";
						$temp = array();
						foreach( $links as $link ){
							$content .= "<a href=\"{$link['url']}\" target=\"_blank\">{$link['url']}</a><br>";
						}
					}
					$this->post_to_scribd(  
								$queue[0]['link'], 
								$queue[0]['title'], 
								$content, 
								$queue[0]['id'] );
				}
			}
		}
	}

}
?>

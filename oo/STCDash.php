<?php

add_action( 'wp_ajax_stc_lj', 'stc_lj' );
add_action( 'wp_ajax_stc_gplus', 'stc_gplus' );
add_action( 'wp_ajax_stc_diigo', 'stc_diigo' );
add_action( 'wp_ajax_stc_scribd', 'stc_scribd' );
add_action( 'wp_ajax_stc_reddit', 'stc_reddit' );
add_action( 'wp_ajax_stc_blogger', 'stc_blogger' );
add_action( 'wp_ajax_stc_pinterest', 'stc_pinterest' );
add_action( 'wp_ajax_stc_delicious', 'stc_delicious' );
add_action( 'wp_ajax_stc_friendfeed', 'stc_friendfeed' );
add_action( 'wp_ajax_stc_stumbleupon', 'stc_stumbleupon' );
add_action( 'wp_ajax_stc_del_user_data', 'stc_del_user_data' );

add_action( 'init', 'init_sessions');

if (!function_exists('init_sessions')){
	function init_sessions() {
		if (!session_id()) {
			session_start();
		}
	}
}
function stc_friendfeed(){
	global $stc;
	$token = array();
	$token['social'] = 'friendfeed';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];
	
	$flag = $stc->stcpost->friendfeed_check( $token['username'], $token['password'] );
	if( is_object( $flag ) ){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	} else {
		$response = json_encode( array( 'success' => 'NOT_OK', 'username' => $token['username'], 'password' => $token['password'] ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_stumbleupon(){
	global $stc;
	$token = array();
	$token['social'] = 'stumbleupon';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];
	
	$flag = $stc->stcpost->stumbleupon_check( $token['username'], $token['password'] );
	if( $flag ){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	} else {
		$response = json_encode( array( 'success' => 'NOT_OK', 'username' => $token['username'], 'password' => $token['password'] ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_gplus(){
	global $stc;
	$token = array();
	$token['social'] = 'gplus';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];

	$flag = $stc->stcpost->gplus_check( $token['username'], $token['password'] );
	if( $flag ){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	} else {
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_pinterest(){
	global $stc;
	$token = array();
	$token['social'] = 'pinterest';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];

	$flag = $stc->stcpost->pinterest_check( $token['username'], $token['password'] );
	if( $flag ){
		if( is_array( $flag ) ){
			foreach( $flag as $f ){
				$token['access_key'] = $f['name'];
				$token['access_secret'] = $f['id'];
				$id = $stc->stcdb->insert_user_data( $token );
			}
		}
		$response = json_encode( array( 'success' => 'OK' ));
	} else {
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_diigo(){
	global $stc;
	$token = array();
	$token['social'] = 'diigo';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];
	$token['access_key'] = strlen( $stc->get_option( 'diigo_api_key' ) ) > 5 ? 
				trim( $stc->get_option( 'diigo_api_key' ) ) :
				'd24acea948316353';
	
	$flag = $stc->stcpost->diigo_check( $token['access_key'], $token['username'], $token['password']);
	if(count($flag['http_code'] == 200 )){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	} else {
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_scribd(){
	global $stc;
	$key = '2posp90au53raolkutm6x';
	$secret = 'sec-jg973yuxob46e68iekf48slf6';

	if( strlen( "$key$secret" ) > 20 ){
		$title = "Social Traffic Commando Wordpress Plugin";
		$content = '<html><body><p>Thank you for using Social Traffic'.
				' Wordpress Plugin.</p></body></html>';
		$response = $stc->stcpost->scribd_post( $key, $secret, $content, $title );
	}
	if( $response ){
		$token = array();
		$token['social'] = 'scribd';
		$token['username'] = $key;
		$token['access_key'] = $key;
		$token['access_secret'] = $secret;
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK', 'key' => $key,
						'secret' => $secret, 'id' => $id ) );
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK', 'key' => $key,
						'secret' => $secret ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;

    	exit;
	die();
}
function stc_blogger(){
	global $stc;
	$token = array();
	$token['social'] = 'blogger';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];
	
	$blogger = new Blogger($token['username'], $token['password']);
	$flag = $blogger->get_blogs();

	if(count($flag)){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_lj(){
	global $stc;
	$token = array();
	$token['social'] = 'lj';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];
	
    	$flag  = $stc->stcpost->lj_check( $token['username'], $token['password']);

	if($flag){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}

function stc_reddit(){
	global $stc;
	$token = array();
	$token['social'] = 'reddit';
	$token['username'] = trim($_POST['username']);
	$token['password'] = trim($_POST['password']);
	
	$reddit = $this->stc->stcpost->reddit_check( $token['username'], $token['password'] );
    	$flag  = $reddit->isLoggedIn();

	if($flag){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_delicious(){
	global $stc;
	$token = array();
	$token['social'] = 'delicious';
	$token['username'] = trim($_POST['username']);
	$token['password'] = $_POST['password'];

    	$flag  = $stc->stcpost->delicious_check($token['username'], $token['password']);
	if( $flag ){
		$id = $stc->stcdb->insert_user_data( $token );
		$response = json_encode( array( 'success' => 'OK' ));
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}
function stc_del_user_data(){
	global $stc;
	$token = array();
	$stcdb = new StcDb($wpdb);
	$token['id'] = trim($_POST['varid']);
	$flag = $stc->stcdb->delete_user_data($token);

	if($flag){
		$response = json_encode( array( 'success' => 'OK' ));
	}
	else{
		$response = json_encode( array( 'success' => 'NOT_OK' ));
	}
    	header( "Content-Type: application/json" );
    	echo $response;
 
    	exit;
	die();
}

function start_stc_admin(){
}
class STCDash{
	private $stc;

	public function __construct( $stc ){
		global $wpdb;
		$this->stc = $stc;
		$this->wpdb = $wpdb;

	  	$menu_page = add_menu_page($this->stc->plugin_longname,
				$this->stc->plugin_longname, 10, 'stcsettings',
				array( $this, 'print_settings' ) );
		$api_sub_menu = add_submenu_page('stcsettings', 
				'Accounts', 'Accounts', 10, 'stcsettings', 
				array( $this, 'print_settings' ));
		$queue_sub_menu = add_submenu_page('stcsettings', 
				'Post Queue', 'Post Queue', 10, 'stcqueue', 
				array( $this, 'print_queue' ));
		$proxy_sub_menu = add_submenu_page('stcsettings', 
				'Proxy Settings', 'Proxy Settings', 10, 'stcproxy', 
				array( $this, 'print_proxy' ));
		$api_sub_menu = add_submenu_page('stcsettings', 
				'Advanced Settings', 'Advanced Settings', 10, 'stcapiconfig', 
				array( $this, 'print_api_config' ));
		$import_sub_menu = add_submenu_page(NULL, 
				'Import Accounts', 'Import Accounts', 10, 'stcimport', 
				array( $this, 'print_import' ));
		$account_sub_menu = add_submenu_page('stcsettings', 
				'Auto Follow', 'Auto Follow', 10, 'stctwt', 
				array( $this, 'print_twitter' ));
	  	$help_menu = add_submenu_page('stcsettings',
				'Help', 'Help', 10, 'stchelp',
				array( $this, 'print_help') );

	        add_action("admin_print_styles-$menu_page",  array( $this, 'load_styles' ));
	        add_action("admin_print_scripts-$menu_page", array( $this, 'load_scripts' ));
	        add_action("admin_print_styles-$settings_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_scripts-$settings_menu", array( $this, 'load_scripts' ));
	        add_action("admin_print_styles-$import_sub_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_styles-$api_sub_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_styles-$queue_sub_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_styles-$proxy_sub_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_styles-$account_sub_menu",  array( $this, 'load_styles' ));
	        add_action("admin_print_styles-$help_menu",  array( $this, 'load_styles' ));

		if(isset($_POST['stc_api_config'])){
			$diigo_api_key		= $_POST['diigo_api_key'];
			$facebook_api_key	= $_POST['facebook_api_key'];
			$facebook_api_secret	= $_POST['facebook_api_secret'];
			$wordpress_api_key	= $_POST['wordpress_api_key'];
			$wordpress_api_secret	= $_POST['wordpress_api_secret'];
			$tumblr_api_key		= $_POST['tumblr_api_key'];
			$tumblr_api_secret	= $_POST['tumblr_api_secret'];
			$plurk_api_key		= $_POST['plurk_api_key'];
			$plurk_api_secret	= $_POST['plurk_api_secret'];
			$twitter_api_key	= $_POST['twitter_api_key'];
			$twitter_api_secret	= $_POST['twitter_api_secret'];
			$linkedin_api_key	= $_POST['linkedin_api_key'];
			$linkedin_api_secret	= $_POST['linkedin_api_secret'];
			$url_shortener		= $_POST['url_shortener'];
			$sr_email		= $_POST['sr_email'];
			$sr_api_key		= $_POST['sr_api_key'];
			$bs_username		= $_POST['bs_username'];
			$bs_password		= $_POST['bs_password'];
			$this->stc->add_option( 'diigo_api_key', trim($diigo_api_key));
			$this->stc->add_option( 'tumblr_api_key', trim($tumblr_api_key));
			$this->stc->add_option( 'tumblr_api_secret', trim($tumblr_api_secret));
			$this->stc->add_option( 'facebook_api_key', trim($facebook_api_key));
			$this->stc->add_option( 'facebook_api_secret', trim($facebook_api_secret));
			$this->stc->add_option( 'wordpress_api_key', trim($wordpress_api_key));
			$this->stc->add_option( 'wordpress_api_secret', trim($wordpress_api_secret));
			$this->stc->add_option( 'plurk_api_key', trim($plurk_api_key));
			$this->stc->add_option( 'plurk_api_secret', trim($plurk_api_secret));
			$this->stc->add_option( 'twitter_api_key', trim($twitter_api_key));
			$this->stc->add_option( 'twitter_api_secret', trim($twitter_api_secret));
			$this->stc->add_option( 'linkedin_api_key', trim($linkedin_api_key));
			$this->stc->add_option( 'linkedin_api_secret', trim($linkedin_api_secret));
			$this->stc->add_option( 'url_shortener', trim($url_shortener));
			$this->stc->add_option( 'sr_email', trim($sr_email));
			$this->stc->add_option( 'sr_api_key', trim($sr_api_key));
			$this->stc->add_option( 'bs_username', trim($bs_username));
			$this->stc->add_option( 'bs_password', trim($bs_password));
		}

		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'facebook'){
			$this->facebook_verifier();
		}
		if(isset($_GET['stcfacebook']) && $_GET['stcfacebook'] = 'facebook' ){
			$this->facebook_register();
		}
		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'wordpress'){
			$this->wordpress_verifier();
		}
		if(isset($_GET['stcwordpress']) && $_GET['stcwordpress'] = 'wordpress' ){
			$this->wordpress_register();
		}
		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'tumblr'){
			$this->tumblr_verifier();
		}
		if(isset($_GET['stctumblr']) && $_GET['stctumblr'] = 'tumblr'){
			$this->tumblr_register();
		}
		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'plurk'){
			$this->plurk_verifier();
		}
		if(isset($_GET['stcplurk']) && $_GET['stcplurk'] = 'plurk'){
			$this->plurk_register();
		}
		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'twitter'){
			$this->twitter_verifier();
		}
		if(isset($_GET['stctwitter']) && $_GET['stctwitter'] = 'twitter'){
			$this->twitter_register();
		}
		if(isset($_GET['stcredon']) && $_GET['stcredon'] == 'linkedin'){
			$this->linkedin_verifier();
		}	
		if(isset($_GET['stclinkedin']) && $_GET['stclinkedin'] = 'linkedin'){
			$this->linkedin_register();
		}
		if(isset($_GET['stcpostnow']) ){
			$this->stc->stcpost->process_queue( $_GET['stcpostnow'] );
			$redirect_url = "admin.php?page=stcqueue";
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stcprocessqueue']) ){
			$this->stc->stcpost->process_queue(  );
			$redirect_url = "admin.php?page=stcqueue";
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stcprocesstwitter']) ){
			$this->stc->stcpost->process_twitter_queue( );
			$redirect_url = "admin.php?page=stctwt";
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stctwitterfollow']) ){
			$this->stc->stcpost->process_twitter_queue( $_GET['stctwitterfollow'] );
			$redirect_url = "admin.php?page=stctwt";
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stctwitterdelete']) ){
			$this->stc->stcdb->delete_twitter_item( $_GET['stctwitterdelete'] );
			$redirect_url = "admin.php?page=stctwt";
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stcdeletequeue']) ){
			$this->stc->stcdb->delete_queue_item( $_GET['stcdeletequeue'] );
			$redirect_url = "admin.php?page=stcqueue";
			wp_redirect( $redirect_url );
		}

		if(isset($_POST['del_queue']) && count($_POST['del_queue']) ){
			$queue_ids = $_POST['del_queue'];
			foreach($queue_ids as $queue_id ){
				$this->stc->stcdb->delete_queue_item( $queue_id );
			}
		}
		if(isset($_POST['del_account']) && count($_POST['del_account']) ){
			$account_ids = $_POST['del_account'];
			foreach($account_ids as $account_id ){
				$this->stc->stcdb->delete_user_data( 
					array( 'id' => $account_id ) 
				);
			}
		}
		if(isset($_POST['del_twitter']) && count($_POST['del_twitter']) ){
			$twitter_ids = $_POST['del_twitter'];
			foreach($twitter_ids as $twitter_id ){
				$this->stc->stcdb->delete_twitter_item($twitter_id);
			}
		}
		if(isset($_GET['stcaccountdelete']) ){
			$this->stc->stcdb->delete_user_data( array( 'id' => $_GET['stcaccountdelete'] ) );
			$redirect_url = add_query_arg(array( 'stcaccountdelete' => FALSE ));
			wp_redirect( $redirect_url );
		}
		if(isset($_GET['stcauthority']) ){
			$this->stc->stcdb->change_authority( $_GET['stcauthority'], $_GET['stcsocial'], $_GET['stcval'] );
			$redirect_url = add_query_arg(array( 'stcauthority' => FALSE ));
			wp_redirect( $redirect_url );
		}
		if(isset($_REQUEST['account_submit'])){
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];

			$token['social'] = $_REQUEST['stcnetwork'];
			$token['username'] = trim($_REQUEST['username']);
			$token['password'] = $_REQUEST['password'];
			if( $token['social'] == 'pinterest' ){
				$boards = $this->stc->stcpost->pinterest_check(
					$username, $password
				);
				if( is_array($boards) && count($boards) ){
					foreach($boards as $board ){
						$token['access_key'] = $board['n'];
						$token['access_secret'] = $board['id'];
						$id = $this->stc->stcdb->insert_user_data( $token );
					}
				}
			} else {
				$id = $this->stc->stcdb->insert_user_data( $token );
			}
			$redirect_url = "admin.php?page=stcsettings&display={$token['social']}";
			wp_redirect( $redirect_url );	
		}
	}
	public function facebook_verifier(){	
    		$app_id 	= $this->stc->get_option('facebook_api_key');
		$url 		= $this->stc->get_url();
		$callback_url 	= urlencode( add_query_arg( 
					array('stcredon' => NULL, 'stcfacebook' => 'facebook'), $url) );
		$redirect_url  	= "https://www.facebook.com/dialog/oauth?".
					"client_id={$app_id}&redirect_uri={$callback_url}&".
					"response_type=code&". 						"scope=publish_actions,publish_stream,offline_access,".
						"read_stream,email,user_groups,manage_pages";

		$this->stc->add_option('facebook_redirect_url', $callback_url);
		wp_redirect($redirect_url);
	}
	public function facebook_register(){
		if(isset($_GET['code'])){
			$app_id	= $this->stc->get_option('facebook_api_key');
			$app_secret = $this->stc->get_option('facebook_api_secret');
			$callback_url = $this->stc->get_option('facebook_redirect_url');
			$code 	= $_GET['code'];
			$request_url  	= "https://graph.facebook.com/oauth/access_token?".
 						"client_id={$app_id}".
						"&redirect_uri={$callback_url}".
						"&client_secret={$app_secret}".
						"&code={$code}";
			$response = file_get_contents( $request_url );var_dump($response);
			if( strpos( $response, "&expires=" ) ){
				$response = explode("&expires=", $response );
				$token_str = $response[0];
				$access_token = str_replace("access_token=", "", $token_str );
				$query = urlencode("SELECT uid, name FROM user WHERE uid=me()");
				$query_url = "https://graph.facebook.com/fql?q={$query}".
					"&access_token={$access_token}";
				$response = file_get_contents($query_url);
				$response = json_decode($response, true );
				if( count( $response ) ){
					$uid = $response['data'][0]['uid'];
					$username = $response['data'][0]['name'];
					$token['social'] = 'facebook';
					$token['username'] = trim( $username );
					$token['access_key'] = $app_id;
					$token['access_secret'] = $app_secret;
					$token['access_token'] = trim($access_token);
					$token['access_token_secret'] = trim($uid);
					$id = $this->stc->stcdb->insert_user_data( $token );
					$url 		= $this->stc->get_url();
					$redirect_url 	= add_query_arg( array(	'stcfacebook' => NULL, 
										'code' => NULL,
										'display' => 'facebook'), $url);
			    		wp_redirect( $redirect_url );
				}
			}
		}
	}
	public function linkedin_verifier(){
	
		$key 		= $this->stc->get_option('linkedin_api_key');
		$secret 	= $this->stc->get_option('linkedin_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'ps07ex1gehnn';
		$api_secret    	= (strlen($secret) > 5)? $secret : '6TaG5UfQRYb7tcEJ';

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);
		define('CONNECTION_COUNT', 20);
		define('PORT_HTTP', '80');
		define('PORT_HTTP_SSL', '443');
		define('UPDATE_COUNT', 10);

		$_REQUEST[LINKEDIN::_GET_TYPE] = (isset($_REQUEST[LINKEDIN::_GET_TYPE])) ? 
						$_REQUEST[LINKEDIN::_GET_TYPE] : '';


		if($_SERVER['HTTPS'] == 'on') {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		
		$url = $protocol . '://' . $_SERVER['SERVER_NAME'] . 
					((($_SERVER['SERVER_PORT'] != PORT_HTTP) || 
					($_SERVER['SERVER_PORT'] != PORT_HTTP_SSL)) ? ':' .
					$_SERVER['SERVER_PORT'] : '') . 
					$_SERVER['PHP_SELF'] . '?' . 
					LINKEDIN::_GET_TYPE . '=initiate&' . 
					LINKEDIN::_GET_RESPONSE . '=1';
		$callback_url 	= add_query_arg(array('page' => $_REQUEST['page'], 
					'stcredon' => NULL, 'stclinkedin' => 'linkedin'), $url);
		$API_CONFIG['callbackUrl'] = $callback_url;

		$OBJ_linkedin = new LinkedIn($API_CONFIG);

		$_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? 
					$_GET[LINKEDIN::_GET_RESPONSE] : '';

		if(!$_GET[LINKEDIN::_GET_RESPONSE]) {
			$response = $OBJ_linkedin->retrieveTokenRequest();
     
			if($response['success'] === TRUE) {
		          $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
          
		          wp_redirect( LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);
        		} else {
          			echo "Somethin went wrong";
			}
		}

	}
	public function linkedin_register(){
		$key 		= $this->stc->get_option('linkedin_api_key');
		$secret 	= $this->stc->get_option('linkedin_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'ps07ex1gehnn';
		$api_secret    	= (strlen($secret) > 5)? $secret : '6TaG5UfQRYb7tcEJ';

		$API_CONFIG = array(
			'appKey'       => $api_key,
			'appSecret'    => $api_secret,
			'callbackUrl'  => NULL 
		);

		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		
 		$response = $OBJ_linkedin->retrieveTokenAccess(
					$_SESSION['oauth']['linkedin']['request']['oauth_token'], 
					$_SESSION['oauth']['linkedin']['request']['oauth_token_secret'],
					$_GET['oauth_verifier']);
        
		if( $response['success'] === TRUE) {

			$_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
          		$_SESSION['oauth']['linkedin']['authorized'] = TRUE;
			$oauth_verifier		= $_GET['oauth_verifier'];
			$access_token		= $_SESSION['oauth']['linkedin']['access']['oauth_token'];
			$access_token_secret  	= $_SESSION['oauth']['linkedin']['access']['oauth_token_secret'];
		
			$token = array();
			$token['social'] = 'linkedin';
			$token['username'] = trim( $access_token );
			$token['access_key'] = $api_key;
			$token['access_secret'] = $api_secret;
			$token['access_token'] = trim($access_token);
			$token['access_token_secret'] = trim($access_token_secret);
			if( strlen( $token['username'] ) ){
				$id = $this->stc->stcdb->insert_user_data( $token );		
			}
			$url 		= $this->stc->get_url();
			$redirect_url 	= add_query_arg( array(	'lType' => NULL,
								'lResponse' => NULL,
								'stclinkedin' => NULL,  
								'oauth_verifier' => NULL,
								'oauth_token' => NULL,
								'display' => 'linkedin'), $url);
	    		wp_redirect( $redirect_url );
        	} else {
          		echo "Somethin went wrong";
		}
	}
	public function tumblr_verifier(){

		$key 		= $this->stc->get_option('tumblr_api_key');
		$secret 	= $this->stc->get_option('tumblr_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'yM4yJLGv82ivdmGGD4XeV6eGakWbbMvOceSeF8esZYzMwIzwEP';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'QCeaxxY5XaIzkrncXqyzON7Z5Pgdg73U583Ix4JdEwdF36vGNh';
		$tumblr = new Tumblr( $api_key, $api_secret );

		$url = $this->stc->get_url();

		$callback_url = add_query_arg(array('stcredon' => NULL, 'stctumblr' => 'tumblr'), $url);	
		$request_token = $tumblr->getRequestToken( $callback_url );

		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		if($tumblr->http_code==200){
	    		$temp_url = $tumblr->getAuthorizeURL($request_token['oauth_token']);
	    		wp_redirect( $temp_url );
		}else{
	    		die('Something wrong happened.');
		}
	}
	public function tumblr_register(){
		$key 		= $this->stc->get_option('tumblr_api_key');
		$secret 	= $this->stc->get_option('tumblr_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'yM4yJLGv82ivdmGGD4XeV6eGakWbbMvOceSeF8esZYzMwIzwEP';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'QCeaxxY5XaIzkrncXqyzON7Z5Pgdg73U583Ix4JdEwdF36vGNh';
		$tumblr = new Tumblr( $api_key, $api_secret,
					$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

		$access_token = $tumblr->getAccessToken($_GET['oauth_verifier']);
		$user_info = $tumblr->post('user/info'); 

		if(isset($user_info->error)){
			$this->tumblr_verifier();
		}else{
			$token = array();
			$token['social'] = 'tumblr';
			$token['username'] = trim( $user_info->response->user->name );
			$token['access_key'] = $api_key;
			$token['access_secret'] = $api_secret;
			$token['access_token'] = trim($access_token['oauth_token']);
			$token['access_token_secret'] = trim($access_token['oauth_token_secret']);
			if( strlen( $token['username'] ) ){
				$id = $this->stc->stcdb->insert_user_data( $token );		
			}
			$url 		= $this->stc->get_url();
			$redirect_url 	= add_query_arg( array(	'stctumblr' => NULL, 
								'oauth_verifier' => NULL,
								'oauth_token' => NULL,
								'display' => 'tumblr'), $url);
	    		wp_redirect( $redirect_url );
		}
	}
	public function plurk_verifier(){
		$key 		= $this->stc->get_option('plurk_api_key');
		$secret 	= $this->stc->get_option('plurk_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'CFYmuki6slva';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'zCd0lC1CVygMPE6PHBkUtUOW9355A3Wd';

		$plurk = new Plurk( $api_key, $api_secret );
		$url = $this->stc->get_url();

		$callback_url = add_query_arg(array('stcredon' => NULL, 'stcplurk' => 'plurk'), $url);	
		$request_token = $plurk->getRequestToken( $callback_url );

		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		if($plurk->http_code==200){
	    		$temp_url = $plurk->getAuthorizeURL($request_token['oauth_token']); 
	    		wp_redirect( $temp_url );
		}else{
	    		die('Something wrong happened.');  
		}
	}
	public function plurk_register(){
		$key 		= $this->stc->get_option('plurk_api_key');
		$secret 	= $this->stc->get_option('plurk_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'CFYmuki6slva';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'zCd0lC1CVygMPE6PHBkUtUOW9355A3Wd';

		$plurk = new Plurk( $api_key, $api_secret,
				$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access_token = $plurk->getAccessToken($_GET['oauth_verifier']);
		$user_info = $plurk->get('APP/Profile/getOwnProfile'); 

		if(isset($user_info->error)){
			$this->plurk_verifier();
		}else{
			$token = array();
			$token['social'] = 'plurk';
			$token['username'] = trim($user_info->user_info->nick_name);
			$token['access_key'] = $api_key;
			$token['access_secret'] = $api_secret;
			$token['access_token'] = trim($access_token['oauth_token']);
			$token['access_token_secret'] = trim($access_token['oauth_token_secret']);
			if( strlen( $token['username'] ) ){
				$id = $this->stc->stcdb->insert_user_data( $token );		
			}
			$url 		= $this->stc->get_url();
			$redirect_url 	= add_query_arg( array(	'stcplurk' => NULL, 
								'oauth_verifier' => NULL,
								'oauth_token' => NULL,
								'display' => 'plurk'), $url);
	    		wp_redirect( $redirect_url );
		}
	}
	public function friendfeed_verifier(){

		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';

		$twitter = new Twitter( $api_key, $api_secret );
		$url = $this->stc->get_url();

		$callback_url = add_query_arg(array('stcredon' => NULL, 'stctwitter' => 'twitter'), $url);	
		$request_token = $twitter->getRequestToken( $callback_url );

		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		if($twitter->http_code==200){
	    		$temp_url = $twitter->getAuthorizeURL($request_token['oauth_token']); 
	    		wp_redirect( $temp_url ); 
		} else {  
	    		die('Something wrong happened.');  
		}  
	}
	public function friendfeeed_register(){

		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';

		$twitter = new Twitter( $api_key, $api_secret, 
					$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

		$access_token = $twitter->getAccessToken($_GET['oauth_verifier']);
		$user_info = $twitter->get('account/verify_credentials'); 

		if(isset($user_info->error)){
			$this->twitter_verifier();
		}else{
			$token = array();
			$token['social'] = 'twitter';
			$token['username'] = trim($user_info->screen_name);
			$token['access_key'] = $api_key;
			$token['access_secret'] = $api_secret;
			$token['access_token'] = trim($access_token['oauth_token']);
			$token['access_token_secret'] = trim($access_token['oauth_token_secret']);
			$id = $this->stc->stcdb->insert_user_data( $token );
			$url 		= $this->stc->get_url();
			$redirect_url 	= add_query_arg( array(	'stctwitter' => NULL, 
								'oauth_verifier' => NULL,
								'oauth_token' => NULL,
								'display' => 'twitter'), $url);
	    		wp_redirect( $redirect_url );
		}
	}
	public function wordpress_verifier(){
		$client_id 	= $this->stc->get_option('wordpress_api_key');
		$url 		= $this->stc->get_url();
		$callback_url 	= urlencode( get_option("siteurl"). "/wp-admin/admin.php?page=stcsettings&stcwordpress=wordpress" );
		$redirect_url 	= "https://public-api.wordpress.com/oauth2/authorize?".
					"client_id=$client_id&redirect_uri=$callback_url&response_type=code";
	    	wp_redirect( $redirect_url );  
	}
	public function wordpress_register(){
		$client_id 	= $this->stc->get_option('wordpress_api_key');
		$client_secret 	= $this->stc->get_option('wordpress_api_secret');
		$callback_url 	=  get_option("siteurl") . "/wp-admin/admin.php?page=stcsettings&stcwordpress=wordpress";
		if( isset( $_GET['code']) ){
			$curl = curl_init( "https://public-api.wordpress.com/oauth2/token" );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
			    'client_id' => $client_id,
			    'redirect_uri' => $callback_url,
			    'client_secret' => $client_secret,
			    'code' => $_GET['code'],
			    'grant_type' => 'authorization_code'
			) );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
			$auth = curl_exec( $curl );
			$secret = json_decode($auth);
			$access_key = $secret->access_token;
			$blog_id = $secret->blog_id;
			$blog_url = $secret->blog_url;
			if( strlen( $access_key ) ){
				$token = array();
				$token['social'] = 'wordpress';
				$token['username'] = trim($blog_url);
				$token['access_key'] = $client_id;
				$token['access_secret'] = $client_secret;
				$token['access_token'] = trim($access_key);
				$token['access_token_secret'] = trim($blog_id);
				$id = $this->stc->stcdb->insert_user_data( $token );
			}
		}
		$url 		= $this->stc->get_url();
		$redirect_url 	= add_query_arg( array(	'stcwordpress' => NULL, 
							'code' => NULL,
							'state' => NULL,
							'display' => 'wordpress'), $url);
	    	wp_redirect( $redirect_url );  
	}
	public function twitter_verifier(){

		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';

		$twitter = new Twitter( $api_key, $api_secret );
		$url = $this->stc->get_url();

		$callback_url = add_query_arg(array('stcredon' => NULL, 'stctwitter' => 'twitter'), $url);	
		$request_token = $twitter->getRequestToken( $callback_url );

		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		if($twitter->http_code==200){
	    		$temp_url = $twitter->getAuthorizeURL($request_token['oauth_token']); 
	    		wp_redirect( $temp_url ); 
		} else {  
	    		die('Something wrong happened.');  
		}  
	}
	public function twitter_register(){

		$key 		= $this->stc->get_option('twitter_api_key');
		$secret 	= $this->stc->get_option('twitter_api_secret');
	
		$api_key 	= (strlen($key) > 5)? $key : 'wlne3127Fr7EbYCXy52zMA';
		$api_secret    	= (strlen($secret) > 5)? $secret : 'lCOShmCQrPgGNQTM0HkT1T9TWwP8evxXOYIrI78Y';

		$twitter = new Twitter( $api_key, $api_secret, 
					$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

		$access_token = $twitter->getAccessToken($_GET['oauth_verifier']);
		$user_info = $twitter->get('account/verify_credentials'); 

		if(isset($user_info->error)){
			$this->twitter_verifier();
		}else{
			$token = array();
			$token['social'] = 'twitter';
			$token['username'] = trim($user_info->screen_name);
			$token['access_key'] = $api_key;
			$token['access_secret'] = $api_secret;
			$token['access_token'] = trim($access_token['oauth_token']);
			$token['access_token_secret'] = trim($access_token['oauth_token_secret']);
			$id = $this->stc->stcdb->insert_user_data( $token );
			$url 		= $this->stc->get_url();
			$redirect_url 	= add_query_arg( array(	'stctwitter' => NULL, 
								'oauth_verifier' => NULL,
								'oauth_token' => NULL,
								'display' => 'twitter'), $url);
	    		wp_redirect( $redirect_url );
		}
	}	
	public function load_styles(){
		wp_enqueue_style('thickbox');
		wp_enqueue_style("stc-admin-style", 
				"{$this->stc->plugin_url}views/css/stc.css" );
		wp_enqueue_style("stc-bootstrap-style", 
				"{$this->stc->plugin_url}views/css/bootstrap.min.css" );
		wp_enqueue_style("stc-settings-style", 
				"{$this->stc->plugin_url}views/css/stc-settings.css" );
	}
	public function load_scripts(){ 
    		wp_enqueue_script("stc-settings-scripts", 
			"{$this->stc->plugin_url}views/js/stc-settings.js", array('jquery'));
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
	}
	public function print_settings(){
		if(isset($_GET['stcnetwork'])){
			include( "{$this->stc->plugin_dir}views/add.php" );
		} elseif($_GET['display']){
			include( "{$this->stc->plugin_dir}views/accounts.php" );
		}else{
			include( "{$this->stc->plugin_dir}views/settings.php" );
		}
	}
	public function print_queue(){
		include( "{$this->stc->plugin_dir}views/queue.php" );
	}
	public function print_proxy(){
		if( isset($_GET['add']))
			include( "{$this->stc->plugin_dir}views/proxy-add.php" );
		else
			include( "{$this->stc->plugin_dir}views/proxy.php" );
	}
	public function print_import(){
		include( "{$this->stc->plugin_dir}views/import.php" );
	}
	public function print_api_config(){
		include( "{$this->stc->plugin_dir}views/api-config.php" );
	}
	public function print_account(){
		include( "{$this->stc->plugin_dir}views/account.php" );
	}
	public function print_twitter(){
		include( "{$this->stc->plugin_dir}views/twitter.php" );
	}
	public function print_help(){
		include( "{$this->stc->plugin_dir}views/help.php" );
	}
}

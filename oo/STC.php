<?php
class STC{
	private $plugin_options;
	private $db_prefix;
	private $wpdb;
	private $dash;
	public $stcdb;
	public $stcpost;
	public $plugin_dir;
	public $plugin_url;
	public $plugin_name;
	public $plugin_tables;
	public $plugin_longname;
	public $plugin_namespace;

	public function __construct($plugin_name){
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->plugin_longname = 'Social Traffic Commando';
		$this->plugin_name = $plugin_name;
        	$dir_name = dirname(  dirname( plugin_basename(__FILE__) ) );
        	$this->plugin_url = trailingslashit( WP_PLUGIN_URL . '/' . $dir_name );
        	$this->plugin_dir = trailingslashit( WP_PLUGIN_DIR . '/' . $dir_name );
		$this->plugin_slug = strtolower($plugin_name);
        	$this->plugin_namespace = "{$this->plugin_slug}_";
		$this->plugin_options = "{$this->plugin_namespace}options";
		$db_prefix = "{$this->wpdb->prefix}{$this->plugin_namespace}";
		$this->plugin_tables = array(
				'queue' => "{$db_prefix}queue",
				'proxy' => "{$db_prefix}proxies",
				'twitter' => "{$db_prefix}twitter",
				'accounts' => "{$db_prefix}accounts",
		);
		$this->class_loaders();
		$db_class = "{$this->plugin_name}Db";
		$api_class = "{$this->plugin_name}API";
		$post_class = "{$this->plugin_name}Post";
		$metabox_class = "{$this->plugin_name}Post";
        	$this->api = new $api_class($this);
        	$this->stcdb = new $db_class($this->plugin_tables);
		$this->stcpost = new $post_class($this);
		$this->stcmetabox = new $metabox_class($this);
		add_action('admin_menu', array( $this, 'start_admin'));
		add_action('stc_event_hook', array( $this, 'run_scheduler'));
	}
	private function class_loaders(){
		require_once "{$this->plugin_dir}oo/{$this->plugin_name}Db.php";
		require_once "{$this->plugin_dir}oo/{$this->plugin_name}API.php";
		require_once "{$this->plugin_dir}oo/{$this->plugin_name}Dash.php";
		require_once "{$this->plugin_dir}oo/{$this->plugin_name}Post.php";
		require_once "{$this->plugin_dir}oo/{$this->plugin_name}Metabox.php";
	}
	public function start_admin(){
		$dashboard_class = "{$this->plugin_name}Dash";
        	return new $dashboard_class($this);
	}
	public function run_scheduler(){
		set_time_limit( 300 );
		$this->stcpost->process_queue(  );
		$this->stcpost->process_twitter_queue( );
		$this->stcpost->process_twitter_queue( );
		$this->stcpost->process_twitter_queue( );
		$this->stcpost->process_twitter_queue( );
	}
	public function array_random($arr, $num = 1) {
		shuffle($arr);

		$r = array();
		for ($i = 0; $i < $num; $i++) {
			$r[] = $arr[$i];
		}
		return $r;
	}
	public function add_option( $option, $value ){
		$data = unserialize(base64_decode(get_option($this->plugin_options)));
		$data[$option] = $value;
		$data = base64_encode(serialize($data));
		update_option($this->plugin_options, $data);
	}
	public function checkbox( $flag ){
		if( $flag ){
			return 'checked="checked"';
		} else {
			return '';
		}
	}
	public function get_url(){
		$page_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80"){
			$page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $page_url;
	}
	public function curl( $url, $fields = array(), $flag = TRUE ){
		if( !$flag && count($fields) ){
			$postfields = http_build_query( $fields );
			$url .= '?' .  $postfields;
		}					
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 0 );
		curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );				
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if( $flag ){
			$postfields = http_build_query( $fields );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields );
		}			
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		$status = curl_exec( $ch );
		curl_close( $ch );
		return $status;
	}
	public function get_images_from_html( $html_content ) {
		$html_dom = new DOMDocument();
		@$html_dom->loadHTML(  $html_content );
		$images = array();

		foreach($html_dom->getElementsByTagName("img") as $image_obj ) {
			$src = (string)$image_obj->getAttribute("src");
			if( strpos( $src, "http://" ) !== FALSE ) 
				$images[] = $src;
		}
		return (!empty( $images )) ? $images : array();
	}
	public function csv_add( $filename ) { 
		$token = array();

		$patterns = array();
		$patterns[0] = "/l(.*)j/i";
		$patterns[1] = "/d(.*)o/i";
		$patterns[2] = "/g(.*)s/i";
		$patterns[3] = "/g(.*)\+/i";
		$patterns[4] = "/r(.*)t/i";
		$patterns[5] = "/d(.*)s/i";
		$patterns[6] = "/p(.*)k/i";
		$patterns[7] = "/l(.*)n/i";
		$patterns[8] = "/s(.*)d/i";
		$replacements[0] = 'lj';
		$replacements[1] = 'diigo';
		$replacements[2] = 'gplus';
		$replacements[3] = 'gplus';
		$replacements[4] = 'reddit';
		$replacements[5] = 'delicious';
		$replacements[6] = 'plurk';
		$replacements[7] = 'linkedin';
		$replacements[8] = 'scribd';

		$fh = fopen($filename, 'r') or die("can't open file");
		$count = 0;
		while(!feof($fh)) {
			$account = fgets($fh, 4096);
			if( mb_strlen( $account ) > 10 ){
				$account = explode( "," , $account );
				if( preg_match("/Exported/i", $account[7]) ) continue;
				if( count($account) > 2 ){
					$temp = preg_replace($patterns, $replacements, trim( $account[0] ) );
					$token['social'] = strtolower( trim($account[0]) );
					$token['username'] = trim($account[1]);
					$token['password'] = trim($account[2]);
					$token['access_key'] = trim($account[3]);
					$token['access_secret'] = trim($account[4]);
					$token['access_token'] = trim($account[5]);
					$token['access_token_secret'] = trim($account[6]);
					if( preg_match("/n/i", $account[7]) ){
						$this->stcdb->insert_user_data( $token );
					} else {
						$this->stcdb->insert_raw_user_data( $token );
					}
					$count++;
				}
			}
		}
		fclose($fh);
		return $count;
	}
	public function get_option( $option ){
		$data = unserialize(base64_decode(get_option( $this->plugin_options )));
		return $data[$option];
	}
	public function create_tables(){
		$accounts = " CREATE TABLE IF NOT EXISTS " . $this->plugin_tables['accounts'] . " (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			social VARCHAR( 255 ) NOT NULL,
			authority INT UNSIGNED NOT NULL,
			username text,
			password text,
			access_key text,
			access_token text,
			access_secret text,
			access_token_secret text
			);";

		$proxy = " CREATE TABLE IF NOT EXISTS " . $this->plugin_tables['proxy'] . " (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			proxy VARCHAR( 255 ) NOT NULL,
			port VARCHAR( 255 ) NOT NULL,
			request_count INT UNSIGNED NOT NULL,
			request_errors INT UNSIGNED NOT NULL,
			last_request INT UNSIGNED NOT NULL,
			last_request_time INT UNSIGNED NOT NULL,
			created INT UNSIGNED NOT NULL,
			password text,
			username text,
			status text
			);";

		$queue = " CREATE TABLE IF NOT EXISTS " . $this->plugin_tables['queue'] . " (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			social_id INT UNSIGNED NOT NULL,
			post_id INT UNSIGNED NOT NULL,
			url text,
			meta text,
			link text,
			title text,
			action text,
			social text,
			response text,
			description text,
			status INT UNSIGNED,
			response_interpretation text,
			posted_timestamp INT UNSIGNED,
			created_timestamp INT UNSIGNED
			);";

		$twitter = " CREATE TABLE IF NOT EXISTS " . $this->plugin_tables['twitter'] . " (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			username text,
			twitter_id text,
			profile_image text,
			description text,
			is_follower INT UNSIGNED,
			status text,
			created_timestamp INT UNSIGNED
			);";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $accounts );
		dbDelta( $twitter );
		dbDelta( $proxy );
		dbDelta( $queue );
	}
	public function time_stamp($time_ago){ 
		$cur_time=time();
		$time_elapsed = $cur_time - $time_ago; 

		$seconds = $time_elapsed ; 
		$minutes = round($time_elapsed / 60 );
		$hours = round($time_elapsed / 3600); 
		$days = round($time_elapsed / 86400 ); 
		$weeks = round($time_elapsed / 604800); 
		$months = round($time_elapsed / 2600640 ); 
		$years = round($time_elapsed / 31207680 ); 
		// Seconds
		if($seconds <= 60){
			return "$seconds seconds ago"; 
		} else if($minutes <=60) {
			if($minutes==1) {
				return "one minute ago"; 
			} else {
				return "$minutes minutes ago"; 
			}
		} else if($hours <=24) {
			if($hours==1) {
				return "an hour ago";
			} else {
				return "$hours hours ago";
			}
		} else if($days <= 7) {
			if($days==1) {
				return "yesterday";
			} else {
				return "$days days ago";
			}
		} else if($weeks <= 4.3) {
			if($weeks==1){
				return "a week ago";
			} else {
				return "$weeks weeks ago";
			}
		} else if($months <=12) {
			if($months==1) {
				return "a month ago";
			} else {
				return "$months months ago";
			}
		} else {
			if($years==1) {
				return "one year ago";
			} else {
				return "$years years ago";
			}
		}

	}
	public function install(){
		$options = array();
		$options = base64_encode(serialize( $options ));
		add_option( $this->plugin_options, $options );
		$this->create_tables();
		wp_schedule_event( time(), 'hourly', 'stc_event_hook' );
	}
	public function uninstall(){
		wp_clear_scheduled_hook( 'stc_event_hook' );
	}
	public function convert_to_pdf( $content, $title = "" ){
		require_once "{$this->plugin_dir}oo/external/tcpdf/config/lang/eng.php";
		require_once "{$this->plugin_dir}oo/external/tcpdf/tcpdf.php";

		$tmp_name = date("D M j G:i:s T Y", time() );
		if( strlen( $title ) < 2 ){
		
		} else {
			$tmp_name = sanitize_title( $title . "-" .$tmp_name ) ;
		}
		$file_name = sys_get_temp_dir( );
		$file_name =  "$file_name/$tmp_name.pdf";

		$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$tcpdf->SetCreator(PDF_CREATOR);
		$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$tcpdf->setLanguageArray($l);
		$tcpdf->setFontSubsetting(true);
		$tcpdf->AddPage();

		$content = preg_replace( "/\r|\n/", "", $content );
		preg_match_all("/<iframe.*?\/iframe>/i", $content, $outputs );
		foreach( $outputs[0] as $output ){
			$doc = new DOMDocument();
			@$doc->loadHTML( $output );
			$iframe = $doc->getElementsByTagName( "iframe" );
			foreach( $iframe as $element ){
				$src = $element->getAttribute('src');

				$height = $element->getAttribute('height');
				$width = $element->getAttribute('width');
				$height = intval( $height ) > 100 ? $height : 480;
				$width = intval( $width ) > 100 ? $width : 360;
				preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $src, $matches );
				if(isset($matches[2]) && $matches[2] != ''){
					$video_id = $matches[2];//var_dump($video_id);
					$yvid = "<br><a target=\"_blank\" ".
						"href=\"http://www.youtube.com/embed/$video_id\">".
						"<img src=\"{$this->plugin_url}views/images/play.png\" ".
						"height=\"$height\" width=\"$width\" ".
						"style=\"padding:15px;\" />".
						"</a><br>$video_after<br>";
					$content = str_replace($output, $yvid, $content);
				}

			}
		}
		preg_match_all("/<object.*?\/object>/i", $content, $outputs );
		foreach( $outputs[0] as $output ){
			$doc = new DOMDocument();
			@$doc->loadHTML( $output );
			$iframe = $doc->getElementsByTagName( "param" );
			foreach( $iframe as $element ){
				$src = $element->getAttribute('value');
				preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $src, $matches );
				if(isset($matches[2]) && $matches[2] != ''){
					$video_id = $matches[2];//var_dump($video_id);
					$yvid = "<br><a target=\"_blank\" ".
						"href=\"http://www.youtube.com/embed/$video_id\">".
						"<img src=\"{$this->plugin_url}views/images/play.png\" ".
						" height=\"$height\" width=\"$width\"/>".
						"</a><br>$video_after<br>";
					$content = str_replace($output, $yvid, $content);
				}

			}
		}
		$tcpdf->writeHTML( "<h3>$title</h3><br/>$content", true, 0, true, 0,'');
		$tcpdf->Output( $file_name, 'F');

		return array( 'path' => $file_name );
	}
}
?>

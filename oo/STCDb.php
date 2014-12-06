<?php

class StcDb{
	private $salt;
	private $wpdb;
	private $table;

	public function __construct( $table ){
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->salt = 'p3MhcNGn';
		$this->table = $table;
	}
	public function check_if_exists( $token ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'] . " WHERE username = '" .
		 	$token['username'] . "' AND social = '" . $token['social'] . "' ".
			"AND access_key = '" . $token['access_key'] . "' AND access_secret = '" . $token['access_secret'] . "'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result[0]['id'];
	}
	public function check_if_proxy_exists( $token ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['proxy'] . " WHERE proxy = '" .
		 	$token['proxy'] . "'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result[0]['id'];
	}
	public function fetch_current_user( $token ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'] . " WHERE username = '" .
		 	$token['username'] . "' AND social = '" . $token['social'] . "'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result[0];
	}
	public function fetch_all_users($token){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'] . " WHERE social = '" . $token['social'] . "'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_authority_user($token){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'] . " WHERE social = '" . 
			$token['social'] . "' AND authority = '1'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result[0];;
	}
	public function fetch_all_twitter( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['twitter'];
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_accounts( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'];
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_twitter_item( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['twitter'] . " WHERE status = '0' ORDER BY RAND() LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_twitter_item_by_id( $id  ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['twitter'] . " WHERE id = '$id' LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_queue_item( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['queue'] . " WHERE status = '0' ".
			" AND social != 'scribd' ORDER BY RAND() LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_queue_item_by_id( $id  ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['queue'] . " WHERE id = '$id' LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_all_queue_items( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['queue'];
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_all_proxy_items( ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['proxy'];
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_account_data( $id ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['accounts'] . " WHERE id = '$id'";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function check_for_posting( $post_id, $status = 0 ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['queue'] . " WHERE post_id = '$post_id' ".
			"AND status = '$status' AND social != 'scribd' LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function fetch_scribd_queue( $post_id ){
		$this->wpdb->show_errors();
		$sql = "SELECT * FROM " . $this->table['queue'] . " WHERE post_id = '$post_id' ".
			"AND status = '0' AND social = 'scribd' LIMIT 1";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}
	public function change_authority( $id, $social, $val ){
		$this->wpdb->show_errors();

		$this->wpdb->update(  $this->table['accounts'], 
			array( 'authority' => 0 ), 
			array(  'social' => $social ));

		$this->wpdb->update(  $this->table['accounts'], 
			array( 'authority' => $val ), 
			array(  'id' => $id ));
	}
	public function insert_raw_user_data($token){
		$this->wpdb->show_errors();
		$flag = $this->check_if_exists( $token );
		if( !$flag ){
			$this->wpdb->insert( $this->table['accounts'], 
				array( 
					'social'	=> $token['social'],
					'username' 	=> $token['username'],
					'password'	=> $token['password'],
					'access_key'	=> $token['access_key'],
					'access_secret'	=> $token['access_secret'],
					'access_token'	=> $token['access_token'],
					'access_token_secret'	=> $token['access_token_secret'] )
				);
			return $this->wpdb->insert_id;
		}
		else{
			$this->wpdb->update(  $this->table['accounts'], 
				array( 
					'social'	=> $token['social'],
					'username' 	=> $token['username'],
					'password'	=> $token['password'],
					'access_key'	=> $token['access_key'],
					'access_secret'	=> $token['access_secret'],
					'access_token'	=> $token['access_token'],
					'access_token_secret'	=> $token['access_token_secret']), 
				array(  'id' => $flag ));
			return $flag;
		}
	}
	public function insert_twitter_data($token){
		$this->wpdb->show_errors();
		$flag = $token['id'];
		if( !$flag ){
			$this->wpdb->insert( $this->table['twitter'], 
				array( 
					'username' 	=> $token['username'],
					'twitter_id'	=> $token['twitter_id'],
					'description'	=> $token['description'],
					'profile_image'	=> $token['profile_image'],
					'is_follower'	=> $token['is_follower'],
					'status'	=> $token['status'],
					'created_timestamp'	=> time() )
				);
			return $this->wpdb->insert_id;
		}
		else{
			$this->wpdb->update(  $this->table['twitter'], 
				array( 
					'is_follower'	=> $token['is_follower'],
					'status'	=> $token['status'], ), 
				array(  'id' => $flag ));
			return $flag;
		}
	}
	public function insert_proxy_data($token){
		$this->wpdb->show_errors();
		$flag = $this->check_if_proxy_exists( $token );
		if( !$flag ){
			$this->wpdb->insert( $this->table['proxy'], 
				array( 
					'port' 		=> $token['port'],
					'proxy'		=> $token['proxy'],
					'status'	=> $token['status'],
					'username'	=> $token['username'],
					'password'	=> $token['password'],
					'last_request'	=> $token['last_request'],
					'request_count'	=> $token['request_count'],
					'request_errors'=> $token['request_errors'],
					'last_request_time'	=> $token['last_request_time'],
					'created'	=> time() )
				);
			return $this->wpdb->insert_id;
		}
		else{
			$this->wpdb->update(  $this->table['proxy'], 
				array( 
					'port'		=> $token['port'],
					'proxy'		=> $token['proxy'],
					'status'	=> $token['status'],
					'username'	=> $token['username'],
					'password'	=> $token['password']), 
				array(  'id' => $flag ));
			return $flag;
		}
	}
	public function insert_user_data($token){
		$this->wpdb->show_errors();
		$flag = $this->check_if_exists( $token );
		if( !$flag ){
			$this->wpdb->insert( $this->table['accounts'], 
				array( 
					'social'	=> $token['social'],
					'username' 	=> $token['username'],
					'password'	=> $this->encrypt($token['password']),
					'access_key'	=> $token['access_key'],
					'access_secret'	=> $token['access_secret'],
					'access_token'	=> $this->encrypt($token['access_token']),
					'access_token_secret'	=> $this->encrypt($token['access_token_secret']))
				);
			return $this->wpdb->insert_id;
		}
		else{
			$this->wpdb->update(  $this->table['accounts'], 
				array( 
					'social'	=> $token['social'],
					'username' 	=> $token['username'],
					'password'	=> $this->encrypt($token['password']),
					'access_key'	=> $token['access_key'],
					'access_secret'	=> $token['access_secret'],
					'access_token'	=> $this->encrypt($token['access_token']),
					'access_token_secret'	=> $this->encrypt($token['access_token_secret'])), 
				array(  'id' => $flag ));
			return $flag;
		}
	}
	public function insert_queue_data($token){
		$this->wpdb->show_errors();
		$flag = isset( $token['id']) ? TRUE : FALSE;
		if( !$flag ){
			$this->wpdb->insert( $this->table['queue'], 
				array( 
					'url'		=> trim( $token['url'] ),
					'link' 		=> trim( $token['link'] ),
					'meta'	 	=> trim( $token['meta'] ),
					'title' 	=> trim( $token['title'] ),
					'action' 	=> trim( $token['action'] ),
					'social'	=> trim( $token['social'] ),
					'status' 	=> trim( $token['status'] ),
					'post_id' 	=> trim( $token['post_id'] ),
					'response' 	=> trim( $token['response'] ),
					'social_id' 	=> trim( $token['social_id'] ),
					'description' 	=> trim( $token['description'] ),
					'posted_timestamp' => trim( $token['posted_timestamp'] ),
					'created_timestamp' => time(),
					'response_interpretation' => trim( $token['response_interpretation'] ) )
				);
			return $this->wpdb->insert_id;
		}
		else{
			$this->wpdb->update(  $this->table['queue'], 
				array( 
					'url'		=> trim( $token['url'] ),
					'status' 	=> trim( $token['status'] ),
					'response' 	=> trim( $token['response'] ),
					'posted_timestamp' => time(),
					'response_interpretation' => trim( $token['response_interpretation'] ) ), 
				array(  'id' => $token['id']));
			return $flag;
		}
	}
	public function access_data_update($token){

	}
	public function delete_user_data($token){
		$this->wpdb->show_errors();
		$sql_delete = " DELETE FROM ". $this->table['accounts'] ." WHERE id = '" . $token['id'] . "'";
		return $this->wpdb->query($sql_delete);
	}
	public function delete_queue_item( $id ){
		$this->wpdb->show_errors();
		$sql_delete = " DELETE FROM ". $this->table['queue'] ." WHERE id = '$id'";
		return $this->wpdb->query($sql_delete);
	}
	public function delete_proxy_item( $id ){
		$this->wpdb->show_errors();
		$sql_delete = " DELETE FROM ". $this->table['proxy'] ." WHERE id = '$id'";
		return $this->wpdb->query($sql_delete);
	}
	public function delete_twitter_item( $id ){
		$this->wpdb->show_errors();
		$sql_delete = " DELETE FROM ". $this->table['twitter'] ." WHERE id = '$id'";
		return $this->wpdb->query($sql_delete);
	}
	public function encrypt($text){ 
    		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,
			 $this->salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 				MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	} 
	public function decrypt($text){
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->salt, base64_decode($text), MCRYPT_MODE_ECB, 
				mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
}

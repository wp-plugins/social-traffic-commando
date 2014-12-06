<?php
class Diigo {

    function Diigo( $key, $user , $pass) {
	$this->key = $key;
        $this->username = $user;
        $this->password = $pass;
	
        $this->response = array();
        $this->timeout  = 5;
    }
    
    function get_bookmarks( ) {
        $url = 'https://secure.diigo.com/api/v2/bookmarks?key=' . 
		urlencode($this->key) .'&user=' . urlencode($this->username);
        return $this->fetch($url, $fields);
    }
    function submit($link,$title, $description, $tags = 'bookmarks' ) {
       	$url = 'https://secure.diigo.com/api/v2/bookmarks';
	$fields = array(
		'key'    =>  $this->key,
		'user'   =>  $this->username,
		'title'  => $title,
		'url'    =>   $link,
		'desc'   =>  $description,
		'tags'   =>  $tags,
		'shared' => 'yes'
	);
        return $this->fetch($url, $fields, true);
    }
    
    function fetch($url, $fields, $method = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Social Traffic Commando Wordpress Plugin');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
	if($method){
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	}
        $output = curl_exec($ch);
        $this->response = curl_getinfo($ch);
        curl_close($ch);
        if($this->response) {
            return $this->response;
        }
        else {
            return false;
        }
    }

}
?>

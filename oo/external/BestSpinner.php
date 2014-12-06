<?php

class BestSpinner{
	private $url;
	private $username;
	private $password;
	
	public function __construct( $username, $password ){
		$this->url = 'http://thebestspinner.com/api.php';
		$this->username = $username;
		$this->password = $password;
	}
	public function curl_post($url, $data, &$info){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curl_postData($data));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $this->url);
		$html = trim(curl_exec($ch));
		curl_close($ch);
		return $html;
	}
	public function curl_postData( $data ){
		$fdata = "";
		foreach($data as $key => $val){
			$fdata .= "$key=" . urlencode($val) . "&";
		}
		return $fdata;
	}
	public function get_variation( $content ){
		$testmethod = 'replaceEveryonesFavorites';

		$data = array();
		$data['action'] = 'authenticate';
		$data['format'] = 'php'; 
		$data['username'] = $this->username;
		$data['password'] = $this->password;
		$output = unserialize( $this->curl_post($this->url, $data, $info));
		//var_dump( $data );
		//var_dump($output);
		if($output['success']=='true'){
			$session = $output['session'];
			$data['action'] = 'apiQuota';
			$quota = unserialize( $this->curl_post($this->url, $data, $info) );
			//var_dump($quota);
			if( $quota['output'] > 0 ){
				$session = $output['session'];
				$data = array();
				$data['session'] = $session;
				$data['format'] = 'php';
				$data['text'] = $content;
				$data['action'] = $testmethod;
				$data['maxsyns'] = '3';
				if($testmethod=='replaceEveryonesFavorites'){
					$data['quality'] = '1';
				}
				$output = $this->curl_post($this->url, $data, $info);
				$output = unserialize($output);
				return $output['success']=='true' ? $data['text'] : $content;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	}
}
?>

<?php
class LiveJournal {
	private $p;
	private $p_title;
	private $p_content;
	private $p_backlink;
	private $p_username;
	private $p_password;

	public function __construct( $username, $password ){
		$this->p = '';
		$this->p_title = '';
		$this->p_content = '';
		$this->p_backlink = '';
		$this->p_password = $password;
		$this->p_username = $username;
	}

	private function get_username(){
	}

	private function get_password(){
	}

	public function set_username( $p_username ){
	}

	public function set_password( $p_password ){
	}

	public function set_post_title( $p_title ){
		$this->p_title = trim($p_title);
	}

	public function set_post_content( $p_content ){
		$this->p_content = trim($p_content);
	}

	public function get_backlink(){
		return $this->p_backlink;
	}
	public function check_if_correct(){
		$fp = fsockopen("www.livejournal.com", 80, $errno, $errstr, 30);
		if (!$fp) {
    			echo "$errstr ($errno)<br />\n";
		}
		else{
			$user = $this->p_username;
			$pass = $this->p_password;
			$out  = "POST /interface/flat HTTP/1.1\r\n";
			$out .= "Host: www.livejournal.com\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Content-length: ". mb_strlen( $query ) ."\r\n\r\n";
	
			$out .= "mode=login&user=" . $user . "&password=" . $pass;
	
			fwrite($fp, $out);
			$temp = fgets($fp, 128);
			fclose($fp);
			$pos = stripos( $temp, 'OK');
			if($pos)
				return true;
			else
				return false;
		}
	}
	public function post_submit( ){
		$fp = fsockopen("www.livejournal.com", 80, $errno, $errstr, 30);
		if (!$fp) {
    			echo "$errstr ($errno)<br />\n";
		}
		else{
			$user = $this->p_username;
			$pass = $this->p_password;
			$query = "mode=postevent&user=" . $user . "&password=" . $pass . 
				"&event=". urlencode($this->p_content)  . 
				"&subject=" . urlencode($this->p_title) .
				"&year=" . date("Y", time() ) . "&mon=" . date("n", time() ) .
				"&day=" . date("j", time() ) ."&hour=" . date("G", time() ) . 
				"&min=" . date("i", time() );

			$out  = "POST /interface/flat HTTP/1.1\r\n";
			$out .= "Host: www.livejournal.com\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Content-length: ". mb_strlen( $query ) ."\r\n\r\n";
	
			$out .= $query;
			$temp = array();
			fwrite($fp, $out);
			while (! feof ( $fp )) {
				$temp[] = fgets($fp, 128);
			}
			fclose($fp);
			return $temp;
		}
	}
	
}
?>

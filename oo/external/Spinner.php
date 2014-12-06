<?php

class Spinner{

	public function spin($string, $seedPageName = false, $openingConstruct = '{', 
				$closingConstruct = '}', $separator = '|'){

		if(strpos($string, $openingConstruct) === false){	
			return $string;
		}
		$startPositions = $this->strpos_all($string, $openingConstruct);
		$endPositions   = $this->strpos_all($string, $closingConstruct);
	
		if($startPositions === false OR count($startPositions) !== count($endPositions)){
			return $string;
		}
		if($seedPageName){
			mt_srand(crc32($_SERVER['REQUEST_URI']));
		}
		$openingConstructLength = mb_strlen($openingConstruct);
		$closingConstructLength = mb_strlen($closingConstruct);
	
		foreach($startPositions as $pos){
			$order[$pos] = 'open';
		}
		foreach($endPositions as $pos){
			$order[$pos] = 'close';
		}
		ksort($order);
	
		$depth = 0;
		$chunk = 0;
		foreach($order as $position => $state){
			if($state == 'open'){
				$depth++;
				$history[] = $position;
			}
			else{
				$lastPosition   = end($history);
				$lastKey		= key($history);
				unset($history[$lastKey]);
	
				$store[$depth][] = mb_substr($string, $lastPosition + $openingConstructLength, 
						$position - $lastPosition - $closingConstructLength);
				$depth--;
			}
		}
		krsort($store);
		unset($order);
		$original = $store[1];
		foreach($store as $depth => $values){
			foreach($values as $key => $spin){
				$choices = explode($separator, $store[$depth][$key]);
				$replace = $choices[mt_rand(0, count($choices) - 1)];
				$level = $depth;
				while($level > 0){
					foreach($store[$level] as $k => $v){
						$find = $openingConstruct.$store[$depth][$key].$closingConstruct;
						if($level == 1 AND $depth == 1){
							$find = $store[$depth][$key];
						}
						$store[$level][$k] = $this->str_replace_first($find, $replace,
									 $store[$level][$k]);
					}
					$level--;
				}
			}
		}
		foreach($original as $key => $value){	
			$string = $this->str_replace_first($openingConstruct.$value.$closingConstruct, 
						$store[1][$key], $string);
		}
		return $string;
	}
	public function str_replace_first($find, $replace, $string){
		if(!is_array($find)){
			$find = array($find);
		}
		if(!is_array($replace)){
		
			$replace = array($replace);
		}
		foreach($find as $key => $value){
			if(($pos = mb_strpos($string, $value)) !== false){
				if(!isset($replace[$key])){
					$replace[$key] = '';
				}
				$string = mb_substr($string, 0, $pos).$replace[$key].mb_substr($string, $pos + 
						mb_strlen($value));
			}
		}
		return $string;
	}
	public function strpos_all($haystack, $needle){
		$offset = 0;
		$i	  = 0;
		$return = false;
		while(is_integer($i)){
			$i = mb_strpos($haystack, $needle, $offset);
			if(is_integer($i)){
				$return[]   = $i;
				$offset	 = $i + mb_strlen($needle);
			}
		}
		return $return;
	}
	public function magic_number($string, $separator = '|'){
		$temp = substr_count ( $string, $separator );
		if($temp <= 0){
			return 1;
		}
		else{
			return $temp;
		}
	}
}

?>

<?php
class Delicious {

    function Delicious($user,$pass) {
        $this->username = $user;
        $this->password = $pass;
        $this->response = array();
        $this->timeout  = 5;
    }
    
    function getURLs($tag = '',$results = 15) {
        $url = 'https://api.del.icio.us/v1/posts/all?results=' . urlencode($results) . '&tag=' . urlencode($tag);
        return $this->fetch($url);
    }
    
    function getDates($tag = '') {
        $url = 'https://api.del.icio.us/v1/posts/dates?tag=' . urlencode($tag);
        return $this->fetch($url);
    }
    
    function getTags() {
        $url = 'https://api.del.icio.us/v1/tags/get?';
        return $this->fetch($url);
    }
    
    function suggestTags($link) {
        $url = 'https://api.del.icio.us/v1/posts/suggest?url=' . urlencode($link);
        return $this->fetch($url);
    }
    
    function addURL($link,$description,$tags,$notes) {
        $url = 'https://api.del.icio.us/v1/posts/add?url=' . urlencode($link) . '&description=' . urlencode($description) . '&extended=' . urlencode($notes) . '&tags=' . urlencode($tags) . '&replace=no&shared=yes';
        return $this->fetch($url);
    }
    
    function fetch($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Social Commando Wordpress Plugin');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $output = curl_exec($ch);
        $this->response = curl_getinfo($ch);
        curl_close($ch);
        if((int)$this->response['http_code'] == 200) {
            return  $output;
        }
        else {
            return false;
        }
    }

}
?>

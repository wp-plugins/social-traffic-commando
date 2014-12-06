<?php
/*#############################################################################
Project Name: NextScripts Google+ AutoPoster
Project URL: http://www.nextscripts.com/google-plus-automated-posting
Description: Automatically posts to your Google+ profile and/or Google+ page.
Author: NextScripts, Inc
Version: 2.11.8 (jan 27, 2014)
Author URL: http://www.nextscripts.com
Copyright 2012-2013  Next Scripts, Inc
#############################################################################*/
//## Google Verification email. - Fill it if you get message "Login Verification is required. Please Enter your Google backup/recovery email ot the postToGooglePlus.php"
$gPlusRecoveryEmail = '';
$gPlusRecoveryPhone = '';

if (!function_exists('prr')){ function prr($str) { echo "<pre>"; print_r($str); echo "</pre>\r\n"; }}        
//## Code - General Functions
if (!function_exists("CutFromTo")) {function CutFromTo($string, $from, $to) {$fstart = stripos($string, $from); $tmp = substr($string,$fstart+strlen($from));$flen = stripos($tmp, $to);  return substr($tmp,0, $flen); }}
if (!function_exists("getUqID")) {function getUqID() {return mt_rand(0, 9999999);}}
if (!function_exists("build_http_query")) {function build_http_query( $query ){ $query_array = array(); foreach( $query as $key => $key_value ){ $query_array[] = $key . '=' . urlencode( $key_value );} return implode( '&', $query_array );}}
if (!function_exists("rndString")) {function rndString($lngth){$str='';$chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";$size=strlen($chars);for($i=0;$i<$lngth;$i++){$str .= $chars[rand(0,$size-1)];} return $str;}}
if (!function_exists("prcGSON")) {function prcGSON($gson){ $json = substr($gson, 5); $json = str_replace(',{',',{"',$json); $json = str_replace(':[','":[',$json); $json = str_replace(',{""',',{"',$json); $json = str_replace('"":[','":[',$json); 
  $json = str_replace('[,','["",',$json); $json = str_replace(',,',',"",',$json); $json = str_replace(',,',',"",',$json); return $json; 
}}
if (!function_exists("nxsCheckSSLCurl")){function nxsCheckSSLCurl($url){
  $ch = curl_init($url); $headers = array(); $headers[] = 'Accept: text/html, application/xhtml+xml, */*'; $headers[] = 'Cache-Control: no-cache';
  $headers[] = 'Connection: Keep-Alive'; $headers[] = 'Accept-Language: en-us';  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)"); 
  $content = curl_exec($ch); $err = curl_errno($ch); $errmsg = curl_error($ch); if ($err!=0) return array('errNo'=>$err, 'errMsg'=>$errmsg); else return false;
}}
if (!function_exists("cookArrToStr")){function cookArrToStr($cArr){ $cs = ''; if (!is_array($cArr)) return ''; foreach ($cArr as $cName=>$cVal){ $cs .= $cName.'='.$cVal.'; '; } return $cs; }}
if (!function_exists("getCurlPageMC")){function getCurlPageMC($ch, $ref='', $ctOnly=false, $fields='', $dbg=false, $advSettings='') { $ccURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 
  if ($dbg) echo '<br/><b style="font-size:16px;color:green;">#### START CURL:'.$ccURL.'</b><br/>'; 
  static $curl_loops = 0; static $curl_max_loops = 20; global $nxs_gCookiesArr, $nxs_gCookiesArrBD; $cookies =  cookArrToStr($nxs_gCookiesArr); if ($dbg) { echo '<br/><b style="color:#005800;">## Request Cookies:</b><br/>'; prr($cookies);}
  if ($curl_loops++ >= $curl_max_loops){ $curl_loops = 0; return false; }
  $headers = array(); $headers[] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'; $headers[] = 'Cache-Control: no-cache';
  $headers[] = 'Connection: Keep-Alive';  $headers[] = 'Accept-Language: en-US,en;q=0.8';// $headers[] = 'Accept-Encoding: gzip, deflate';   
  
  if (isset($advSettings['Content-Type'])) $headers[] = 'Content-Type: '.$advSettings['Content-Type']; else 
    if ($fields!='') { if((stripos($ccURL, 'www.blogger.com/blogger_rpc')!==false)) $headers[] = 'Content-Type: application/javascript; charset=UTF-8'; else $headers[] = 'Content-Type: application/x-www-form-urlencoded;charset=utf-8';}  
  if (stripos($ccURL, 'www.blogger.com/blogger_rpc')!==false) {$headers[] = 'X-GWT-Permutation: 0408F3763409DF91729BBA5B25869425';
    $headers[] = 'X-GWT-Module-Base: https://www.blogger.com/static/v1/gwt/';    
  }
  if (isset($advSettings['liXMLHttpRequest'])) $headers[] = 'X-Requested-With: XMLHttpRequest';
  if (isset($advSettings['Origin'])) $headers[] = 'Origin: '.$advSettings['Origin'];    
  if (stripos($ccURL, 'blogger.com')!==false && (isset($advSettings['cdomain']) &&  $advSettings['cdomain']=='google.com') ) $advSettings['cdomain']='blogger.com';
  if(isset($advSettings['noSSLSec'])){curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); } 
  if(isset($advSettings['proxy']) && $advSettings['proxy']['host']!='' && $advSettings['proxy']['port']!==''){
    if ($dbg) { echo '<br/><b style="color:#005800;">## Using Proxy:</b><br/>'; /*prr($advSettings); */}
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
    curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP ); curl_setopt( $ch, CURLOPT_PROXY, $advSettings['proxy']['host'] ); curl_setopt( $ch, CURLOPT_PROXYPORT, $advSettings['proxy']['port'] );
    if ( isset($advSettings['proxy']['up']) && $advSettings['proxy']['up']!='' ) { curl_setopt( $ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY ); curl_setopt( $ch, CURLOPT_PROXYUSERPWD, $advSettings['proxy']['up'] );}
  }
  if(isset($advSettings['headers'])){$headers = array_merge($headers, $advSettings['headers']);}  // prr($advSettings);
  curl_setopt($ch, CURLOPT_HEADER, true);     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_COOKIE, $cookies); curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // prr($headers);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);  if (is_string($ref) && $ref!='') curl_setopt($ch, CURLOPT_REFERER, $ref); 
  if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) { curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); } 
  curl_setopt($ch, CURLOPT_USERAGENT, (( isset( $advSettings['UA']) && $advSettings['UA']!='')?$advSettings['UA']:"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.44 Safari/537.36")); 
  if ($fields!=''){ curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); } else { curl_setopt($ch, CURLOPT_POST, false); curl_setopt($ch, CURLOPT_POSTFIELDS, '');  curl_setopt($ch, CURLOPT_HTTPGET, true); } 
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
  $content = curl_exec($ch); //prr($content);  
  $errmsg = curl_error($ch);  if (isset($errmsg) && stripos($errmsg, 'SSL')!==false) { curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  $content = curl_exec($ch); }
  if (strpos($content, "\n\n")!=false && strpos($content, "\n\n")<100)  $content = substr_replace($content, "\n", strpos($content,"\n\n"), strlen("\n\n"));    
  if (strpos($content, "\r\n\r\n")!=false && strpos($content, "\r\n\r\n")<100) $content = substr_replace($content, "\r\n", strpos($content,"\r\n\r\n"), strlen("\r\n\r\n"));
  $ndel = strpos($content, "\n\n"); $rndel = strpos($content, "\r\n\r\n"); if ($ndel==false) $ndel = 1000000; if ($rndel==false) $rndel = 1000000; $rrDel = $rndel<$ndel?"\r\n\r\n":"\n\n";   
  @list($header, $content) = explode($rrDel, $content, 2);
  if ($ctOnly!==true) { $nsheader = curl_getinfo($ch); $err = curl_errno($ch); $errmsg = curl_error($ch); $nsheader['errno'] = $err;  $nsheader['errmsg'] = $errmsg;  $nsheader['headers'] = $header; $nsheader['content'] = $content; }
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $headers = curl_getinfo($ch); if ($dbg) { echo '<br/><b style="color:#005800;">## Headers:</b><br/>';  prr($headers); prr($header);} 
  if (empty($headers['request_header'])) $headers['request_header'] = 'Host: None'."\n";
  $results = array(); preg_match_all('|Host: (.*)\n|U', $headers['request_header'], $results); $ckDomain = str_replace('.', '_', $results[1][0]);  $ckDomain = str_replace("\r", "", $ckDomain); $ckDomain = str_replace("\n", "", $ckDomain);
  if ($dbg) { echo '<br/><b style="color:#005800;">## Domain:</b><br/>'; prr($ckDomain); } 
  
  $results = array(); $cookies = '';  preg_match_all('|Set-Cookie: (.*);|U', $header, $results); $carTmp = $results[1]; //$nxs_gCookiesArr = array_merge($nxs_gCookiesArr, $ret['cookies']); 
  preg_match_all('/Set-Cookie: (.*)\b/', $header, $xck); $xck = $xck[1]; if ($dbg) { echo "Full Resp Cookies"; prr($xck); echo "Plain Resp Cookies"; prr($carTmp); }
  //$clCook = array();
  if (isset($advSettings['cdomain']) &&  $advSettings['cdomain']!=''){
      foreach ($carTmp as $iii=>$cTmp) if (stripos($xck[$iii],'Domain=')===false || stripos($xck[$iii],'Domain=.'.$advSettings['cdomain'].';')!==false){ $ttt = explode('=',$cTmp,2); $nxs_gCookiesArr[$ttt[0]]=$ttt[1];  }
  } else { foreach ($carTmp as $cTmp){ $ttt = explode('=',$cTmp,2); $nxs_gCookiesArr[$ttt[0]]=$ttt[1];}}   
  foreach ($carTmp as $cTmp){ $ttt = explode('=',$cTmp,2); $nxs_gCookiesArrBD[$ckDomain][$ttt[0]]=$ttt[1]; }  
  if ($dbg) { echo '<br/><b style="color:#005800;">## Common/Response Cookies:</b><br/>'; prr($nxs_gCookiesArr); echo "\r\n\r\n<br/>".$ckDomain."\r\n\r\n"; prr($nxs_gCookiesArrBD); }
  if ($dbg && $http_code == 200){  $contentH = htmlentities($content);    prr($contentH);  } $rURL = '';
  
  if ($http_code == 200 && stripos($content, 'http-equiv="refresh" content="0; url=&#39;')!==false ) {
    $http_code=301; $rURL = CutFromTo($content, 'http-equiv="refresh" content="0; url=&#39;','&#39;"'); 
    if (stripos($rURL, 'blogger.com')===false) $nxs_gCookiesArr = array(); 
  } 
  elseif ($http_code == 200 && stripos($content, 'location.replace')!==false ) {$http_code=301; $rURL = CutFromTo($content, 'location.replace("','"'); }// echo "~~~~~~~~~~~~~~~~~~~~~~".$rURL."|".$http_code;
  if ($http_code == 301 || $http_code == 302 || $http_code == 303){  
    if ($rURL!='') { $rURL = str_replace('\x3d','=',$rURL); $rURL = str_replace('\x26','&',$rURL);
      $url = @parse_url($rURL); } else { $matches = array(); preg_match('/Location:(.*?)\n/', $header, $matches); $url = @parse_url(trim(array_pop($matches))); } $rURL = ''; //echo "#######"; prr($url);
    if (!$url){ $curl_loops = 0; return ($ctOnly===true)?$content:$nsheader;}
    $last_urlX = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); $last_url = @parse_url($last_urlX);
    if (!isset($url['scheme'])) $url['scheme'] = $last_url['scheme'];  if (!isset($url['host'])) $url['host'] = $last_url['host'];  if (!$url['path']) $url['path'] = $last_url['path']; if (!isset($url['query'])) $url['query'] = '';
    $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:''); curl_setopt($ch, CURLOPT_URL, $new_url);
    if ($dbg) echo '<br/><b style="color:#005800;">Redirecting to:</b>'.$new_url."<br/>"; return getCurlPageMC($ch, $last_urlX, $ctOnly, '', $dbg, $advSettings); 
  } else { $curl_loops=0; return ($ctOnly===true)?$content:$nsheader;}
}}
if (!function_exists("getCurlPageX")){function getCurlPageX($url, $ref='', $ctOnly=false, $fields='', $dbg=false, $advSettings='') { if ($dbg) echo '<br/><b style="font-size:16px;color:green;">#### GSTART URL:'.$url.'</b><br/>'; 
  $ch = curl_init($url); $contents = getCurlPageMC($ch, $ref, $ctOnly, $fields, $dbg, $advSettings); curl_close($ch); return $contents;
}}
if (!function_exists("nxs_clFN")){ function nxs_clFN($fn){$sch = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
  return trim(preg_replace('/[\s-]+/', '-', str_replace($sch, '', $fn)), '.-_');    
}}
if (!function_exists("nxs_mkImgNm")){ function nxs_mkImgNm($fn, $cType){ $iex = array(".png", ".jpg", ".gif", ".jpeg"); $map = array('image/gif'=>'.gif','image/jpeg'=>'.jpg','image/png'=>'.png');
  $fn = str_replace($iex, '', $fn); if (isset($map[$cType])){return $fn.$map[$cType];} else return $fn.".jpg";    
}}
//================================GOOGLE===========================================
// Back Version 1.x Compatibility
if (!function_exists("doConnectToGooglePlus")) {function doConnectToGooglePlus($connectID, $email, $pass){ return doConnectToGooglePlus2($email, $pass);}}
if (!function_exists("doGetGoogleUrlInfo")) {function doGetGoogleUrlInfo($connectID, $url){ return doGetGoogleUrlInfo2($url);}}
if (!function_exists("doPostToGooglePlus")) {function doPostToGooglePlus($connectID, $msg, $lnk='', $pageID=''){ return doPostToGooglePlus2($msg, $lnk, $pageID);}}
// New 2.X Functions
if (!function_exists("doConnectToGooglePlus2")) {function doConnectToGooglePlus2($email, $pass, $srv = 'GP'){ global $nxs_gCookiesArr, $nxs_gCookiesArrBD, $gPlusRecoveryEmail, $gPlusRecoveryPhone; $nxs_gCookiesArr = array(); $advSettings = array();
  if ($gPlusRecoveryPhone=='' && isset($_COOKIE['gPlusRecoveryPhone']) && $_COOKIE['gPlusRecoveryPhone']!='') { $gPlusRecoveryPhone = $_COOKIE['gPlusRecoveryPhone']; 
    if (!headers_sent()) { setcookie ("gPlusRecoveryPhone", "", time() - 3600); setcookie ("gPlusRecoveryPhoneHint", "", time() - 3600);}}
  if ($gPlusRecoveryEmail=='' && isset($_COOKIE['gPlusRecoveryEmail']) && $_COOKIE['gPlusRecoveryEmail']!='') { $gPlusRecoveryEmail = $_COOKIE['gPlusRecoveryEmail']; 
    if (!headers_sent()) { setcookie ("gPlusRecoveryEmail", "", time() - 3600); setcookie ("gPlusRecoveryEmailHint", "", time() - 3600);}}    
  $err = nxsCheckSSLCurl('https://accounts.google.com/ServiceLogin'); if ($err!==false && $err['errNo']=='60') $advSettings['noSSLSec'] = true;     
  if ($srv == 'GP') $lpURL = 'https://accounts.google.com/ServiceLogin?service=oz&continue=https://plus.google.com/?gpsrc%3Dogpy0%26tab%3DwX%26gpcaz%3Dc7578f19&hl=en-US'; 
  if ($srv == 'YT') $lpURL = 'https://accounts.google.com/ServiceLogin?service=oz&checkedDomains=youtube&checkConnection=youtube%3A271%3A1%2Cyoutube%3A69%3A1&continue=https://www.youtube.com/&hl=en-US';   
  if ($srv == 'BG') $lpURL = 'https://accounts.google.com/ServiceLogin?service=blogger&passive=1209600&continue=https://www.blogger.com/home&followup=https://www.blogger.com/home&ltmpl=start#s01';
  $contents = getCurlPageX($lpURL, '', true, '', false, $advSettings);        
  //## GET HIDDEN FIELDS
  $md = array(); $mids = ''; $flds  = array();
  while (stripos($contents, '<input')!==false){ $inpField = trim(CutFromTo($contents,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"'));
     if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val;      $mids .= "&".$name."=".$val;}
     $contents = substr($contents, stripos($contents, '<input')+8);
  } $flds['Email'] = $email; $flds['Passwd'] = $pass;  $flds['signIn'] = 'Sign%20in'; // $flds['bgresponse'] = $bg;
  $fldsTxt = build_http_query($flds); if ($srv == 'GP' || $srv == 'BG') $advSettings['cdomain']='google.com';
  //## ACTUAL LOGIN    
  $contents = getCurlPageX('https://accounts.google.com/ServiceLoginAuth', '', false, $fldsTxt, false, $advSettings);  // prr($flds);  $contents['content'] = ''; prr($contents);  die();
  
  if ($srv == 'YT') { unset($advSettings['cdomain']); $nxs_gCookiesArr = $nxs_gCookiesArrBD['accounts_youtube_com'];   //## YouTube Login
   // $contents = getCurlPageX('https://www.youtube.com/?tab=m1&authuser=0', 'https://mail.google.com/mail/u/0/#inbox', false, '', true, $advSettings);    prr($contents);  
  }  
  if (stripos($contents['url'], 'https://accounts.google.com/ServiceLoginAuth')!==false && stripos($contents['content'], '<span color="red">')!==false) return CutFromTo($contents['content'],'<span color="red">', '</span>');  
  if (stripos($contents['url'], 'NewPrivacyPolicy')!==false) return 'Please login to your account and accept new "New Privacy Policy"';  
  if (stripos($contents['content'], 'captcha-box')!==false || stripos($contents['content'], 'CaptchaChallengeOptionContent')!==false) return 'Captcha is "On" for your account. Please login to your account from the bworser and try clearing the CAPTCHA by visiting this link: <a href="https://www.google.com/accounts/DisplayUnlockCaptcha" target="_blank">https://www.google.com/accounts/DisplayUnlockCaptcha</a>. If you\'re a Google Apps user, visit https://www.google.com/a/yourdomain.com/UnlockCaptcha in order to clear the CAPTCHA. Be sure to replace \'yourdomain.com\' with your actual domain name.';
  if (stripos($contents['url'], 'ServiceLoginAuth')!==false) return 'Incorrect Username/Password '.$contents['errmsg'];  
  if (stripos($contents['url'], 'google.com/SmsAuth')!==false || stripos($contents['url'], 'google.com/SecondFactor')!==false ) return '<b style="color:#800000;">2-step verification is on.</b> <br/><br/> 2-step verification is not compatible with auto-posting. <br/><br/>Please see more here:<br/> <a href="http://www.nextscripts.com/blog/google-2-step-verification-and-auto-posting" target="_blank">Google+, 2-step verification and auto-posting</a><br/>';  
  $contents['content'] = str_ireplace('\'CREATE_CHANNEL_DIALOG_TITLE_IDV_CHALLENGE\': "Verify your identity"', "", $contents['content']);
  if (stripos( $contents['content'], 'is that really you')!==false || stripos( $contents['content'], 'Verify your identity')!==false  || stripos( $contents['url'], 'LoginVerification')!==false) { $text = $contents['content'];  
      $flds = array(); while (stripos($text, '"hidden"')!==false){$text = substr($text, stripos($text, '"hidden"')+8); $name = trim(CutFromTo($text,'name="', '"'));
        if (!in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($text,'value="', '"')); $flds[$name]= $val;      $mids .= "&".$name."=".$val;}
      } //prr($flds);   
    if ($gPlusRecoveryEmail=='' && $gPlusRecoveryPhone=='') {       
      if (stripos($contents['content'], 'RecoveryEmailChallenge')!==false) { $recEm = '';
        if (stripos($contents['content'], "Confirm my recovery email address:")!==false) $recEm = trim(CutFromTo($contents['content'], "Confirm my recovery email address:","</label>"));
        return "<b style='color:red'>Google Error Message: </b><b>Login Verification is required. <br/><br/>   Please do http://www.nextscripts.com/support-faq/#q21 <br/><br/> or <br/><br/> Please Enter your Google backup/recovery email (".$recEm.").</b><br/>Please see here how to add your backup/recovery email to Google: <a href='http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726'>http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726</a>".'        
        Enter full recovery email address: <input type="tel" name="recoveryEmail" onchange="document.cookie = \'gPlusRecoveryEmail=\'+this.value;document.cookie = \'gPlusRecoveryEmailHint='.$recEm.'\';" id="recoveryEmail" size="30" placeholder="Enter full recovery email address"><br/>
        Please click "OK", then click "Submit Test Post to Google+" button again to confirm and verify your account.<br/>';     
      }
      elseif (stripos($contents['content'], 'PhoneVerificationChallenge')!==false) { 
        if (stripos($contents['content'], "Confirm my phone number:")!==false) $recEm = trim(CutFromTo($contents['content'], "Confirm my phone number:","</label>"));
        return "<b style='color:red'>Google Error Message: </b><b>Login Verification is required. <br/><br/>   Please do http://www.nextscripts.com/support-faq/#q21 <br/><br/> or <br/><br/> Please Enter your Google phone number (".$recEm.").</b><br/>".'        
        Enter full phone number: <input type="tel" name="phoneNumber" onchange="document.cookie = \'gPlusRecoveryPhone=\'+this.value;document.cookie = \'gPlusRecoveryPhoneHint='.$recEm.'\';" id="phoneNumber" size="30" placeholder="Enter full phone number"><br/>
        Please click "OK", then click "Submit Test Post to Google+" button again to confirm and verify your account.<br/>';     
      } else {
          return "Your Google+ account is locked for the new applications to connect. Please follow this instructions to unlock it: <a href='http://www.nextscripts.com/support-faq/#q21' target='_blank'>http://www.nextscripts.com/support-faq/#q21</a> - Question #1.";
      }
      
    } else {      
     if ($gPlusRecoveryEmail!='') {
      if  (trim($gPlusRecoveryEmail)==trim($email)) return "<b style='color:red'>Google Error Message: </b><b>Your recovery email could not be the same as your login email.</b> Google Help: <a href='http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726'>http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726</a>";
    
      $bgc = CutFromTo($contents['content'], "document.bg = new botguard.bg('", "');");
      $contents = getCurlPageX('http://www.nextscripts.com/bg.php','', true, 'bg='.$bgc);
      $fldsTxt='continue=https%3A%2F%2Fplus.google.com%2F%3Fgpsrc%3Dogpy0%26tab%3DwX%26gpcaz%3D38f4feed&_utf8=%E2%98%83&bgresponse='.$contents.'&phoneNumber=&challengetype=RecoveryEmailChallenge&emailAnswer='.urlencode($gPlusRecoveryEmail).'&answer=&challengestate='.$flds['challengestate'];
      $contents = getCurlPageX('https://accounts.google.com/LoginVerification?Email='.urlencode($email).'&continue=https%3A%2F%2Fplus.google.com%2F%3Fgpsrc%3Dogpy0%26tab%3DwX%26gpcaz%3D38f4feed&service=oz','', false, $fldsTxt);          
      
      if (stripos($contents['content'], 'class="errormsg"')!==false){ $errMsg = CutFromTo($contents['content'], 'class="errormsg"', "/div>"); $errMsg =CutFromTo($errMsg, '>', "<"); 
        return '<b style="color:red">Google Error Message: </b><b>Unable to verify your recovery email.</b> Google Help: <a target="_blank" href="http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726">http://support.google.com/accounts/bin/answer.py?hl=en&answer=183726</a>. Enter full recovery email address: '.$_COOKIE["gPlusRecoveryEmailHint"].'<input type="tel" name="recoveryEmail" onchange="document.cookie = \'gPlusRecoveryEmail=\'+this.value; document.cookie = \'gPlusRecoveryEmailHint='.$_COOKIE["gPlusRecoveryEmailHint"].'\';" id="recoveryEmail" size="30" placeholder="Enter full recovery email address"><br/>Please click "OK", then click "Submit Test Post to Google+" button again to confirm and verify your account.<br/>';  }
      if ($contents['http_code']=='400' || stripos($contents['content'], 'there seems to be a problem')!==false) { return '<b style="color:red">NX Error Message: </b><b>Unable to verify your Phone. Something went wrong. Please contact support.';  }
              
      }
     if ($gPlusRecoveryPhone!='') {   
      $bgc = CutFromTo($contents['content'], "document.bg = new botguard.bg('", "');");
      $contents = getCurlPageX('http://www.nextscripts.com/bg.php','', true, 'bg='.$bgc);
      $fldsTxt='continue=https%3A%2F%2Fplus.google.com%2F%3Fgpsrc%3Dogpy0%26tab%3DwX%26gpcaz%3D38f4feed&_utf8=%E2%98%83&bgresponse='.$contents.'&phoneNumber='.urlencode($gPlusRecoveryPhone).'&challengetype=PhoneVerificationChallenge&emailAnswer=&answer=&challengestate='.$flds['challengestate'];
      $contents = getCurlPageX('https://accounts.google.com/LoginVerification?Email='.urlencode($email).'&continue=https%3A%2F%2Fplus.google.com%2F%3Fgpsrc%3Dogpy0%26tab%3DwX%26gpcaz%3D38f4feed&service=oz','', false, $fldsTxt);
      if (stripos($contents['content'], 'class="errormsg"')!==false){
          $errMsg = CutFromTo($contents['content'], 'class="errormsg"', "/div>"); $errMsg =CutFromTo($errMsg, '>', "<");
          return '<b style="color:red">Google Error Message: </b> '.$errMsg.'<br/><br/> <b>Unable to verify your Phone '.$gPlusRecoveryPhone.'.</b><br/> Google Help: <a target="_blank" href="http://support.google.com/accounts/bin/answer.py?hl=en&answer=1187657">http://support.google.com/accounts/bin/answer.py?hl=en&answer=1187657</a><br/>. Enter full phone number: '.$_COOKIE["gPlusRecoveryPhoneHint"].'<input type="tel" name="phoneNumber" onchange="document.cookie = \'gPlusRecoveryPhone=\'+this.value; document.cookie = \'gPlusRecoveryPhoneHint='.$_COOKIE["gPlusRecoveryPhoneHint"].'\';" id="phoneNumber" size="30" placeholder="Enter full phone number"><br/>Please click "OK", then click "Submit Test Post to Google+" button again to confirm and verify your account.<br/>';
      }//prr($contents);      
      if ($contents['http_code']=='400' || stripos($contents['content'], 'there seems to be a problem')!==false) { return '<b style="color:red">NX Error Message: </b><b>Unable to verify your Phone. Something went wrong. Please contact support.';  }
      } 
    }
  }
  return false;  
}}
if (!function_exists("doGetGoogleUrlInfo2")) {function doGetGoogleUrlInfo2($url){  $rnds = rndString(13); $url = urlencode($url);
  $contents = getCurlPageX('https://plus.google.com/','',false);  $at = CutFromTo($contents['content'], 'csi.gstatic.com/csi","', '",');         
  $spar='f.req=%5B%22'.$url.'%22%2Cfalse%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Ctrue%5D&at='.$at."&";
  $gurl = 'https://plus.google.com/u/0/_/sharebox/linkpreview/?soc-app=1&cid=0&soc-platform=1&hl=en&rt=j'; $contents = getCurlPageX($gurl,'',false, $spar);   //  prr($contents);
  $json = prcGSON($contents['content']);  $arr = json_decode($json, true); if (!is_array($arr)) return;
  if (!isset($arr[0]) || !is_array($arr[0])) return; if (!isset($arr[0][1]) || !is_array($arr[0][1])) return; if (!isset($arr[0][1][4]) || !is_array($arr[0][1][4])) return; if (!isset($arr[0][1][4][0]) || !is_array($arr[0][1][4][0])) return; 
  $out['link'] = $arr[0][1][4][0][1]; $out['title'] = $arr[0][1][4][0][3]; $out['domain'] = $arr[0][1][4][0][4];  $out['txt'] = $arr[0][1][4][0][7];   
  if (isset($arr[0][1][4][0][2]) && trim($arr[0][1][4][0][2])!='') $out['fav'] = $arr[0][1][4][0][2]; else $out['fav'] = 'https://s2.googleusercontent.com/s2/favicons?domain='.$out['domain'];  
  if (isset($arr[0][1][4][0][6][0])) { $out['img'] = $arr[0][1][4][0][6][0][8]; $out['imgType'] = $arr[0][1][4][0][6][0][1]; } else {
    if (isset($arr[0][1][2][1][24][3])) $out['imgType'] = $arr[0][1][2][1][24][3];
    if (isset($arr[0][1][2][1][41][0])) $out['img'] = $arr[0][1][2][1][41][0][1]; elseif (isset($arr[0][1][2][1][41][1])) $out['img'] = $arr[0][1][2][1][41][1][1];
  } $out['title'] = str_replace('&#39;',"'",$out['title']); $out['txt'] = str_replace('&#39;',"'",$out['txt']);   
  $out['txt'] = html_entity_decode($out['txt'], ENT_COMPAT, 'UTF-8');  $out['title'] = html_entity_decode($out['title'], ENT_COMPAT, 'UTF-8');   
  if (isset($arr[0][1][5][0]) && is_array($arr[0][1][5][0])) { $gArr = $arr[0][1][5][0];  $gArrCnt = count($gArr);       
      for ($i=2;$i<=$gArrCnt;$i++) if (isset($gArr[$i]) && is_array($gArr[$i]) && count($gArr[$i])==1) { $outKeys = array_keys($gArr[$i]); if ((int)array_shift($outKeys)>100) $out['arr'] = $gArr[$i]; }
  } return $out;
}}
if (!function_exists("doGetCCatsFromGooglePlus")) { function doGetCCatsFromGooglePlus($commPageID){ $gpp = 'https://plus.google.com/_/sharebox/post/?spam=20&_reqid=1203718&rt=j'; $items = '';
  $contents = getCurlPageX('https://plus.google.com/communities/'.$commPageID, '', false); // prr($contents);
  $commPageID2 = '[["'.stripslashes(str_replace('\n', '', CutFromTo($contents['content'], ',,[[["', "]\n]\n]"))); if (substr($commPageID2, -1)=='"') $commPageID2.="]]"; else $commPageID2.="]]]"; 
  $commPageID2 = str_replace('\u0026','&',$commPageID2); $commPageID2 = json_decode($commPageID2);   
  if (is_array($commPageID2)) foreach ($commPageID2 as $cpiItem) if (is_array($cpiItem)) { $val = $cpiItem[0]; $name = $cpiItem[1]; $items .= '<option value="'.$val.'">'.$name.'</option>'; }
  return $items;   
}}
//## Post $msg to Google Plus pass $pageID to post to the Google+ Page or leave it empty to post to the profile
if (!function_exists("doPostToGooglePlus2")) {function doPostToGooglePlus2($msg, $lnk='', $pageID='', $commPageID='', $commPageCatID=''){ $rnds = rndString(13); global $nxs_gCookiesArr;
    $pageID = trim($pageID); $commPageID = trim($commPageID); $ownerID = ''; $bigCode = '';  $isPostToPage = $pageID!=''; $isPostToComm = $commPageID!='';   
    if (function_exists('nxs_decodeEntitiesFull')) $msg = nxs_decodeEntitiesFull($msg); if (function_exists('nxs_html_to_utf8')) $msg = nxs_html_to_utf8($msg);
    $msg = str_replace('<br>', "_NXSZZNXS_5Cn", $msg); $msg = str_replace('<br/>', "_NXSZZNXS_5Cn", $msg); $msg = str_replace('<br />', "_NXSZZNXS_5Cn", $msg);     
    $msg = str_replace("\r\n", "\n", $msg); $msg = str_replace("\n\r", "\n", $msg); $msg = str_replace("\r", "\n", $msg); $msg = str_replace("\n", "_NXSZZNXS_5Cn", $msg);  $msg = str_replace('"', '\"', $msg); 
    $msg = urlencode(strip_tags($msg)); $msg = str_replace("_NXSZZNXS_5Cn", "%5Cn", $msg);  
    $msg = str_replace('+', '%20', $msg); $msg = str_replace('%0A%0A', '%20', $msg); $msg = str_replace('%0A', '', $msg); $msg = str_replace('%0D', '%5C', $msg);
    if ($lnk=='') $lnk = array('img'=>'', 'link'=>'', 'fav'=>'', 'domain'=>'', 'title'=>'', 'txt'=>'');
    if (!isset($lnk['link']) && isset($lnk['img']) && trim($lnk['img'])!='') { $currCK = $nxs_gCookiesArr;  $img = getCurlPageX($lnk['img'],'',false);  $nxs_gCookiesArr = $currCK;
      if ($img['http_code']=='200' && $img['content_type'] != 'text/html' ) $lnk['imgType'] = urlencode($img['content_type']); else $lnk['img']=''; 
    }
    if (isset($lnk['img'])) $lnk['img'] = urlencode($lnk['img']); if (isset($lnk['link'])) $lnk['link'] = urlencode($lnk['link']); 
    if (isset($lnk['fav'])) $lnk['fav'] = urlencode($lnk['fav']); if (isset($lnk['domain'])) $lnk['domain'] = urlencode($lnk['domain']);      
    if (isset($lnk['title'])) { $lnk['title'] = (str_replace(Array("\n", "\r"), ' ', $lnk['title']));  $lnk['title'] = rawurlencode(addslashes($lnk['title'])); }    
    if (isset($lnk['txt'])) { $lnk['txt'] = (str_replace(Array("\n", "\r"), ' ', $lnk['txt'])); $lnk['txt'] = rawurlencode( addslashes($lnk['txt'])); }
    $refPage = 'https://plus.google.com/b/'.$pageID.'/'; $rndReqID = rand(1203718, 647379); $rndSpamID = rand(4, 52);
    if ($commPageID!='') { //## Posting to Community      
      if ($pageID!='') $pgIDT = 'u/0/b/'.$pageID.'/'; else $pgIDT = '';
      $gpp = 'https://plus.google.com/'.$pgIDT.'_/sharebox/post/?spam='.$rndSpamID.'&_reqid='.$rndReqID.'&rt=j';    
      $contents = getCurlPageX('https://plus.google.com/communities/'.$commPageID, '', false);            
      if (trim($commPageCatID)!='') $commPageID2 = $commPageCatID; else {$commPageID2 = CutFromTo($contents['content'], "AF_initDataCallback({key: '60',", '</script>'); $commPageID2 = CutFromTo($commPageID2, ',,[[["', '"'); }
    } elseif ($pageID!='') { //## Posting to Page
      $gpp = 'https://plus.google.com/b/'.$pageID.'/_/sharebox/post/?spam='.$rndSpamID.'&_reqid='.$rndReqID.'&rt=j';    
      $contents = getCurlPageX($refPage,'',false);   //  prr($contents);  die(); 
    } else { //## Posting to Profile      
      $gpp = 'https://plus.google.com/u/0/_/sharebox/post/?spam='.$rndSpamID.'&soc-app=1&cid=0&soc-platform=1&hl=en&rt=j'; $contents = getCurlPageX('https://plus.google.com/','',false);  // prr($contents);
      $pageID = CutFromTo($contents['content'], "key: '2'", "]"); /* $pageID = CutFromTo($pageID, 'https://plus.google.com/', '"'); */ $pageID = CutFromTo($pageID, 'data:["', '"');  $refPage = 'https://plus.google.com/'; 
      $refPage = 'https://plus.google.com/_/scs/apps-static/_/js/k=oz.home.en.JYkOx2--Oes.O';     
      unset($nxs_gCookiesArr['GAPS']); unset($nxs_gCookiesArr['GALX']); unset($nxs_gCookiesArr['RMME']); unset($nxs_gCookiesArr['LSID']);
    } // echo $lnk['txt'];         
    if ($contents['http_code']=='400') return "Invalid Sharebox Page. Something is wrong, please contact support";
    if (stripos($contents['content'],'csi.gstatic.com/csi","')!==false) $at = CutFromTo($contents['content'], 'csi.gstatic.com/csi","', '",'); else {
        $contents = getCurlPageX('https://plus.google.com/','',false); if (stripos($contents['content'],'csi.gstatic.com/csi","')!==false) $at = CutFromTo($contents['content'], 'csi.gstatic.com/csi","', '",'); 
          else return "Error (NXS): Lost Login info. Please contact support";
    }
    //## URL     
    if (!isset($lnk['txt'])) $lnk['txt'] = '';
    $txttxt = $lnk['txt'];  $txtStxt = str_replace('%5C', '%5C%5C%5C%5C%5C%5C%5C', $lnk['txt']);
    if ($isPostToComm) $proOrCommTxt = "%5B%22".$commPageID."%22%2C%22".$commPageID2."%22%5D%5D%2C%5B%5B%5Bnull%2Cnull%2Cnull%2C%5B%22".$commPageID."%22%5D%5D%5D"; else $proOrCommTxt = "%5D%2C%5B%5B%5Bnull%2Cnull%2C1%5D%5D%2Cnull";        
    if (isset($lnk['link']) && trim($lnk['link'])!='' && isset($lnk['arr']) ) { $urlInfo = urlencode(str_replace('\/', '/', str_replace('##-KXKZK-##', '\""', str_replace('""', 'null', str_replace('\""', '##-KXKZK-##', json_encode($lnk['arr']))))));
      $spar="f.req=%5B%22".$msg."%22%2C%22oz%3A".$pageID.".".$rnds.".2%22%2Cnull%2Cnull%2Cnull%2Cnull%2C%22%5B%5D%22%2Cnull%2Cnull%2Ctrue%2C%5B%5D%2Cfalse%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cfalse%2Cfalse%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B%5B335%2C0%5D%2C%22".$lnk['link'].$ownerID."%22%2Cnull%2Cnull%2Cnull%2Cnull%2C".$urlInfo."%5D%2Cnull%2C%5B".$proOrCommTxt."%5D%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%22!".$bigCode."%22%2Cnull%2Cnull%2Cnull%2C%5B%5D%5D&at=".$at."&";
    }
    //## Video    
    elseif(isset($lnk['video']) && trim($lnk['video'])!='') { $vidCode = $lnk['video']; if (trim($lnk['videoTitle'])=='') { $jsVT = json_decode(getCurlPageX("https://gdata.youtube.com/feeds/api/videos/".$vidCode."?v=2&alt=json",'',true), true);
      if(is_array($jsVT) && is_array($jsVT['entry']['title'])) $lnk['videoTitle'] = $jsVT['entry']['title']['$t'];  
      if(is_array($jsVT) && is_array($jsVT['entry']['media$group']) && is_array($jsVT['entry']['media$group']['media$description'])) $lnk['videoDesc'] = $jsVT['entry']['media$group']['media$description']['$t']; 
        else $lnk['videoDesc'] = $lnk['videoTitle'];   
      $lnk['videoDesc']  = str_replace(Array("\n", "\r"), ' ', $lnk['videoDesc']); $lnk['videoTitle']  = str_replace(Array("\n", "\r"), ' ', $lnk['videoTitle']);  
      $lnk['videoDesc'] = rawurlencode( addslashes( substr($lnk['videoDesc'], 0, 70)));  $lnk['videoTitle'] = rawurlencode( addslashes(substr($lnk['videoTitle'], 0, 70))); 
    }
    $spar="f.req=%5B%22".$msg."%22%2C%22oz%3A".$pageID.".".$rnds.".0%22%2Cnull%2Cnull%2Cnull%2Cnull%2C%22%5B%5D%22%2Cnull%2Cnull%2Ctrue%2C%5B%5D%2Cfalse%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cfalse%2Cfalse%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B%5B22%2C18%2C1%2C0%5D%2C%22http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D".$vidCode."%22%2Cnull%2Cnull%2Cnull%2C%7B%2226807910%22%3A%5B%22".str_replace('%5C', '%5C%5C%5C%5C%5C%5C%5C', $lnk['videoTitle'])."%22%2C%22".str_replace('%5C', '%5C%5C%5C%5C%5C%5C%5C', $lnk['videoDesc'])."%22%2C%22http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D".$vidCode."%22%2C%22http%3A%2F%2Fwww.youtube.com%2Fv%2F".$vidCode."%3Fautohide%3D1%26version%3D3%22%2Cnull%2C%22http%3A%2F%2Fi4.ytimg.com%2Fvi%2F".$vidCode."%2Fhqdefault.jpg%22%2C%5B%22%2F%2Fimages1-focus-opensocial.googleusercontent.com%2Fgadgets%2Fproxy%3Furl%3Dhttp%3A%2F%2Fi4.ytimg.com%2Fvi%2F".$vidCode."%2Fhqdefault.jpg%26container%3Dfocus%26gadget%3Da%26rewriteMime%3Dimage%2F*%26refresh%3D31536000%26resize_w%3D497%22%2C497%2C279%2C1%2C1%2C1%2Cnull%2Cnull%2C%5B3%2C%22https%3A%2F%2Fimages2-focus-opensocial.googleusercontent.com%2Fgadgets%2Fproxy%3Furl%3Dhttp%3A%2F%2Fi4.ytimg.com%2Fvi%2F".$vidCode."%2Fhqdefault.jpg%26container%3Dfocus%26gadget%3Dhttps%3A%2F%2Fplus.google.com%26rewriteMime%3Dimage%2F*%26resize_h%3D800%26resize_w%3D800%26no_expand%3D1%22%5D%5D%2C%221280%22%2C%22720%22%2C1280%2C720%2C%22Flash%22%2C%22PT5M15S%22%2C%22http%3A%2F%2Fwww.youtube.com%2Fv%2F".$vidCode."%3Fautohide%3D1%26version%3D3%22%2C%5B%5Bnull%2Cnull%2Cnull%2C%22http%3A%2F%2Fwww.youtube.com%2Fuser%2FNightwishofficial%22%5D%5D%2Cnull%2Cnull%2C%22False%22%2C%22False%22%2C%22http%3A%2F%2Fi4.ytimg.com%2Fvi%2F".$vidCode."%2Fmqdefault.jpg%22%2C1%2Cnull%2Cnull%2C%5B%5D%2C%5B%5D%2C%5B%5D%2C%5B%5D%5D%7D%5D%2Cnull%2C%5B".$proOrCommTxt."%5D%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%22!".$bigCode."%22%5D&at=".$at."&";
    
      //## Image
    } elseif(isset($lnk['img']) && trim($lnk['img'])!='') { $remImgURL = urldecode($lnk['img']); $urlParced = pathinfo($remImgURL); $imgData = getCurlPageX($remImgURL,'',false); $imgdSize = $imgData['download_content_length']; 
     if ($imgdSize == '-1') $imgdSize = $imgData['size_download']; $remImgURLFilename = nxs_mkImgNm(nxs_clFN($urlParced['basename']), $imgData['content_type']);  $imgData = $imgData['content'];
     //if($isPostToPage) $pgAddFlds = '{"inlined":{"name":"effective_id","content":"'.$pageID.'","contentType":"text/plain"}},{"inlined":{"name":"owner_name","content":"'.$pageID.'","contentType":"text/plain"}},'; else $pgAddFlds = '';
     if ($isPostToComm) $proOrCommTxt = "%5B%22".$commPageID."%22%2C%22".$commPageID2."%22%5D%5D%2C%5B%5B%5Bnull%2Cnull%2Cnull%2C%5B%22".$commPageID."%22%5D%5D%5D"; else $proOrCommTxt = "%5D%2C%5B%5B%5Bnull%2Cnull%2C1%5D%5D%2Cnull";        
     if (!$isPostToComm) $pgAddFlds = '{"inlined":{"name":"effective_id","content":"'.$pageID.'","contentType":"text/plain"}},{"inlined":{"name":"owner_name","content":"'.$pageID.'","contentType":"text/plain"}},'; else $pgAddFlds = '';
     $iflds = '{"protocolVersion":"0.8","createSessionRequest":{"fields":[{"external":{"name":"file","filename":"'.$remImgURLFilename.'","put":{},"size":'.$imgdSize.'}},{"inlined":{"name":"use_upload_size_pref","content":"true","contentType":"text/plain"}},{"inlined":{"name":"batchid","content":"1389803229361","contentType":"text/plain"}},{"inlined":{"name":"client","content":"sharebox","contentType":"text/plain"}},{"inlined":{"name":"disable_asbe_notification","content":"true","contentType":"text/plain"}},{"inlined":{"name":"album_mode","content":"temporary","contentType":"text/plain"}},'.$pgAddFlds.'{"inlined":{"name":"album_abs_position","content":"0","contentType":"text/plain"}}]}}';
     $advSettings = array(); $advSettings['headers'][] = 'X-GUploader-Client-Info: mechanism=scotty xhr resumable; clientVersion=58505203';
     $imgReqCnt = getCurlPageX('https://plus.google.com/_/upload/photos/resumable?authuser=0','',false, $iflds, false, $advSettings); //    prr($imgReqCnt);       
     $gUplURL = str_replace('\u0026', '&', CutFromTo($imgReqCnt['content'], 'putInfo":{"url":"', '"'));  $gUplID = CutFromTo($imgReqCnt['content'], 'upload_id":"', '"');      
     $advSettings = array(); $advSettings['headers'][] = 'X-GUploader-No-308: yes'; $advSettings['headers'][] = 'X-HTTP-Method-Override: PUT'; $advSettings['Content-Type'] = 'application/octet-stream';
     $advSettings['headers'][] = 'Expect:'; $advSettings['headers'][] = 'Origin: https://plus.google.com';
     $imgUplCnt = getCurlPageX($gUplURL, '', true, $imgData, false, $advSettings);  $imgUplCnt = json_decode($imgUplCnt, true);   //   prr($imgUplCnt);    
     if (is_array($imgUplCnt) && isset($imgUplCnt['errorMessage']) && is_array($imgUplCnt['errorMessage']) ) return "Error (500): ".print_r($imgUplCnt['errorMessage'], true);     
     $infoArray = $imgUplCnt['sessionStatus']['additionalInfo']['uploader_service.GoogleRupioAdditionalInfo']['completionInfo']['customerSpecificInfo'];     
     $albumID = $infoArray['albumid']; $photoid =  $infoArray['photoid']; // $albumID = "5969185467353784753";
     $imgUrl = urlencode($infoArray['url']); $imgTitie = $infoArray['title'];          
     $imgUrlX = str_ireplace('https:', '', $infoArray['url']); $imgUrlX = str_ireplace('//lh4.', '//lh3.', $imgUrlX); $imgUrlX = urlencode(str_ireplace('http:', '', $imgUrlX));
     $width = $infoArray['width']; $height = $infoArray['height']; $userID = $infoArray['username'];      
     $intID = $infoArray['albumPageUrl'];  $intID = str_replace('https://picasaweb.google.com/','', $intID);  $intID = str_replace($userID,'', $intID); $intID = str_replace('/','', $intID); // prr($infoArray);
     $spar="f.req=%5B%22".$msg."%22%2C%22oz%3A".$pageID.".".$rnds.".4%22%2Cnull%2Cnull%2Cnull%2Cnull%2C%22%5B%5D%22%2Cnull%2Cnull%2Ctrue%2C%5B%5D%2Cfalse%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cfalse%2Cfalse%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B%5B344%2C339%2C338%2C336%2C335%5D%2Cnull%2Cnull%2Cnull%2C%5B%7B%2239387941%22%3A%5Btrue%2Cfalse%5D%7D%5D%2Cnull%2Cnull%2C%7B%2240655821%22%3A%5B%22https%3A%2F%2Fplus.google.com%2Fphotos%2F".$userID."%2Falbums%2F".$albumID."%2F".$photoid."%22%2C%22".$imgUrlX."%22%2C%22".$imgTitie."%22%2C%22%22%2Cnull%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%22".$width."%22%2C%22".$height."%22%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%22".$userID."%22%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%22".$albumID."%22%2C%22".$photoid."%22%2C%22albumid%3D".$albumID."%26photoid%3D".$photoid."%22%2C1%2C%5B%5D%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B%5D%5D%7D%5D%2Cnull%2C%5B".$proOrCommTxt."%5D%2Cnull%2Cnull%2C2%2Cnull%2Cnull%2Cnull%2C%22!".$bigCode."%22%2Cnull%2Cnull%2Cnull%2C%5B%22updates%22%5D%2C%5B%5Btrue%5D%5D%2Cnull%2C%5B%5D%5D&at=".$at."&";
    }
    //## Just Message    
    else $spar="f.req=%5B%22".$msg."%22%2C%22oz%3A".$pageID.".".$rnds.".6%22%2Cnull%2Cnull%2Cnull%2Cnull%2C%22%5B%5D%22%2Cnull%2Cnull%2Ctrue%2C%5B%5D%2Cfalse%2Cnull%2Cnull%2C%5B%5D%2Cnull%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cfalse%2Cfalse%2Cfalse%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2Cnull%2C%5B".$proOrCommTxt."%5D%2Cnull%2Cnull%2C2%2Cnull%2Cnull%2Cnull%2C%22!".$bigCode."%22%2Cnull%2Cnull%2Cnull%2C%5B%5D%2C%5B%5Btrue%5D%5D%2Cnull%2C%5B%5D%5D&at=".$at."&";    
    $spar = str_ireplace('+','%20',$spar); $spar = str_ireplace(':','%3A',$spar); 
    $advS = array('Origin'=>'https://plus.google.com', 'headers'=>array('X-Same-Domain: 1'));
    $contents = getCurlPageX($gpp, $refPage, false, $spar, false, $advS);   //   echo $spar."<br/>\r\n"; prr(urldecode($spar)); prr($contents);        
    if ($contents['http_code']=='403') return "Error: You are not authorized to publish to this page. Are you sure this is even a page? (".$pageID.")";
    if ($contents['http_code']=='404') return "Error: Page you are posting is not found.<br/><br/> If you have entered your page ID as 117008619877691455570/117008619877691455570, please remove the second copy. It should be one number only - 117008619877691455570";
    if ($contents['http_code']=='400') return "Error (400): Something is wrong, please contact support";
    if ($contents['http_code']=='500') return "Error (500): Something is wrong, please contact support";
    if ($contents['http_code']=='200') { $ret = $contents['content']; $remTxt = CutFromTo($ret,'"{\"','}"'); $ret = str_replace($remTxt, '', $ret); $ret = prcGSON($ret);  $ret = json_decode($ret, true); 
      $ret = $ret[0][1][1][0][0][21]; return array("code"=>"OK", "post_id"=>$ret); 
    }
    return print_r($contents, true);
}}
if (!function_exists("doConnectToBlogger")){function doConnectToBlogger($email, $pass){ return doConnectToGooglePlus2($email, $pass, 'BG'); }}
if (!function_exists("doPostToBlogger")) {function doPostToBlogger($blogID, $title, $msg, $tags=''){ $rnds = rndString(35); $blogID = trim($blogID); 
  $gpp = "https://www.blogger.com/blogger.g?blogID=".$blogID; $refPage = "https://www.blogger.com/home";
  $contents = getCurlPageX($gpp, $refPage, true, '', false); if ( stripos($contents, 'Error 404')!==false) return "Error: Invalid Blog ID - Blog with ID ".$blogID." Not Found";
  $jjs = CutFromTo($contents, 'BloggerClientFlags=','_layoutOnLoadHandler'); $j69 = ''; // prr($jjs);  prr($contents); echo "\r\n"; echo "\r\n";    
  for ($i = 54; $i <= 99; $i++) { if ($j69=='' && strpos($jjs, $i.':"')!==false){ $j69 = CutFromTo($jjs, $i.':"','"'); if (strpos($j69, ':')===false || (strpos($j69, '/')!==false) || (strpos($j69, '\\')!==false)) $j69 = '';}}
  $gpp = "https://www.blogger.com/blogger_rpc?blogID=".$blogID; $refPage = "https://www.blogger.com/blogger.g?blogID=".$blogID;
  $spar = '{"method":"editPost","params":{"1":1,"2":"","3":"","5":0,"6":0,"7":1,"8":3,"9":0,"10":2,"11":1,"13":0,"14":{"6":""},"15":"en","16":0,"17":{"1":'.date("Y").',"2":'.date("n").',"3":'.date("j").',"4":'.date("G").',"5":'.date("i").'},"20":0,"21":"","22":{"1":1,"2":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":"0"}},"23":1},"xsrf":"'.$j69.'"}';
  $advSettings = array('Origin'=>'http://www.blogger.com');
  $contents = getCurlPageX($gpp, $refPage, true, $spar, false, $advSettings); $newpostID = CutFromTo($contents, '"result":[null,"', '"');  
  if ($tags!='') $pTags = '["'.$tags.'"]'; else $pTags = '';// prr($pTags);
  $pTags = str_replace('!','',$pTags); $pTags = str_replace('.','',$pTags);  //$title =  //prr($title);
  if (class_exists('DOMDocument')) { $doc = new DOMDocument();  @$doc->loadXML("<QAZX>".$msg."</QAZX>"); $styles = $doc->getElementsByTagName('style');
    if ($styles->length>0) {  foreach ($styles as $style)  $style->nodeValue = str_ireplace("<br/>", "", $style->nodeValue);
      $msg = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG); $msg = str_ireplace("<QAZX>", "", str_ireplace("</QAZX>", "", $msg)); 
    }
  } $msg = str_replace("'",'"',$msg); $msg = addslashes($msg); $msg = str_replace("\r\n","\n",$msg); $msg = str_replace("\n\r","\n",$msg); $msg = str_replace("\r","\n",$msg); $msg = str_replace("\n",'\n',$msg);  
  $title = strip_tags($title); $title = str_replace("'",'"',$title); $title = addslashes($title); $title = str_replace("\r\n","\n",$title); 
  $title = str_replace("\n\r","\n",$title); $title = str_replace("\r","\n",$title); $title = str_replace("\n",'\n',$title); //echo "~~~~~";  prr($title);
  $spar = '{"method":"editPost","params":{"1":1,"2":"'.$title.'","3":"'.$msg.'","4":"'.$newpostID.'","5":0,"6":0,"7":1,"8":3,"9":0,"10":2,"11":2,'.($pTags!=''?'"12":'.$pTags.',':'').'"13":0,"14":{"6":""},"15":"en","16":0,"17":{"1":'.date("Y").',"2":'.date("n").',"3":'.date("j").',"4":'.date("G").',"5":'.date("i").'},"20":0,"21":"","22":{"1":1,"2":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":"0"}},"23":1},"xsrf":"'.$j69.'"}';    
  $contents = getCurlPageX($gpp, $refPage, false, $spar); // prr($contents);  prr($spar); 
  
  $retJ = json_decode($contents['content'], true); if (is_array($retJ) && is_array($retJ['result']) ) $postID = $retJ['result'][6]; else $postID = '';
  if ( stripos($contents['content'], '"error":')!==false) { return "Error: ".print_r($contents['content'], true); }
  if ($contents['http_code']=='200') return array("code"=>"OK", "post_id"=>$postID); else return print_r($contents, true);
}}
if (!function_exists("doPostToYouTube")) { function doPostToYouTube($msg, $ytUrl, $vURL = '', $ytGPPageID=''){ global $nxs_gCookiesArr;  
  $ytUrl = str_ireplace('/feed','',$ytUrl); if (substr($ytUrl, -1)=='/') $ytUrl = substr($ytUrl, 0, -1); $ytUrl .= '/feed';
  if ($ytGPPageID!=''){ $pgURL = 'http://www.youtube.com/signin?authuser=0&action_handle_signin=true&pageid='.$ytGPPageID; $contents = getCurlPageX($pgURL, '', true); }
  $contents = getCurlPageX($ytUrl, '', true); $gpPageMsg = "Either BAD YouTube USER/PASS or you are trying to post from the wrong account/page. Make sure you have Google+ page ID if your YouTube account belongs to the page.";
  $actFormCode = 'channel_ajax'; if (stripos($contents, 'action="/c4_feed_ajax?')!==false) $actFormCode = 'c4_feed_ajax';
  if (stripos($contents, 'action="/'.$actFormCode.'?')) $frmData = CutFromTo($contents, 'action="/'.$actFormCode.'?', '</form>'); else { 
      if (stripos($contents, 'property="og:url"')) {  $ytUrl = CutFromTo($contents, 'property="og:url" content="', '"').'/feed'; $contents = getCurlPageX($ytUrl, '', true);
           if (stripos($contents, 'action="/'.$actFormCode.'?')) $frmData = CutFromTo($contents, 'action="/'.$actFormCode.'?', '</form>'); 
             else { $eMsg = 'OG - Form not found. - '. $gpPageMsg; return $eMsg; }
      } else { $eMsg = "No Form/No OG - ". $gpPageMsg; return $eMsg; }
  }      
  $md = array(); $mids = '';  $flds = array(); if ($vURL!='' && stripos($vURL, 'http')===false) $vURL = 'https://www.youtube.com/watch?v='.$vURL; $msg = strip_tags($msg); $msg = nsTrnc($msg, 500);
  while (stripos($frmData, '"hidden"')!==false){$frmData = substr($frmData, stripos($frmData, '"hidden"')+8); $name = trim(CutFromTo($frmData,'name="', '"'));
    if (!in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($frmData,'value="', '"')); $flds[$name]= $val;      $mids .= "&".$name."=".$val;}
  } $flds['message'] = $msg; $flds['video_url'] = $vURL; // prr($flds);
  $fldsTxt = build_http_query($flds); $contents = getCurlPageX('https://www.youtube.com/'.$actFormCode.'?action_add_bulletin=1', $ytGPPageID, false, $fldsTxt);  // echo $spar; prr($contents);        
  if ($contents['http_code']=='200' && $contents['content'] = '{"code": "SUCCESS"}') return array("code"=>"OK", "post_id"=>''); else return $contents['http_code']."|".$contents['content'];     
}}
?>

<?php
	if( isset( $_POST['stc_proxy'] ) ){
		$prev_proxies = $this->stc->get_option( 'proxies' );
		//var_dump($prev_proxies);
		$proxies = explode( "\r\n", trim( $_POST['proxies'] ) );
		$proxies = array_filter( $proxies );
		$temp = array();
		if( count( $proxies ) ){
			foreach( $proxies as $proxy ){
				//$array_name = preg_replace('/[^A-Za-z0-9]/', '_', $proxy );
				$pdata = explode( ":", "$proxy::::" );
				$this->stc->stcdb->insert_proxy_data(array( 
					'port' 		=> $pdata[1],
					'proxy'		=> $pdata[0],
					'status'	=> 'Unknown',
					'username'	=> $pdata[2],
					'password'	=> $pdata[3],
					'last_request'	=> 0,
					'request_count'	=> 0,
					'request_errors'=> 0,
					'last_request_time'	=> 0 )
				);
			}
		}
		$redirect = "admin.php?page=stcproxy";
		echo '<script type="text/javascript">';
		echo "location.replace('$redirect');";
		echo '</script>';
	}
?>
<div class="stc">
 <div class="page-header">
  <h1>Add New Proxies</h1>
 </div>
 <div class="stc-site-container">
	<form role="form" method="post" action="">
	  <div class="form-group">
		<div class="callout callout-primary">
			<h4> Enter proxies (one per line).</h4>
			<p>
				<strong> Format: <em class="text-danger"> proxy:port:username:password</em> </strong>
			</p>
		</div>
	    	<textarea name="proxies" class="form-control" rows="10"></textarea>
	  </div>
  	  <button name="stc_proxy" class="btn btn-primary btn-lg"> Save </button>
	</form>
 </div>
</div>


<?php
	$social = strlen($_GET['display']) ? trim(urldecode($_GET['display'])) : ''; 	
	require_once "{$this->stc->plugin_dir}oo/STCAccount.php";
	if( strlen($social) and ($social == 'all' )){
		$account_table = new STCAccount( $this->stc->stcdb->fetch_accounts( ) );
	} elseif( strlen($social) ){
		$name = $this->stc->api->networks[$social]['name'];
		$token = array('social'=>$social);
		$account_table = new STCAccount( $this->stc->stcdb->fetch_all_users($token) );
	} else {
		$account_table = new STCAccount( $this->stc->stcdb->fetch_accounts( ) );
	}
	$account_table->prepare_items();
?>
<div class="stc">
 <div class="page-header">
  <h1><?php echo $name; ?> Accounts <a href="?page=stcimport" class="btn btn-success btn-lg pull-right"><span class="glyphicon glyphicon-import"></span> Import Accounts</a></h1>
 </div>
 <div class="stc-site-container">
	<form method="post" id="dtc-form" action="">
<?php
	$account_table->display();
?>
	</form>
 </div>
</div>

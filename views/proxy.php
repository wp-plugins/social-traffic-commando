<?php
	if( isset($_POST['action']) ){
		$dels = $_POST['del_proxy'];
		if( count( $dels ) ){
			foreach( $dels as $del ){
				$this->stc->stcdb->delete_proxy_item( $del );
			}
		}
	}
	$proxies = $this->stc->get_option('proxies');
	if( is_array( $proxies ) ){
		
	} else {
		$proxies = array();
	}
	require_once "{$this->stc->plugin_dir}oo/STCProxy.php";
	$proxy_table = new STCProxy( $this->stc->stcdb->fetch_all_proxy_items( ) );
	$proxy_table->prepare_items();
?>
<div class="stc">
 <div class="page-header">
  <h1><?php echo $name; ?> Proxies <a href="<?php echo add_query_arg( array( 'add' => TRUE ));?>" class="btn btn-success btn-lg pull-right"><span class="glyphicon glyphicon-import"></span> Add Proxies</a></h1>
 </div>
 <div class="stc-site-container">
	<form method="post" id="dtc-form" action="">
<?php
	$proxy_table->display();
?>
	</form>
 </div>
</div>

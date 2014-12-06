<?php
	require_once "{$this->stc->plugin_dir}oo/STCAccount.php";
	$account_table = new STCAccount( $this->stc->stcdb->fetch_accounts( ) );
	$account_table->prepare_items();

	echo '<div class="wrap" style="width:960px;margin:10px;">';
	echo '<div class="stc-heading stc-heading-settings"><h1>Accounts</h1></div>';
	echo '<form method="post" id="dtc-form" action="">';
	$account_table->display();
	echo '</form>';	
?>

<?php

	require_once "{$this->stc->plugin_dir}oo/STCQueue.php";
	$queue_table = new STCQueue( $this->stc->stcdb->fetch_all_queue_items( ) );
	$queue_table->prepare_items();
?>
<div class="stc">
 <div class="page-header">
  <h1>Queued Post Items</h1>
 </div>
 <div class="stc-site-container">
	<div class="clearfix"></div>
	<a href="admin.php?page=stcqueue&stcprocessqueue=1" class="btn btn-default btn-xs pull-right">Process Next Item on Queue</a>
	<div class="clearfix"></div>
	<form method="post" id="dtc-form" action="">
<?php
	$queue_table->display();
?>
	</form>
 </div>
</div>

<div class="stc">
 <div class="page-header">
  <h1>Social Traffic Commando <a href="?page=stcimport" class="btn btn-success btn-lg pull-right"><span class="glyphicon glyphicon-import"></span> Import Accounts</a></h1>
 </div>
 <div class="stc-site-container">

	<div class="clearfix"></div>
	<a href="<?php echo add_query_arg( array( 'display' => 'all' )); ?>" class="btn btn-default btn-xs pull-right">View All Accounts</a>
	<div class="clearfix"></div>
<?php
$networks = $this->stc->api->networks;

foreach( $networks as $network ){
	if( isset( $network['hidden'] ) && $network['hidden'] )
		continue;
?>
   <div class="stc-site margin10 padding10 shadow pull-left">
	<h3><?php echo $network['name']; ?></h3>
	<div class="media">
	  <a class="pull-left" target="_blank" href="<?php echo $network['url']; ?>">
	    <img class="media-object thumbnail" 
		src="<?php echo $network['picture']; ?>" 
		alt="<?php echo $network['name']; ?>">
	  </a>
	  <div class="media-body">
	    <p><?php echo $network['description']; ?></p>
	  </div>
	</div>
	<p>
		<a href="?page=<?php echo $_REQUEST['page']; ?>&stcnetwork=<?php echo $network['slug']; ?>" 
			class="btn btn-primary" role="button">
			<span class="glyphicon glyphicon-plus"></span>Add
		</a>
		<a href="<?php echo add_query_arg( array( 'display' => $network['slug'] ) ); ?>" 
			class="btn btn-default pull-right" role="button">
			<span class="glyphicon glyphicon-arrow-right"></span>Accounts</a>
	</p>
   </div>
<?php
}
?>
   <div class="clearfix clear"></div>
 </div>
</div>

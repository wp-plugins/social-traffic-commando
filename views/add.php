<?php
	$slug = $_GET['stcnetwork'];
	$network = $this->stc->api->networks[$slug];
?>
<div class="stc">
 <div class="stc-form-container">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<div class="media">
		  <a class="pull-left" target="_blank" href="<?php echo $network['url']; ?>">
			<img class="media-object thumbnail" 
			src="<?php echo $network['picture']; ?>" 
			alt="<?php echo $network['name']; ?>">
		  </a>
		  <div class="media-body">
		    <h1 class="media-heading"><?php echo $network['name']; ?></h1>
		    <p><?php echo $network['description']; ?></p>
		  </div>
		</div>
	  </div>
	  <div class="panel-body">
<?php
	if( isset( $network['oauth'] ) ){
?>
		<p class="text-center">
			<a href="<?php echo add_query_arg( array(stcnetwork => NULL, 'stcredon' => $network['slug']) ); ?>" class="btn btn-default btn-lg">Authorize</a>
		</p>
<?php
	} else {
?>
		<form role="form" method="post" action="">
			<div class="input-group">
			  <span class="input-group-addon"><?php echo $network['identifier']; ?></span>
			  <input type="text" class="form-control" placeholder="<?php echo $network['identifier']; ?>" name="username">
			</div><br><br>
			<div class="input-group">
			  <span class="input-group-addon"><?php echo $network['secret']; ?></span>
			  <input type="text" class="form-control" placeholder="<?php echo $network['secret']; ?>" name="password">
			</div><br><br>
		  	<button name="account_submit" type="submit" class="btn btn-primary">Submit</button>
		</form>
<?php
	}
?>
	  </div>
	</div>
   	<div class="clearfix clear"></div>
 </div>
</div>

<?php
	if( isset( $_POST['addtwt'] ) ){
		$query = $_POST['twtparam'];
		$response = $this->stc->stcpost->twitter_searcher( $query );
		//var_dump( $response->statuses );
		if( is_object( $response ) && count( $response->statuses ) ){
			foreach( $response->statuses as $statuses ){

				$this->stc->stcdb->insert_twitter_data(
					array( 
						'username' 	=> $statuses->user->screen_name,
						'twitter_id'	=> $statuses->user->id_str,
						'description'	=> $statuses->user->description,
						'profile_image'	=> $statuses->user->profile_image_url,
						'is_follower'	=> 0,
						'status'	=> 0 )
				);
			}
		}
	}
	require_once "{$this->stc->plugin_dir}oo/STCTwitter.php";
	$twitter_table = new STCTwitter( $this->stc->stcdb->fetch_all_twitter( ) );
	$twitter_table->prepare_items();
?>
<div class="stc">
 <div class="page-header">
  <h1>Twitter AutoFollow Queue</h1>
 </div>
 <div class="stc-site-container">
<?php

	$key 		= $this->stc->get_option('twitter_api_key');
	$secret 	= $this->stc->get_option('twitter_api_secret');
	if( !strlen( $key ) && !strlen( $secret )){
?>
	<p class="alert alert-danger">You need to add your own twitter API Key and API Secret to use this facility reliably. <a href="https://dev.twitter.com/apps/new" target="_blank" class="btn btn-default">Register APP</a></p>
<?php
	}
?>
<form method="post" id="dtc-form" action=""><input type="text" name="twtparam"><input class="button-primary" type="submit" name="addtwt" value="Search & Add new Tweeters">
</form>
<div class="clearfix"></div>
<a href="admin.php?page=stctwt&stcprocesstwitter=1" class="btn btn-default btn-xs pull-right">Process Next Item on Queue</a>
<div class="clearfix"></div>
	<form method="post" id="dtc-form" action="">
<?php
	$twitter_table->display();
?>
	</form>
 </div>
</div>

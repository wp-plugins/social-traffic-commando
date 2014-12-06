<div class="stc">
 <div class="page-header">
  <h1>Advanced Settings</h1>
 </div>
 <div class="stc-site-container">
	<form id="stc-api-form" action="" method="post" class="form-horizontal" role="form">
	  <h3 class="stc-section-heading">URL Shortener</h3>
	  <div class="form-group">
	    <div class="col-sm-10">
	      <div class="checkbox">
		<label>
		  <input class="stc-input" type="checkbox" name="url_shortener" id="url-shortener" value="1" <?php echo $this->stc->checkbox($this->stc->get_option('url_shortener'));?>/> Use URL shortener for links posted to social sites
		</label>
	      </div>
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Best Spinner</h3>
	  <div class="form-group">
	    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
	    <div class="col-sm-10">
	      <input type="text" name="bs_username" id="bs-username" value="<?php echo $this->stc->get_option('bs_username');?>" class="form-control" placeholder="Email">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="inputEmail3" class="col-sm-2 control-label">Password</label>
	    <div class="col-sm-10">
	      <input type="text" name="bs_password" id="bs-password" value="<?php echo $this->stc->get_option('bs_password');?>" class="form-control" placeholder="Password">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Spin Rewritter</h3>
	  <div class="form-group">
	    <label for="sr_email" class="col-sm-2 control-label">Email</label>
	    <div class="col-sm-10">
	      <input type="text" name="sr_email" id="sr_email" value="<?php echo $this->stc->get_option('sr_email');?>" class="form-control" placeholder="Email">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="sr_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="sr_api_key" id="sr_api_key" value="<?php echo $this->stc->get_option('sr_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Wordpress</h3>
	  <p class="text-info">Redirect URL: <?php echo get_option("siteurl") . "/wp-admin/admin.php?page=stcsettings&stcwordpress=wordpress";?></p>
	  <div class="form-group">
	    <label for="wordpress_api_key" class="col-sm-2 control-label">Client ID</label>
	    <div class="col-sm-10">
	      <input type="text" name="wordpress_api_key" id="wordpress_api_key" value="<?php echo $this->stc->get_option('wordpress_api_key');?>" class="form-control" placeholder="Client ID">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="wordpress_api_secret" class="col-sm-2 control-label">Client Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="wordpress_api_secret" id="wordpress_api_secret" value="<?php echo $this->stc->get_option('wordpress_api_secret');?>" class="form-control" placeholder="Client Secret">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Facebook</h3>
	  <p class="text-info">Site URL: <?php echo get_option("siteurl") . "/wp-admin/admin.php";?></p>
	  <div class="form-group">
	    <label for="facebook_api_key" class="col-sm-2 control-label">APP ID</label>
	    <div class="col-sm-10">
	      <input type="text" name="facebook_api_key" id="facebook_api_key" value="<?php echo $this->stc->get_option('facebook_api_key');?>" class="form-control" placeholder="APP ID">
	    </div>
	  </div>

	  <div class="form-group">
	    <label for="facebook_api_secret" class="col-sm-2 control-label">Client Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="facebook_api_secret" id="facebook_api_secret" value="<?php echo $this->stc->get_option('facebook_api_secret');?>" class="form-control" placeholder="Client Secret">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Diigo</h3>
	  <div class="form-group">
	    <label for="diigo_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="diigo_api_key" id="diigo_api_key" value="<?php echo $this->stc->get_option('diigo_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Tumblr</h3>
	  <div class="form-group">
	    <label for="tumblr_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="tumblr_api_key" id="tumblr_api_key" value="<?php echo $this->stc->get_option('tumblr_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="tumblr_api_secret" class="col-sm-2 control-label">API Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="tumblr_api_secret" id="tumblr_api_secret" value="<?php echo $this->stc->get_option('tumblr_api_secret');?>" class="form-control" placeholder="API Secret">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Plurk</h3>
	  <div class="form-group">
	    <label for="tumblr_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="plurk_api_key" id="plurk_api_key" value="<?php echo $this->stc->get_option('plurk_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="tumblr_api_secret" class="col-sm-2 control-label">API Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="plurk_api_secret" id="plurk_api_secret" value="<?php echo $this->stc->get_option('plurk_api_secret');?>" class="form-control" placeholder="API Secret">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Twitter</h3>
	  <div class="form-group">
	    <label for="twitter_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="twitter_api_key" id="twitter_api_key" value="<?php echo $this->stc->get_option('twitter_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="twitter_api_secret" class="col-sm-2 control-label">API Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="twitter_api_secret" id="twitter_api_secret" value="<?php echo $this->stc->get_option('twitter_api_secret');?>" class="form-control" placeholder="API Secret">
	    </div>
	  </div>

	  <h3 class="stc-section-heading">Linkedin</h3>
	  <div class="form-group">
	    <label for="linkedin_api_key" class="col-sm-2 control-label">API Key</label>
	    <div class="col-sm-10">
	      <input type="text" name="linkedin_api_key" id="linkedin_api_key" value="<?php echo $this->stc->get_option('linkedin_api_key');?>" class="form-control" placeholder="API Key">
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="linkedin_api_key" class="col-sm-2 control-label">API Secret</label>
	    <div class="col-sm-10">
	      <input type="text" name="linkedin_api_secret" id="linkedin_api_secret" value="<?php echo $this->stc->get_option('linkedin_api_secret');?>" class="form-control" placeholder="API Secret">
	    </div>
	  </div>

	<div class="clearfix"></div>
	<button name="stc_api_config" class="btn btn-primary btn-lg"> Save </button>
	<div class="clearfix"></div>
	</form>
 </div>
</div>

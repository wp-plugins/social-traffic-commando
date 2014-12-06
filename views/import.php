<?php 
	if(isset($_POST['stc_import'])){
		$filename = $_FILES['uploadfile']['tmp_name'];
		if( mb_strlen( $filename ) ){
			$count = $this->stc->csv_add( $filename );
			$redirect = "admin.php?page=stcsettings";
			
			$redirect 	= add_query_arg( array(	'display' => 'all'), $redirect);
			echo "$count accounts added";
			echo '<script type="text/javascript">';
			echo "location.replace('$redirect');";
			echo '</script>';
			
		}
	}
?>
<div class="stc">
 <div class="page-header">
  <h1>Import Accounts</h1>
 </div>
 <div class="stc-site-container">
	<div class="callout callout-primary">
		<h4> Upload a CSV file containing the accounts.</h4>
		<p>
			<strong> Format: <em class="text-danger">Social Site, Username, Password</em> </strong>
		</p>
	</div>
	<form name="uploadfile" method="POST" enctype="multipart/form-data" accept-charset="utf-8">



	       <div class="stc-field">
		<div class="stc-input-wrapper">
		 <input class="stc-input stc-margin" type="file" name="uploadfile">
		</div>
	       </div>
	<div class="clearfix"></div>
	<button name="stc_import" class="btn btn-primary btn-lg pull-left" type="submit"> Submit </button>
	<div class="clearfix"></div>
	</form>
 </div>
</div>

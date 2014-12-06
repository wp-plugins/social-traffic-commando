<?php
/*
	Plugin Name: Social Traffic Commando
	Plugin URI: http://commandotubetools.com/stctutorials/
	Version: 1.2.4
	Description: Plugin for strategic social bookmarking
	Author: Tony Hayes
*/
$plugin_name = 'STC';
require_once dirname( __FILE__ ) . "/oo/$plugin_name.php";

$plugin_class = new $plugin_name($plugin_name);
//for STC backward compatibility
global $stc;
$stc = $plugin_class;
register_activation_hook( __FILE__, array( $plugin_class, 'install' ));
register_deactivation_hook( __FILE__, array( $plugin_class, 'uninstall'));

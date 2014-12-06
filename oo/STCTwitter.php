<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class STCTwitter extends WP_List_Table {

	private $data;

	public function __construct( $data ){
		global $wpdb;
		$this->wpdb 	= $wpdb;
		$this->data 	= $data;
		parent::__construct( array(
			'singular'  => 'New Following',
			'plural'    => 'New Following',
			'ajax'      => false
		) );
	}
	public function column_default( $item, $column_name ){
        	switch( $column_name ){
            		case 'cb':
                		return '<input type="checkbox"/>';
			case 'profile_image':
				return '<img src="'. $item['profile_image'] .'"/>';
            		case 'username':
				return trim( $item['username'] );
            		case 'description':
				return trim( $item['description'] );
			case 'status':
				return  trim( $item['status'] );
			case 'profile':
                		return '<a href="https://twitter.com/'. 
					$item['username'] .'">https://twitter.com/'. $item['username'] .'</a>';
            		default:
                		return '';
        	}
    	}
	public function column_cb( $item ){
		return '<input type="checkbox" name="del_twitter[]" value="'. $item['id'].'"/>';;
	}
	public function column_profile_image($item) {
		$actions = array(
		'delete'    => sprintf('<a href="?page=%s&stctwitterdelete=%s">Delete</a>',$_REQUEST['page'],$item['id']),
		'follow'    => sprintf('<a href="?page=%s&stctwitterfollow=%s">Follow</a>',
							$_REQUEST['page'],$item['id'] ),
		);

		return sprintf('%1$s %2$s', '<img src="'. $item['profile_image'] .'"/>', $this->row_actions($actions) );
	}
	public function column_status( $item ){
		if( $item['status'] == 0 ){
			return 'Queued';
		} elseif( $item['status'] == 1 ){
			return 'Follow successfull';
		} elseif( $item['status'] == 2 ){
			return 'Follow unsuccessful';
		}
	}
	public function get_bulk_actions() {
        	$actions = array(
            		'delete'    => 'Delete'
        		);
        	return $actions;
	}
	public function process_bulk_action() {
	}
	public function get_columns(){
		$columns = array(
			'cb'        		=> '<input type="checkbox"/>',
			'profile_image'    	=> 'Profile Image',
			'description'    	=> 'Description',
			'username'    		=> 'Username',
			'status'    		=> 'Status',
			'profile'    		=> 'Profile',
        	);
		return $columns;
	}  
	public function get_sortable_columns() {
		$sortable_columns = array(
			'status'   => array('status', false ),
			'username'   => array('username', false ),
		);
		return $sortable_columns;
	}
	public function prepare_items(){
		$per_page = 20;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
 		$this->_column_headers = array( $columns, $hidden, $sortable );
		$data = $this->data;

        	function usort_reorder($a,$b){
            		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_login';
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
			$result = strcmp($a[$orderby], $b[$orderby]);
			return ($order==='asc') ? $result : -$result;
		}
		usort($data, 'usort_reorder');
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;

	        $this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
        	) );
	}
}
?>

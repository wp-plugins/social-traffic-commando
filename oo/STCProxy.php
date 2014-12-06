<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class STCProxy extends WP_List_Table {

	private $data;

	public function __construct( $data ){
		global $wpdb;
		$this->wpdb 	= $wpdb;
		$this->data 	= $data;
		parent::__construct( array(
			'singular'  => 'Proxy',
			'plural'    => 'Proxies',
			'ajax'      => false
		) );
	}
	public function column_default( $item, $column_name ){
		//var_dump( $item );
        	switch( $column_name ){
            		case 'cb':
                		return '<input type="checkbox"/>';
            		case 'proxy':
				return $item['proxy'];
            		case 'port':
				return $item['port'];
            		case 'username':
				return $item['username'];
            		case 'password':
				return $item['password'];
            		default:
                		return '';
        	}
    	}
	public function column_cb( $item ){
		return '<input type="checkbox" name="del_proxy[]" value="'. $item['id'].'"/>';;
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
			'proxy'    		=> 'Proxy',
			'port'    		=> 'Port',
			'username'    		=> 'Username',
			'password'    		=> 'Password',
        	);
		return $columns;
	}  
	public function get_sortable_columns() {
		$sortable_columns = array(
			'port'   => array('port', false ),
			'proxy'   => array('proxy', false ),
			'username'   => array('username', false ),
			'password'   => array('password', false ),
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
            		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'proxy';
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

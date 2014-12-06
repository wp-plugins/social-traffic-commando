<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class STCQueue extends WP_List_Table {

	private $data;

	public function __construct( $data ){
		global $wpdb;
		$this->wpdb 	= $wpdb;
		$this->data 	= $data;
		parent::__construct( array(
			'singular'  => 'Queued Post',
			'plural'    => 'Queued Posts',
			'ajax'      => false
		) );
	}
	public function column_default( $item, $column_name ){
		//var_dump( $item );
        	switch( $column_name ){
            		case 'cb':
                		return '<input type="checkbox"/>';
            		case 'site':
				return ucwords( trim( $item['social'] ));
			case 'url':
				return '<a target="_blank" href="'.
					$item['url'] .'">'.
					$item['url'] . '</a>';
			case 'link':
				return '<a target="_blank" href="'.
					$item['link'] .'">'.
					$item['link'] . '</a>';
			case 'title':
				return ucwords( strip_tags( trim( $item['title'] ) ) );
			case 'description':
				if( strlen( $item['description'] ) > 150 ){
					return  strip_tags( substr($item['description'], 0, 150 )) . '.....';
				} else {
					return  strip_tags( trim( $item['description'] ) );
				}
			case 'action':
				if( ( $item['social'] == 'diigo' ) or ( $item['social'] == 'delicious' ) )
					return  "Bookmark";
				else 
					return ucwords( trim( $item['action'] ));
			case 'status':
				return  trim( $item['status'] );
			case 'post_time':
				return  $stc->time_stamp( $item['posted_timestamp'] );
            		default:
                		return '';
        	}
    	}
	public function column_post_time( $item ){
		global $stc;
		if( $item['posted_timestamp'] > 0 ){ 
			return $stc->time_stamp( $item['posted_timestamp'] );
		} else {
			return 'Future';
		}
	}
	public function column_cb( $item ){
		return '<input type="checkbox" name="del_queue[]" value="'. $item['id'].'"/>';;
	}
	public function column_site($item) {
		if( ( $item['social'] == 'scribd' ) or ( $item['status'] == 1 ) ){
			$actions = array(
			'delete'    => sprintf('<a href="?page=%s&stcdeletequeue=%s">Delete</a>',$_REQUEST['page'],$item['id']),
			);
		} else {
			$actions = array(
			'edit'      => sprintf('<a href="?page=%s&stcpostnow=%s">Post Now</a>',$_REQUEST['page'],$item['id']),
			'delete'    => sprintf('<a href="?page=%s&stcdeletequeue=%s">Unqueue</a>',$_REQUEST['page'],$item['id']),
			);
		}

		return sprintf('%1$s %2$s', ucwords( trim( $item['social'] )), $this->row_actions($actions) );
	}
	public function column_status( $item ){
		if( $item['status'] == 0 ){
			return 'Queued';
		} elseif( $item['status'] == 1 ){
			return 'Posted successfully';
		} elseif( $item['status'] == 2 ){
			return 'Posting unsuccessful';
		}
	}
	public function get_bulk_actions() {
        	$actions = array(
            		'delete'    => 'Delete'
        		);
        	return $actions;
	}
	public function get_columns(){
		$columns = array(
			'cb'        		=> '<input type="checkbox"/>',
			'site'    		=> 'Site',
			'title'    		=> 'Title',
			'link'    		=> 'Link',
			'url'    		=> 'Url',
			'description'    	=> 'Description',
			'action'    		=> 'Action',
			'status'    		=> 'Status',
			'post_time'    		=> 'Time Posted',
        	);
		return $columns;
	}  
	public function get_sortable_columns() {
		$sortable_columns = array(
			'site'   => array('site', false ),
			'status'   => array('status', false ),
			'action'   => array('action', false ),
			'title'   => array('title', false ),
			'link'   => array('link', false ),
			'url'   => array('url', false ),
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

<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class STCAccount extends WP_List_Table {

	private $data;

	public function __construct( $data ){
		global $wpdb;
		$this->wpdb 	= $wpdb;
		$this->data 	= $data;
		parent::__construct( array(
			'singular'  => 'Account',
			'plural'    => 'Accounts',
			'ajax'      => false
		) );
	}
	public function column_default( $item, $column_name ){
		//var_dump( $item );
        	switch( $column_name ){
            		case 'cb':
                		return '<input type="checkbox"/>';
            		case 'social':
				return ucwords( trim( $item['social'] ));
			case 'username':
				if( $item['social'] == 'pinterest' ){
					return trim( $item['access_key']) . " - " . trim( $item['username'] );
				} else {
					return trim( $item['username'] );
				}
            		default:
                		return '';
        	}
    	}
	public function column_cb( $item ){
		return '<input type="checkbox" name="del_account[]" value="'. $item['id'].'"/>';;
	}
	public function column_profile( $item ){
		$social = $item['social'];
		switch ($social){
			case 'twitter':
                		return '<a href="https://twitter.com/'. 
					$item['username'] .'">https://twitter.com/'. $item['username'] .'</a>';
				break;
			case 'tumblr':
                		return '<a href="http://'. $item['username'] .'.tumblr.com/">'.
						'http://'. $item['username'] .'.tumblr.com/</a>';
				break;
			case 'linkedin':
                		return '<a href="http://www.linkedin.com/profile/view?id=' . $item['username']. '">'.
						'http://www.linkedin.com/profile/view?id=' . $item['username']. '</a>';
				break;
			case 'pinterest':
                		return '<a href="http://www.pinterest.com/">'.
						'http://www.pinterest.com/</a>';
				break;
			case 'delicious':
                		return '<a href="https://delicious.com/' . $item['username']. '">'.
						'https://delicious.com/' . $item['username']. '</a>';
				break;
			case 'diigo':
                		return '<a href="https://www.diigo.com/user/' . $item['username']. '">'.
						'https://www.diigo.com/user/' . $item['username']. '</a>';
				break;
			case 'plurk':
                		return '<a href="http://www.plurk.com/' . $item['username']. '">'.
						'http://www.plurk.com/' . $item['username']. '</a>';
				break;
			case 'lj':
                		return '<a href="http://'. $item['username'] .'.livejournal.com/">'.
						'http://'. $item['username'] .'.livejournal.com/</a>';
				break;
			case 'stumbleupon':
                		return '<a href="http://www.stumbleupon.com/stumbler/' . $item['username']. '">'.
						'http://www.stumbleupon.com/stumbler/' . $item['username']. '</a>';
				break;
			case 'scribd':
                		return '<a href="http://www.scribd.com/' . $item['username']. '">'.
						'http://www.scribd.com/' . $item['username']. '</a>';
				break;
			default:
				return '';
		}
	}
	public function column_social($item) {
		if( $item['authority'] == 1 ){
			$actions = array(
			'delete'    => sprintf('<a href="?page=%s&stcaccountdelete=%s">Delete</a>',$_REQUEST['page'],$item['id']),
			'authority'    => sprintf('<a href="?page=%s&stcauthority=%s&stcval=0&stcsocial=%s">Remove Authority</a>',
								$_REQUEST['page'],$item['id'],$item['social'] ),
			);
		} else {
			$actions = array(
			'delete'    => sprintf('<a href="?page=%s&stcaccountdelete=%s">Delete</a>',$_REQUEST['page'],$item['id']),
			'authority'    => sprintf('<a href="?page=%s&stcauthority=%s&stcval=1&stcsocial=%s">Make Authority</a>',
								$_REQUEST['page'],$item['id'],$item['social'] ),
			);
		}
		return sprintf('%1$s %2$s', ucwords( trim( $item['social'] )), $this->row_actions($actions) );
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
			'social'    		=> 'Site',
			'username'    		=> 'Username/Identifier',
        	);
		return $columns;
	}  
	public function get_sortable_columns() {
		$sortable_columns = array(
			'site'   => array('site', false ),
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

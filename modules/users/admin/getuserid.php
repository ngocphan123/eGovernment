<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 26/5/2011, 23:28
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$area = $nv_Request->get_title( 'area', 'get', '' );
if( empty( $area ) )
{
	nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] );
}

$page_title = $lang_module['pagetitle'];
$filtersql = $nv_Request->get_string( 'filtersql', 'get', '' );

$xtpl = new XTemplate( $op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );

$lang_module['fullname'] = $global_config['name_show'] == 0 ? $lang_module['lastname_firstname'] : $lang_module['firstname_lastname'];
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'GLOBAL_CONFIG', $global_config );
$xtpl->assign( 'NV_LANG_INTERFACE', NV_LANG_INTERFACE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'AREA', $area );
$xtpl->assign( 'FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&area=' . $area . '&filtersql=' . $filtersql );

$array = array();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&amp;area=' . $area . '&amp;submit=1';

if( $nv_Request->isset_request( 'submit', 'get' ) )
{
	$array_user = array();
	$generate_page = '';

	$orderid = $nv_Request->get_title( 'orderid', 'get', '' );
	$orderusername = $nv_Request->get_title( 'orderusername', 'get', '' );
	$orderemail = $nv_Request->get_title( 'orderemail', 'get', '' );
	$orderregdate = $nv_Request->get_title( 'orderregdate', 'get', '' );

	if( $orderid != 'DESC' and $orderid != '' ) $orderid = 'ASC';
	if( $orderusername != 'DESC' and $orderusername != '' ) $orderusername = 'ASC';
	if( $orderemail != 'DESC' and $orderemail != '' ) $orderemail = 'ASC';
	if( $orderregdate != 'DESC' and $orderregdate != '' ) $orderregdate = 'ASC';

	$array['username'] = $nv_Request->get_title( 'username', 'get', '' );
	$array['full_name'] = $nv_Request->get_title( 'full_name', 'get', '' );
	$array['email'] = $nv_Request->get_title( 'email', 'get', '' );
	$array['sig'] = $nv_Request->get_title( 'sig', 'get', '' );
	$array['regdatefrom'] = $nv_Request->get_title( 'regdatefrom', 'get', '' );
	$array['regdateto'] = $nv_Request->get_title( 'regdateto', 'get', '' );
	$array['last_loginfrom'] = $nv_Request->get_title( 'last_loginfrom', 'get', '' );
	$array['last_loginto'] = $nv_Request->get_title( 'last_loginto', 'get', '' );
	$array['last_ip'] = $nv_Request->get_title( 'last_ip', 'get', '' );
	$array['gender'] = $nv_Request->get_title( 'gender', 'get', '' );

	if( preg_match( '/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $array['regdatefrom'], $m ) )
	{
		$array['regdatefrom1'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$array['regdatefrom1'] = '';
	}

	if( preg_match( '/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $array['regdateto'], $m ) )
	{
		$array['regdateto1'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$array['regdateto1'] = '';
	}

	if( preg_match( '/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $array['last_loginfrom'], $m ) )
	{
		$array['last_loginfrom1'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$array['last_loginfrom1'] = '';
	}

	if( preg_match( '/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $array['last_loginto'], $m ) )
	{
		$array['last_loginto1'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$array['last_loginto1'] = '';
	}

	$is_null = true;
	foreach( $array as $check )
	{
		if( ! empty( $check ) )
		{
			$is_null = false;
			break;
		}
	}

	$array_where = array();

	if( ! $is_null )
	{
		if( ! empty( $array['username'] ) )
		{
			$base_url .= '&amp;username=' . rawurlencode( $array['username'] );
			$array_where[] = "( username LIKE '%" . $db->dblikeescape( $array['username'] ) . "%' )";
		}

		if( ! empty( $array['full_name'] ) )
		{
			$base_url .= '&amp;full_name=' . rawurlencode( $array['full_name'] );

			$where_fullname = $global_config['name_show'] == 0 ? "concat(last_name,' ',first_name)" : "concat(first_name,' ',last_name)";
			$array_where[] =  "(" . $where_fullname ." LIKE '%" . $db->dblikeescape( $array['full_name'] ) . "%' )";
		}

		if( ! empty( $array['email'] ) )
		{
			$base_url .= '&amp;email=' . rawurlencode( $array['email'] );
			$array_where[] = "( email LIKE '%" . $db->dblikeescape( $array['email'] ) . "%' )";
		}

		if( ! empty( $array['sig'] ) )
		{
			$base_url .= '&amp;sig=' . rawurlencode( $array['sig'] );
			$array_where[] = "( sig LIKE '%" . $db->dblikeescape( $array['sig'] ) . "%' )";
		}

		if( ! empty( $array['last_ip'] ) )
		{
			$base_url .= '&amp;last_ip=' . rawurlencode( $array['last_ip'] );
			$array_where[] = "( last_ip LIKE '%" . $db->dblikeescape( $array['last_ip'] ) . "%' )";
		}

		if( ! empty( $array['gender'] ) )
		{
			$base_url .= '&amp;gender=' . rawurlencode( $array['gender'] );
			$array_where[] = '( gender =' . $db->quote( $array['gender'] ) . ' )';
		}

		if( ! empty( $array['regdatefrom1'] ) )
		{
			$base_url .= '&amp;regdatefrom=' . rawurlencode( nv_date( 'd/m/Y', $array['regdatefrom1'] ) );
			$array_where[] = '( regdate >= ' . $array['regdatefrom1'] . ' )';
		}

		if( ! empty( $array['regdateto1'] ) )
		{
			$base_url .= '&amp;regdateto=' . rawurlencode( nv_date( 'd/m/Y', $array['regdateto1'] ) );
			$array_where[] = '( regdate <= ' . $array['regdateto1'] . ' )';
		}

		if( ! empty( $array['last_loginfrom1'] ) )
		{
			$base_url .= '&amp;last_loginfrom=' . rawurlencode( nv_date( 'd/m/Y', $array['last_loginfrom1'] ) );
			$array_where[] = '( last_login >= ' . $array['last_loginfrom1'] . ' )';
		}

		if( ! empty( $array['last_loginto1'] ) )
		{
			$base_url .= '&amp;last_loginto=' . rawurlencode( nv_date( 'd/m/Y', $array['last_loginto1'] ) );
			$array_where[] = '( last_login <= ' . $array['last_loginto1'] . ' )';
		}
		if( ! empty( $filtersql ) )
		{
			$data_str = $crypt->aes_decrypt( nv_base64_decode( $filtersql ), md5( $global_config['sitekey'] . $client_info['session_id'] ) );
			if( ! empty( $data_str ) )
			{
				$array_where[] = $data_str;
			}
		}

		// Order data
		$orderida = array( 'url' => ( $orderid == 'ASC' ) ? $base_url . '&amp;orderid=DESC' : $base_url . '&amp;orderid=ASC', 'class' => ( $orderid == '' ) ? 'nooder' : strtolower( $orderid ) );

		$orderusernamea = array( 'url' => ( $orderusername == 'ASC' ) ? $base_url . '&amp;orderusername=DESC' : $base_url . '&amp;orderusername=ASC', 'class' => ( $orderusername == '' ) ? 'nooder' : strtolower( $orderusername ) );

		$orderemaila = array( 'url' => ( $orderemail == 'ASC' ) ? $base_url . '&amp;orderemail=DESC' : $base_url . '&amp;orderemail=ASC', 'class' => ( $orderemail == '' ) ? 'nooder' : strtolower( $orderemail ) );

		$orderregdatea = array( 'url' => ( $orderregdate == 'ASC' ) ? $base_url . '&amp;orderregdate=DESC' : $base_url . '&amp;orderregdate=ASC', 'class' => ( $orderregdate == '' ) ? 'nooder' : strtolower( $orderregdate ) );

		// SQL data
		$order_by = '';
		if( ! empty( $orderid ) )
		{
			$base_url .= '&amp;orderid=' . $orderid;
			$order_by = 'userid ' . $orderid;
		}
		elseif( ! empty( $orderusername ) )
		{
			$base_url .= '&amp;orderusername=' . $orderusername;
			$order_by = 'username ' . $orderusername;
		}
		elseif( ! empty( $orderemail ) )
		{
			$base_url .= '&amp;orderemail=' . $orderemail;
			$order_by = 'email ' . $orderemail;
		}
		elseif( ! empty( $orderregdate ) )
		{
			$base_url .= '&amp;orderregdate=' . $orderregdate;
			$order_by = 'regdate ' . $orderregdate;
		}

		$page = $nv_Request->get_int( 'page', 'get', 1 );
		$per_page = 10;

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_USERS_GLOBALTABLE );
		if( ! empty( $array_where ) )
		{
			$db->where( implode( ' AND ', $array_where ) );
		}

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'userid, username, email, regdate' )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );
		if( ! empty( $order_by ) )
		{
			$db->order( $order_by );
		}

		$result2 = $db->query( $db->sql() );
		while( $row = $result2->fetch() )
		{
			$array_user[$row['userid']] = array(
				'userid' => $row['userid'],
				'username' => $row['username'],
				'email' => $row['email'],
				'regdate' => nv_date( 'd/m/Y H:i', $row['regdate'] )
			);
		}

		$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
	}

	if( ! empty( $array_user ) )
	{
		$xtpl->assign( 'ODER_ID', $orderida );
		$xtpl->assign( 'ODER_USERNAME', $orderusernamea );
		$xtpl->assign( 'ODER_EMAIL', $orderemaila );
		$xtpl->assign( 'ODER_REGDATE', $orderregdatea );

		foreach( $array_user as $row )
		{
			$xtpl->assign( 'ROW', $row );
			$xtpl->parse( 'resultdata.data.row' );
		}

		if( ! empty( $generate_page ) )
		{
			$xtpl->assign( 'GENERATE_PAGE', $generate_page );
			$xtpl->parse( 'resultdata.data.generate_page' );
		}

		$xtpl->parse( 'resultdata.data' );
	}
	elseif( $nv_Request->isset_request( 'submit', 'get' ) )
	{
		$xtpl->parse( 'resultdata.nodata' );
	}

	$xtpl->parse( 'resultdata' );
	$contents = $xtpl->text( 'resultdata' );

	include NV_ROOTDIR . '/includes/header.php';
	echo $contents;
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{
	$gender = isset( $array['gender'] ) ? $array['gender'] : '';
	$array['gender'] = array();
	$array['gender'][] = array(
		'key' => '',
		'title' => $lang_module['select_gender'],
		'selected' => ( '' == $gender ) ? ' selected="selected"' : ''
	);
	$array['gender'][] = array(
		'key' => 'M',
		'title' => $lang_module['select_gender_male'],
		'selected' => ( 'M' == $gender ) ? ' selected="selected"' : ''
	);
	$array['gender'][] = array(
		'key' => 'F',
		'title' => $lang_module['select_gender_female'],
		'selected' => ( 'F' == $gender ) ? ' selected="selected"' : ''
	);

	foreach( $array['gender'] as $gender )
	{
		$xtpl->assign( 'GENDER', $gender );
		$xtpl->parse( 'main.gender' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents, 0 );
	include NV_ROOTDIR . '/includes/footer.php';
}
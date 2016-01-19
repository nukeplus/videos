<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$channel = array();
$data = array();
$array_item = array();

// Get info from URL
$raw_alias = isset( $array_op[1] ) ? trim( $array_op[1] ) : '';
$arr_alias = explode('-',$raw_alias);
$p_id = intval(substr($arr_alias[0],4));
$check_ss = $arr_alias[1];
$vid_id = intval(substr($arr_alias[2],4));
if(sizeof($arr_alias) == 4)
{
	$embed = $arr_alias[3];
}

$channel['title'] = $module_info['custom_title'];
$channel['link'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$channel['description'] = ! empty( $module_info['description'] ) ? $module_info['description'] : $global_config['site_description'];

$cache_file = '';
$contents = '';

if( isset( $embed ) AND $embed == 'embed') // embed to FB
{
	if( $p_id > 0 )
	{
		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows t1' )
			->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_playlist t2 ON t1.id = t2.id' )
			->where( 't2.playlist_id= ' . $p_id . ' AND t1.status= 1' );

		$db->select( 't1.id, t1.catid, t1.admin_id, t1.author, t1.vid_path, t1.vid_type, t1.addtime, t1.edittime, t1.publtime, t1.title, t1.alias, t1.hometext, t1.homeimgfile, t1.homeimgalt, t1.homeimgthumb, t2.playlist_id, t2.playlist_sort' )
			->order( 't2.playlist_sort ASC' );
	}
	elseif( $vid_id > 0 )
	{
		if( defined( 'NV_IS_MODADMIN' ) ) // Allow ADMINMOD preview video
		{
			$where = '';
		}
		else
		{
			$where = 'status=1 AND ';
		}

		$db->sqlreset()
			->select( '*' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
			->where( $where . 'id= ' . $vid_id );
	}
	$result = $db->query( $db->sql() );
	while( $data = $result->fetch( ) )
	{
		$data['link'] = NV_MY_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$data['catid']]['alias'] . '/' . $data['alias'] . '-' . $data['id'] . $global_config['rewrite_exturl'], true );
		if( !empty($data['homeimgfile']) )
		{
			if( $data['homeimgthumb'] == 1 ) // image thumb
			{
				$data['rss_img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $data['homeimgfile'];
			}
			elseif( $data['homeimgthumb'] == 2 ) // image file
			{
				$data['rss_img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $data['homeimgfile'];
			}
			elseif( $data['homeimgthumb'] == 3 ) // image url
			{
				$data['rss_img'] = $data['homeimgfile'];
			}
		}
		else
		{
			$data['rss_img'] = '';
		}
		// Fake playlist_id and playlist_sort
		$data['playlist_id'] = 'No.';
		$data['playlist_sort'] = 99;
		// Export video link
		if( ! empty( $data['vid_path'] ) )
		{
			if( $data['vid_type'] == 1 )
			{
				$data['href'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/vid/' . $data['vid_path'];
				$data['quality'] = '';
			}
			elseif( $data['vid_type'] == 2 )
			{
				$data['href'] = $data['vid_path'];
				$data['quality'] = '';
			}
			elseif( $data['vid_type'] == 3 )
			{
				$data['href'] = array();
				$data['href'] = get_link_mp4_picasa($data['vid_path']);
			}
			elseif( $data['vid_type'] == 4 )
			{
				$data['href'] = array();
				$data['href'] = get_facebook_mp4($data['vid_path']);
			}
			elseif( $data['vid_type'] == 5 )
			{
				$data['href'] = $data['vid_path'];
				$data['quality'] = '';
			}
		}
		$array_item[] = $data;
	}

	$contents.='<config>';
	foreach ( $array_item as $items )
	{
		$contents .='<title>'.$items['title'].'</title>';
		$contents .='<link>'.$items['link'].'</link>';
		$contents .='<image>'.$items['rss_img'].'</image>';
		$contents .='<linktarget>_blank</linktarget>';
		$contents .='<repeat>linktargettrue</repeat>';
		$contents .='<resizing>false</resizing>';
		$contents .='<smoothing>true</smoothing>';
		$contents .='<autostart>true</autostart>';
		$contents .='<fullscreen>false</fullscreen>';
		$contents .='<displayclick>play</displayclick>';		
		$contents .= '<file>'.htmlentities($items['href']).'</file>';
	}
	$contents .='</config>';
	echo $contents;
	die();
}
else
{
	// cache call
	$cache_file = NV_LANG_DATA . '_' . $module_name . '_' . $op . '_' .  md5( $p_id.$vid_id ) .'_' . NV_CACHE_PREFIX . '.cache';
	if( ! defined( 'NV_IS_MODADMIN' ) )
	{
		if( ( $cache = $nv_Cache->getItem( $module_name, $cache_file ) ) != false )
		{
			$time_set_cache = NV_CURRENTTIME - filemtime( NV_ROOTDIR . '/' . NV_CACHEDIR .'/'. $module_name . '/' . $cache_file);
			if( $time_set_cache > 86400 )
			{
				unlink( NV_ROOTDIR . '/' . NV_CACHEDIR .'/'. $module_name . '/' . $cache_file );
				$contents = '';
			}
			else
			{
				$contents = $cache;
			}
		}
	}

	if( empty( $contents )  )
	{
		if( $p_id > 0 )
		{
			if($check_ss != (md5( $p_id . session_id() . $global_config['sitekey'] )))die("Wrong session!");
			$db->sqlreset()
				->select( 'COUNT(*)' )
				->from( NV_PREFIXLANG . '_' . $module_data . '_rows t1' )
				->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_playlist t2 ON t1.id = t2.id' )
				->where( 't2.playlist_id= ' . $p_id . ' AND t1.status= 1' );

			$db->select( 't1.id, t1.catid, t1.admin_id, t1.author, t1.vid_path, t1.vid_type, t1.addtime, t1.edittime, t1.publtime, t1.title, t1.alias, t1.hometext, t1.homeimgfile, t1.homeimgalt, t1.homeimgthumb, t2.playlist_id, t2.playlist_sort' )
				->order( 't2.playlist_sort ASC' );
		}
		elseif( $vid_id > 0 )
		{
			if($check_ss != (md5( $vid_id . session_id() . $global_config['sitekey'] )))die("Wrong session!");
			if( defined( 'NV_IS_MODADMIN' ) ) // Allow ADMINMOD preview video
			{
				$where = '';
			}
			else
			{
				$where = 'status=1 AND ';
			}

			$db->sqlreset()
				->select( '*' )
				->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
				->where( $where . 'id= ' . $vid_id );
		}
		$result = $db->query( $db->sql() );
		while( $data = $result->fetch( ) )
		{
			$data['link'] = NV_MY_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$data['catid']]['alias'] . '/' . $data['alias'] . '-' . $data['id'] . $global_config['rewrite_exturl'], true );
			if( !empty($data['homeimgfile']) )
			{
				if( $data['homeimgthumb'] == 1 ) // image thumb
				{
					$data['rss_img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $data['homeimgfile'];
				}
				elseif( $data['homeimgthumb'] == 2 ) // image file
				{
					$data['rss_img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $data['homeimgfile'];
				}
				elseif( $data['homeimgthumb'] == 3 ) // image url
				{
					$data['rss_img'] = $data['homeimgfile'];
				}
			}
			else
			{
				$data['rss_img'] = '';
			}
			// Fake playlist_id and playlist_sort
			$data['playlist_id'] = 'No.';
			$data['playlist_sort'] = 99;
			// Export video link
			if( ! empty( $data['vid_path'] ) )
			{
				if( $data['vid_type'] == 1 )
				{
					$data['href'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/vid/' . $data['vid_path'];
					$data['quality'] = '';
				}
				elseif( $data['vid_type'] == 2 )
				{
					$data['href'] = $data['vid_path'];
					$data['quality'] = '';
				}
				elseif( $data['vid_type'] == 3 )
				{
					$data['href'] = array();
					$data['href'] = get_link_mp4_picasa($data['vid_path']);
				}
				elseif( $data['vid_type'] == 4 )
				{
					$data['href'] = array();
					$data['href'] = get_facebook_mp4($data['vid_path']);
				}
				elseif( $data['vid_type'] == 5 )
				{
					$data['href'] = $data['vid_path'];
					$data['quality'] = '';
				}
			}
			$array_item[] = $data;
		}

		$contents.='<rss xmlns:jwplayer="http://rss.jwpcdn.com/">';
		$contents .='<channel>';
		$contents .='<title>'.$channel['title'].'</title>';
		$contents .='<description>'.$channel['description'].'</description>';
		$contents .='<link>'.$channel['link'].'</link>';
		foreach ( $array_item as $items )
		{
			$contents .='<item>';
			$contents .='<title>'.$items['title'].'</title>';
			$contents .='<link>'.$items['link'].'</link>';
			$contents .='<description>'.$items['hometext'].'</description>';
			$contents .='<guid>'.$items['playlist_id'].$items['playlist_sort'].'</guid>';
			$contents .='<jwplayer:image>'.$items['rss_img'].'</jwplayer:image>';
			if( $items['vid_type'] == 3 || $items['vid_type'] == 4 )
			{
				foreach ($items['href'] as $source_file_i)
				{
					$contents .= '<jwplayer:source file="'.htmlentities($source_file_i['link_mp4']).'" label="'.$source_file_i['quality'].'" type="mp4" />';
				}
			}
			else
			{
				$contents .= '<jwplayer:source file="'.htmlentities($items['href']).'" />';
			}
			$contents .='</item>';	
		}
		$contents .='</channel>';
		$contents .='</rss>';

		if( ! defined( 'NV_IS_MODADMIN' ) and $contents != '' and $cache_file != '' )
		{
			$nv_Cache->setItem( $module_name, $cache_file, $contents );
		}
	}
	echo $contents;
	die();
}
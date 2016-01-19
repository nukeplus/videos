<?php

/**
 * @Project NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 03 Oct 2015 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_block_news_groups_tabs' ) )
{
	function nv_block_config_news_groups_tabs( $module, $data_block, $lang_block )
	{
		global $site_mods;

		$html_input = '';
		$html = '';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['blockid'] . '</td>';
		$html .= '<td><select name="config_blockid" class="form-control w200">';
		$html .= '<option value="0"> -- </option>';
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
		$list = $nv_Cache->db( $sql, '', $module );
		foreach( $list as $l )
		{
			$html .= '<option value="' . $l['bid'] . '" ' . ( ( $data_block['blockid'] == $l['bid'] ) ? ' selected="selected"' : '' ) . '>' . $l['title'] . '</option>';
		}
		$html .= '</select>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['numrow'] . '</td>';
		$html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['title_length'] . '</td>';
		$html .= '<td><input type="text" class="form-control w200" name="config_title_length" size="5" value="' . $data_block['title_length'] . '"/></td>';
		$html .= '</tr>';
		
		return $html;
	}

	function nv_block_config_news_groups_tabs_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['blockid'] = $nv_Request->get_int( 'config_blockid', 'post', 0 );
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['title_length'] = $nv_Request->get_string( 'config_title_length', 'post', 0 );
		return $return;
	}

	function nv_block_news_groups_tabs( $block_config )
	{
		global $module_array_cat, $module_info, $site_mods, $module_config, $global_config, $db;
		$module = $block_config['module'];
		$show_no_image = $module_config[$module]['show_no_image'];
		$blockwidth = $module_config[$module]['blockwidth'];
		
		if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/news/block_groups_tabs.tpl' ) )
		{
			$block_theme = $global_config['module_theme'];
		}
		else
		{
			$block_theme = 'default';
		}
		$xtpl = new XTemplate( 'block_groups_tabs.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/news' );

		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block_cat WHERE bid='.$block_config['blockid'];
		$list = $nv_Cache->db( $sql, '', $module );
		foreach( $list as $l )
		{
			$block_select = $l;
		}

		$db->sqlreset()
			->select( 't1.id, t1.catid, t1.title, t1.alias, t1.homeimgfile, t1.homeimgthumb,t1.hometext,t1.publtime' )
			->from( NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows t1' )
			->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id' )
			->where( 't2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1' )
			->order( 't2.weight ASC' )
			->limit( $block_config['numrow'] );
		$list = $nv_Cache->db( $db->sql(), '', $module );

		if( ! empty( $list ) )
		{
			foreach( $list as $l )
			{
				$l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . '-' . $l['id'] . $global_config['rewrite_exturl'];
				if( $l['homeimgthumb'] == 1 )
				{
					$l['thumb'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['homeimgfile'];
				}
				elseif( $l['homeimgthumb'] == 2 )
				{
					$l['thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['homeimgfile'];
				}
				elseif( $l['homeimgthumb'] == 3 )
				{
					$l['thumb'] = $l['homeimgfile'];
				}
				elseif( ! empty( $show_no_image ) )
				{
					$l['thumb'] = NV_BASE_SITEURL . $show_no_image;
				}
				else
				{
					$l['thumb'] = '';
				}

				$l['blockwidth'] = $blockwidth;
				$l['title'] = nv_clean60( $l['title'], $block_config['title_length'] );

				$xtpl->assign( 'ROW', $l );
				if( ! empty( $l['thumb'] ) ) $xtpl->parse( 'main.group.loop.img' );
				$xtpl->parse( 'main.group.loop' );
			}
			$xtpl->assign( 'BLOCK_NAME', $block_select['title']);
			$xtpl->parse( 'main.group' );
		}

		$db->sqlreset()
			->select( '*' )
			->from( NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows' )
			->where( 'status= 1' )
			->order( 'publtime DESC' )
			->limit( $block_config['numrow'] );
		$last = $nv_Cache->db( $db->sql(), '', $module );
		if( ! empty( $last ) )
		{
			foreach( $last as $ls )
			{
				$ls['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$ls['catid']]['alias'] . '/' . $ls['alias'] . '-' . $ls['id'] . $global_config['rewrite_exturl'];
				if( $ls['homeimgthumb'] == 1 )
				{
					$ls['thumb'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $ls['homeimgfile'];
				}
				elseif( $ls['homeimgthumb'] == 2 )
				{
					$ls['thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $ls['homeimgfile'];
				}
				elseif( $ls['homeimgthumb'] == 3 )
				{
					$ls['thumb'] = $ls['homeimgfile'];
				}
				elseif( ! empty( $show_no_image ) )
				{
					$ls['thumb'] = NV_BASE_SITEURL . $show_no_image;
				}
				else
				{
					$ls['thumb'] = '';
				}

				$ls['blockwidth'] = $blockwidth;
				$ls['title'] = nv_clean60( $ls['title'], $block_config['title_length'] );

				$xtpl->assign( 'NEWS', $ls );
				if( ! empty( $ls['thumb'] ) ) $xtpl->parse( 'main.latest.loop.img' );
				$xtpl->parse( 'main.latest.loop' );
			}
			$xtpl->parse( 'main.latest' );
		}

			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $global_array_cat, $module_array_cat;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		if( $module == $module_name )
		{
			$module_array_cat = $global_array_cat;
			unset( $module_array_cat[0] );
		}
		else
		{
			$module_array_cat = array();
			$sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid, numlinks, description, inhome, keywords, groups_view FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
			$list = $nv_Cache->db( $sql, 'catid', $module );
			foreach( $list as $l )
			{
				$module_array_cat[$l['catid']] = $l;
				$module_array_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
			}
		}
		$content = nv_block_news_groups_tabs( $block_config );
	}
}

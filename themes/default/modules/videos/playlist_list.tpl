<!-- BEGIN: list_videos -->
<form name="block_list" action="{NV_BASE_SITEURL}index.php?language={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;playlist_id={PLAYLIST_ID}" method="get">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<colgroup>
				<col span="w50" />
				<col class="w50" />
				<col class="w150" />
			</colgroup>
			<thead>
				<tr>
					<th class="text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);" /></th>
					<th class="w50">{LANG.weight}</th>
					<th>{LANG.name}</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5"><input class="btn btn-primary" type="button" onclick="nv_del_playlist_list(this.form, {PLAYLIST_ID})" value="{LANG.delete_from_block}Xoá khỏi list"></td>
				</tr>
			</tfoot>
			<tbody>
			<!-- BEGIN: loop -->
				<tr>
					<td class="text-center"><input type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{ROW.id}" name="idcheck[]" /></td>
					<td class="text-center">
					<select class="form-control" id="id_playlist_sort_{ROW.id}" onchange="nv_change_playlist({PLAYLIST_ID},{ROW.id},'playlist_sort');">
						<!-- BEGIN: playlist_sort -->
						<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
						<!-- END: playlist_sort -->
					</select></td>
					<td class="text-left"><a target="_blank" href="{ROW.link}">{ROW.title}</a></td>
				</tr>
			<!-- END: loop -->
			</tbody>
		</table>
	</div>
</form>

<!-- END: list_videos -->
<!-- BEGIN: no_videos -->
<div class="alert alert-info" role="alert">{LANG.playlist_empty_video}</div>
<!-- END: no_videos -->
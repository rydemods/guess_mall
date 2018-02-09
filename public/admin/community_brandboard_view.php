<?php
$thisBoard = brandBoardView($board_num);
$prive_next = brandBoardView_privew($thisBoard[board_code],$thisBoard[board_num]);
$thisBosrdItem = brandBoardItem($board_num);
?>
<script>
function goview(board_num){
	$("#board_num").val(board_num);
	$("#boardmode").val('view');
	$("#go_np").submit();
}
function go_modify(board_num){
	$("#board_num").val(board_num);
	$("#boardmode").val('modify');
	$("#go_np").submit();
}
function go_delete(board_num){
	$("#del_num").val(board_num);
	$("#mode").val('delete');
	$("#delForm").submit();
}
function go_write(){
	$("#boardmode").val('write');
	$("#go_np").submit();
}

</script>

<form id="go_np" name="go_np" method="POST">
	<input type=hidden name="board_code" value="<?=$board_code?>">
	<input type=hidden name="board_num" id="board_num">
	<input type=hidden name="max_row_number" id="max_row_number" value="<?=$max_row_number?>">
	<input type=hidden name="brandBoardMode" id="boardmode">
</form>
<form id="delForm" name="delForm" action="community_brandboard_indb.php" method="POST">
	<input type=hidden name="board_code" value="<?=$board_code?>">
	<input type=hidden name="board_num" id="del_num">
	<input type=hidden name="mode" id="mode">
</form>

<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
			<tr><td height="10"></td></tr>
			<tr>
				<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#f0f0f0" style="table-layout:fixed">
				<tbody>
				<tr>
					<td style="border:#f0f0f0 solid 1px">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#F1F1F1" style="table-layout:fixed">
					<tbody>
					<tr>
						<td align="center" style="padding:5">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
						<tbody>
						<tr>
							<td bgcolor="#FFFFFF" style="border:#f0f0f0 solid 1px; padding:5">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<colgroup><col width="80">
								<col width="8">
								<col width="3">
								<col width="15">
								<col width="">
								<col width="100">
								</colgroup>
								<tbody>
							<?if($thisBoard[productcode] && $thisBoard[productname]){?>
								<tr>
									<td align="center">
										<a><img src="<?=$Dir.DataDir?>shopimages/product/<?=$thisBoard[tinyimage]?>" border="0" width="60"></a>
									</td>
									<td>&nbsp;</td>
									<td bgcolor="#f0f0f0"></td>
									<td>&nbsp;</td>
									<td>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tbody>
										<tr height="20">
											<td>대표상품<br>
												상<img width="6" height="0">품<img width="6" height="0">명 : 
												<font class="prname"><?=$$thisBoard[productname]?></font>
											</td>
										</tr>
										<tr height="20">
											<td>상품가격 : 
											<font class="prprice">
												<img src="<?=$Dir?>images/common/won_icon.gif" border="0" align="absmiddle">
												<?=number_format($thisBoard[sellprice])?>원
											</font>
											</td>
										</tr>
										</tbody>
										</table>
									</td>
									<td>
										<a></a>
									</td>
								</tr>
							<?}?>
							<?foreach($thisBosrdItem as $boardItemKey=>$bosrdItem){?>
								<tr>
									<td align="center">
										<a><img src="<?=$Dir.DataDir?>shopimages/product/<?=$bosrdItem[tinyimage]?>" border="0" width="60"></a>
									</td>
									<td>&nbsp;</td>
									<td bgcolor="#f0f0f0"></td>
									<td>&nbsp;</td>
									<td>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tbody>
										<tr height="20">
											<td>관련상품<br>
												상<img width="6" height="0">품<img width="6" height="0">명 : 
												<font class="prname"><?=$bosrdItem[productname]?></font>
											</td>
										</tr>
										<tr height="20">
											<td>상품가격 : 
											<font class="prprice">
												<img src="<?=$Dir?>images/common/won_icon.gif" border="0" align="absmiddle">
												<?=number_format($bosrdItem[sellprice])?>원
											</font>
											</td>
										</tr>
										</tbody>
										</table>
									</td>
									<td>
										<a></a>
									</td>
								</tr>
							<?}?>
								</tbody>
								</table>
							</td>
						</tr>
						</tbody>
						</table>
						</td>
					</tr>
					</tbody>
					</table>
					</td>
				</tr>
				</tbody>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
		</tbody>
		</table>
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
		<tbody>
		<tr>
			<td height="15" style="padding-left:5"><b><?=$brandBoardCategory[$thisBoard[board_code]]->board_name?></b></td>
		</tr>
		</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td width="100%">
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
			<tbody>
			<tr>
				<td background="images/table_top_line1.gif" colspan="4" width="762"><img src="img/table_top_line1.gif" height="2"></td>
			</tr>
			<tr>
				<td class="board_cell1" align="center" width="80"><p align="center">글제목</p></td>
				<td class="board_cell1" align="center" width="683" colspan="3">
					<p align="left"><b><span class="font_orange"><?=$thisBoard[board_title]?></span></b></p>
				</td>
			</tr>
			<tr>
				<td colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
			</tr>
			<tr>
				<td align="center" height="30" class="board_con1s">작성일</td>
				<td colspan="3" align="center" height="30" width="" class="board_con1" style="text-align: right">
					<?=substr($thisBoard[date],0,4)."/".substr($thisBoard[date],4,2)."/".substr($thisBoard[date],6,2)."(".substr($thisBoard[date],8,2).":".substr($thisBoard[date],10,2).")"?>
				</td>
			</tr>
			<tr>
				<td colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
			</tr>
			<tr>
				<td class="board_con1s" align="center" width="50"><p align="center">대표 이미지</p></td>
				<td class="board_con1" align="center" width="50%">
					<a href="<?=$Dir.DataDir."shopimages/brandboard/".$thisBoard[big_image]?>" target="_brank"><?=$thisBoard[big_image]?></a>
				</td>
				<td class="board_con1s" align="center">썸네일 이미지</td>
				<td class="board_con1" align="center" width="231">
					<a href="<?=$Dir.DataDir."shopimages/brandboard/".$thisBoard[thumbnail_image]?>" target="_brank"><?=$thisBoard[thumbnail_image]?></a>
				</td>
			</tr>
				<tr>
				<td colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
			</tr>
			<tr>
				<td class="board_con1" width="753" colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%" height="300">
				<tbody>
				<tr>
					<td valign="top">
					<div class="MsgrScroller" id="contentDiv" style="OVERFLOW-x: auto; OVERFLOW-y: hidden">
					<div id="bodyList">
					<table border="0" cellspacing="0" cellpadding="10" style="table-layout:fixed">
					<tbody>
					<tr>
						<td style="word-break:break-all;" bgcolor="#ffffff" valign="top">
						<span style="width:100%;line-height:160%;"> 
							<?=$thisBoard[board_content]?>
						</span>
						</td>
					</tr>
					</tbody>
					</table>
					</div>
					</div>
					</td>
				</tr>
				</tbody>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
			</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td width="100%">

			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF">
			
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tbody><tr height="40">
				<td width="100%"><p align="right">
				
				<a href="javascript:go_modify('<?=$board_num?>');"><img src="images/board/butt-modify.gif" border="0"></a>

				<a href="javascript:go_delete('<?=$board_num?>');"><img src="images/board/butt-delete.gif" border="0"></a>

				<a href="javascript:go_write();"><img src="images/board/butt-write.gif" border="0"></a>
				
				<a href="javascript:history.back()"><img src="images/board/butt-list.gif" border="0"></a>

				</p></td>
			</tr>
			</tbody></table>
			</td>
		</tr>
		<tr>
			<td width="100%"><p>&nbsp;</p></td>
		</tr>
		<tr>
			<td width="100%"><p>&nbsp;</p></td>
		</tr>
		<tr>
			<td width="100%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
				<td>
				<div class="table_style03">
				<table cellspacing="0" cellpadding="0" width="100%">
				<tbody id=prevnext>
				<?if($prive_next[0][board_num]){?>
				<tr>
					<td align="right" width="71" height="27"><img height="14" src="images/bbs_pre.gif" width="62" border="0">
					</td>
					<td width="688">
					<a href="javascript:goview('<?=$prive_next[0][board_num]?>');">이전글</a>
					</td>
				</tr>
				<?}?>
				<?if($prive_next[1][board_num]){?>
				<tr>
					<td align="right" width="71" height="27"><img height="14" src="images/bbs_next.gif" width="62" border="0">
					</td>
					<td width="688">
					<a href="javascript:goview('<?=$prive_next[1][board_num]?>');" >다음글</a>
					</td>
				</tr>
				<?}?>
				</tbody>
				</table>
				</div>
				</td>
			</tr>
			</tbody>
			</table>
			</td>
		</tr>
		</tbody>
		</table>
		<br><br>
	</td>
</tr>
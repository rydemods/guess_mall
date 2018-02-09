

<?php
if($_data->icon_type == 'tem_001'){
	$delButten = '';
	$delButten = "location='board.php?pagetype=delete_comment&board={$board}&num={$num}&c_num={$c_num}&s_check={$s_check}&search={$search}&block={$block}&gotopage={$gotopage}'";
?>

<!-- 이부분부터 새로 -->
<ul class="comment-list">
	<li>
		<form name="comment_retwo" action="board2.php" method="post" onsubmit="return chkCommentForm2(this)">
		<input type=hidden name=pagetype value="comment_indb">
		<input type=hidden name=board value="<?=$board?>">
		<input type="hidden" name="c_num" value="<?=$c_num?>">
		<input type=hidden name=num value="<?=$this_num?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<input type=hidden name=search value="<?=$search?>">
		<input type=hidden name=s_check value="<?=$s_check?>">
		<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">

		<div class="reg-name">
			<strong><?=$c_name?><?=$c_id?></strong>(<?=$c_writetime?>)
<?php
	if( $c_is_secret == '1' ) {
?>
			<img src="../img/icon/icon_reply_secret.gif" alt="비밀글">
<?php
	}
	if( strlen( $_ShopInfo->id ) > 0 || ( ( $c_mem_id == $_ShopInfo->memid ) && ( !is_null( $c_mem_id ) && $c_mem_id != ''  ) ) ) {
?>
			<button type="button" class="btn-del" onClick="<?=$delButten?>">삭제</button>
<?php
	}
?>
		</div>
		<div class="txt-reply">
<?php
	if( $c_is_secret == '0' || strlen( $_ShopInfo->id ) > 0 || ( ( $c_mem_id == $_ShopInfo->memid ) && ( !is_null( $c_mem_id ) && $c_mem_id != ''  ) ) ) {
?>
			<?=$c_comment?>
<?php
	}
?>
			
		</div>
<?php
	if ( ( $setup['use_comment'] == "Y" && $member['grant_comment']=="Y" ) ) {
?>
		<div class="function-btn">
			<button type="button" class="btn-dib-bbs comment_reply_btn" num="<?=$data[num]?>" >답글</button>
			<button type="button" class="btn-dib-bbs cancle" style='display: none;'>취소</button>
		</div>
		<div class="reply-plus hide">
			<div class="comment-write-box">
				<div class="reg-person">
					<label for="inpt-name">작성자</label><input type="text" name='up_name_two' id="inpt-name" title="작성자 입력자리">
					<label for="inpt-pwd">비밀번호</label><input type="password" id='up_passwd_two' id="inpt-pwd" title="비밀전호 입력자리">
					<input type="checkbox" name='up_is_secret_two' id="inpt-check" value='1' ><label for="inpt-check">비밀글등록</label>
				</div>
				<div class="area-box"><textarea name="up_comment_two" id=""></textarea></div>
				<button type="button" class="btn-comment-write" onClick='this.form.submit()'>COMMENT</button>
			</div>
		</div><!-- //입력박스 -->
<?php
		$comm_qry="select * from tblboardcomment_re where parent={$c_num} order by writetime desc";
		$comm_result=pmysql_query($comm_qry);
		while($comm_data=pmysql_fetch_object($comm_result)){
			if($setup['use_comip']!="Y") {
				$c_uip_re=$comm_data->ip;
			}
			$comUserId='';
			$c_writetime_re = getTimeFormat($comm_data->writetime);
			$c_comment_re = nl2br($comm_data->comment);
			$c_comment_re = getStripHide($c_comment_re);
			$c_mem_re_id = $comm_data->c_mem_id;
?>
		<div class="reply-plus">
			<div class="reg-name">
				<strong><?=$comm_data->name?></strong>(<?=$c_writetime_re?>)
<?php
			if( $comm_data->is_secret == '1' ) {
?>
				<img src="../img/icon/icon_reply_secret.gif" alt="비밀글">
<?php
			}
			if( strlen( $_ShopInfo->id ) > 0 || ( $c_mem_re_id == $_ShopInfo->memid && ( !is_null( $c_mem_re_id ) && $c_mem_re_id != ''  ) ) ){
?>
				<button type="button" class="btn-del" onClick="location='board2.php?pagetype=delete_comment_re&board=<?=$board?>&num=<?=$num?>&c_num=<?=$comm_data->num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'">삭제</button>
<?php
			}
?>
			</div>
			<div class="txt-reply">
<?php		
			if( $comm_data->is_secret == '0' || strlen( $_ShopInfo->id ) > 0 || ( $c_mem_re_id == $_ShopInfo->memid && !is_null( $c_mem_re_id ) ) ){
?>
				<?=$c_comment_re?>
<?php
			}
?>
			</div>
		</div><!-- //리플에 리플 -->
<?php
		}
	}
?>
	</form>
	</li>
</ul><!-- //리플 리스트 끝 -->

<!-- // 이부분부터 새로 -->

<?}else{?>


<TABLE cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
<tr onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor='';">
	<td style="padding-left:10px;padding-right:10px;">
	<TABLE cellSpacing="0" cellPadding="0" width="100%">
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	<TR>
		<TD height="22"><B><font color="#74ACE6"><?=$c_name?><?=$c_id?></B></td>
		<td align="right"><font color="#74ACE6"><?=$c_uip?>&nbsp;&nbsp;<font color="#74ACE6"><?=$c_writetime?>
		<?if($mypageid){?>
			<IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>'">
		<?}else{?>
			<IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'">
		<?}?>
		
			
		</font></TD>
	</TR>
	<TR>
		<TD colspan="2"><?=$c_comment?></TD>
	</TR>
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>
<?}?>
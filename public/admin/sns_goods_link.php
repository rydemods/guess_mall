<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/instagram.php");
include("access.php");
#include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
//exdebug($_POST);

$mode = $_POST['mode'];
$del_media_id = $_POST['del_media_id'];
$del_prdcode  = $_POST['del_prdcode'];

switch($mode){
	case "del":
		$sql = "DELETE FROM tblsnsinstamedialink WHERE media_id = {$del_media_id} AND productcode='{$del_prdcode}'";
		pmysql_query($sql);
		if(pmysql_error()) echo "0"; else echo "1";
		exit;
		break;

	case "del_sns":
		$sql = "DELETE FROM tblsnsinstamedialink WHERE media_id = {$del_media_id}";
		pmysql_query($sql);
        //exdebug($sql);

        $sql = "DELETE FROM tblsnsinstamedia WHERE media_id = {$del_media_id}";
		pmysql_query($sql);
        //exdebug($sql);
		break;

	case "add":
		$media_id = $_POST['media_id'];
		foreach($media_id as $i => $v){
			if($_POST['relationProduct_'.$i][0]) $prd_id[$i] = $_POST['relationProduct_'.$i][0];
            if($_POST['sns_link_'.$i][0]) $arr_link[$i] = $_POST['sns_link_'.$i][0];
		}
		foreach($prd_id as $k => $productcode){
			$sql = "SELECT count(*) cnt FROM tblsnsinstamedialink WHERE media_id = {$media_id[$k]}";
			list($cnt) = pmysql_fetch_array(pmysql_query($sql));
			if($cnt==0)
				$sql = "INSERT INTO tblsnsinstamedialink (media_id,productcode) VALUES ({$media_id[$k]},'{$productcode}')";
			else
				$sql = "UPDATE tblsnsinstamedialink SET productcode = '{$productcode}' WHERE media_id = {$media_id[$k]}";
			pmysql_query($sql);
			//exdebug($sql);
			$msg =  pmysql_error() ? "상품연결에 오류가 발생했습니다." : "상품연결 등록되었습니다.";
		}

        foreach($arr_link as $k => $snslink){
			$sql = "SELECT count(*) cnt FROM tblsnsinstamedialink WHERE media_id = {$media_id[$k]}";
			list($cnt) = pmysql_fetch_array(pmysql_query($sql));
			if($cnt==0)
				$sql = "INSERT INTO tblsnsinstamedialink (media_id,sns_link) VALUES ({$media_id[$k]},'{$snslink}')";
			else
				$sql = "UPDATE tblsnsinstamedialink SET sns_link = '{$snslink}' WHERE media_id = {$media_id[$k]}";
			pmysql_query($sql);
			//exdebug($sql);
			$msg2 =  pmysql_error() ? "링크등록 중 오류가 발생했습니다." : "링크정보 등록되었습니다.";
		}
}
include("header.php");
?>
<style>
textarea.insta_txt { width:300px; height:150px; vertical-align:top; background-color:#eee; border:0; padding:3px }
ul#media li input[type=checkbox]{ vertical-align:top }
</style>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script>
$( document ).ready( function() {
<?php if($msg) echo "\talert('{$msg}');"; ?>
<?php if($msg2) echo "\talert('{$msg2}');"; ?>
<? /*
	$('#chkall').click( function() {
		$('ul#media input[type=checkbox]').prop( 'checked', this.checked );
	});
*/ ?>
});
function insta_search() {
	//document.form1.mode.value = "search";
	document.form1.submit();
}
function insta_add(){
	document.form1.mode.value = "add";
	document.form1.submit();
}
function insta_product_del(code,idx,media){
	$.post("<?=$_SERVER['PHP_SELF']?>", { mode: "del", del_media_id: media, del_prdcode:code },function(data) {
		if(data=="1") T_relationPrDel(code,idx);
	});
}
function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function snsDelete(media_id) {
    document.form1.mode.value = "del_sns";
    document.form1.del_media_id.value = media_id;
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; SNS 관리 &gt; <span>인스타그램 연동 리스트</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">인스타그램 연동</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>저장된 인스타그램 미디어와 쇼핑몰상품을 연결합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center">
					<a href="javascript:insta_search();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;
					<a href="javascript:insta_add()"><img src="images/btn_badd2.gif" border="0"></a>&nbsp;
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
	$sql = "SELECT COUNT(*) as t_count FROM tblsnsinstamedia";
	$result = pmysql_query($sql,get_db_conn());
	$paging = new Paging($sql,10,10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
					<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<?php include("layer_prlistPop.php"); ?>
					<input type=hidden name=block value="<?=$block?>">
					<input type=hidden name=gotopage value="<?=$gotopage?>">
					<input type=hidden name=mode>
					<input type=hidden name=del_prdcode>
					<input type=hidden name=del_media_id>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<colgroup>
					<col width=60></col>
					<col width=470></col>
					<col width=60></col>
					<col width=150></col>
					<col width=200></col>
					<col width=60></col>
					</colgroup>
					<TR align=center>
						<th>No</th>
						<th>인스타그램 미디어</th>
						<th></th>
						<th>쇼핑몰 연결상품</th>
						<th>쇼핑몰 연결링크</th>
						<th>삭제</th>
					</TR>
<?php
	$imagepath = $Dir.DataDir."shopimages/product/";

	//$sql = "SELECT a.*, b.productcode, c.productname, c.tinyimage FROM tblsnsinstamedia a LEFT JOIN tblsnsinstamedialink b ON a.media_id=b.media_id LEFT JOIN tblproduct c ON b.productcode=c.productcode ORDER BY a.id DESC";
	$sql = "SELECT a.*, b.productcode, c.productname, c.tinyimage, b.sns_link FROM tblsnsinstamedia a LEFT JOIN tblsnsinstamedialink b ON a.media_id=b.media_id LEFT JOIN tblproduct c ON b.productcode=c.productcode ORDER BY a.media_id DESC";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$idx = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
		$tinyimage = getProductImage($imagepath, $row->tinyimage );
		$cnt++;
?>
						<tr align=center>
							<td><?=$idx?></td>
							<td>
								<a href="<?=$row->link?>" target="_blank"><img src="<?=$row->image_thum?>" /></a>
								<textarea class="insta_txt"><?=$row->text?></textarea>
							</td>
							<td>
								<a href="javascript:T_layer_open('layer_product_sel','relationProduct_<?=$idx?>');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a>
							</td>
							<td>
								<input type='hidden' name='media_id[<?=$idx?>]' value="<?=$row->media_id?>" />
								<input type="hidden" name="limit_relationProduct_<?=$idx?>" id="limit_relationProduct_<?=$idx?>" value="1"/>
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px; padding:0' name="prList" id="check_relationProduct_<?=$idx?>">
								<colgroup>
									<col width=150></col>
									<col width=></col>
								</colgroup>
<?php if($row->productcode){ ?>
								<tr>
									<td style='border:0px'>
										<img width=150 height=150 src="<?=$tinyimage?>" />
										<input type='hidden' name='relationProduct_<?=$idx?>[]' class='relationProduct_<?=$idx?>' value='<?=$row->productcode?>' />
									</td>
									<td style='border:0px; padding:0 0 0 10px'>
										<?=addslashes($row->productname)?>&nbsp;&nbsp;
										<img src="images/icon_del1.gif" onclick="javascript:insta_product_del('<?=$row->productcode?>','relationProduct_<?=$idx?>','<?=$row->media_id?>');" border="0" style="cursor: hand;vertical-align:middle;" />
									</td>
								</tr>
<?php } ?>
								</table>
							</td>
                            <td style="text-align:left;">
                                &nbsp;링크 <input name="sns_link_<?=$idx?>[]" size="80" maxlength="80" value="<?=$row->sns_link?>" class=input>
                                <br><br>&nbsp;(내부일 경우는 /front/.... 외부일 경우는 http://www.cash-store.com/ 의 형식입니다.)
                            </td>
                            <td align=center><A HREF="javascript:snsDelete('<?=$row->media_id?>')"><img src="images/btn_del.gif"></A></td>
						</tr>
<?php
	}
	pmysql_free_result($result);
	if ($cnt==0) {
		echo "<tr><td colspan=6 align=center>저장된 인스타그램 미디어가 없습니다.</td></tr>";
	}
?>
					</TABLE>
					</form>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center">
					<a href="javascript:insta_search();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;
					<a href="javascript:insta_add()"><img src="images/btn_badd2.gif" border="0"></a>&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<div id="page_navi01" style="height:'40px'">
						<div class="page_navi">
							<ul>
								<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
							</ul>
						</div>
					</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>저장된 인스타그램 미디어 정보와 쇼핑몰 상품 연결</span></dt>
							<dd>- 인스타그램의 미디어를 확인 후, 연결상품을 조회해서 선택합니다.</dd>
							<dd>- 연결상품 선택 후 추가하기 버튼을 누르면 저장됩니다.</dd>
							<dd>- 저장되지 않은 상품의 이미지는 작게, 저장된 이미지는 크게 보여집니다.</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php
include("copyright.php");
?>

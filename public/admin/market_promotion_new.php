<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

include("calendar.php");
include("header.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$display_type       = $_POST['display_type'];
$keyword            = $_POST['keyword'];
$type               = $_POST['type'];
$disabled_idx       = $_POST['disabled_idx'];
$mode               = $_POST['mode'];
$s_checklist        = $_POST["s_checklist"];
$s_notchecklist     = $_POST["s_notchecklist"];

$search_event_type  = $_POST['search_event_type'];
$search_is_gnb      = $_POST['search_is_gnb'];
$search_hidden      = $_POST['search_hidden'];

$idx = $_POST['idx'];

if ( count($idx) >= 1 ) {
    $whereIdx = "'" . implode("','", $idx) . "'";
    
    $sql = "";
    if ( $mode == "gnb_set" ) {
        $sql  = "UPDATE tblpromo SET is_gnb = 1 WHERE idx in ({$whereIdx}) ";
    } else if ( $mode == "gnb_unset" ) {
        $sql  = "UPDATE tblpromo SET is_gnb = 0 WHERE idx in ({$whereIdx}) ";
    } else if ( $mode == "visible_set" ) {
        $sql  = "UPDATE tblpromo SET hidden = 1 WHERE idx in ({$whereIdx}) ";
    } else if ( $mode == "visible_unset" ) {
        $sql  = "UPDATE tblpromo SET hidden = 0 WHERE idx in ({$whereIdx}) ";
    }

    if ( !empty($sql) ) { $result = pmysql_query($sql); }
}

if( $type == 'vender_on' ){
	$vSql = "UPDATE tblpromo SET disabled = '0' WHERE idx='".$disabled_idx."'";
	pmysql_query( $vSql, get_db_conn() );
} else if( $type == 'vender_off' ) {
	$vSql = "UPDATE tblpromo SET disabled = '1' WHERE idx='".$disabled_idx."'";
	pmysql_query( $vSql, get_db_conn() );
}

//exdebug($display_type);exdebug($keyword);
/*
if($_POST['sword']){
	if($_POST['skey']=='title'){
		$where[]="title like '%".$_POST['sword']."%' ";
	}
}
*/

if($keyword){
// 	$where[] = "lower(title) like lower('%".$keyword."%') ";
	$search = trim($keyword);
	$temp_search = explode("\r\n", $search);
	$cnt = count($temp_search);
	
	$search_arr = array();
	for($i = 0 ; $i < $cnt ; $i++){
		array_push($search_arr, "'%".$temp_search[$i]."%'");
	}
	
	$where[] = " lower(title) LIKE any ( array[".implode(",", $search_arr)."] ) ";
}

if($display_type != 'ALL' && $display_type!=""){
	$where[] = "display_type = '".$display_type."' ";
}

// 20170511 검색 제약조건 수정 
// if ( !is_null($search_event_type) && $search_event_type != "" ) {
//     $where[] = "event_type = '" . $search_event_type . "' ";
// }

if ( !is_null($search_is_gnb) && $search_is_gnb != "" ) {
    $where[] = "is_gnb = " . $search_is_gnb . " ";
}

if ( !is_null($search_hidden) && $search_hidden != "" ) {
    $where[] = "hidden = " . $search_hidden . " ";
}

if ( count($where) > 0 ) {
    $where=" and ".implode(' and ',$where);
}

$selected[skey][$_POST['skey']]='selected';

$imagepath = $cfg_img_path[timesale];

$listnum = 20;
//$listnum = 5;

$sql = "select * from tblpromo where event_type in ('0','1') ".$where." order by idx::int desc";
//exdebug($sql);
$res = pmysql_query($sql,get_db_conn());
$total = pmysql_num_rows($res);
$paging = new newPaging((int)$total,10,$listnum);
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
$res = pmysql_query($sql,get_db_conn());


$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

?>


<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function changeSelectEventType(obj) {
    document.form1.search_event_type.value = $(obj).children("option:selected").val();
    document.form1.submit();
}

function changeSelectIsGnb(obj) {
    document.form1.search_is_gnb.value = $(obj).children("option:selected").val();
    document.form1.submit();

}

function changeSelectHidden(obj) {
    document.form1.search_hidden.value = $(obj).children("option:selected").val();
    document.form1.submit();
}

function event_pop(mode,idx){

	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="market_promotion_reg_new.php?mode="+mode+"&pidx="+idx;
}

function event_ins(mode,idx,seq){
	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="market_promotion_reg_new.php?mode="+mode+"&pidx="+idx+"&seq="+seq;
		}
	}else{
		document.location.href="market_promotion_reg_new.php?mode="+mode+"&pidx="+idx;
	}
}

function evnet_reg(idx){
	blockval=$('#block').val();
	gotoval=$('#gotopage').val();
	//product_test -> product_new 로 변경 2015 12 08 유동혁
	document.location.href="market_promotion_product_new.php?pidx="+idx;
	//document.location.href="market_promotion_product_new.php?pidx="+idx;
	
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

function vender_disabled( idx, type ){
	var disabledType = '';
	if( type == 'disabled_true' ){
		disabledType = 'vender_on'
	} else {
		disabledType = 'vender_off'
	}
	document.form1.type.value=disabledType;
	document.form1.disabled_idx.value=idx;
	document.form1.submit();
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='idx[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='idx[]']").attr("checked", false);
    }
}

function lbEdit() {
    if ( confirm("GNB에 등록하시겠습니까?") ) {
        /*
        var arrChkList = new Array();       // 체크된 것들
        var arrNotChkList = new Array();    // 체크되지 않은 것들
        $("input:checkbox[name='idx[]']").each(function(idx) {
            if ( $(this).is(":checked") ) {
                arrChkList.push($(this).val());
            } else {
                arrNotChkList.push($(this).val());
            }
        });
        */

        document.form1.s_checklist.value = "'" + arrChkList.join("','") + "'";
        document.form1.s_notchecklist.value = "'" + arrNotChkList.join("','") + "'";
        document.form1.mode.value = "modify";
        document.form1.submit();
    }
}

function changeStatus(mode) {
    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
        return;
    }

    switch(mode) {
        case 1:
            // GNB 등록
            document.form1.mode.value = "gnb_set";
            msg = "GNB 등록을 하시겠습니까?";
            break;
        case 2:
            // GNB 등록해제
            document.form1.mode.value = "gnb_unset";
            msg = "GNB 등록해제를 하시겠습니까?";
            break;
        case 3:
            // 노출 설정
            document.form1.mode.value = "visible_set";
            msg = "노출 설정을 하시겠습니까?";
            break;
        case 4:
            // 비노출 설정
            document.form1.mode.value = "visible_unset";
            msg = "비노출 설정을 하시겠습니까?";
            break;
    }

    if ( confirm(msg) ) {
        document.form1.submit();
    }
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>기획전 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
            <input type="hidden" name="mode" value="<?=$mode?>" />
			<input type="hidden" id="type" name="type" />
			<input type="hidden" id="num" name="num" value="<?=$num?>" />
			<input type="hidden" id="htmlmode" name="htmlmode" value='wysiwyg' />
			<input type="hidden" id="block" name="block" value="<?=$_REQUEST['block']?>" />
			<input type="hidden" id="gotopage" name="gotopage" value="<?=$gotopage?>" />
			<input type="hidden" id="board" name="board" value=<?=$board?> />
			<input type='hidden' id='disabled_idx' name='disabled_idx' value='' />
            <input type=hidden name='s_checklist' value='<?=$s_checklist?>'>
            <input type=hidden name='s_notchecklist' value='<?=$s_notchecklist?>'>
            <input type=hidden name='search_event_type' value='<?=$search_event_type?>'>
            <input type=hidden name='search_is_gnb' value='<?=$search_is_gnb?>'>
            <input type=hidden name='search_hidden' value='<?=$search_hidden?>'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">기획전 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>기획전 검색</span></div>
					<div class="table_style01 pt_20">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>타이틀 검색</span></th>
								<td>
								<!--  
									<input class="w200" type="text" name="keyword" value="<?=$keyword?>"/>
								-->
								<textarea rows="2" cols="10" class="w200" name="keyword" id="keyword" style="resize:none;vertical-align:middle;"><?=$keyword?></textarea>
								</td>
								<th><span>진열상태</span></th>
								<?
								/*
								if($display_type=='ALL') echo "selected";
								if($display_type=='A') echo "selected";
								if($display_type=='P') echo "selected";
								if($display_type=='M') echo "selected";
								if($display_type=='N') echo "selected";
								if($display_type=='S') echo "selected";
								if($display_type=='D') echo "selected";
								if($display_type=='B') echo "selected";
								if($display_type=='C') echo "selected";
								*/
								?>
								<td>
									<select name="display_type" id="display_type" style="width:80px;height:32px;vertical-align:middle;">
										<option value="ALL" <?php if ($display_type == "ALL") echo "selected" ?> >선택</option>
										<option value="A" <?php if ($display_type == "A") echo "selected" ?> >모두</option>
										<option value="P" <?php if ($display_type == "P") echo "selected" ?> >PC만</option>
										<option value="M" <?php if ($display_type == "M") echo "selected" ?> >모바일만</option>
										<option value="N" <?php if ($display_type == "N") echo "selected" ?> >보류</option>
										<!-- <option value="S" >PC 비전시</option>
										<option value="D" >모바일 비전시</option>
										<option value="B" >fitflop 모바일만</option>
										<option value="C" >fitflop 모바일 비전시</option> -->
									</select>
								</td>
							</tr>
						</table>
						<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색"></a></p>
					</div>
				</td>
			</tr>

            <tr>
                <td align="right">
				
                    이벤트 종류 : 
                    <select name="search_event_type" onchange="javascript:changeSelectEventType(this);">
                        <option value="">========전체=======</option>
                        <?php foreach($arrPromotionList as $key => $val) {
							if($key=="1" or $key=="0"){
						?>
                            <option value="<?=$key?>" <?php if ( $key == $search_event_type ) echo "selected"; ?> ><?=$val?></option>
                        <?php }} ?>
                    </select> &nbsp;

					<!--
                    GNB 노출 : 
                    <select name="search_is_gnb" onchange="javascript:changeSelectIsGnb(this);">
                        <option value="">========전체=======</option>
                        <option value="1" <?php if ( $search_is_gnb == "1" ) echo "selected"; ?> >노출</option>
                        <option value="0" <?php if ( $search_is_gnb == "0" ) echo "selected"; ?> >비노출</option>
                    </select> &nbsp;-->

                    사용 : 
                    <select name="search_hidden" onchange="javascript:changeSelectHidden(this);">
                        <option value="">========전체=======</option>
                        <option value="1" <?php if ( $search_hidden == "1" ) echo "selected"; ?>>노출</option>
                        <option value="0" <?php if ( $search_hidden == "0" ) echo "selected"; ?>>비노출</option>
                    </select>
                </td>
            </tr>

            <tr><td height="10"></td></tr>

			<tr>
				<td>
				<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					    <col width=50>
					    <col width=50>
                        <col width=80>
                        <col width=auto>

                        <col width=230>
                        <col width=210>
					    <!--<col width=70>-->
					    <col width=70>
                        <col width=70>
                        <col width=100>
                        <col width=80>
                        <col width=80>
                        <col width=80>
					<TR align=center>
						<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
						<th>No</th>
						<th>이벤트 종류</th>
						<th>기획전 타이틀</th>

						<th>기간</th>
						<th>Url</th>
						<!--<th>GNB 노출</th>-->
						<th>사용</th>
						<th>진열상태</th>
						<th>등록일</th>
						<th>상품 등록</th>
						<th>수정</th>
						<th>삭제</th>
					</TR>
				<?
				while($row=pmysql_fetch_object($res)) {
					$cnt++;
				?>
					<TR>
                    <TD><input type="checkbox" name="idx[]" value="<?=$row->idx?>"></TD>
					<TD><?=$cnt?></TD>
					<!--TD><?=$venderlist[$row->vender]->com_name?></TD-->
					<!--TD>
<?
						if( $row->vender > 0 ){
							if( $row->disabled == '1' ){
								echo "<a href=\"javascript:vender_disabled('".$row->idx."', 'disabled_true')\" ><img src='images/icon_off.gif' ></a>";
							} else {
								echo "<a href=\"javascript:vender_disabled('".$row->idx."', 'disabled_false')\" ><img src='images/icon_on.gif' ></a>";
							}
						}
?>
					</TD-->

                    <?php
                        $eventName = $arrPromotionList[$row->event_type];

                        $detailViewIcon = "-";
                        if ( $row->event_type == 2 ) {
                            $detailViewIcon = "<img src=\"images/btn_viewbbs.gif\" onClick=\"popup_window('popup.event_comment.php?no=" . $row->idx . "', 870, 800)\" />";
                        } else if ( $row->event_type == 3 ) {
                            $detailViewIcon = "<img src=\"images/btn_viewbbs.gif\" onClick=\"popup_window('popup.event_photo.php?no=" . $row->idx . "', 870, 800)\" />";
                        }
                    ?>

					<TD style="text-align:center;"><?=$eventName?></TD>
					<TD style="text-align:left;"><?=$row->title?></TD>

					<TD><?=$row->start_date?> <?=substr($row->start_date_time,0,2)?>:<?=substr($row->start_date_time,2,4)?> &nbsp;~
					&nbsp;<?=$row->end_date?> <?=substr($row->end_date_time,0,2)?>:<?=substr($row->end_date_time,2,4)?></TD>
					<TD>
					<?
						
						if($row->display_type=="A"){
							?><a target="_balnk" href="/front/promotion_detail.php?idx=<?=$row->idx?>&event_type=<?=$row->event_type?>"> <?echo "/front/promotion_detail.php?idx=".$row->idx."&event_type=".$row->event_type;?></a>
								<a target="_balnk" href="/m/promotion_detail.php?idx=<?=$row->idx?>&event_type=<?=$row->event_type?>"> <?echo "/m/promotion_detail.php?idx=".$row->idx."&event_type=".$row->event_type;?></a> <?
						}
						if($row->display_type=="P"){
							?><a target="_balnk" href="/front/promotion_detail.php?idx=<?=$row->idx?>&event_type=<?=$row->event_type?>"> <?echo "/front/promotion_detail.php?idx=".$row->idx."&event_type=".$row->event_type;?></a> <?
						}
						if($row->display_type=="M"){
							?><a target="_balnk" href="/m/promotion_detail.php?idx=<?=$row->idx?>&event_type=<?=$row->event_type?>"> <?echo "/front/promotion_detail.php?idx=".$row->idx."&event_type=".$row->event_type;?></a> <?
						}
						
					?>
					</TD>
<!--
                    <td>
                        <?php
                            if ( $row->is_gnb === '1' ) {
                                echo "노출";
                            } else {
                                echo "비노출";
                            }
                        ?>
                    </td>-->
                    <td>
                        <?php
                            if ( $row->hidden === '1' ) {
                                echo "노출";
                            } else {
                                echo "비노출";
                            }
                        ?>
                    </td>

					<td><?
						switch($row->display_type){
							case 'A' : echo "ALL"; break;
							case 'P' : echo "PC"; break;
							case 'M' : echo "모바일"; break;
							case 'N' : echo "보류"; break;
							case 'S' : echo "PC 비전시"; break;
							case 'D' : echo "모바일 비전시"; break;
							case 'B' : echo "fitflop 모바일만"; break;
							case 'C' : echo "fitflop 모바일 비전시"; break;
						}
					?></td>
					<!--TD><?=$row->no_coupon?></TD-->
					<TD><?=$row->rdate?></TD>
                    <?php if ( $row->event_type == "1" or $row->event_type == "0" ) { ?>
                        <TD><a href="javascript:evnet_reg(<?=$row->idx?>);"><img src="images/btn_add2.gif" border="0"></a></TD>
                    <?php } else { ?>
                        <TD>-</TD>
                    <?php } ?>
					<TD><a href="javascript:event_pop('mod','<?=$row->idx?>');"><img src="images/btn_edit.gif" border="0"></a></TD>
					<TD><a href="javascript:event_ins('del','<?=$row->idx?>','<?=$row->display_seq?>');"><img src="images/btn_del.gif" border="0"></a></TD>
				    </TR>
				<?
				}
				pmysql_free_result($res);
				if ($cnt==0) {
					echo "<TR><TD colspan=12 align=center>등록된 목록이 없습니다.</TD></TR>";
				}
?>

				</TABLE>

				 <div class="list_search" style="width:100%;text-align:right;padding-top:20px; display:none;">

					<select class="option" name="skey">
						<option value="title" <?=$selected['skey']['title']?>>타이틀</option>
					</select>
					<input type="text" class="bar" name="keyword2222" value="<?=$_POST['keyword']?>"/>
					<input type="image" src="../admin/images/btn_search_com.gif" style="vertical-align:middle">
				 </div>
				</div>
<?

							echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
							echo "<div class=\"page_navi\">";
							echo "<ul>";
							echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</ul>";
							echo "</div>";
							echo "</div>";
?>

				</td>
			</tr>
			<tr>
				<td>
                    <div style="text-align:center;padding-bottom:40px;">
                        <!--<img src="../admin/images/btn_gnb_reg.png" onclick="javascript:changeStatus(1);"/>
                        <img src="../admin/images/btn_gnb_cancel.png" onclick="javascript:changeStatus(2);"/>-->
                        <img src="../admin/images/btn_visible_set.png" onclick="javascript:changeStatus(3);"/>
                        <img src="../admin/images/btn_visible_unset.png" onclick="javascript:changeStatus(4);"/>
						<img src="../admin/images/btn_confirm_com.gif" onclick="javascript:event_pop('ins');"/>
                    </div>
                </td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

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
<script language="javascript">
    function popup_window(src,width,height)
    {
        window.open(src,'','width='+width+',height='+height+',scrollbars=1');
    }
    </script>
<?=$onload?>
<?php
include("copyright.php");

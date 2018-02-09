<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

$mode               = $_POST['mode'];
$s_checklist        = $_POST["s_checklist"];
$s_notchecklist     = $_POST["s_notchecklist"];
$idxs=$_POST["idxs"];

// if ( $s_checklist != "''" && $s_checklist != "" ) {
//     if ( $mode == "best_review_set" ) {
//         $sql  = "UPDATE tblproductreview SET best_type = 1 WHERE num in ({$s_checklist})  ";
//     } else {
//         $sql  = "UPDATE tblproductreview SET best_type = 0 WHERE num in ({$s_checklist})  ";
//     }

//     $result = pmysql_query($sql);
// }

/*$regdate = $_shopdata->regdate;*/
$regdate = date('Ymd');
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*30));

$search_all     = $_POST["search_all"];
$type           = $_POST["type"];
$reviewtype     = $_POST["reviewtype"];
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$vperiod        = (int)$_POST["vperiod"];
$search         = $_POST["search"];
$date           = $_POST["date"];
$productcode    = $_POST["productcode"];
$review_class   = $_POST["review_class"]; // 빈값 : 전체, 0 : 텍스트A/S, 1 : 포토A/S

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
$s_check=$_POST["s_check"];
$brandname=$_POST["brandname"];
if(!$s_check) $s_check="0";

if($s_check=="2") {
	$search="";
	$search_style="disabled style=\"background:#f4f4f4\"";
}
${"check_s_check".$s_check} = "checked";
${"check_vperiod".$vperiod} = "checked";


$sql = "SELECT review_type FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$review_type = $row->review_type;
if($row->review_type=="N") {
	echo "<script>alert(\"A/S 기능이 설정이 안되었습니다.\");parent.topframe.location.href=\"JavaScript:GoMenu(1,'shop_review.php')\";</script>";exit;
}
pmysql_free_result($result);

if($mode=="delete" && ord($idxs)) {
	$sql = "UPDATE tblasinfo SET status = 2 WHERE idx IN ({$idxs}) ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"선택하신 게시물을 삭제하였습니다.\");}</script>";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function SearchAll(){
	document.rForm.search_all.value="all";
	document.rForm.submit();
}

function CheckSearch() {
	s_check="";
	for(i=0;i<document.form1.s_check.length;i++) {
		if (document.form1.s_check[i].checked) {
			s_check=document.form1.s_check[i].value;
			break;
		}
	}
    /*
	if (s_check!="2" && s_check!="3" && s_check!="4") {
		if (document.form1.search.value.length<3) {
			if(document.form1.search.value.length==0) alert("검색어를 입력하세요.");
			else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
			document.form1.search.focus();
			return;
		}
	}
    */
	document.form1.type.value="up";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function OnChangeSearchType(val) {
	if (val==2) {
		document.form1.search.disabled=true;
		document.form1.search.style.background="#f4f4f4";
		document.form1.brandname.disabled=true;
		document.form1.brandname.style.background="#f4f4f4";
	} else if(val==4){
		document.form1.search.disabled=true;
		document.form1.search.style.background="#f4f4f4";
		document.form1.brandname.disabled=false;
		document.form1.brandname.style.background="";		
	}	
	 else {
		document.form1.search.disabled=false;
		document.form1.search.style.background="";
		document.form1.brandname.disabled=true;
		document.form1.brandname.style.background="#f4f4f4";		
	}
}

function Searchid(id) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="";
	document.form1.search.value=id;
	document.form1.s_check[1].checked=true;
	document.form1.submit();
}
function SearchProduct(prname) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="#FFFFFF";
	document.form1.search.value=prname;
	document.form1.s_check[0].checked=true;
	document.form1.submit();
}


function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.form4.search.value=id;
	document.form4.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.action="product_register.add.php";
		document.form2.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form2.action="product_register.set.php";
		document.form2.target="";
	}
	document.form2.submit();
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
function AuthReview(date,prcode) {
	if(confirm('해당 A/S를 인증하시겠습니까?')){
		document.rForm.type.value="auth";
		document.rForm.date.value=date;
		document.rForm.productcode.value=prcode;
		document.rForm.submit();
	}
}
function DeleteReview(no) {
	if(confirm('해당 A/S를 삭제하시겠습니까?')){
		document.form1.mode.value="delete";
		document.form1.idxs.value=no;
		document.form1.submit();
	}
}
function ReserveSet(id,date,prcode) {
	window.open("about:blank","reserve_set","width=250,height=150,scrollbars=no");
	document.form5.type.value="review";
	document.form5.id.value=id;
	document.form5.date.value=date;
	document.form5.productcode.value=prcode;
	document.form5.target="reserve_set";
	document.form5.submit();
}
function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=400,height=320,scrollbars=no");
	document.orderform.target="orderinfo";
	document.orderform.id.value=id;
	document.orderform.submit();
}
function ReviewReply(date,prcode) {
	window.open("about:blank","reply","width=400,height=500,scrollbars=yes");
	document.replyform.target="reply";
	document.replyform.date.value=date;
	document.replyform.productcode.value=prcode;
	document.replyform.submit();
}
function GoPage(block,gotopage) {
	document.rForm.block.value = block;
	document.rForm.gotopage.value = gotopage;
	document.rForm.submit();
}

// function allCheck(obj) {
//     if ( $(obj).is(":checked") ) {
//         $("input:checkbox[name='idx[]']").attr("checked", true);
//     } else {
//         $("input:checkbox[name='idx[]']").attr("checked", false);
//     }
// }

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	try {
		cnt=document.form1.delcheck.length;
		for(i=1;i<=cnt;i++){
			document.form1.delcheck[i].checked=chkval;
		}
	} catch(e) {}
}

function lbEdit() {
    if ( confirm("베스트A/S에 등록하시겠습니까?") ) {

        var arrChkList = new Array();       // 체크된 것들
        var arrNotChkList = new Array();    // 체크되지 않은 것들
        $("input:checkbox[name='idx[]']").each(function(idx) {
            if ( $(this).is(":checked") ) {
                arrChkList.push($(this).val());
            } else {
                arrNotChkList.push($(this).val());
            }
        });

        document.form1.s_checklist.value = "'" + arrChkList.join("','") + "'";
        document.form1.s_notchecklist.value = "'" + arrNotChkList.join("','") + "'";
        document.form1.mode.value = "modify";
        document.form1.submit();
    }
}

function changeBestReview(mode) {

    var arrChkList = new Array();       // 체크된 것들
    $("input:checkbox[name='idx[]']").each(function(idx) {
        if ( $(this).is(":checked") ) {
            arrChkList.push($(this).val());
        }
    });

    if ( arrChkList.length == 0 ) {
        alert("하나 이상을 선택해 주세요.");
        return;
    }

    var msg = "베스트A/S 해제하시겠습니까?";
    var modeVal = "best_review_unset";
    if ( mode === 1 ) { 
        msg = "베스트A/S 등록하시겠습니까?";
        modeVal = "best_review_set";
    }

    if ( confirm(msg) ) {
        document.form1.s_checklist.value = "'" + arrChkList.join("','") + "'";
        document.form1.mode.value = modeVal;
        document.form1.submit();
    }
}

// 기존 1:1 문의폼양식
function ViewAsInfo(idx) {
	window.open("about:blank","asinfo_pop","width=960,height=650,scrollbars=yes");
	document.asinfoform.idx.value=idx;
	document.asinfoform.submit();
}

// 선택 삭제
function CheckDelete(form) {
	try {
		idxs="";
		for(i=1;i<form.delcheck.length;i++) {
			if(form.delcheck[i].checked) {
				idxs+=","+form.delcheck[i].value;
			}
		}
		if(idxs.length==0) {
			alert("삭제할 게시물을 선택하세요.");
			return;
		}
		if(confirm("선택하신 게시물을 삭제하시겠습니까?")) {
			idxs=idxs.substring(1,idxs.length);
			form.mode.value="delete";
			form.idxs.value=idxs;
			form.submit();
		}
	} catch(e){
		alert(e);
	}
}
</script>
<style>
a.search_btn {
  display: inline-block;
  color: #fff;
  height: 26px;
  padding: 0px 5px;
  text-align: center;
  border-radius: 2px;
  font: 12px/24px bold;
  background-color: #44474c;
}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>A/S고객 게시판 관리</span></p></div></div>
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
			<?php include("menu_cscenter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">A/S 문의 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체 상품들의 A/S를 관리할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">A/S 문의 검색</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=idxs>
            <input type=hidden name='s_checklist' value='<?=$s_checklist?>'>
            <input type=hidden name='s_notchecklist' value='<?=$s_notchecklist?>'>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <tr>
                    <th><span>문의 타입</span>
                    <td>
                        <input type="radio" id="review_class1" name="review_class" value="" <? if ( $review_class == "" ) { echo "checked"; } ?>/> <label for=review_class1>전체</label>&nbsp;
                        <input type="radio" id="review_class2" name="review_class" value="AS접수" <? if ( $review_class == "AS접수" ) { echo "checked"; } ?>/> <label for=review_class2>AS접수</label>&nbsp;
                        <input type="radio" id="review_class3" name="review_class" value="제품도착" <? if ( $review_class == "제품도착" ) { echo "checked"; } ?> /> <label for=review_class3>제품도착</label>&nbsp;
                        <input type="radio" id="review_class4" name="review_class" value="심의중" <? if ( $review_class == "심의중" ) { echo "checked"; } ?> /> <label for=review_class4>심의중</label>&nbsp;
                        <input type="radio" id="review_class5" name="review_class" value="수선중" <? if ( $review_class == "수선중" ) { echo "checked"; } ?> /> <label for=review_class5>수선중</label>&nbsp;
                        <input type="radio" id="review_class6" name="review_class" value="수선완료" <? if ( $review_class == "수선완료" ) { echo "checked"; } ?> /> <label for=review_class6>수선완료</label>&nbsp;
                        <input type="radio" id="review_class7" name="review_class" value="고객발송" <? if ( $review_class == "고객발송" ) { echo "checked"; } ?> /> <label for=review_class7>고객발송</label>&nbsp;
                        <input type="radio" id="review_class8" name="review_class" value="AS반품처리" <? if ( $review_class == "AS반품처리" ) { echo "checked"; } ?> /> <label for=review_class8>AS반품처리</label>&nbsp;
                        <input type="radio" id="review_class9" name="review_class" value="반품처리" <? if ( $review_class == "반품처리" ) { echo "checked"; } ?> /> <label for=review_class9>반품처리</label>&nbsp;
                        <input type="radio" id="review_class10" name="review_class" value="교환처리" <? if ( $review_class == "교환처리" ) { echo "checked"; } ?> /> <label for=review_class10>교환처리</label>&nbsp;
                    </td>
                </tr>
				<TR>
					<th><span>검색기간 선택</span></th>
					<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
					</td>
				</TR>
				<TR>
					<th><span>검색조건 선택</span></th>
					<TD><input type=radio name=s_check value="0" onClick="OnChangeSearchType(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>상품명으로 검색</label>&nbsp;&nbsp;
					<input type=radio name=s_check value="1" onClick="OnChangeSearchType(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>작성자로 검색</label>&nbsp;&nbsp;
					</TD>
				</TR>

				<TR>
					<th><span>검색어 입력</span></th>
					<TD><input name=search size=47 value="<?=$search?>" <?=$search_style?> class="input"> 
						<a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle  border="0"></a>&nbsp;&nbsp;
					</TD>
				</TR>				
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색 내역</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="20"></col>
				<col width="100"></col>
				<col width="140"></col>
				<col width=""></col>
				<col width="80"></col>
				<col width="80"></col>
				<TR align=center>
				<!-- 
					<th><input type="checkbox" onClick="javascript:allCheck(this);"/></th>
				 -->
					<th><INPUT onclick=CheckAll() type=checkbox name=allcheck></th>
					<th>등록일</th>
					<th>작성자 정보</th>
					<th>상품명/문의내용<!-- 및 <FONT color=red>＊</FONT>답글--></th>
					<th>문의 상태</th>
					<th width="51">삭제</th>
				</TR>
				<input type=hidden name=delcheck>
<?php
				$qry.= "WHERE a.productcode = b.productcode AND to_char(a.date, 'yyyymmddhhmmss') >= '{$search_s}' AND to_char(a.date, 'yyyymmddhhmmss') <= '{$search_e}' AND status != 2";
				if (strlen(trim($search))>2) {
					if($s_check=="0") {
						$qry.= "AND (b.productname LIKE '%{$search}%' OR a.content LIKE '%{$search}%') ";
					} else if ($s_check=="1") {
						//$qry.= "AND a.id = '{$search}' ";
						$qry.= "AND a.name LIKE '%{$search}%' ";
					}
				}
				if ( $review_class != "" ) {
					$qry .= "AND a.type_mode = '{$review_class}' ";
				}
				$sql = "SELECT count(*) FROM tblasinfo a, tblproduct b {$qry} ";
				
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT b.minimage, a.id,a.name,a.subject,a.content,to_char(a.date, 'yyyy-mm-dd')as date,a.productcode,b.productname,b.tinyimage,b.selfcode,b.assembleuse, a.type_mode, a.up_filename, a.ori_filename, a.idx ";
				$sql.= "FROM tblasinfo a, tblproduct b {$qry} ";
				$sql.= "ORDER BY a.idx desc ";
// 				exdebug($sql);
// 				exit();
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$contents=explode("=",$row->content);
					
					echo "<tr>\n";
                    echo "  <td><input type=\"checkbox\" name=\"delcheck\" value=\"{$row->idx}\" ></td>\n";
					echo "	<TD align=center class=\"td_con2\">".$row->date."</td>\n";
					echo "	<TD>";
					echo "	<NOBR><TABLE cellSpacing=0 cellPadding=0 border=0 width=\"100%\">";
					if (ord($row->id)) {
						echo "	<TR><TD style=\"text-align:left;border-bottom:none;word-break:break-all;\"><img src=\"images/icon_name.gif\" border=\"0\" align=absMiddle> <A HREF=\"javascript:MemberView('{$row->id}');\">[<U>{$row->name}</U>]</A></td></tr>\n";
						echo "	<TR><TD style=\"text-align:left;border-bottom:none;\"><img src=\"images/icon_id.gif\" border=\"0\" align=absMiddle> <A HREF=\"javascript:Searchid('{$row->id}');\">[<U>{$row->id}</U>]</A></td></tr>\n";
						echo "	<TR><TD style=\"text-align:left;border-bottom:none;\"><img src=\"images/icon_order.gif\" border=\"0\" align=absMiddle> <A HREF=\"javascript:OrderInfo('{$row->id}');\">[<U>내역확인</U>]</A></td></tr>\n";
					} else {
						echo "	<TR><TD style=\"text-align:left;border-bottom:none;\"><img src=\"images/icon_name.gif\" border=\"0\" align=absMiddle> [<U>{$row->name}</U>]</td></tr>\n";
					}
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<TD>";

					echo "	<div class=\"ta_l\"> \n";
					echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
					echo "	<tr>\n";
					echo "		<td rowspan='2' style='padding:10px; border:0px;'><img src='".getProductImage($Dir.DataDir.'shopimages/product/',$row->minimage)."' width='50'></td>\n";
					echo "		<td style=\"text-align:left;border-bottom:none;word-break:break-all;\">\n";
					echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
					echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','')\"><font color=#3D3D3D><u>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</u></font></a>";
					echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><IMG src=\"images/icon_newwin.gif\" align=absMiddle border=0 ></a>";
					echo "		</span>\n";
					echo "		<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
					echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
					echo "		<tr bgcolor=#FFFFFF>\n";
					if (ord($row->tinyimage)) {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src='".getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage)."' width='100%'></td>\n";
					} else {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src={$Dir}images/product_noimg.gif></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					// 내용클릭시 기존 1:1 수정폼 호출
					echo "		<td style=\"text-align:left;padding-top:3;border-bottom:none;\"><table border=0 cellpadding=0 cellspacing=0><tr><td valign='top' style='BORDER-BOTTOM: #ffffff 1px solid'><img src=\"images/icon_contents.gif\" border=\"0\" align=absMiddle hspace=\"2\"></td><td valign='top' style='text-align:left;BORDER-BOTTOM: #ffffff 1px solid'><a href=\"JavaScript:ViewAsInfo('{$row->idx}')\" title=\"".htmlspecialchars($row->subject)."\"><b>".titleCut(38,$row->subject)."</b><br>".titleCut(38,htmlspecialchars($contents[0]))."</a></td></tr></table> ";
					if(ord($contents[1])) echo "<font color=red>＊</font>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	</div> \n";
					echo "	<td align=center>".$row->type_mode."</td>";
					echo "	<TD align=center width=\"59\">";
					echo "	<a href=\"javascript:DeleteReview('{$row->idx}');\"><img src=\"images/btn_del.gif\"  border=\"0\"></a>";
					echo "	</td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan=6 align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align=center width="100%" class="font_size">
							<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page ?>
						</td>
					</tr>
					<tr>
						<td><a href="javascript:CheckDelete(document.form1);"><img src="images/btn_del2.gif"  border="0" vspace="3" hspace="3"></a></td>
					</tr>
<?php
// 				echo "<tr>\n";
// 				echo "	<td align=center width=\"100%\" class=\"font_size\">\n";
// 				echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
// 				echo "	</td>\n";
// 				echo "</tr>\n";
?>

				</table>
				</td>
			</tr>
			</form>
			

			<form name=rForm action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=reviewtype value="<?=$reviewtype?>">
            <input type=hidden name=review_class value="<?=$review_class?>">
			<input type=hidden name=vperiod value="<?=$vperiod?>">
			<input type=hidden id="search_all" name="search_all" value="<?=$search_all?>">
			</form>

			<form name=form2 action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=productcode value="<?=$productcode?>">
			</form>

			<form name=form4 action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=orderform action="orderinfopop.php" method=post>
			<input type=hidden name=id>
			</form>

			<form name=replyform action="product_reviewreply.php" method=post>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			</form>

			<form name=form5 action="reserve_money_new.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			</form>
			
			<form name=asinfoform action="community_asinfo_pop.php" method=post target="asinfo_pop">
			<input type=hidden name=idx>
			</form>
			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품 A/S 관리</span></dt>
							<dd>
							- 회원아이디로 검색시 정확한 아이디 입력을 하셔야만 검색이 됩니다.<br>
							- [회원이름] 클릭시 해당 회원의 정보를 확인하실 수 있습니다.<br>
							- [회원아이디] 클릭시 해당 회원아이디로 A/S 검색이 이루어집니다.<br>
							- [내역확인] 클릭시 해당 회원의 구매내역을 확인할 수 있습니다.<br>
							- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.<br>
							- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.<Br>
							- A/S 클릭시 해당 A/S의 전체 내용 및 답변을 등록할 수 있습니다.<br>
							- [적립금 지급] 버튼 클릭시 해당 A/S 작성자에게 적립금을 지급/차감할 수 있습니다.<br>
							- [다른A/S 보기] 버튼 클릭시 해당 상품명으로 A/S 검색이 이루어집니다.<br>
							- [삭제] 버튼 클릭시 해당 A/S가 삭제됩니다.
							</dd>
	
						</dl>
						<dl>
							<dt><span>상품 A/S 관리 주의사항</span></dt>
							<dd>
							- 삭제된 A/S는 복원되지 않으므로 신중히 처리하시기 바랍니다.<br>
							- 적립금 지급으로 인한 적립/차감된 적립금은 복원되지 않으므로 신중히 처리하시기 바랍니다.
							</dd>

						</dl>
						<dl>
							<dt><span>상품 A/S 문의타입 관련 설명</span></dt>
							<dd>
							- AS접수 : 고객 최초 접수.<br>
							- 제품도착 : 제품 도착 상태.<br>
							- 심의중 : AS 사유 및 확인.<br>
							- 수선중 : 수선진행.<br>
							- 수선완료 : 수선처리완료.<br>
							- 고객발송 : 고객에게 발송.<br>
							- AS반품처리 : 제품 불량 반품.<br>
							- 반품처리 : 고객 과실 반품.<br>
							- 교환처리 : 타제품으로 교환.
							</dd>

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
<?=$onload?>
<?php 
include("copyright.php");

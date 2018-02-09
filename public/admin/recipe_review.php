<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/recipe.class.php");
include("access.php");
include("calendar.php");





####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate = $_shopdata->regdate;
$CurrentTime = time();
//$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",strtotime("-7 day"));
$period[2] = date("Y-m-d",strtotime("-14 day"));
$period[3] = date("Y-m-d",strtotime("-1 month"));

$type=$_REQUEST["type"];
$reviewtype=$_REQUEST["reviewtype"];
$search_start=$_REQUEST["search_start"];
$search_end=$_REQUEST["search_end"];
$vperiod=(int)$_REQUEST["vperiod"];
$search=$_REQUEST["search"];
$date=$_REQUEST["date"];
$productcode=$_REQUEST["productcode"];
$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
$s_check=$_REQUEST["s_check"];
$recipe_num=$_REQUEST["recipe_num"];
$best_type=$_REQUEST["best_type"];
$mode=$_REQUEST["mode"];

if($mode=="best_change"){
	foreach($recipe_num as $k){
		$best_num=$best_type[$k]?$best_type[$k]:"0";
		$qry="update tblrecipecomment set best_type='".$best_num."' where num='".$k."'";
		pmysql_query($qry);
	}
}
if(!$s_check) $s_check="0";

if($s_check=="2") {
	$search="";
	$search_style="disabled style=\"background:#f4f4f4\"";
}
${"check_s_check".$s_check} = "checked";
${"check_vperiod".$vperiod} = "checked";



$recipe = new RECIPE();
$param[page_no] = $_REQUEST[page_no];
$param[search_start] = $search_start;
$param[search_end] = $search_end;
$param[search_field] = $_REQUEST[search_field];
$param[search_word] = $_REQUEST[search_word];
$recipe->setSearch($param);
$list = $recipe->getRecipeCommentList();



$sql = "SELECT review_type FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$review_type = $row->review_type;
if($row->review_type=="N") {
	echo "<script>alert(\"리뷰 기능이 설정이 안되었습니다.\");parent.topframe.location.href=\"JavaScript:GoMenu(1,'shop_review.php')\";</script>";exit;
}
pmysql_free_result($result);

if ($type=="delete" && ord($date)) {
	$sql = "DELETE FROM tblproductreview WHERE productcode = '{$productcode}' AND date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	$onload = "<script>window.onload=function(){alert('해당 레시피리뷰 삭제가 완료되었습니다.');}</script>\n";
} else if ($type=="auth" && ord($date)) {
	$sql = "UPDATE tblproductreview SET display = 'Y' ";
	$sql.= "WHERE productcode = '{$productcode}' AND date = '{$date}'";
	pmysql_query($sql,get_db_conn());
	$onload = "<script>window.onload=function(){alert('해당 레시피리뷰 인증이 완료되었습니다.');}</script>\n";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	s_check="";
	for(i=0;i<document.form1.s_check.length;i++) {
		if (document.form1.s_check[i].checked) {
			s_check=document.form1.s_check[i].value;
			break;
		}
	}
	if (s_check!="2") {
		if (document.form1.search.value.length<3) {
			if(document.form1.search.value.length==0) alert("검색어를 입력하세요.");
			else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
			document.form1.search.focus();
			return;
		}
	}
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
	} else {
		document.form1.search.disabled=false;
		document.form1.search.style.background="";
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
	if(confirm('해당 리뷰를 인증하시겠습니까?')){
		document.rForm.type.value="auth";
		document.rForm.date.value=date;
		document.rForm.productcode.value=prcode;
		document.rForm.submit();
	}
}
function DeleteReview(date,prcode) {
	if(confirm('해당 리뷰를 삭제하시겠습니까?')){
		document.rForm.type.value="delete";
		document.rForm.date.value=date;
		document.rForm.productcode.value=prcode;
		document.rForm.submit();
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
	window.open("about:blank","reply","width=400,height=500,scrollbars=no");
	document.replyform.target="reply";
	document.replyform.date.value=date;
	document.replyform.productcode.value=prcode;
	document.replyform.submit();
}

function GoPage(page_no) {
	document.form1.page_no.value = page_no;
	document.form1.submit();
}

function reviewDetail(num){
	window.open("recipe_review_view.php?num="+num,"register","width=820,height=700,scrollbars=yes,status=no");
}

function delRecipe(num){
	if(confirm(' 정말 삭제하시겠습니까? \n 삭제시 답글도 함께 삭제되며 삭제한 데이터는 복구가 불가능합니다.')){
		document.form1.method="post";
		document.form1.action="recipe_indb.php";
		document.form1.num.value=num;
		document.form1.module.value="recipe_contents";
		document.form1.mode.value="del_comment";
		document.form1.submit();
	}
}

function bestRecipe(){
	if(confirm(' 선택된 리뷰를 베스트 리뷰로 등록하시겠습니까? ')){
	document.form1.mode.value="best_change";
	document.form1.submit();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티관리 &gt; 레시피 관리 &gt;<span>레시피 리뷰 관리</span></p></div></div>
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
					<div class="title_depth3">레시피 리뷰 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체 레시피들의 리뷰를 관리할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">레시피리뷰 검색</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method="get">
			<input type=hidden name=type>
			<input type=hidden name=date>
			<input type=hidden name=page_no>
			<input type=hidden name=num>
			<input type=hidden name=productcode>
			<input type=hidden name="module">
			<input type=hidden name="mode">
			<input type=hidden name="returnUrl" value="<?=$_REQUEST[returnUrl]?$_REQUEST[returnUrl]:$_SERVER[REQUEST_URI]?>">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>검색조건 선택</span></th>
					<TD>
					<input type="checkbox" name="search_field[]" value="all" <?=in_array("all",$_REQUEST[search_field])||!$_REQUEST[search_field]?"checked":""?>> <label>통합검색</label>
					<input type="checkbox" name="search_field[]" value="subject" <?=in_array("subject",$_REQUEST[search_field])?"checked":""?>> <label>레시피명</label>
					<input type="checkbox" name="search_field[]" value="name" <?=in_array("name",$_REQUEST[search_field])?"checked":""?>> <label>작성자</label>
					<input type="checkbox" name="search_field[]" value="contents" <?=in_array("contents",$_REQUEST[search_field])?"checked":""?>> <label>내용</label>
					<input type="checkbox" name="search_field[]" value="best" <?=in_array("best",$_REQUEST[search_field])?"checked":""?>> <label>베스트</label>
					<!--
					<input type=radio name=s_check value="0" onClick="OnChangeSearchType(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>레시피명으로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="1" onClick="OnChangeSearchType(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>작성자로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="2" onClick="OnChangeSearchType(this.value);" id=idx_s_check2 <?=$check_s_check2?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>최다리뷰 작성자 20명</label>
					-->
					</TD>
				</TR>
				<TR>
					<th><span>검색기간 선택</span></th>
					<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
					</td>
				<TR>
					<th><span>검색어 입력</span></th>
					<TD><input name=search_word size=47 value="<?=$_REQUEST[search_word]?>" <?=$search_style?> class="input"> <input type="image" src="images/btn_search2.gif" align=absmiddle  border="0"></a>
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
				<col width="80"></col>
				<col width="80"></col>
				<col width="200"></col>
				<col width="100"></col>
				<col width=""></col>
				<col width="80"></col>
				<col width="80"></col>
				<col width="80"></col>
				<TR align=center>
					<th>번호</th>
					<th>&nbsp;</th>
					<th>레시피</th>
					<th>작성자</th>
					<th>레시피명/리뷰 및 <FONT color=red>＊</FONT>답글</th>
					<th>베스트</th>
					<th>등록일</th>
					<th>삭제</th>
				</TR>
				<?
				if(is_array($list)){foreach($list as $data){
					
				?>
				<tr>
					<td><?=$data[vnum]?></td>
					<td><img src="<?=$data[timg_src]?>" width="50"></td><td><?=$data[subject]?></td>
					<td><?=$data[name]?></td>
					<td style="text-align:left;">
						<div><a href="#" onclick="reviewDetail('<?=$data[num]?>'); return false;"><?=$data[comment_tag]?></div>
					</td>
					<td><input type="checkbox" name="best_type[<?=$data[num]?>]" value="1" <?if($data[best_type]){echo "checked";}?>><input type="hidden" name="recipe_num[]" value="<?=$data[num]?>"></td>
					<td><?=$data[regdt]?></td>
					<td><img src="images/btn_del.gif" style="cursor:pointer" onclick="delRecipe(<?=$data[num]?>)"></td>
				</tr>
				<?}}else{?>
				<tr>
					<td colspan="6" height="80"><strong>검색된 레시피 리뷰가 없습니다.</strong></td>
				</tr>
				<?}?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<?$recipe->getPageNavi()?>

				</td>
			</tr>
			<tr>
				<td align=right>
					<img src="images/botteon_save.gif" onclick="bestRecipe()" style="cursor:pointer" >
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
			<input type=hidden name=vperiod value="<?=$vperiod?>">
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

			<form name=form5 action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			</form>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>레시피 리뷰 관리</span></dt>
							<dd>
							- 회원아이디로 검색시 정확한 아이디 입력을 하셔야만 검색이 됩니다.<br>
							- [회원이름] 클릭시 해당 회원의 정보를 확인하실 수 있습니다.<br>
							- [회원아이디] 클릭시 해당 회원아이디로 리뷰 검색이 이루어집니다.<br>
							- [내역확인] 클릭시 해당 회원의 구매내역을 확인할 수 있습니다.<br>
							- 레시피명을 클릭시 해당 레시피 카테고리내 레시피들의 정보를 확인하실 수 있습니다.<br>
							- [새창] 버튼 클릭시 해당 레시피의 정보를 수정할 수 있습니다.<Br>
							- 리뷰 클릭시 해당 리뷰의 전체 내용 및 답변을 등록할 수 있습니다.<br>
							- [적립금 지급] 버튼 클릭시 해당 리뷰 작성자에게 적립금을 지급/차감할 수 있습니다.<br>
							- [다른리뷰 보기] 버튼 클릭시 해당 레시피명으로 리뷰 검색이 이루어집니다.<br>
							- [삭제] 버튼 클릭시 해당 리뷰가 삭제됩니다.
							</dd>
	
						</dl>
						<dl>
							<dt><span>레시피 리뷰 관리 주의사항</span></dt>
							<dd>
							- 삭제된 리뷰는 복원되지 않으므로 신중히 처리하시기 바랍니다.<br>
							- 적립금 지급으로 인한 적립/차감된 적립금은 복원되지 않으므로 신중히 처리하시기 바랍니다.
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

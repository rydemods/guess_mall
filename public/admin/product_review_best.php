<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
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
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";

$type=$_POST["type"];
$reviewtype=$_POST["reviewtype"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
$search=$_POST["search"];
$date=$_POST["date"];
$productcode=$_POST["productcode"];
$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
$s_check=$_POST["s_check"];
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
	echo "<script>alert(\"리뷰 기능이 설정이 안되었습니다.\");parent.topframe.location.href=\"JavaScript:GoMenu(1,'shop_review.php')\";</script>";exit;
}
pmysql_free_result($result);

if ($type=="delete" && ord($date)) {
	$sql = "DELETE FROM tblproductreview WHERE productcode = '{$productcode}' AND date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	$onload = "<script>window.onload=function(){alert('해당 상품리뷰 삭제가 완료되었습니다.');}</script>\n";
} else if ($type=="auth" && ord($date)) {
	$sql = "UPDATE tblproductreview SET display = 'Y' ";
	$sql.= "WHERE productcode = '{$productcode}' AND date = '{$date}'";
	pmysql_query($sql,get_db_conn());
	$onload = "<script>window.onload=function(){alert('해당 상품리뷰 인증이 완료되었습니다.');}</script>\n";
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
	if (s_check!="2" && s_check!="3") {
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
function GoPage(block,gotopage) {
	document.rForm.block.value = block;
	document.rForm.gotopage.value = gotopage;
	document.rForm.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt;<span>상품 리뷰 관리</span></p></div></div>
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
					<div class="title_depth3">상품 리뷰 베스트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>리뷰 베스트 진열을 관리할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드 검색</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>브랜드 선택</span></th>
					<TD><select><option></option></select></TD>
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
				<TABLE cellSpacing=0 cellPadding=0 width="70%" border=0>
				<col width="120"></col>
				<col width="140"></col>
				<col width=""></col>
				<col width="80"></col>
				<col width="80"></col>
				<col width="80"></col>
				
				<TR align=center>
					<th>등록일</th>
					<th>작성자 정보</th>
					<th>상품명/리뷰 및 <FONT color=red>＊</FONT>답글</th>
					<th>베스트 / 별점</th>
					<th>적립금</th>
					<th>동일상품</th>
					<th width="51">삭제</th>
				</TR>
<?php
				$qry.= "WHERE a.productcode = b.productcode ";
				if ($reviewtype=="N") {
					$qry.= "AND a.display = 'Y' ";
				} else if ($reviewtype=="Y") {
					$qry.= "AND a.display='N' ";
				}
				$qry.= "AND a.date >= '{$search_s}' AND a.date <= '{$search_e}' ";
				if (strlen(trim($search))>2) {
					if($s_check=="0") {
						$qry.= "AND (b.productname LIKE '%{$search}%' OR a.content LIKE '%{$search}%') ";
					} else if ($s_check=="1") {
						$qry.= "AND a.id = '{$search}' ";
					}
				}
				if($s_check=="3"){
					$qry.= "AND a.best_type = '1' ";
				}
				$sql = "SELECT COUNT(*) as t_count FROM tblproductreview a, tblproduct b {$qry} ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.content,a.date,a.productcode,b.productname,b.tinyimage,b.selfcode,b.assembleuse, a.best_type, a.marks ";
				$sql.= "FROM tblproductreview a, tblproduct b {$qry} ";
				$sql.= "ORDER BY a.date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$contents=explode("=",$row->content);
					//별점
					$marks="";
					for($i=0;$i<$row->marks;$i++){
						$marks.="<FONT color=#000000>★</FONT>";
					}
					for($i=$row->marks;$i<5;$i++){
						$marks.="<FONT color=#DEDEDE>★</FONT>";
					}
					
					
					echo "<tr>\n";
					echo "	<TD align=center class=\"td_con2\">".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</td>\n";
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
					echo "		<td rowspan='2' style='padding:10px; border:0px;'><img src='/data/shopimages/product/{$row->minimage}' width='50'></td>\n";
					echo "		<td style=\"text-align:left;border-bottom:none;word-break:break-all;\">\n";
					echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
					echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','')\"><font color=#3D3D3D><u>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</u></font></a>";
					echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><IMG src=\"images/icon_newwin.gif\" align=absMiddle border=0 ></a>";
					echo "		</span>\n";
					echo "		<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
					echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
					echo "		<tr bgcolor=#FFFFFF>\n";
					if (ord($row->tinyimage)) {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src=".$Dir.DataDir."shopimages/product/{$row->tinyimage}></td>\n";
					} else {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src={$Dir}images/product_noimg.gif></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					echo "		<td style=\"text-align:left;padding-top:3;border-bottom:none;\"><img src=\"images/icon_review.gif\" border=\"0\" align=absMiddle hspace=\"2\"><a href=\"JavaScript:ReviewReply('{$row->date}','{$row->productcode}')\" title=\"".htmlspecialchars($contents[0])."\">".titleCut(38,htmlspecialchars($contents[0]))."</a> ";
					if(ord($contents[1])) echo "<font color=red>＊</font>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	</div> \n";
					echo "	<TD align=center>";
					if($row->best_type){
						echo "Yes";
					}else{
						echo "No";
					}
					echo "<br />{$marks}";
					echo "	</td>";
					echo "	<TD align=center>";
					if(ord($row->id)==0) {
						echo "<font color=red><B>X</B></font>";
					} else if ($row->reserve==0) {
						echo "<a href=\"javascript:ReserveSet('{$row->id}','{$row->date}','{$row->productcode}')\"><img src=\"images/icon_pointi.gif\"  border=\"0\" valign=absmiddle></a>";
					} else {
						echo number_format($row->reserve);
					}
					echo "	</td>\n";
					echo "	<TD align=center><a href=\"javascript:SearchProduct('{$row->productname}');\"><img src=\"images/icon_review1.gif\"  border=\"0\"></a></td>\n";
					if ($review_type=="A") {
						echo "	<TD align=center width=\"59\">";
						if($row->display=="Y") {
							echo "<B>Y</B>";
						} else {
							echo "	<a href=\"javascript:AuthReview('{$row->date}','{$row->productcode}');\"><img src=\"images/btn_ok2.gif\"  border=\"0\" valign=absmiddle></a>";
						}
						echo "	</td>\n";
					}
					echo "	<TD align=center width=\"59\">";
					echo "	<a href=\"javascript:DeleteReview('{$row->date}','{$row->productcode}');\"><img src=\"images/btn_del.gif\"  border=\"0\"></a>";
					echo "	</td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="70%">
					<tr>
						<td align=center width=\"100%\" class=\"font_size\">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page ?>
						</td>
					</tr>			
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
							<dt><span></span></dt>
							<dd>							
							</dd>	
						</dl>
						<dl>
							<dt><span></span></dt>
							<dd>
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

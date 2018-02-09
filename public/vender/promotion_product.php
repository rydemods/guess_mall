<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
//include($Dir."lib/paging.php");

$sort=$_POST["sort"];
$mode=$_POST["mode"];
$keyword=$_POST["keyword"];
$s_check=$_POST["s_check"];
$display=$_POST["display"];
$vperiod=$_POST["vperiod"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_POST["search_end"];
$search_start=$_POST["search_start"];
$sellprice_min=$_POST["sellprice_min"];
$sellprice_max=$_POST["sellprice_max"];
$special = $_REQUEST["special"];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;


$_ShopData=new ShopData($_ShopInfo);
$_ShopData=$_ShopData->shopdata;
$regdate = $_ShopData->regdate;

$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));


$checked["display"][$display] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["check_vperiod"][$vperiod] = "checked";

$pidx = $_REQUEST["pidx"];
$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}


$imagepath=$Dir.DataDir."shopimages/product/";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>기획전 진열 상품군 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../admin/codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">


function MainInsert(prcode,chk) {
	/*
	max=4;
	if(document.MainPrdtFrame.form1.num.value>=max){
		alert("메인카테고리 상품은 최대 "+max+"개의 상품을 등록하실 수 있습니다.\n\n다른 상품을 삭제후 등록하세요.");
		return;
	}
	*/
	var is_special = false;

	if(document.MainPrdtFrame.form1.special.checked){ // 하위 기획전이 1개 일때
		is_special=true;
	}

	for(i=0;i<document.MainPrdtFrame.form1.special.length;i++) { // 하위 기획전이 2개 이상
		if (document.MainPrdtFrame.form1.special[i].checked) {
			is_special=true;
			break;
		}
	}

	if(!is_special){
		alert("이동할 기획전을 선택하세요.");
		document.MainPrdtFrame.form1.special[0].focus();
		return;
	}
	//if (confirm("해당 상품을 메인 진열상품으로 이동하시겠습니까?")){

		document.MainPrdtFrame.form1.prcode.value=prcode;
		document.MainPrdtFrame.InsertSpecial(chk);
	//}
}

function chk_in(){//20150604 체크박스로 진열리스트 추가. 원재
	var chk=1;
	var chkList=[];
	var chkList2=[];
	$(".chk_list").each(function(index,item){

		if($(this).prop("checked")){
			chkList += $(item).val()+",";
		}

	});
	//chkList2=chkList.split(",");
	MainInsert(chkList,chk);
}


function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function ProductInfo(prcode,popuptype) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	if (popup=="YES") {
		document.form_register.action="product_register.add.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.add.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

function ProductDel(prcode){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		document.form1.mode.value="delete";
		document.form1.prcode.value=prcode;
		document.form1.submit();
	}
}
var ProductInfoStop="";
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	ProductInfoStop = "1";
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>


function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";

	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}


</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>

<table border=0 cellpadding=3 cellspacing=0 width=100% style="table-layout:fixed;">
<tr>
	<td bgcolor="#5F9FDF" style="padding-left:15"><FONT COLOR="#ffffff"><B>기획전 진열 상품군 선택</B></FONT></td>
</tr>
</table>
<table cellpadding="8" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">

	<tr>
		<td valign=top>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>		
			<td valign=top>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<tr>
				<td valign=top bgcolor=D4D4D4 style=padding:1>
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=mode>
				<input type=hidden name=prcode>
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type=hidden name=sort value="<?=$sort?>">
				<input type=hidden name=pidx value="<?=$pidx?>">
				<input type=hidden name=special value="<?=$special?>">
				<tr>
					<td valign=top bgcolor=F0F0F0 style=padding:10>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<colgroup>
					<col width=70>
					<col width=345>
					<col width=70>
					<col width=>
					</colgroup>
					<tr>
						<td>&nbsp;<U>카테고리</U></td>
						<td colspan=3>
			<?php
							$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
							$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
							$i=0;
							$ii=0;
							$iii=0;
							$iiii=0;
							$strcodelist = "";
							$strcodelist.= "<script>\n";
							$result = pmysql_query($sql,get_db_conn());
							$selcode_name="";

							while($row=pmysql_fetch_object($result)) {
								$strcodelist.= "var clist=new CodeList();\n";
								$strcodelist.= "clist.code_a='{$row->code_a}';\n";
								$strcodelist.= "clist.code_b='{$row->code_b}';\n";
								$strcodelist.= "clist.code_c='{$row->code_c}';\n";
								$strcodelist.= "clist.code_d='{$row->code_d}';\n";
								$strcodelist.= "clist.type='{$row->type}';\n";
								$strcodelist.= "clist.code_name='{$row->code_name}';\n";
								if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
									$strcodelist.= "lista[{$i}]=clist;\n";
									$i++;
								}
								if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
									if ($row->code_c=="000" && $row->code_d=="000") {
										$strcodelist.= "listb[{$ii}]=clist;\n";
										$ii++;
									} else if ($row->code_d=="000") {
										$strcodelist.= "listc[{$iii}]=clist;\n";
										$iii++;
									} else if ($row->code_d!="000") {
										$strcodelist.= "listd[{$iiii}]=clist;\n";
										$iiii++;
									}
								}
								$strcodelist.= "clist=null;\n\n";
							}
							pmysql_free_result($result);
							$strcodelist.= "CodeInit();\n";
							$strcodelist.= "</script>\n";

							echo $strcodelist;


							echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
							echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
							echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
							echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_d style=\"width:170px;\">\n";
							echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
			?>
						</td>
					</tr>
					<tr><td height=5 colspan=4></td></tr>
					<tr>
						<td>&nbsp;<U>기간선택</U></td>
						<td colspan=3><input type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
							<img src=images/btn_dayall.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
						</td>
					</tr>
					<tr><td height=5 colspan=4></td></tr>
					<tr>
						<td>&nbsp;<U>상품금액</U></td>
						<td colspan=3><input type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
					</tr>
					<tr><td height=5 colspan=4></td></tr>
					<tr>
						<td>&nbsp;<U>품절유무</U></td>
						<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						<td>&nbsp;<U>진열유무</U></td>
						<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열</td>
					</tr>
					<tr><td height=5 colspan=4></td></tr>
					<tr>
						<td>&nbsp;<U>상품명</U></td>
						<td colspan=3><input  style="width:299" type="text" name="keyword" value="<?=$keyword?>">
						&nbsp;<a href="javascript:;"><input type="image" src="images/btn_inquery03.gif" align=absmiddle alt="" /></a></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					

					<table  width=100% cellpadding=0 cellspacing=0 border=0>
						<colgroup>
							<col width="" />
                            <col width="50" />
                            <col width="450" />
						</colgroup>
						<tr>
							<td valign=top>

								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=130></col>
								<col width=></col>
								<tr><td height=25 colspan=2></td></tr>
								<tr>
									<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>상품리스트</B></td>
									<td align=right valign=bottom>
									<B>정렬 :</B> 
									<a href="javascript:GoSort('modifydate');">등록순</a> l <a href="javascript:GoSort('productname');">상품명순</a> l <a href="javascript:GoSort('price');">가격순</a>
									</td>
								</tr>
								<tr><td height=2 colspan=2></td></tr>
								<tr><td height=1 bgcolor=red colspan=2></td></tr>
								</table>
								<div  style="min-height:430px;">
								<table border=0 cellpadding=5 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
									<colgroup>
										<?
										$colspan=6;
										?>
										<col width="30" />
										<col width="70" />
										<col width="" />
										<col width="80" />
										<col width="50" />
										<col width="48" />
									</colgroup>
									<tr height=32 align=center bgcolor=F5F5F5>
										<td align=center style="font-size:8pt" colspan="3"><B>상품명</B></td>
										<td align=center style="font-size:8pt"><B>판매가격</B></td>
										<td align=center style="font-size:8pt"><B>상태</B></td>
										<td align=center style="font-size:8pt"><B>진열</B></td>
									</tr>

				<?php
									$page_numberic_type=1;

									if ($likecode){
										$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
										$link_result=pmysql_query($link_qry);
										while($link_data=pmysql_fetch_object($link_result)){
											$linkcode[]=$link_data->c_productcode;
										}

										$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";
									//	 $qry= "AND b.c_category LIKE '{$likecode}%' ";
									}
									if ($keyword) $qry.= "AND (productname || productcode)LIKE '%{$keyword}%' ";
									if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
									elseif($s_check==2)$qry.="AND quantity <= 0 ";
									if($display==1)	$qry.="AND display='Y' ";
									elseif($display==2)	$qry.="AND display='N'";
									//if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
									if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
									if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

									$qry.= "AND vip_product='0' AND staff_product='0' ";

									$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a WHERE a.vender='".$_VenderInfo->getVidx()."' ";//AND sabangnet_flag='N'
									$sql0.= $qry;
									$paging = new Paging($sql0,10,8);
									$t_count = $paging->t_count;
									$gotopage = $paging->gotopage;

									$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
									$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, selfcode, modifydate ";
									$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode and c_maincate=1) WHERE a.vender='".$_VenderInfo->getVidx()."' "; //AND sabangnet_flag='N'
									$sql.= $qry." ";
									if ($sort=="price")				$sql.= "ORDER BY sellprice ";
									elseif ($sort=="productname")	$sql.= "ORDER BY productname ";
									else							$sql.= "ORDER BY modifydate DESC ";
									$sql = $paging->getSql($sql);
									$result = pmysql_query($sql,get_db_conn());
									$cnt=0;
//exdebug($sql);
									while($row=pmysql_fetch_object($result)) {
									$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

									echo "<tr bgcolor=#FFFFFF>";
									echo "<td><input type='checkbox' class='chk_list' value=".$row->productcode."></td>";
									echo "	<TD>";
									if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
										echo "<img src='".$imagepath.$row->tinyimage."' style=\"width:60px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
									} else {
										echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
									}
									echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
									echo "		<tr bgcolor=\"#FFFFFF\">\n";
									if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
										echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;width:100px\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
									} else {
										echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
									}
									echo "		</tr>\n";
									echo "		</table>\n";
									echo "		</div>\n";
									echo "	</td>\n";
									echo "<td height=\"50\"><a href=\"javascript:ProductInfo('".$row->productcode."','YES');\"><p class=\"ta_l\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</p></a></td>";
									echo "	<TD style=\"text-align:right;\"><img src=\"/admin/images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span></TD>\n";

									echo "	<TD>".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
									echo "	</TD>\n";
									echo "<td><img src=\"images/btn_s_register.gif\" onclick=\"javascript:MainInsert('".$row->productcode."');\" style = 'cursor:pointer;'></td>";

									echo "</tr>";
									$cnt++;
									}
									if ($cnt==0) {
										$colspan='6';
										$page_numberic_type="";
										echo "<tr bgcolor=#FFFFFF><td colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
									}
			?>
									</table>
								</div>
								<div style="padding-top:10px;text-align:right"><a href="javascript:;" class="chk_in_all" onclick="chk_in()"><img src="images/btn_select_registered.gif" border=0></a></div>
<?php
						//페이징

							echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>";
							echo "<tr>";
							echo "<td align=center style='padding-top:10'>";
							if($page_numberic_type) echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</td>";
							echo "</tr>";
							echo "</table>";

		?>
							</td>
							<td align=center><img src="/admin/images/btn_next.gif" alt="" /></td>
							<td valign=top style="min-height: 688px;">
								<!-- 소제목 -->
								<?
								$tsql = "SELECT title FROM tblpromo WHERE idx='{$pidx}'";
								$tres = pmysql_fetch_array(pmysql_query($tsql));
								?>
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<tr><td height=25></td></tr>
								<tr>
									<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B><?=$tres[title]?> 진열 리스트</B></td>
								</tr>
								<tr><td height=2></td></tr>
								<tr><td height=1 bgcolor=red></td></tr>
								</table>

								<IFRAME name="MainPrdtFrame" src="promotion_product.main.php?pidx=<?=$pidx?>&special=<?=$special?>" width=100% height=100% frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

							</td>
						</tr>
					</table>

				</td>
			</tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
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
<form name=form2 action="" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type="hidden" name="pidx" value="<?=$pidx?>">
</form>

<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type="hidden" name="pidx" value="<?=$pidx?>">
</form>
</table>
</body>
</html>


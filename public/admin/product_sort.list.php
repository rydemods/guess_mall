<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$Scrolltype=$_REQUEST["Scrolltype"];

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$change=$_POST["change"];
$prcodes=$_POST["prcodes"];

if(strlen($code)==12) {
	$sql = "SELECT type FROM tblproductcode WHERE code_a='".substr($code,0,3)."' ";
	$sql.= "AND code_b='".substr($code,3,3)."' ";
	$sql.= "AND code_c='".substr($code,6,3)."' AND code_d='".substr($code,9,3)."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row) {
		$code="";
	}
	$type = $row->type;
} else {
	$code="";
}

if ($mode=="sequence" && $change=="Y" && ord($prcodes)) {
	$date1=date("Ym");
	$date=date("dHis");
	$productcode = explode(",",$prcodes);
	$codeabcd = explode(",",$codes);
	$cnt = count($productcode);
	for($i=0;$i<$cnt;$i++){
		$date=$date-1;
		$date = sprintf("%08d",$date);

		if(strpos($type,'T')===FALSE) {
			$sql = "UPDATE tblproduct SET date = '".$date1.$date."' ";
			$sql.= "WHERE productcode='{$productcode[$i]}' ";
		} else {
			$likecode=substr($code,0,3);
			if(substr($code,3,3)!="000") {
				$likecode.=substr($code,3,3);
				if(substr($code,6,3)!="000") {
					$likecode.=substr($code,6,3);
					if(substr($code,9,3)!="000") {
						$likecode.=substr($code,9,3);
					}
				}
			}
			$sql = "UPDATE tblproducttheme SET date = '".$date1.$date."' ";
			$sql.= "WHERE code LIKE '{$likecode}%'  AND code='{$codeabcd[$i]}' ";
			$sql.= "AND productcode = '".substr($productcode[$i],-18)."' ";
		}
		pmysql_query($sql,get_db_conn());
	}
	$onload="<script>alert('상품순서 변경이 완료되었습니다.');</script>\n";

	$log_content = "[등록상품 진열순서 조정] 카테고리코드 : $code";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

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

<?php include("header.php"); ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
function CheckForm() {

}

document.onkeydown = CheckKeyPress;
var all_list_i = new Array(); //num에 대한 리스트값 셋팅
var preselectnum = ""; //num에 대한 기존리스트값 셋팅
var all_list = new Array();
var selnum="";
var ProductInfoStop="";

function CheckKeyPress(updownValue) {
	prevobj=null;
	selobj=null;

	if(updownValue)
		ekey = updownValue;
	else
		ekey = event.keyCode;

	if(selnum!="" && (ekey==38 || ekey==40 || ekey=="up" || ekey=="down")) {
		var h=0;

		h=all_list_i[selnum];

		if(ekey==38 || ekey == "up") {			//위로 이동
			kk=h-1;
		} else {	//아래로 이동
			kk=h+1;
		}

		prevobj=all_list[kk];

		if(prevobj!=null) {
			selobj=all_list[h];

			t1=prevobj.sort;
			prevobj.sort=selobj.sort;
			selobj.sort=t1;


			o1=prevobj.no;
			prevobj.no=selobj.no;
			selobj.no=o1;

			all_list[h]=prevobj;
			all_list[kk]=selobj;

			all_list_i[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
			all_list_i[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
			preselectnum=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅

			takeChange(prevobj);
			takeChange(selobj);

			all_list[kk].selected=false;
			selnum="";
			document.form1.change.value="Y";
			ChangeList(all_list[kk].num);
		}
	}
}

function takeChange(argObj)
{
	var innerHtmlStr = "";

	innerHtmlStr="<TD>"+argObj.num+"</td>";
	innerHtmlStr+="<TD><a href=\"javascript:updown_click('"+argObj.num+"','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('"+argObj.num+"','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>";
	<?php if($vendercnt>0) {echo "innerHtmlStr+=argObj.venderidx;\n";}?>
	innerHtmlStr+=argObj.imgidx;
	innerHtmlStr+=argObj.nameidx;
	innerHtmlStr+=argObj.sellidx;
	innerHtmlStr+=argObj.quantityidx;
	innerHtmlStr+=argObj.displayidx;
	innerHtmlStr+=argObj.editidx;
	document.all["idx_inner_"+argObj.sort].innerHTML="<TABLE onclick=\"ChangeList('"+argObj.num+"');\" border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed\"><col width=40></col><col width=12></col><?=($vendercnt>0?"<col width=70></col>":"")?><col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><tr align=\"center\">"+innerHtmlStr+"</tr></table>";
}

function updown_click(num,updownValue)
{
	if(selnum != num)
		ChangeList(num);

	CheckKeyPress(updownValue);
}

function ChangeList(num) {
	if(ProductInfoStop)
		ProductInfoStop = "";
	else
	{
		if(all_list[all_list_i[num]].selected) {
			preselectnum="";  //기존num 값 셋팅
			selnum="";
			all_list[all_list_i[num]].selected=false;
			document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#FFFFFF";
		} else {
			if(preselectnum>0) { //기존 선택되어 있는 값 비우기
				all_list[all_list_i[preselectnum]].selected=false;
				document.all["idx_inner_"+all_list[all_list_i[preselectnum]].sort].style.backgroundColor="#FFFFFF";
			}
			preselectnum=num;  //기존num 값 셋팅
			selnum=num;
			all_list[all_list_i[num]].selected=true;
			document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#EFEFEF";
		}
		jumpdivshow(num,all_list[all_list_i[num]].selected);
	}
}

function jumpdivshow(num,selectValue) {
	if(document.getElementById("idx_inner_"+all_list[all_list_i[num]].sort) && document.getElementById("jumpdiv")) {
		var inneridxObj = document.getElementById("idx_inner_"+all_list[all_list_i[num]].sort);
		var jumpdivObj = document.getElementById("jumpdiv");

		jumpdivObj.style.display="none";
		if(selectValue) {
			jumpdivObj.style.display="";
			if(inneridxObj.offsetHeight>jumpdivObj.offsetHeight) {
				jumpdivObj.style.top = inneridxObj.offsetTop+((inneridxObj.offsetHeight-jumpdivObj.offsetHeight)/2);
			} else {
				jumpdivObj.style.top = inneridxObj.offsetTop-(jumpdivObj.offsetHeight-inneridxObj.offsetHeight-1);
			}
			jumpdivObj.style.left = (inneridxObj.offsetWidth-jumpdivObj.offsetWidth)/2;
		}
	}
}

function CheckJump(updownValue) {
	prevobj=null;
	selobj=null;

	h=all_list_i[selnum];
	if(updownValue == "up") {			//위로 이동
		kk=h-1;
	} else {	//아래로 이동
		kk=h+1;
	}

	if(all_list[kk]!=null) {
		prevobj=all_list[kk];
		selobj=all_list[h];

		t1=prevobj.sort;
		prevobj.sort=selobj.sort;
		selobj.sort=t1;

		o1=prevobj.no;
		prevobj.no=selobj.no;
		selobj.no=o1;

		all_list[h]=prevobj;
		all_list[kk]=selobj;

		all_list_i[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
		all_list_i[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
		preselectnum=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅

		takeChange(prevobj);

		selnum=all_list[kk].num;
		all_list[kk].selected=true;
	}
}

function jumpgo() {
	form = document.form1;

	if(selnum.length) {
		if(form.jumpnumber.value.length>0 && all_list_i[form.jumpnumber.value]>-1 && all_list[all_list_i[form.jumpnumber.value]] && all_list[all_list_i[form.jumpnumber.value]].sort>-1) {
			if(form.jumpnumber.value!=selnum) {
				var updowntype = "down";
				var selnum_Obj = all_list[all_list_i[selnum]];
				var jumpnumber_Obj = all_list[all_list_i[form.jumpnumber.value]];

				var selnum_sort = selnum_Obj.sort;
				var jumpnumber_sort = jumpnumber_Obj.sort;
				var num_subtract = selnum_sort-jumpnumber_sort;
				var preselectnum_num="";

				preselectnum = selnum_Obj.num;
				if(num_subtract>0) {
					updowntype = "up";
				}

				num_subtract = Math.abs(num_subtract);

				for(var i=0; i<num_subtract; i++) {
					CheckJump(updowntype);
					if(i==0) {
						preselectnum_num = preselectnum;
					}
				}
				takeChange(selnum_Obj);
				preselectnum = preselectnum_num;

				form.jumpnumber.value="";
				document.form1.change.value="Y";
				selnum="";
				selnum_Obj.selected=false;
				ChangeList(selnum_Obj.num);
			}
		} else {
			if(form.jumpnumber.value.length==0) {
				alert("이동위치 No를 입력해 주세요.");
			} else {
				alert("이동위치 No는 존재하지 않는 번호 입니다.");
			}
		}
	}
}

function ObjList() {
	var argv = ObjList.arguments;
	var argc = ObjList.arguments.length;

	//Property 선언
	this.classname		= "ObjList";
	this.debug			= false;
	this.num			= new String((argc > 0) ? argv[0] : "0");
	this.productcode	= new String((argc > 1) ? argv[1] : "");
	this.imgidx			= new String((argc > 2) ? argv[2] : "");
	this.nameidx		= new String((argc > 3) ? argv[3] : "");
	this.sellidx		= new String((argc > 4) ? argv[4] : "");
	this.quantityidx	= new String((argc > 5) ? argv[5] : "");
	this.displayidx		= new String((argc > 6) ? argv[6] : "");
	this.editidx		= new String((argc > 7) ? argv[7] : "");
	this.no				= new String((argc > 8) ? argv[8] : "");
	this.sort			= new String((argc > 9) ? argv[9] : "");
	this.selected		= new Boolean((argc > 10) ? argv[10] : false );
	<?php if($vendercnt>0) {echo "this.venderidx		= new String((argc > 11) ? argv[11] : \"\");\n";}?>
}

function move_save()
{
	if (document.form1.change.value!="Y") {
		alert("순서 변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	val="";
	val2="";
	for(i=0;i<all_list.length;i++)
	{
		var all_list_pcode = all_list[i].productcode.split('|');

		val +=","+all_list_pcode[0];
		val2+=","+all_list_pcode[1];
	}

	if(val.length>0)
	{
		val=val.substring(1);
		val2=val2.substring(1);
		document.form1.mode.value = "sequence";
		document.form1.prcodes.value=val;
		document.form1.codes.value=val2;
		document.form1.submit();
	}
}

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

function ProductInfo(prcode) {
	ProductInfoStop = "1";
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function DivScrollActive(arg1)
{
	if(!self.id)
		self.id = self.name;

	if(document.getElementById("divscroll") && document.getElementById("ListTTableId") && document.getElementById("ListLTableId") && parent.document.getElementById(self.id))
	{
		if(!document.getElementById("divscroll").height)
			document.getElementById("divscroll").height=document.getElementById("divscroll").offsetHeight;

		if(arg1>0)
		{
			if(document.getElementById("ListLTableId").offsetHeight > document.getElementById("divscroll").offsetHeight)
			{
				document.getElementById("divscroll").style.height="100%";
				parent.document.getElementById(self.id).style.height=document.getElementById("ListTTableId").offsetHeight;
			}
		}
		else
		{
			document.getElementById("divscroll").style.height=document.getElementById("divscroll").height;
			parent.document.getElementById(self.id).style.height="100%";
		}
	}

	document.form1.Scrolltype.value = arg1;
}
</script>
<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=prcode>
<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" bgcolor="#FFFFFF" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></td>
			<td align="right"><a href="javascript:DivScrollActive(1);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive(0);"><b>펼침닫기</b></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_top_line.gif"></TD>
	</TR>
	<TR>
		<TD width="100%">
		<DIV id="divscroll" style="position:relative;z-index:1;width:100%;height:523px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
		<div id="jumpdiv" style="position:absolute;display:none;">
		<table border="0" cellspacing="1" cellpadding="0" bgcolor="#B9B9B9" width="210">
		<col width="100"></col>
		<col width=""></col>
		<tr bgcolor="#FFFFFF">
			<td bgcolor="#F8F8F8" style="padding:2px;" align="center"><img src="images/icon_point5.gif" border="0"><b>이동위치 No</b></td>
			<td style="padding:3px;"><input type=text name="jumpnumber" value="" size="4" maxlength="5" style="height:19;font-size:8pt"><a href="javascript:jumpgo();"><img src="images/btn_ok3.gif" border="0" align="absmiddle" hspace="5"></a></td>
		</tr>
		</table>
		</div>
		<TABLE id="ListLTableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
		$colspan=8;
		if($vendercnt>0) $colspan++;
?>
		<col width=40></col>
		<col width=12></col>
		<?php if($vendercnt>0){?>
		<col width=70></col>
		<?php }?>
		<col width=50></col>
		<col width=></col>
		<col width=70></col>
		<col width=45></col>
		<col width=45></col>
		<col width=45></col>
		<TR align="center">
			<TD class="table_cell" colspan="2">No</TD>
			<?php if($vendercnt>0){?>
			<TD class="table_cell1">입점업체</TD>
			<?php }?>
			<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
			<TD class="table_cell1">판매가격</TD>
			<TD class="table_cell1">수량</TD>
			<TD class="table_cell1">상태</TD>
			<TD class="table_cell1">수정</TD>
		</TR>
<?php
		$image_i=0;
		if(strlen($code)==12) {
			$page_numberic_type=1;
			if(strpos($type,'X')!==FALSE) {
				$likecode=$code;
			} else {
				$likecode=substr($code,0,3);
				if(substr($code,3,3)!="000") {
					$likecode.=substr($code,3,3);
					if(substr($code,6,3)!="000") {
						$likecode.=substr($code,6,3);
						if(substr($code,9,3)!="000") {
							$likecode.=substr($code,9,3);
						}
					}
				}
			}

			if (strpos($type,'T')===FALSE) {		//기본카테고리
				$sql = "SELECT a.option_price,a.productcode,a.productname,a.production,a.sellprice,a.consumerprice, ";
				$sql.= "a.buyprice,a.quantity,a.reserve,a.reservetype,a.addcode,a.display,a.vender,a.tinyimage,a.selfcode,a.assembleuse ";
				$sql.= "FROM tblproduct AS a ";
				$sql.= "WHERE a.productcode LIKE '{$likecode}%' ";
				$sql.= "ORDER BY a.date DESC ";
			} else {	//가상카테고리
				$sql = "SELECT a.option_price,a.productcode,a.productname,a.production,a.sellprice,a.consumerprice, ";
				$sql.= "a.buyprice,a.quantity,a.reserve,a.reservetype,a.addcode,a.display,a.vender,a.tinyimage,b.code,a.selfcode,a.assembleuse ";
				$sql.= "FROM tblproduct AS a, tblproducttheme AS b ";
				$sql.= "WHERE b.code LIKE '{$likecode}%' ";
				$sql.= "AND a.productcode=b.productcode ";
				$sql.= "ORDER BY b.date DESC ";
			}

			$result = pmysql_query($sql,get_db_conn());
			$cnt = @pmysql_num_rows($result);

			if($cnt>0)
			{
				$j=0;
				$strlist="<script>\n";
				$jj=$cnt;
				$ii=0;
				while($row=pmysql_fetch_object($result)) {
					$j++;
					$strlist.= "var objlist=new ObjList();\n";
					$strlist.= "objlist.num=\"{$j}\";\n";
					$strlist.= "all_list_i[objlist.num]={$ii};\n";

					$strlist.= "objlist.productcode=\"".((strpos($type,'T')===FALSE)?$row->productcode:$row->productcode."|".$row->code)."\";\n";
					if($vendercnt>0) {$strlist.= "objlist.venderidx=\"<TD class=\\\"td_con1\\\"><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\\\"viewVenderInfo({$row->vender});\\\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\";\n";}
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
						$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"".$imagepath.$row->tinyimage."\\\" height=\\\"40\\\" width=\\\"40\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$row->tinyimage."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
					} else {
						$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"{$Dir}images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
					}
					$strlist.= "objlist.nameidx=\"<TD class=\\\"td_con1\\\" align=\\\"left\\\" style=\\\"word-break:break-all;\\\"><img src=\\\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:""))."&nbsp;</td>\";\n";
					$strlist.= "objlist.sellidx=\"<TD align=\\\"right\\\" class=\\\"td_con1\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format($row->sellprice)."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</td>\";\n";
					if (ord($row->quantity)==0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">무제한</td>\";\n";
					else if ($row->quantity<=0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\"><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
					else $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">{$row->quantity}</td>\";\n";

					$strlist.= "objlist.displayidx=\"<TD class=\\\"td_con1\\\">".($row->display=="Y"?"<font color=\\\"#0000FF\\\">판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";

					$strlist.= "objlist.editidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"images/icon_newwin1.gif\\\" border=\\\"0\\\" onclick=\\\"ProductInfo('{$row->productcode}');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
					$strlist.= "objlist.no=\"".($jj--)."\";\n";
					$strlist.= "objlist.sort=\"{$ii}\";\n";
					$strlist.= "objlist.selected=false;\n";
					$strlist.= "all_list[{$ii}]=objlist;\n";
					$strlist.= "objlist=null;\n";

					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td id=\"idx_inner_{$ii}\" colspan=\"{$colspan}\" style=\"background-color:'#FFFFFF';\" onmouseover=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';\" style=\"cursor:hand;\">\n";
					echo "	<TABLE border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('{$j}');\">\n";
					echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col>\n";
					echo "	<tr align=\"center\">\n";
					echo "		<TD>{$j}</td>\n";
					echo "		<TD><a href=\"javascript:updown_click('{$j}','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('{$j}','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender});\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\n";
					}
					echo "		<TD class=\"td_con1\">";
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
						echo "<img src=\"".$imagepath.$row->tinyimage."\" height=\"40\" width=\"40\" border=\"1\" onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
					} else {
						echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
					}

					echo "<div id=\"primage{$image_i}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
					echo "		<tr bgcolor=\"#FFFFFF\">\n";
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)) {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
					} else {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "		</td>\n";
					echo "		<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "		<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "		<TD class=\"td_con1\">";
					if (ord($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "		</TD>\n";
					echo "		<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "		<TD class=\"td_con1\"><img src=\"images/icon_newwin1.gif\" border=\"0\" onclick=\"ProductInfo('{$row->productcode}');\" style=\"cursor:hand;\"></td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</td>\n";
					echo "</tr>\n";
					$ii++;
					$image_i++;
				}

				pmysql_free_result($result);

				$strlist.="</script>\n";
				echo $strlist;
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
			}
		} else {
			$page_numberic_type="";
			echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
		}
?>
		<TR>
			<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
		</TR>
		</TABLE>
		</div>

		</td>
	</TR>
	<TR>
		<TD background="images/table_top_line.gif"></TD>
	</TR>
	<TR>
		<TD><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></TD>
	</TR>
	<TR>
		<TD align=center><a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a></TD>
	</TR>
	<tr>
		<td height="10"></td>
	</tr>

	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=change>
	<input type=hidden name=prcodes>
	<input type=hidden name=codes>
		</table>
	</td>

</tr>

	</form>

	<form name=form_reg action="product_register.php" method=post>
	<input type=hidden name=code>
	<input type=hidden name=prcode>
	<input type=hidden name=popup>
	</form>

	<?php if($vendercnt>0){?>
	<form name=vForm action="vender_infopop.php" method=post>
	<input type=hidden name=vender>
	</form>
	<?php }?>
	</TABLE>

<?="<script>DivScrollActive(".(int)$Scrolltype.");</script>"?>
<?=$onload?>
</body>
</html>

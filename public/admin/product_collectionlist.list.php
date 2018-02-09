<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-3";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$sort=$_REQUEST["sort"];
$Scrolltype=$_REQUEST["Scrolltype"];
if(!$Scrolltype) $Scrolltype = "0:0:0";

$Scrolltype_exp = @explode(":", $Scrolltype);

$mode=$_POST["mode"];
$code=$_POST["code"];
$productcode=$_POST["productcode"];
$prcode=$_POST["prcode"];
$aname=$_POST["aname"];
$selcodes=$_POST["selcodes"];

if ($mode=="modify") {
	if($colcodestype>0)
	{
		$nums = 18;
		$codes = $prcode;
	}
	else
	{
		$nums = 12;
		$codes = $code;
	}
	
	if (ord($colcodes) && strlen($codes)==$nums) {
		$sql = "UPDATE tblcollection SET collection_list = '{$colcodes}' ";
		$sql.= "WHERE productcode = '{$codes}' ";
		$update = pmysql_query($sql,get_db_conn());
	} else if (strlen($codes)==$nums && ord($colcodes)==0) {
		$sql = "DELETE FROM tblcollection ";
		$sql.= "WHERE productcode = '{$codes}' ";
		$delete = pmysql_query($sql,get_db_conn());
	}
} else if ($mode=="delete") {
	if (ord($selcodes)==0) {
		$sql = "DELETE FROM tblcollection WHERE productcode='{$productcode}'";
		$delete = pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tblcollection SET collection_list = '{$selcodes}' ";
		$sql.= "WHERE productcode = '{$productcode}'";
		$update = pmysql_query($sql,get_db_conn());
	}
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
<!--
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
-->

<script type="text/javascript">
 function init(){
  var doc= document.getElementById("divName");
  if(doc.offsetHeight!=0){
   pageheight = doc.offsetHeight;
   parent.document.getElementById("ListFrame").height=pageheight+"px";
   }
 }

 window.onload=function(){
  init();
 }
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
var all_list_i = new Array(); //num에 대한 리스트값 셋팅
var preselectnum = ""; //num에 대한 기존리스트값 셋팅
var all_list_i1 = new Array(); //num에 대한 리스트값 셋팅
var preselectnum1 = ""; //num에 대한 기존리스트값 셋팅
var all_list = new Array();
var all_list1 = new Array();
var selnum="";
var ProductInfoStop="";
var ListTarget="";

function CheckKeyPress(updownValue) {
	prevobj=null;
	selobj=null;
	
	if(updownValue)
		ekey = updownValue;
	else
		ekey = event.keyCode;
	
	if(ListTarget) {
		all_selectlist = all_list1;
	} else {
		all_selectlist = all_list;
	}

	if(selnum!="" && (ekey==38 || ekey==40 || ekey=="up" || ekey=="down")) {
		var h=0;
		
		if(ListTarget) {
			h=all_list_i1[selnum];
		} else {
			h=all_list_i[selnum];
		}

		if(ekey==38 || ekey == "up") {			//위로 이동
			kk=h-1;
		} else {	//아래로 이동
			kk=h+1;
		}
		
		prevobj=all_selectlist[kk];
		
		if(prevobj!=null) {
			selobj=all_selectlist[h];

			t1=prevobj.sort;
			prevobj.sort=selobj.sort;
			selobj.sort=t1;

			o1=prevobj.no;
			prevobj.no=selobj.no;
			selobj.no=o1;

			all_selectlist[h]=prevobj;
			all_selectlist[kk]=selobj;
			
			if(ListTarget) {
				all_list_i1[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
				all_list_i1[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
				preselectnum1=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅
			} else {
				all_list_i[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
				all_list_i[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
				preselectnum=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅
			}

			takeChange(prevobj);
			takeChange(selobj);

			all_selectlist[kk].selected=false;
			selnum="";
			document.form1.change.value="Y";
			ChangeList(all_selectlist[kk].num,ListTarget);
		}
	}
}

function takeChange(argObj)
{
	var innerHtmlStr = "";

	innerHtmlStr="<TD>"+argObj.num+"</td>";
	innerHtmlStr+="<TD><a href=\"javascript:updown_click('"+argObj.num+"','up','"+ListTarget+"')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('"+argObj.num+"','down','"+ListTarget+"')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>";
	<?php if($vendercnt>0) {echo "innerHtmlStr+=argObj.venderidx;\n";}?>
	innerHtmlStr+=argObj.imgidx;
	innerHtmlStr+=argObj.nameidx;
	innerHtmlStr+=argObj.sellidx;
	innerHtmlStr+=argObj.quantityidx;
	innerHtmlStr+=argObj.displayidx;
	innerHtmlStr+=argObj.deleteidx;
	document.all["idx"+ListTarget+"_inner_"+argObj.sort].innerHTML="<TABLE onclick=\"ChangeList('"+argObj.num+"','"+ListTarget+"');\" border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed\"><col width=40></col><col width=12></col><?=($vendercnt>0?"<col width=70></col>":"")?><col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><tr align=\"center\">"+innerHtmlStr+"</tr></table>";
}

function updown_click(num,updownValue,cValue)
{
	if(selnum != num)
		ChangeList(num,cValue);
	
	CheckKeyPress(updownValue);
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
	this.deleteidx		= new String((argc > 7) ? argv[7] : "");
	this.no				= new String((argc > 8) ? argv[8] : "");
	this.sort			= new String((argc > 9) ? argv[9] : "");
	this.selected		= new Boolean((argc > 10) ? argv[10] : false );
	<?php if($vendercnt>0) {echo "this.venderidx		= new String((argc > 11) ? argv[11] : \"\");\n";}?>
}

function ChangeList(num,cValue) {
	if(ProductInfoStop)
		ProductInfoStop = "";
	else
	{
		if(cValue)
		{
			if(all_list1[all_list_i1[num]].selected) {
				preselectnum1="";  //기존num 값 셋팅
				selnum="";
				all_list1[all_list_i1[num]].selected=false;
				document.all["idx1_inner_"+all_list1[all_list_i1[num]].sort].style.backgroundColor="#ffffff";
			} else {
				if(preselectnum1>0) { //기존 선택되어 있는 값 비우기
					all_list1[all_list_i1[preselectnum1]].selected=false;
					document.all["idx1_inner_"+all_list1[all_list_i1[preselectnum1]].sort].style.backgroundColor="#FFFFFF";
				}
				if(preselectnum>0) { //기존 선택되어 있는 값 비우기
					all_list[all_list_i[preselectnum]].selected=false;
					document.all["idx_inner_"+all_list[all_list_i[preselectnum]].sort].style.backgroundColor="#FFFFFF";
					preselectnum="";
				}

				preselectnum1=num;  //기존num 값 셋팅
				selnum=num;
				all_list1[all_list_i1[num]].selected=true;
				document.all["idx1_inner_"+all_list1[all_list_i1[num]].sort].style.backgroundColor="#efefef";
			}
		}
		else
		{
			if(all_list[all_list_i[num]].selected) {
				preselectnum="";  //기존num 값 셋팅
				selnum="";
				all_list[all_list_i[num]].selected=false;
				document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#ffffff";
			} else {
				if(preselectnum1>0) { //기존 선택되어 있는 값 비우기
					all_list1[all_list_i1[preselectnum1]].selected=false;
					document.all["idx1_inner_"+all_list1[all_list_i1[preselectnum1]].sort].style.backgroundColor="#FFFFFF";
					preselectnum1="";
				}
				if(preselectnum>0) { //기존 선택되어 있는 값 비우기
					all_list[all_list_i[preselectnum]].selected=false;
					document.all["idx_inner_"+all_list[all_list_i[preselectnum]].sort].style.backgroundColor="#FFFFFF";
				}
				preselectnum=num;  //기존num 값 셋팅
				selnum=num;
				all_list[all_list_i[num]].selected=true;
				document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#efefef";
			}
		}

		ListTarget = cValue;
	}
}

function move_save(cValue)
{
	if (document.form1.change.value!="Y") {
		alert("순서 변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	val="";

	if(cValue>0)
	{
		for(i=0;i<all_list1.length;i++)
		{
			val+=","+all_list1[i].productcode;
		}
		
		if(val.length>0)
			val=val.substring(1);
	}
	else
	{
		for(i=0;i<all_list.length;i++)
		{
			val+=","+all_list[i].productcode;
		}
		
		if(val.length>0)
			val=val.substring(1);
	}

	document.form1.mode.value = "modify";
	document.form1.colcodes.value=val;
	document.form1.colcodestype.value=cValue;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.aname.value="prcode";
	document.form1.sort.value = sort;
	document.form1.submit();
}

function CollectionIn(code,len) {
	if (code.length!=len) {
		alert('관련 상품을 등록할 카테고리나 상품을 선택하세요.');
		return;
	}
	window.open("about:blank","collection","width=245,height=140,scrollbars=no");
	document.cForm.productcode.value=code;
	document.cForm.submit();
}

function SelectList(idx)
{
	if(ProductInfoStop)
		ProductInfoStop = "";
	else
	{
		if(idx != document.form1.prcode.value)
		{
			document.form1.mode.value = '';
			document.form1.prcode.value = idx;
			document.form1.aname.value="prcode";
			document.form1.submit();
		}
	}
}

function Delete(delcode,code,cValue) {
	ProductInfoStop = "1";
	if(!confirm("선택하신 상품을 관련상품에서 삭제하시겠습니까?")) return;
	codes="";

	if(cValue)
	{
		for(i=0;i<all_list1.length;i++)
		{
			if(delcode!=all_list1[i].productcode){
				codes+=","+all_list1[i].productcode;
			}
		}
	}
	else
	{
		for(i=0;i<all_list.length;i++)
		{
			if(delcode!=all_list[i].productcode){
				codes+=","+all_list[i].productcode;
			}
		}
	}

	if(codes.length>0)
		codes=codes.substring(1);
	
	document.form1.selcodes.value = codes;
	document.form1.mode.value="delete";
	document.form1.productcode.value=code;
	document.form1.submit();
}

function onMouseColor(argValue)
{
	if(document.form1.prcode.value != argValue)
		return true;
	else
		return false;
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

function DivScrollActive(arg1,divscroll_id,ListTable_id,activetype)
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
	
	if(document.getElementById(divscroll_id) && document.getElementById("ListTTableId") && document.getElementById(ListTable_id) && parent.document.getElementById(self.id))
	{
		if(!document.getElementById(divscroll_id).height)
			document.getElementById(divscroll_id).height=document.getElementById(divscroll_id).offsetHeight;

		if(arg1>0)
		{
			if(document.getElementById(ListTable_id).offsetHeight > document.getElementById(divscroll_id).offsetHeight)
			{
				document.getElementById(divscroll_id).style.height="100%";
				parent.document.getElementById(self.id).style.height=document.getElementById("ListTTableId").offsetHeight;
			}
		}
		else
		{
			var default_divwidth = document.getElementById("ListTTableId").offsetHeight-document.getElementById(divscroll_id).offsetHeight+document.getElementById(divscroll_id).height;
			document.getElementById(divscroll_id).style.height=document.getElementById(divscroll_id).height;
			parent.document.getElementById(self.id).style.height=default_divwidth;
		}
	}
	
	ScrolltypeList = document.form1.Scrolltype.value.split(":");
	ScrolltypeList[activetype]=arg1;
	document.form1.Scrolltype.value = ScrolltypeList[0]+":"+ScrolltypeList[1]+":"+ScrolltypeList[2];
}

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
</script>
<div id="divName">
<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<tr>
	<td valign="top" width="100%" height="100%">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=aname>
	<input type=hidden name=productcode>
	<input type=hidden name=change>
	<input type=hidden name=selcodes>
	<input type=hidden name=colcodes>
	<input type=hidden name=colcodestype>
	<input type=hidden name=prcode value="<?=$prcode?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_collection_text1.gif" border="0"></td>
		</tr>
		<tr>
			<td width="100%" height="100%" valign="top" style="background-color:#FFFFFF;BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="right"><a href="javascript:DivScrollActive('1','divscroll0','List0TableId',0);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive('0','divscroll0','List0TableId',0);"><b>펼침닫기</b></a></td>
			</tr>
			<TR>
				<TD background="images/table_top_line.gif"></TD>
			</TR>
			<tr>
				<td valign="top">
				<DIV id="divscroll0" style="position:relative;width:100%;height:278px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
				<TABLE id="List0TableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
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
					<TD class="table_cell1" colspan="2">상품명</TD>
					<TD class="table_cell1">판매가격</TD>
					<TD class="table_cell1">수량</TD>
					<TD class="table_cell1">상태</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
<?php
			$image_i=0;
			$coll_prcode="";
			$sql = "SELECT collection_list FROM tblcollection WHERE productcode = '{$code}'";
			$result = pmysql_query($sql,get_db_conn());
			if($row = pmysql_fetch_object($result)){
				$cnt_prcode=$row->collection_list;
				$coll_prcode=str_replace(',','\',\'',$cnt_prcode);
			}
			pmysql_free_result($result);
			if(ord($coll_prcode)) {
				$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct ";
				$sql.= "WHERE productcode IN ('{$coll_prcode}')";
				$result = pmysql_query($sql,get_db_conn());

				while($row=pmysql_fetch_object($result)) {
					$arraycode[$row->productcode]=$row->productcode;
					$arrayquantity[$row->productcode]=$row->quantity;
					$arraydisplay[$row->productcode]=$row->display;
					$arrayoption_price[$row->productcode]=$row->option_price;
					$arrayproductname[$row->productcode]=$row->productname;
					$arrayproduction[$row->productcode]=$row->production;
					$arraysellprice[$row->productcode]=$row->sellprice;
					$arrayconsumerprice[$row->productcode]=$row->consumerprice;
					$arraybuyprice[$row->productcode]=$row->buyprice;
					$arrayreserve[$row->productcode]=$row->reserve;
					$arrayreservetype[$row->productcode]=$row->reservetype;
					$arrayaddcode[$row->productcode]=$row->addcode;
					$arrayvender[$row->productcode]=$row->vender;
					$arraytinyimage[$row->productcode]=$row->tinyimage;
					$arrayselfcode[$row->productcode]=$row->selfcode;
					$arrayassembleuse[$row->productcode]=$row->assembleuse;
				}

				$viewproduct = explode(",",$cnt_prcode);
				$cnt =count($viewproduct);
				$j=0;
				$strlist="<script>\n";
				$jj=$cnt;
				$ii=0;
				for($i=0;$i<$cnt;$i++){
					if(ord($arraycode[$viewproduct[$i]])){
						$j++;
						$strlist.= "var objlist=new ObjList();\n";
						$strlist.= "objlist.num=\"{$j}\";\n";
						$strlist.= "all_list_i[objlist.num]={$ii};\n";
						$strlist.= "objlist.productcode=\"{$arraycode[$viewproduct[$i]]}\";\n";
						if($vendercnt>0) {$strlist.= "objlist.venderidx=\"<TD class=\\\"td_con1\\\"><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\\\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\\\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\";\n";}
						if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
							$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" height=\\\"40\\\" width=\\\"40\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
						} else {
							$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"{$Dir}images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
						}
						$strlist.= "objlist.nameidx=\"<TD class=\\\"td_con1\\\" align=\\\"left\\\" style=\\\"word-break:break-all;\\\"><img src=\\\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:""))."&nbsp;</td>\";\n";
						$strlist.= "objlist.sellidx=\"<TD align=\\\"right\\\" class=\\\"td_con1\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format(exchageRate($arraysellprice[$viewproduct[$i]]))."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($arrayreservetype[$viewproduct[$i]]!="N"?number_format(exchageRate($arrayreserve[$viewproduct[$i]])):$arrayreserve[$viewproduct[$i]]."%")."</td>\";\n";
						if (ord($arrayquantity[$viewproduct[$i]])==0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">무제한</td>\";\n";
						else if ($arrayquantity[$viewproduct[$i]]<=0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\"><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
						else $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">{$arrayquantity[$viewproduct[$i]]}</td>\";\n";
						
						$strlist.= "objlist.displayidx=\"<TD class=\\\"td_con1\\\">".($arraydisplay[$viewproduct[$i]]=="Y"?"판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";
						$strlist.= "objlist.deleteidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"images/icon_del1.gif\\\" border=\\\"0\\\" onclick=\\\"Delete('{$arraycode[$viewproduct[$i]]}','{$code}','');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
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
						echo "	<TABLE border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('{$j}','');\">\n";
						echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col>\n";
						echo "	<tr align=\"center\">\n";
						echo "		<TD>{$j}</td>\n";
						echo "		<TD><a href=\"javascript:updown_click('{$j}','up','')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('{$j}','down','')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
						if($vendercnt>0) {
						echo "		<TD class=\"td_con1\"><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\n";
						}
						echo "		<TD class=\"td_con1\">";
						if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
							echo "<img src='".$imagepath.$arraytinyimage[$viewproduct[$i]]."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
						} else {
							echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
						}
						echo "<div id=\"primage{$image_i}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">";
						echo "		<tr bgcolor=\"#FFFFFF\">\n";
						if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" border=\"0\"></td>\n";
						} else {
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
						}
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</div>\n";
						echo "		</td>\n";
						echo "		<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:"")."&nbsp;</td>\n";
						echo "		<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format(exchageRate($arraysellprice[$viewproduct[$i]]))."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($arrayreservetype[$viewproduct[$i]]!="N"?number_format(exchageRate($arrayreserve[$viewproduct[$i]])):$arrayreserve[$viewproduct[$i]]."%")."</TD>\n";
						echo "		<TD class=\"td_con1\">";
						if (ord($arrayquantity[$viewproduct[$i]])==0) echo "무제한";
						else if ($arrayquantity[$viewproduct[$i]]<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
						else echo $arrayquantity[$viewproduct[$i]];
						echo "		</TD>\n";
						echo "		<TD class=\"td_con1\">".($arraydisplay[$viewproduct[$i]]=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
						echo "		<TD class=\"td_con1\"><img src=\"images/icon_del1.gif\" border=\"0\" onclick=\"Delete('{$arraycode[$viewproduct[$i]]}','{$code}','');\" style=\"cursor:hand;\"></td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</td>\n";
						echo "</tr>\n";
						$ii++;
						$image_i++;
					}
				}
				pmysql_free_result($result);
				$strlist.="</script>\n";
				echo $strlist;
				if ($j==0) {
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
				}
			} else {
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
			}
?>
				<TR>
					<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<TR>
				<TD background="images/table_top_line.gif"></TD>
			</TR>
			<tr>
				<td><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" align="center"><a href="javascript:CollectionIn('<?=$code?>','12');"><img src="images/prcollection_searchproduct.gif" border="0"></a></td>
					<td><a href="javascript:move_save('');"><img src="images/icon_sort1.gif" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			</table>
			</TD>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"><a name="cateprcode"></td>
	</tr>
	<tr>
		<td width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_collection_text2.gif" border="0"></td>
		</tr>
		<tr>
			<td width="100%" height="100%" valign="top" style="background-color:#FFFFFF;BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
				<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
				<TR>
					<TD width="100%" background="images/blueline_bg.gif">
					<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
					<col width="250"></col>
					<col width=""></col>
					<col width="250"></col>
					<tr height="30">
						<td><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
						<td align=center><b><span class="font_blue">카테고리내 상품목록</span></b></td>
						<td align="right"><a href="javascript:DivScrollActive('1','divscroll1','List1TableId',1);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive('0','divscroll1','List1TableId',1);"><b>펼침닫기</b></a></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD width="100%">
					<DIV id="divscroll1" style="position:relative;width:100%;height:278px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
					<TABLE id="List1TableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
					<col width=50></col>
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
						<TD class="table_cell">No</TD>
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
			if (strlen($code)==12) {
				$prcode_selected[$prcode] = " bgcolor=\"#EFEFEF\"";
				$prcode_link[$prcode] = "<a name=\"prcodelink\">";

				$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
				$sql.= "WHERE b.c_category LIKE '{$code}%' ";
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$result = pmysql_query($sql,get_db_conn());
				$t_count = @pmysql_num_rows($result);
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\">{$prcode_link[$row->productcode]}</TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\"{$prcode_selected[$row->productcode]} onclick=\"SelectList('{$row->productcode}')\" id=\"pidx_{$row->productcode}\" onmouseover=\"if(onMouseColor('{$row->productcode}'))this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(onMouseColor('{$row->productcode}'))this.style.backgroundColor='';\" style=\"cursor:hand;\">\n";
					echo "	<TD class=\"td_con2\">{$number}<br><img src=\"images/btn_select1a.gif\" border=\"0\"></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender});\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\n";
					}
					echo "	<TD class=\"td_con1\">";
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
						echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
					} else {
						echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
					}
					echo "<div id=\"primage{$image_i}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
					echo "		<tr bgcolor=\"#FFFFFF\">\n";
					if (ord($row->tinyimage)) {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
					} else {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "	</td>\n";
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format(exchageRate($row->sellprice))."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="N"?number_format(exchageRate($row->reserve)):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (ord($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><img src=\"images/icon_newwin1.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"ProductInfo('{$row->productcode}');\"></td>\n";
					echo "</tr>\n";
					$cnt++;
					$image_i++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
			}
?>
					<TR>
						<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
					</TR>
					</table>
					</div>
					</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD background="images/blueline_bg.gif" style="padding-top:3pt; padding-bottom:3pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width="120"></col>
					<col width=""></col>
					<col width="120"></col>
					<tr>
						<td><img width="0" height="0"></td>
						<td align="center"><img src="images/product_collectionlist_img.gif" border="0"></td>
						<td><img width="0" height="0"></td>
					</tr>
					<tr>
						<td><img width="0" height="0"></td>
						<td align="center"><b><span class="font_blue">선택된 상품에 등록된 관련상품목록</span></b></td>
						<td align="right"><a href="javascript:DivScrollActive('1','divscroll2','List2TableId',2);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive('0','divscroll2','List2TableId',2);"><b>펼침닫기</b></a></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif"></TD>
				</TR>
				<tr>
					<td valign="top">
					<DIV id="divscroll2" style="position:relative;width:100%;height:278px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
					<TABLE id="List2TableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
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
						<TD class="table_cell1" colspan="2">상품명</TD>
						<TD class="table_cell1">판매가격</TD>
						<TD class="table_cell1">수량</TD>
						<TD class="table_cell1">상태</TD>
						<TD class="table_cell1">삭제</TD>
					</TR>
<?php
					$coll_prcode="";
					$sql = "SELECT collection_list FROM tblcollection WHERE productcode = '{$prcode}' ";
					$result = pmysql_query($sql,get_db_conn());
					if($row = pmysql_fetch_object($result)){
						$cnt_prcode=$row->collection_list;
						$coll_prcode=str_replace(',','\',\'',$cnt_prcode);
					}
					pmysql_free_result($result);
					if(ord($coll_prcode)){
						$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
						$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
						$sql.= "FROM tblproduct ";
						$sql.= "WHERE productcode IN ('{$coll_prcode}')";
						$result = pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$arraycode[$row->productcode]=$row->productcode;
							$arrayquantity[$row->productcode]=$row->quantity;
							$arraydisplay[$row->productcode]=$row->display;
							$arrayoption_price[$row->productcode]=$row->option_price;
							$arrayproductname[$row->productcode]=$row->productname;
							$arrayproduction[$row->productcode]=$row->production;
							$arraysellprice[$row->productcode]=$row->sellprice;
							$arrayconsumerprice[$row->productcode]=$row->consumerprice;
							$arraybuyprice[$row->productcode]=$row->buyprice;
							$arrayreserve[$row->productcode]=$row->reserve;
							$arrayreservetype[$row->productcode]=$row->reservetype;
							$arrayaddcode[$row->productcode]=$row->addcode;
							$arrayvender[$row->productcode]=$row->vender;
							$arraytinyimage[$row->productcode]=$row->tinyimage;
							$arrayselfcode[$row->productcode]=$row->selfcode;
							$arrayassembleuse[$row->productcode]=$row->assembleuse;
						}

						$viewproduct = explode(",",$cnt_prcode);
						$cnt =count($viewproduct);
						$j=0;
						$strlist="<script>\n";
						$jj=$cnt;
						$ii=0;
						for($i=0;$i<$cnt;$i++){
							if(ord($arraycode[$viewproduct[$i]])){
								$j++;
								$strlist.= "var objlist=new ObjList();\n";
								$strlist.= "objlist.num=\"{$j}\";\n";
								$strlist.= "all_list_i1[objlist.num]={$ii};\n";
								$strlist.= "objlist.productcode=\"{$arraycode[$viewproduct[$i]]}\";\n";
								if($vendercnt>0) {$strlist.= "objlist.venderidx=\"<TD class=\\\"td_con1\\\"><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\\\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\\\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\";\n";}
								if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
									$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" height=\\\"40\\\" width=\\\"40\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
								} else {
									$strlist.= "objlist.imgidx=\"<TD class=\\\"td_con1\\\"><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"{$Dir}images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
								}
								$strlist.= "objlist.nameidx=\"<TD class=\\\"td_con1\\\" align=\\\"left\\\" style=\\\"word-break:break-all;\\\"><img src=\\\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:""))."&nbsp;</td>\";\n";
								$strlist.= "objlist.sellidx=\"<TD align=\\\"right\\\" class=\\\"td_con1\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</td>\";\n";
								if (ord($arrayquantity[$viewproduct[$i]])==0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">무제한</td>\";\n";
								else if ($arrayquantity[$viewproduct[$i]]<=0) $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\"><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
								else $strlist.= "objlist.quantityidx=\"<TD class=\\\"td_con1\\\">{$arrayquantity[$viewproduct[$i]]}</td>\";\n";
								
								$strlist.= "objlist.displayidx=\"<TD class=\\\"td_con1\\\">".($arraydisplay[$viewproduct[$i]]=="Y"?"판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";
								$strlist.= "objlist.deleteidx=\"<TD class=\\\"td_con1\\\"><img src=\\\"images/icon_del1.gif\\\" border=\\\"0\\\" onclick=\\\"Delete('{$arraycode[$viewproduct[$i]]}','{$code}','1');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
								$strlist.= "objlist.no=\"".($jj--)."\";\n";
								$strlist.= "objlist.sort=\"{$ii}\";\n";
								$strlist.= "objlist.selected=false;\n";
								$strlist.= "all_list1[{$ii}]=objlist;\n";
								$strlist.= "objlist=null;\n";
								
								echo "<tr>\n";
								echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "	<td id=\"idx1_inner_{$ii}\" colspan=\"{$colspan}\" style=\"background-color:'#FFFFFF';\" onmouseover=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';\" style=\"cursor:hand;\">\n";
								echo "	<TABLE border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('{$j}','1');\">\n";
								echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col>\n";
								echo "	<tr align=\"center\">\n";
								echo "		<TD>{$j}</td>\n";
								echo "		<TD><a href=\"javascript:updown_click('{$j}','up','1')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('{$j}','down','1')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
								if($vendercnt>0) {
									echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\n";
								}
								echo "		<TD class=\"td_con1\">";
								if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
									echo "<img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" height=\"40\" width=\"40\" border=\"1\" onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
								} else {
									echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
								}
								echo "<div id=\"primage{$image_i}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
								echo "		<tr bgcolor=\"#FFFFFF\">\n";
								if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
									echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" border=\"0\"></td>\n";
								} else {
									echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
								}
								echo "		</tr>\n";
								echo "		</table>\n";
								echo "		</div>\n";
								echo "		</td>\n";
								echo "		<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:"")."&nbsp;</td>\n";
								echo "		<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</TD>\n";
								echo "		<TD class=\"td_con1\">";
								if (ord($arrayquantity[$viewproduct[$i]])==0) echo "무제한";
								else if ($arrayquantity[$viewproduct[$i]]<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
								else echo $arrayquantity[$viewproduct[$i]];
								echo "		</TD>\n";
								echo "		<TD class=\"td_con1\">".($arraydisplay[$viewproduct[$i]]=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
								echo "		<TD class=\"td_con1\"><img src=\"images/icon_del1.gif\" border=\"0\" onclick=\"Delete('{$arraycode[$viewproduct[$i]]}','{$prcode}','1');\" style=\"cursor:hand;\"></td>\n";
								echo "	</tr>\n";
								echo "	</table>\n";
								echo "	</td>\n";
								echo "</tr>\n";
								$ii++;
								$image_i++;
							}
						}
						pmysql_free_result($result);
						$strlist.="</script>\n";
						echo $strlist;
						if ($j==0) {
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
						}
					} else {
						echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
					}
?>
					<TR>
						<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
					</TR>
					</TABLE>
					</div>
					</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif"></TD>
				</TR>
				<tr>
					<td><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></td>
				</tr>
				<tr>
					<td align="center">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" align="center"><a href="javascript:CollectionIn('<?=$prcode?>','18');"><img src="images/prcollection_searchproduct.gif" border="0"></a></td>
						<td><a href="javascript:move_save('1');"><img src="images/icon_sort1.gif" border="0"></a></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				</TABLE>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</form>
	<form name=cForm action="product_collectionin.php" method=post target=collection>
	<input type=hidden name=type value="display">
	<input type=hidden name=productcode>
	</form>
	</table>
	</td>
</tr>
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
</table>
<?php
$script_echo = "<script>";
if($Scrolltype_exp)
{
	for($i=0; $i<count($Scrolltype_exp); $i++)
	{
		if($Scrolltype_exp[$i]>0)
			$script_echo .= "DivScrollActive('1','divscroll{$i}','List{$i}TableId',{$i});";
		else
			$script_echo .= "DivScrollActive('0','divscroll{$i}','List{$i}TableId',{$i});";
	}
}

if (ord($aname)) {
	if(ord($prcode))
		$script_echo .= "location.hash='prcodelink';\n";
	else
		$script_echo .= "location.hash='cateprcode';\n";
}

$script_echo .= "</script>";
?>
<?=$script_echo?>
<?=$onload?>
</div>
</body>
</html>

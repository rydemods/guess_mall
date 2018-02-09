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

$max=50;
$startname=array("","카테고리 신상품","카테고리 인기상품","카테고리 추천상품");

$Scrolltype=$_REQUEST["Scrolltype"];

$code=$_POST["code"];
$mode=$_POST["mode"];
$selcodes=$_POST["selcodes"];
$change=$_POST["change"];
$prcode=$_POST["prcode"];
$selcode=$_POST["selcode"];
$start=$_POST["start"];
${"chk_start".$start} = "checked";

if ($mode=="sequence" && $change=="Y" && ord($selcodes) && ord($code) && ord($start)) {
	$sql = "UPDATE tblstartcode SET start_list = '{$selcodes}' ";
	$sql.= "WHERE code='{$code}' AND start_num='{$start}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('카테고리 진열상품 순서 조정이 완료되었습니다.');</script>\n";
} elseif ($mode=="modify" && ord($selcodes) && ord($code) && ord($start)) {
	$sql = "UPDATE tblstartcode SET start_list = '{$selcodes}' ";
	$sql.= "WHERE code='{$code}' AND start_num='{$start}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품을 카테고리 진열상품에 추가하였습니다.');</script>\n";
} elseif ($mode=="insert" && ord($selcodes) && ord($code) && ord($start)) {
	$sql = "INSERT INTO tblstartcode(code,start_num,start_list) VALUES (
	'{$code}', 
	'{$start}', 
	'{$selcodes}')";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품을 카테고리 진열상품에 추가하였습니다.');</script>\n";
} elseif ($mode=="delete" && ord($code) && ord($start)) {
	if(ord($selcodes)==0) {
		$sql = "DELETE FROM tblstartcode WHERE code='{$code}' AND start_num='{$start}' ";
	} else {
		$sql = "UPDATE tblstartcode SET start_list = '{$selcodes}' ";
		$sql.= "WHERE code='{$code}' AND start_num='{$start}' ";
	}
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품을 카테고리 진열상품에서 삭제하였습니다.');</script>\n";
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
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
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
	innerHtmlStr+=argObj.deleteidx;
	document.all["idx_inner_"+argObj.sort].innerHTML="<div class=\"table_none\"><TABLE onclick=\"ChangeList('"+argObj.num+"');\" border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed\"><col width=40></col><col width=12></col><?=($vendercnt>0?"<col width=70></col>":"")?><col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><col width=45></col><tr align=\"center\">"+innerHtmlStr+"</tr></table></div>";
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
	this.deleteidx		= new String((argc > 8) ? argv[8] : "");
	this.no				= new String((argc > 9) ? argv[9] : "");
	this.sort			= new String((argc > 10) ? argv[10] : "");
	this.selected		= new Boolean((argc > 11) ? argv[11] : false );
	<?php if($vendercnt>0) {echo "this.venderidx		= new String((argc > 12) ? argv[12] : \"\");\n";}?>
}

function move_save()
{
	if (document.form1.change.value!="Y") {
		alert("순서 변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	val="";
	for(i=0;i<all_list.length;i++)
	{
		val+=","+all_list[i].productcode;
	}
	
	if(val.length>0)
	{
		val=val.substring(1);
		document.form1.mode.value = "sequence";
		document.form1.selcodes.value=val;
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
/*
	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
*/
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function Insertstart() {
	if (document.form1.prcode.value.length==0) {
		alert("카테고리 진열상품에 추가할 상품을 선택하세요.");
		document.form1.prcode.focus();
		return;
	}
	num = all_list.length-1;
	if(num+1>=50){
		alert('카테고리 진열상품은 최대 50개까지 등록가능합니다.');
		return;
	}
	if (confirm("해당 상품을 카테고리 진열상품으로 포함하시겠습니까?")){
		temp = "";
		for (i=0;i<=num;i++) {
			if(all_list[i].productcode == document.form1.prcode.value){
				alert('이미 등록된 상품입니다.');
				return;
			} 
			if (i==0) temp = all_list[i].productcode;
			else temp+=","+all_list[i].productcode;
		}
		if(num==-1) temp=document.form1.prcode.value;
		else temp+=","+document.form1.prcode.value;
		document.form1.selcodes.value = temp;
		document.form1.submit();
	}
}

function Changestart(val) {
	if (val!="<?=$start?>") {
		document.form1.submit();
	}
}

function Delete(delcode) {
	ProductInfoStop = "1";
	if(!confirm("해당 상품을 카테고리 진열상품에서 삭제하시겠습니까?")) return;
	val="";
	for(i=0;i<all_list.length;i++)
	{
		if(delcode!=all_list[i].productcode){
			val+=","+all_list[i].productcode;
		}
	}

	if(val.length>0)
		val=val.substring(1);
	
	document.form1.mode.value="delete";
	document.form1.selcodes.value=val;
	document.form1.submit();
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
//-->
</SCRIPT>

<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=prcode>
<input type=hidden name=selcode>
<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
	<div class="main_view_setup_wrap" style="margin-top:15px;">
		<div class="group">
			<input type=radio id="idx_start1" name=start value="1" <?=$chk_start1?> onClick="Changestart(this.value);"> 
			<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_start1 class="font_d12">신상품</label>
			<input type=radio id="idx_start2" name=start value="2" <?=$chk_start2?> onClick="Changestart(this.value);"> 
			<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_start2 class="font_d12">인기상품</label>
			<input type=radio id="idx_start3" name=start value="3" <?=$chk_start3?> onClick="Changestart(this.value);"> 
			<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_start3 class="font_d12">추천상품</label>
			
		</div>

		<div class="list">

			<div class="table_main_setup"  id="divscroll" style="height:400px;overflow-x:hidden;overflow-y:auto;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				
					<colgroup>
					<?php
						$colspan=9;
						if($vendercnt>0) $colspan++;
					?>
						<col width=30></col>
						<col width=></col>
						<?php if($vendercnt>0){?>
						<col width=></col>
						<?php }?>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						
					</colgroup>
					<!--tr>
						<th>No</th>	
						<?php if($vendercnt>0){?>
						<th>입점업체</th>
						<?php }?>
						<th colspan="2">상품명</th>	
						<th>판매가격</th>	
						<th>수량</th>	
						<th>상태</th>	
						<th>수정</th>	
						<th>삭제</th>	
					</tr-->
<?php
			$image_i=0;
			if(ord($start)) {
				$mode="insert";
				$sp_prcode="";
				$sql = "SELECT start_list FROM tblstartcode ";
				$sql.= "WHERE code = '{$code}' AND start_num='{$start}' ";
				$result = pmysql_query($sql,get_db_conn());
				if($row = pmysql_fetch_object($result)){
					$cnt_prcode=$row->start_list;
					$sp_prcode=str_replace(',','\',\'',$cnt_prcode);
					$mode="modify";
				}
				
				pmysql_free_result($result);

				if(ord($sp_prcode)) {
					$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
					$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
					$sql.= "FROM tblproduct ";
					$sql.= "WHERE productcode IN ('{$sp_prcode}')";
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
						$arrayaddcode[$row->productcode]=$row->addcode;
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
									if($vendercnt>0) {$strlist.= "objlist.venderidx=\"<TD><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\\\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\\\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\";\n";}
									if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
										$strlist.= "objlist.imgidx=\"<TD style=\\\"position:relative;\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" height=\\\"40\\\" width=\\\"40\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
									} else {
										$strlist.= "objlist.imgidx=\"<TD style=\\\"position:relative;\\\"><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"{$Dir}images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
									}
									$strlist.= "objlist.nameidx=\"<TD align=\\\"left\\\" style=\\\"word-break:break-all;\\\"><img src=\\\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:""))."&nbsp;</td>\";\n";
									$strlist.= "objlist.sellidx=\"<TD align=\\\"right\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</td>\";\n";
									if (ord($arrayquantity[$viewproduct[$i]])==0) $strlist.= "objlist.quantityidx=\"<TD>무제한</td>\";\n";
									else if ($arrayquantity[$viewproduct[$i]]<=0) $strlist.= "objlist.quantityidx=\"<TD><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
									else $strlist.= "objlist.quantityidx=\"<TD>{$arrayquantity[$viewproduct[$i]]}</td>\";\n";
									
									$strlist.= "objlist.displayidx=\"<TD>".($arraydisplay[$viewproduct[$i]]=="Y"?"판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";

									$strlist.= "objlist.editidx=\"<TD><img src=\\\"images/icon_newwin1.gif\\\" border=\\\"0\\\" onclick=\\\"ProductInfo('{$arraycode[$viewproduct[$i]]}');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
									$strlist.= "objlist.deleteidx=\"<TD><img src=\\\"images/icon_del1.gif\\\" border=\\\"0\\\" onclick=\\\"Delete('{$arraycode[$viewproduct[$i]]}');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
									$strlist.= "objlist.no=\"".($jj--)."\";\n";
									$strlist.= "objlist.sort=\"{$ii}\";\n";
									$strlist.= "objlist.selected=false;\n";
									$strlist.= "all_list[{$ii}]=objlist;\n";
									$strlist.= "objlist=null;\n";
									
									
									echo "<tr>\n";
									echo "	<td id=\"idx_inner_{$ii}\" colspan=\"{$colspan}\" style=\"background-color:'#FFFFFF';\" onmouseover=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';\" style=\"cursor:hand;\">\n";
									echo " <div class=\"table_none\"> ";
									echo "	<TABLE border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('{$j}');\">\n";
									echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><col width=45></col>\n";
									echo "	<tr align=\"center\">\n";
									echo "		<TD>{$j}</td>\n";
									echo "		<TD><a href=\"javascript:updown_click('{$j}','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('{$j}','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
									if($vendercnt>0) {
										echo "		<TD><B>".(ord($venderlist[$arrayvender[$viewproduct[$i]]]->vender)?"<span onclick=\"viewVenderInfo({$arrayvender[$viewproduct[$i]]});\">{$venderlist[$arrayvender[$viewproduct[$i]]]->id}</span>":"-")."</B></td>\n";
									}
									echo "		<TD style=\"position:relative;\">";
									if (ord($arraytinyimage[$viewproduct[$i]]) && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])){
										echo "<img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" height=\"40\" width=\"40\" border=\"1\" onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
									} else {
										echo "<img height=\"40\" width=\"40\" src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
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
									echo "		<TD align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:"")."&nbsp;</td>\n";
									echo "		<TD align=right><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</TD>\n";
									echo "		<TD>";
									if (ord($arrayquantity[$viewproduct[$i]])==0) echo "무제한";
									else if ($arrayquantity[$viewproduct[$i]]<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
									else echo $arrayquantity[$viewproduct[$i]];
									echo "		</TD>\n";
									echo "		<TD>".($arraydisplay[$viewproduct[$i]]=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
									echo "		<TD><img src=\"images/icon_newwin1.gif\" border=\"0\" onclick=\"ProductInfo('{$arraycode[$viewproduct[$i]]}');\" style=\"cursor:hand;\"></td>\n";
									echo "		<TD><img src=\"images/icon_del1.gif\" border=\"0\" onclick=\"Delete('{$arraycode[$viewproduct[$i]]}');\" style=\"cursor:hand;\"></td>\n";
									echo "	</tr>\n";
									echo "	</table>\n";
									echo "	</div> \n";
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
								echo "<tr><td colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
							}
						} else {
							echo "<tr><td colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
						}
					} else {
						echo "<tr><td colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
					}
		?>		

				<input type=hidden name=mode value="<?=$mode?>">
				
				
				<input type=hidden name=change>
				<input type=hidden name=selcodes>
				<input type=hidden name=num value="<?=$count?>">
	
					
				</table>
			</div>
		</div>	

	</div>
</form>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<TR>
	<TD colspan="<?=$colspan?>" align=center><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></TD>
</TR>
<tr>
	<TD align=center><a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a></TD>
</tr>
</table>
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
<?=$onload?>



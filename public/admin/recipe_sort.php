<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

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

if($keyword=="상품명 상품코드")$keyword="";

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";


$likecode="";
if($code_a) $likecode.=$code_a; 
if($code_b) $likecode.=$code_b; else $code_b='000';
if($code_c) $likecode.=$code_c; else $code_c='000';
if($code_d) $likecode.=$code_d; else $code_d='000';


$checked["display"][$display] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["check_vperiod"][$vperiod] = "checked";

$code=$code_a.$code_b.$code_c.$code_d;

$prcode=$_POST["prcode"];
$change=$_POST["change"];
$prcodes=$_POST["prcodes"];

if(strlen($code)==12) {
	$sql = "SELECT type FROM tblrecipecode WHERE code_a='".substr($code,0,3)."' ";
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
			$sql = "UPDATE tblrecipe SET date = '".$date1.$date."' ";
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
	$onload="<script>window.onload=function(){ alert('상품순서 변경이 완료되었습니다.');}</script>\n";

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

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>

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

	if(updownValue=='up' || updownValue=='down')
		ekey = updownValue;
	else{
		ekey = event.keyCode;
	}

	if(selnum!="" && (ekey=='38' || ekey=='40' || ekey=="up" || ekey=="down")) {

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
	document.all["idx_inner_"+argObj.sort].innerHTML="<div class=\"table_none\"><TABLE onclick=\"ChangeList('"+argObj.num+"');\" border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed\"><col width=40></col><col width=12></col><?=($vendercnt>0?"<col width=70></col>":"")?><col width=100></col><col width=></col><col width=90></col><col width=70></col><col width=70></col><col width=70></col><tr align=\"center\">"+innerHtmlStr+"</tr></table></div>";
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

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>상품 진열순서 설정</span></p></div></div>
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
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=prcodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">				
			<tr>
				<td>
				<div class="title_depth3">상품 진열순서 설정</div>
				<div class="title_depth3_sub"><span>각각의 카테고리에 등록된 상품의 진열 순서를 변경할 수 있습니다.</span></div>
				</td>
            </tr>
            <tr>
            	<td>
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<!--tr>
							<th><span>상품검색</span></th>
							<td><input class="input_bd_st01" type="text" name="keyword" onfocus="this.value=''; this.style.color='#000000'; this.style.textAlign='left';" <?=$keyword?"value=".$keyword:"style=\"color:'#bdbdbd';text-align:center;\" value=\"상품명 상품코드\""?>></td>
						</tr-->
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblrecipecode WHERE group_code!='NO' ";
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
						<!--tr>
							<th><span>등록일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(this)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(this)" value="<?=$search_end?>"/>
								<input type=radio id=idx_vperiod0 name=vperiod value="0" checked style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px;" onclick="OnChangePeriod(this.value)" <?=$checked["check_vperiod"][0]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod0>전체</label>
								<input type=radio id=idx_vperiod1 name=vperiod value="1" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px;" onclick="OnChangePeriod(this.value)" <?=$checked["check_vperiod"][1]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod1>오늘</label>
								<input type=radio id=idx_vperiod2 name=vperiod value="2" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px;" onclick="OnChangePeriod(this.value)" <?=$checked["check_vperiod"][2]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod2>1주일</label>
								<input type=radio id=idx_vperiod3 name=vperiod value="3" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px;" onclick="OnChangePeriod(this.value)" <?=$checked["check_vperiod"][3]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod3>이달</label>
								<input type=radio id=idx_vperiod4 name=vperiod value="4" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px;" onclick="OnChangePeriod(this.value)" <?=$checked["check_vperiod"][4]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod4>올해</label>
							</td>
						</tr>
						<tr>
							<th><span>상품금액별 검색</span></th>
							<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						</tr-->
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열</td>
						</tr>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>
		
				
				<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed;">
					<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
					<tr>
						<td width="100%" height="100%" valign="top" bgcolor="#FFFFFF" style="padding-left:5px;padding-right:5px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="text-align:right">
										<a href="javascript:DivScrollActive(1);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive(0);"><b>펼침닫기</b></a>
									</td>
								</tr>
								<TR>
									<TD width="100%">
									<DIV id="divscroll" style="position:relative;z-index:1;width:100%; bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
									<div id="jumpdiv" style="position:absolute; display:none;">
									<table border="0" cellspacing="1" cellpadding="0" bgcolor="#B9B9B9" width="210">
									<col width="100"></col>
									<col width=""></col>
									<tr bgcolor="#FFFFFF">
										<td bgcolor="#F8F8F8" style="padding:2px;" align="center"><img src="images/icon_point5.gif" border="0"><b>이동위치 No</b></td>
										<td style="padding:3px;"><input type=text name="jumpnumber" value="" size="4" maxlength="5" style="height:19;font-size:8pt"><a href="javascript:jumpgo();"><img src="images/btn_ok3.gif" border="0" align="absmiddle" hspace="5"></a></td>
									</tr>
									</table>
									</div>
									<div class="table_style02">
									<TABLE id="ListLTableId" border="0" cellSpacing="0" cellPadding="0" width="100%">	
									<colgroup>
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
									<col width=90></col>
									<col width=70></col>
									<col width=70></col>
									<col width=70></col>
									</colgroup>
									<TR align="center">
										<th colspan="2">No</th>
										<?php if($vendercnt>0){?>
										<th>입점업체</th>
										<?php }?>
										<th colspan="2">상품명/진열코드/특이사항</th>
										<th>판매가격</th>
										<th>수량</th>
										<th>상태</th>
										<th>수정</th>
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

										if ($likecode) $qry= "AND productcode LIKE '{$likecode}%' ";
										if ($keyword) $qry.= "AND (productname || productcode)LIKE '%{$keyword}%' ";
										if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
										elseif($s_check==2)$qry.="AND quantity <= 0 ";
										if($display==1)	$qry.="AND display='Y' ";
										elseif($display==2)	$qry.="AND display='N'";
										if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
										if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";


										$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
										$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, modifydate ";
										$sql.= "FROM tblproduct WHERE 1=1 ";
										$sql.= $qry." ";
										$sql.= "ORDER BY date DESC ";
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
												if($vendercnt>0) {$strlist.= "objlist.venderidx=\"<TD><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\\\"viewVenderInfo({$row->vender});\\\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\";\n";}
												if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
													$strlist.= "objlist.imgidx=\"<TD><img src=\\\"".$imagepath.$row->tinyimage."\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$row->tinyimage."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
												} else {
													$strlist.= "objlist.imgidx=\"<TD><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage{$image_i}')\\\" onMouseOut=\\\"ProductMouseOut('primage{$image_i}');\\\"><div id=\\\"primage{$image_i}\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"{$Dir}images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
												}
												$strlist.= "objlist.nameidx=\"<TD style=\\\"word-break:break-all; text-align:left; padding-left:10px\\\"><img src=\\\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:""))."&nbsp;</td>\";\n";
												$strlist.= "objlist.sellidx=\"<TD style=\\\"text-align:right; padding-right:20px\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format($row->sellprice)."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</td>\";\n";
												if (ord($row->quantity)==0) $strlist.= "objlist.quantityidx=\"<TD>무제한</td>\";\n";
												else if ($row->quantity<=0) $strlist.= "objlist.quantityidx=\"<TD><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
												else $strlist.= "objlist.quantityidx=\"<TD>{$row->quantity}</td>\";\n";
												
												$strlist.= "objlist.displayidx=\"<TD>".($row->display=="Y"?"<font color=\\\"#0000FF\\\">판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";

												$strlist.= "objlist.editidx=\"<TD><img src=\\\"images/icon_newwin1.gif\\\" border=\\\"0\\\" onclick=\\\"ProductInfo('{$row->productcode}','YES');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
												$strlist.= "objlist.no=\"".($jj--)."\";\n";
												$strlist.= "objlist.sort=\"{$ii}\";\n";
												$strlist.= "objlist.selected=false;\n";
												$strlist.= "all_list[{$ii}]=objlist;\n";
												$strlist.= "objlist=null;\n";
												
												echo "<tr>\n";
												echo "	<td id=\"idx_inner_{$ii}\" colspan=\"{$colspan}\" style=\"background-color:'#FFFFFF';\" onmouseover=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';\" style=\"cursor:hand;\">\n";
												echo "<div class=\"table_none\">";
												echo "	<TABLE border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('{$j}');\">\n";
												echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=100></col><col width=></col><col width=90></col><col width=70></col><col width=70></col><col width=70></col>\n";
												echo "	<tr align=\"center\">\n";
												echo "		<TD>{$j}</td>\n";
												echo "		<TD><a href=\"javascript:updown_click('{$j}','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('{$j}','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
												if($vendercnt>0) {
													echo "	<TD><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender});\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\n";
												}
												echo "		<TD>";
												if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
													echo "<img src=\"".$imagepath.$row->tinyimage."\" border=\"1\" onMouseOver=\"ProductMouseOver('primage{$image_i}')\" onMouseOut=\"ProductMouseOut('primage{$image_i}');\">";
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
												echo "		<TD style=\"word-break:break-all; text-align:left; padding-left:10px\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
												echo "		<TD style=\"text-align:right; padding-right:20px\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
												echo "		<TD>";
												if (ord($row->quantity)==0) echo "무제한";
												else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
												else echo $row->quantity;
												echo "		</TD>\n";
												echo "		<TD>".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
												echo "		<TD><img src=\"images/icon_newwin1.gif\" border=\"0\" onclick=\"ProductInfo('{$row->productcode}','YES');\" style=\"cursor:hand;\"></td>\n";
												echo "	</tr>\n";
												echo "	</table>\n";
												echo "</div>";
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
											echo "<tr><td colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
										}
										
									} else {
										$page_numberic_type="";
										echo "<tr><td colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
									}
										
							?>
									</TABLE>
									</div>
									</div>
									</td>
								</TR>
								<input type=hidden name=change>
								<input type=hidden name=codes>
						<? if($cnt>0){?>
								<tr>
									<TD colspan="<?=$colspan?>" align=center><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></TD>
									
								</tr>
								<TR>
									<TD align=center>
										<a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a>
									</TD>
								</TR>
						<?}?>
								
							</table>
						</td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td>
							<!-- 매뉴얼 -->
							<div class="sub_manual_wrap">
								<div class="title"><p>매뉴얼</p></div>
								
								<dl>
									<dt><span>상품 진열순서 설정시 주의사항</span></dt>
									<dd>
										- 카테고리의 상품정렬이 [상품 등록/수정날짜 순서], [상품 등록/수정날짜 순서+품절상품 뒤로] 일때만 상품 진열순서 설정에 따라 출력됩니다.<br>
									<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span> 에서 카테고리의 상품정렬을 확인할 수 있습니다.</a><br>
										- 진열순서 조정을 위해 우측 버튼을 사용할 경우 [저장하기] 를 클릭해야만 적용됩니다.<br>
										- 진열순서 조정을 위해 "진열상품 순서 저장하기"을 사용할 경우 [적용하기] 를 클릭해야만 적용됩니다.<br>
										- <b>하위카테고리가 있는 카테고리의 경우</b> 하위카테고리의 상품 순서를 변경하시면 해당 상품이 맨 위에 위치합니다.
									</dd>				
								</dl>
								
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			</form>
			</table>
			</td>
		</tr>
		</TABLE>
		</td>
		</tr>
	</table>
	</td>
</tr>
</table>

<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
	
<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>

<?php
include("copyright.php");
?>
<?="<script>DivScrollActive(".(int)$Scrolltype.");</script>"?>
<?=$onload?>
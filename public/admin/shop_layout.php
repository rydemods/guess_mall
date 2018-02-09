<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$change=$_POST["change"];
$mainsort=$_POST["mainsort"];
$shopwidth=(int)$_POST["shopwidth"];
$maintype=$_POST["maintype"];
$mainused=$_POST["mainused"];
$shopbgtype=$_POST["shopbgtype"];
$shopbgtypemain=$_POST["shopbgtypemain"];
$bgcolor=$_POST["bgcolor"];
$bgclear=$_POST["bgclear"];

$bgimage = $_FILES['bgimage']['tmp_name'];
$bgimage_type = $_FILES['bgimage']['type'];
$bgimage_name = $_FILES['bgimage']['name'];
$bgimage_size = $_FILES['bgimage']['size'];

$bgimagefreeze=$_POST["bgimagefreeze"];
$bgimagelocat=$_POST["bgimagelocat"];

$bgimagerepet=$_POST["bgimagerepet"];
$mousekeyright=$_POST["mousekeyright"];
$mousekeydrag=$_POST["mousekeydrag"];
$mousekeyover=$_POST["mousekeyover"];
$mousekeyboard=$_POST["mousekeyboard"];

$imagepath = $Dir.DataDir."shopimages/etc/";
$image_name="background.gif";

if ($type=="up") {
	if($shopbgtype == "I")
	{
		$ext = strtolower(pathinfo($bgimage_name,PATHINFO_EXTENSION));
		if (ord($bgimage_name) && $ext=="gif" && $bgimage_size<=153600) {			
			move_uploaded_file($bgimage,"$imagepath$image_name");
			chmod("$imagepath$image_name",0664);
		} else {
			if (ord($bgimage_name)) $msg="올리실 이미지는 150KB 이하의 gif파일만 됩니다.";
		}
	}
	else
		@unlink("$imagepath$image_name");

	$layoutdata_str="";
	if ($shopwidth>0){
		$layoutdata_str[] = "SHOPWIDTH=".$shopwidth;
	}
	
	$layoutdata_str[]= "MAINTYPE=".$maintype;
	$layoutdata_str[]= "MAINUSED=".@implode("", $mainused);
	$layoutdata_str[]= "MAINSORT=".$mainsort;
	$layoutdata_str[]= "MOUSEKEY=".$mousekeyright.$mousekeydrag.$mousekeyover.$mousekeyboard;
	$layoutdata_str[]= "SHOPBGTYPE=".$shopbgtype.$shopbgtypemain;
	
	if($shopbgtype == "B")
		$layoutdata_str[]= "BGCOLOR={$bgclear}#".$bgcolor;
	if($shopbgtype == "I")
		$layoutdata_str[]= "BACKGROUND=".$bgimagefreeze.$bgimagelocat.$bgimagerepet;
	
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "layoutdata		= '".implode("", $layoutdata_str)."' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('정보 수정이 완료되었습니다. $msg'); }</script>";
}

$sql = "SELECT layoutdata FROM tblshopinfo";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

if(strlen($row->layoutdata)<1)
	$row->layoutdata="SHOPWIDTH=MAINTYPE=BMAINUSED=INBHMAINSORT=INBHGAMOUSEKEY=NNNNSHOPBGTYPE=NNBGCOLOR=N#FFFFFFBACKGROUND=YAA";

$layoutdata=array();
if(ord($row->layoutdata)) {
	$laytemp=explode("",$row->layoutdata);
	$laycnt=count($laytemp);
	for ($layi=0;$layi<$laycnt;$layi++) {
		$laytemp2=explode("=",$laytemp[$layi]);
		if(isset($laytemp2[1])) {
			$layoutdata[$laytemp2[0]]=$laytemp2[1];
		} else {
			$layoutdata[$laytemp2[0]]="";
		}
	}
}

if(ord($layoutdata["MAINTYPE"])==0)
	$layoutdata["MAINTYPE"] = "B";
if(ord($layoutdata["MAINUSED"])==0)
	$layoutdata["MAINUSED"] = "INBH";
if(ord($layoutdata["MAINSORT"])==0)
	$layoutdata["MAINSORT"] = "INBHGA";
if(ord($layoutdata["MOUSEKEY"])==0)
	$layoutdata["MOUSEKEY"] = "NNNN";
if(ord($layoutdata["SHOPBGTYPE"])==0)
	$layoutdata["SHOPBGTYPE"] = "NN";
if(ord($layoutdata["BGCOLOR"])==0)
	$layoutdata["BGCOLOR"] = "N#FFFFFF";
if(ord($layoutdata["BACKGROUND"])==0)
	$layoutdata["BACKGROUND"] = "NAA";

$mainsort="";
$bgcolor="";

$maintype_checked[$layoutdata["MAINTYPE"]] = "checked";

for($i=1; $i<strlen($layoutdata["MAINUSED"]); $i++)
{
	$mainused_checked[substr($layoutdata["MAINUSED"],$i,1)] = "checked";
}

for($i=1; $i<strlen($layoutdata["MAINSORT"]); $i++)
{
	$mainsort[] = substr($layoutdata["MAINSORT"],$i,1);
}

for($i=0; $i<strlen($layoutdata["MOUSEKEY"]); $i++)
{
	$mousekey_checked[][substr($layoutdata["MOUSEKEY"],$i,1)] = "checked";
}
$shopbgtype = $layoutdata["SHOPBGTYPE"][0];
$shopbgtype_checked[$layoutdata["SHOPBGTYPE"][0]] = "checked";
$shopbgtypemain_checked[$layoutdata["SHOPBGTYPE"][1]] = "checked";

if(strlen($layoutdata["BGCOLOR"]) == 0)
	$layoutdata["BGCOLOR"] = "N#FFFFFF";
if(strlen($layoutdata["BACKGROUND"]) == 0)
	$layoutdata["BACKGROUND"] = "YAA";

$bgclear_checked[@$layoutdata["BGCOLOR"][0]] = "checked";
$bgcolor = @substr($layoutdata["BGCOLOR"],2);

$bgimagefreeze_checked[@$layoutdata["BACKGROUND"][0]] = "checked";
$bgimagelocat_seleced[@$layoutdata["BACKGROUND"][1]] = "selected";
$bgimagerepet_checked[@$layoutdata["BACKGROUND"][2]] = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function selcolor(obj){
	if(!document.form1.bgcolor.disabled)
	{
		fontcolor = obj.value.substring(1);
		var newcolor = showModalDialog("color.php?color="+fontcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
		if(newcolor){
			obj.value=newcolor;
		}
	}
}

function shopbgtype_change(thisForm,thisValue)
{
	if(document.getElementById("idx_bgcolor"))
		bgcolor_obj = document.getElementById("idx_bgcolor");
	if(document.getElementById("idx_bgimage"))
		bgimage_obj = document.getElementById("idx_bgimage");

	if(thisValue == "N")
	{
		thisForm.bgcolor.disabled=true;
		thisForm.bgclear[0].disabled=true;
		thisForm.bgclear[1].disabled=true;
		thisForm.bgimage.disabled=true;
		thisForm.bgimagefreeze[0].disabled=true;
		thisForm.bgimagefreeze[1].disabled=true;
		thisForm.bgimagelocat.disabled=true;
		thisForm.bgimagerepet[0].disabled=true;
		thisForm.bgimagerepet[1].disabled=true;
		thisForm.bgimagerepet[2].disabled=true;
		thisForm.bgimagerepet[3].disabled=true;
		bgcolor_obj.style.backgroundColor="#EAE9E4";
		bgimage_obj.style.backgroundColor="#EAE9E4";
	}
	else
	{
		if(thisValue == "B")
		{
			thisForm.bgcolor.disabled=false;
			thisForm.bgclear[0].disabled=false;
			thisForm.bgclear[1].disabled=false;
			thisForm.bgimage.disabled=true;
			thisForm.bgimagefreeze[0].disabled=true;
			thisForm.bgimagefreeze[1].disabled=true;
			thisForm.bgimagelocat.disabled=true;
			thisForm.bgimagerepet[0].disabled=true;
			thisForm.bgimagerepet[1].disabled=true;
			thisForm.bgimagerepet[2].disabled=true;
			thisForm.bgimagerepet[3].disabled=true;
			bgcolor_obj.style.backgroundColor="#0099CC";
			bgimage_obj.style.backgroundColor="#EAE9E4";
		}
		else
		{
			thisForm.bgcolor.disabled=true;
			thisForm.bgclear[0].disabled=true;
			thisForm.bgclear[1].disabled=true;
			thisForm.bgimage.disabled=false;
			thisForm.bgimagefreeze[0].disabled=false;
			thisForm.bgimagefreeze[1].disabled=false;
			thisForm.bgimagelocat.disabled=false;
			thisForm.bgimagerepet[0].disabled=false;
			thisForm.bgimagerepet[1].disabled=false;
			thisForm.bgimagerepet[2].disabled=false;
			thisForm.bgimagerepet[3].disabled=false;
			bgcolor_obj.style.backgroundColor="#EAE9E4";
			bgimage_obj.style.backgroundColor="#0099CC";
		}
	}
}

document.onkeydown = CheckKeyPress;
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
		var j=all_list.length;
		var h=0;
		for(i=0;i<all_list.length;i++) {
			j--;
			if(ekey==38 || ekey == "up") {			//위로 이동
				h=i;
				kk=h;
				kk--;
			} else {	//아래로 이동
				h=j;
				kk=h;
				kk++;
			}

			if(selnum==all_list[h].num) {
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

					takeChange(prevobj);
					takeChange(selobj);

					all_list[kk].selected=false;
					selnum="";
					document.form1.change.value="Y";
					ChangeList(all_list[kk].num);
				}
				break;
			} else {
				prevobj=all_list[h];
			}
		}
	}
}

function takeChange(argObj)
{
	var innerHtmlStr = "";
	document.all["idx_inner_"+argObj.sort].innerHTML=argObj.mainused_html+argObj.mainused_check+argObj.mainused_html2;
}

function move_save()
{
	val="";
	for(i=0;i<all_list.length;i++)
	{
		val+=all_list[i].mainused;
	}
	document.form1.mainsort.value="I"+val;
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
		for(i=0;i<all_list.length;i++) {
			if(all_list[i].num==num) {
				if(all_list[i].selected) {
					selnum="";
					all_list[i].selected=false;
					document.all["idx_inner_"+all_list[i].sort].style.backgroundColor="#FFFFFF";
				} else {
					selnum=num;
					all_list[i].selected=true;
					document.all["idx_inner_"+all_list[i].sort].style.backgroundColor="#efefef";
				}
			} else {
				all_list[i].selected=false;
				document.all["idx_inner_"+all_list[i].sort].style.backgroundColor="#FFFFFF";
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
	this.mainused		= new String((argc > 1) ? argv[1] : "");
	this.mainused_html	= new String((argc > 2) ? argv[2] : "");
	this.mainused_html2	= new String((argc > 3) ? argv[2] : "");
	this.mainused_check	= new String((argc > 4) ? argv[4] : "");
	this.no				= new String((argc > 5) ? argv[5] : "");
	this.sort			= new String((argc > 6) ? argv[6] : "");
	this.selected		= new Boolean((argc > 7) ? argv[7] : false );
}

function checkChange(checkedValue,num)
{
	ProductInfoStop = "1";
	
	for(i=0;i<all_list.length;i++) {
		if(all_list[i].num==num) {
			if(checkedValue)
				all_list[i].mainused_check = "checked";
			else
				all_list[i].mainused_check = "";
		}
	}
}

function CheckForm(){
	if (confirm("쇼핑몰 레이아웃 설정을 업데이트 하겠습니까?")) {
		move_save();
		form1.type.value="up";
		form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>쇼핑몰 레이아웃 설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<!-- 내용 시작 -->

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">쇼핑몰 레이아웃 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체사이즈 조절 및 복사방지 기능을 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name="type">
			<input type=hidden name="change">
			<input type=hidden name="mainsort">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 전체 가로 사이즈<span>쇼핑몰 전체 가로(Width)사이즈를 설정 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
				<TR>
					<th><center><img src="images/shop_layout_img1.gif" border="0"></center></Th>
					<td class="td_con1"  ><span class="font_orange"><b>쇼핑몰 전체 가로(Width) 사이즈 : </b></span><input type=text name="shopwidth" size="6" maxlength="6" value="<?=$layoutdata["SHOPWIDTH"]?>" class="input"> 픽셀(Pixel)<br>
						<span class="space_top">* 미입력시 템플릿 자체 사이즈에 따라 출력 됩니다.<br>
						* 쇼핑몰 전체 가로(Width) 최소 사이즈는 900픽셀 까지만 가능합니다.<br>
						* 쇼핑몰 전체 가로(Width) 사이즈는 숫자만 입력 가능합니다.</span></td>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
					<br />
					<div class="help_info01_wrap">
						<ul>
							<li class="title">쇼핑몰 레이아웃 설정</li>
							<li>1) 쇼핑몰 메인 중앙 레이아웃 A,B,C 타입을 선택하여 사용할 수 있습니다.</li>
							<li>2) 쇼핑몰 메인 본문의 각 메뉴출력 여부 및 위치를 조절할 수 있습니다.</li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
				<div class="point_title">쇼핑몰 메인</div>
				<div class="table_style01">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th><span>중앙 레이아웃 선택</span></th>
						<td>
							<div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr align="center">
									<td><IMG src="images/shop_layout_img2.gif" border="0" class="imgline"></td>
									<td><IMG src="images/shop_layout_img3.gif" border="0" class="imgline"></td>
									<td><IMG src="images/shop_layout_img4.gif" border="0" class="imgline"></td>
								</tr>
								<tr>
									<td height="5" colspan="3"></td>
								</tr>
								<tr align="center">
									<td><INPUT type=radio name="maintype" value="A" id="idx_maintype1" <?=$maintype_checked["A"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_maintype1"><b>A 타입</b></label></td>
									<td><INPUT type=radio name="maintype" value="B" id="idx_maintype2" <?=$maintype_checked["B"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_maintype2"><b>B 타입</b></label></td>
									<td><INPUT type=radio name="maintype" value="C" id="idx_maintype3" <?=$maintype_checked["C"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_maintype3"><b>C 타입</b></label></td>
								</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<th><span>본문 메뉴 및 위치 선택</span></th>
						<td>
								<div class="point_title02">메인 소개글</div>
								<div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%" height="100%">
								<input type="hidden" name="mainused[]" value="I">
								<tr>
									<td>
									<div style="padding-left:15px;">
										<ul>
											<li style="padding:5px 0px;">1</li>
											<li style="padding:5px 0px;">2</li>
											<li style="padding:5px 0px;">3</li>
											<li style="padding:5px 0px;">4</li>
											<li style="padding:5px 0px;">5</li>
										</ul>
									</div>
									</td>
									<td height="100%">
									
									
									<table cellpadding="0" cellspacing="0" width="100%" height="100%">
<?php
							if(@count($mainsort)==0)
								$mainsort = array("N", "B", "H", "G", "A");
							$mainsort_count = count($mainsort);

							$mainsort_name = array("N"=>"신상품 출력", "B"=>"추천상품 출력", "H"=>"인기상품 출력", "G"=>"공구표시 출력", "A"=>"경매표시 출력");
							
							$j=1;
							$strlist="<script>\n";
							$jj=$mainsort_count;
							
							for($ii=0; $ii<$mainsort_count; $ii++)
							{
								$strlist.= "var objlist=new ObjList();\n";
								$strlist.= "objlist.num=\"{$j}\";\n";
								$strlist.= "objlist.mainused=\"{$mainsort[$ii]}\";\n";
								$strlist.= "objlist.mainused_html=\"<table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\" onclick=\\\"ChangeList('{$j}');\\\"><tr><TD style=\\\"padding-left:20px;\\\"><a href=\\\"javascript:updown_click('{$j}','up')\\\"><img src=\\\"images/btn_plus.gif\\\" border=\\\"0\\\" style=\\\"margin-bottom:3px;\\\"></a><br><a href=\\\"javascript:updown_click('{$j}','down')\\\"><img src=\\\"images/btn_minus.gif\\\" border=\\\"0\\\" style=\\\"margin-top:3px;\\\"></a></td><td width=\\\"100%\\\" style=\\\"padding-left:10px;\\\"><INPUT type=checkbox name=\\\"mainused[]\\\" value=\\\"{$mainsort[$ii]}\\\" id=\\\"idx_mainused{$ii}\\\" onclick=\\\"checkChange(this.checked,'{$j}');\\\"\";\n";
								$strlist.= "objlist.mainused_html2=\"><label style=\\\"cursor:hand;\\\" onmouseover=\\\"style.textDecoration='underline'\\\" onmouseout=\\\"style.textDecoration='none'\\\" for=\\\"idx_mainused{$ii}\\\" onclick=\\\"checkChange(this.checked,'{$j}');\\\"><b>{$mainsort_name[$mainsort[$ii]]}</b></label></td></tr></table>\";\n";
								$strlist.= "objlist.mainused_check=\"{$mainused_checked[$mainsort[$ii]]}\";\n";
								$strlist.= "objlist.no=\"".($jj--)."\";\n";
								$strlist.= "objlist.sort=\"{$ii}\";\n";
								$strlist.= "objlist.selected=false;\n";
								$strlist.= "all_list[{$ii}]=objlist;\n";
								$strlist.= "objlist=null;\n";
?>
									<tr>
										<td id="idx_inner_<?=$ii?>" onmouseover="if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';" onmouseout="if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';" style="background-Color:'#FFFFFF';cursor:hand;">
										<div class="table_none">
										<table cellpadding="0" cellspacing="0" width="100%" onclick="ChangeList('<?=$j?>');">
										<tr>
											<td style="padding-left:20px;"><a href="javascript:updown_click('<?=$j?>','up')"><img src="images/btn_plus.gif" border="0" style="margin-bottom:3px;"></a><br><a href="javascript:updown_click('<?=$j?>','down')"><img src="images/btn_minus.gif" border="0" style="margin-top:3px;"></a></td>
											<td width="100%" style="padding-left:10px;"><INPUT type=checkbox name="mainused[]" value="<?=$mainsort[$ii]?>" id="idx_mainused<?=$ii?>" <?=$mainused_checked[$mainsort[$ii]]?> onclick="checkChange(this.checked,'<?=$j?>');"><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mainused<?=$ii?>" onclick="checkChange(this.checked,'<?=$j?>');"><b><?=$mainsort_name[$mainsort[$ii]]?></b></label></td>
										</tr>
										</table>
										</div>
										</td>
									</tr>
<?php
								$j++;
							}

							$strlist.="</script>\n";
							echo $strlist;
?>
									</table>
									</td>
								</tr>
								</table>
								</div>
						</td>
					</tr>
				</table>
				</div>


				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<th><span>쇼핑몰 배경 설정</span></th>
							<td class="td_con1"  >
                            	<input type=radio name="shopbgtype" value="N" id="idx_shopbgtype1" onclick="shopbgtype_change(this.form,this.value);" <?=$shopbgtype_checked["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_shopbgtype1">배경 사용 안함</label>
                                <input type=radio name="shopbgtype" value="B" id="idx_shopbgtype2" onclick="shopbgtype_change(this.form,this.value);" <?=$shopbgtype_checked["B"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_shopbgtype2">배경 색상으로 설정</label>
                                <input type=radio name="shopbgtype" value="I" id="idx_shopbgtype3" onclick="shopbgtype_change(this.form,this.value);" <?=$shopbgtype_checked["I"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_shopbgtype3">배경 이미지로 설정</label>
							</td>
						</tr>
                        <tr>
                        	<th><span>메인 중앙 배경 설정</span></th>
                            <td class="td_con1"  >
                            	<input type=radio name="shopbgtypemain" value="N" id="idx_shopbgtypemain1" <?=$shopbgtypemain_checked["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_shopbgtypemain1">메인 중앙에는 적용안함(흰색으로 자동 적용, <font color="#000000">권장</font>)</label>
                                <input type=radio name="shopbgtypemain" value="Y" id="idx_shopbgtypemain2" <?=$shopbgtypemain_checked["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_shopbgtypemain2">메인 중앙에도 적용함</label>
                            </td>
                        </tr>
					</table>
				</div>
				</td>
			</tr>
            

			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 쇼핑몰 내부/외부 배경색을 색상 또는 이미지로 적용할 수 있습니다.</li>
                            <li>2) 색상 선택은 색상코드번호를 입력하여 사용할 수 있으며, 색상표를 이용시 코드번호를 확인할 수 있습니다.</li>
                            <li>3) 배경 이미지는 파일확장자 gif 만 가능하며 용량은 최대 150KByte 까지만 가능합니다.</li>
                            <li>4) 투프레임 타입으로 이용시 상단에는 배경색 및 배경이미지 적용되지 않습니다.</li>
                            <li>5) 배경을 정해진 위치에 고정하시려면 고정 안함의 체크를 해제하셔야 합니다. 체크하신 경우 배경이 스크롤을 따라 움직입니다.</li>
                            <li>6) 메인 레이아웃 중앙에 대한 배경 적용/미적용 선택시에 왼쪽메뉴에 대해서도 같이 적용/미적용 될 수 있습니다.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td id="idx_bgcolor" style="background-Color:<?=($shopbgtype=="B"?"#0099CC":"#EAE9E4")?>;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD width="100%"><div class="point_title">배경 색상으로 설정</div></TD>
						</TR>
						<tr>
							<td>
                            <div class="table_style01">
							<table cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<th><span>색상 선택</span></th>
								<TD class="td_con1"  >
                                <div class="table_none">
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-left:5px;">#</td>
                                        <td style="padding-left:3px;"><input type=text name="bgcolor" value="<?=$bgcolor?>" size="8" maxlength="6" class="input" <?=($shopbgtype=="N" || $shopbgtype=="I"?"disabled":"")?>></td>
                                        <td style="padding-left:5px;"><font color="<?=$bgcolor?>"><span style="font-size:20pt;">■</span></font></td>
                                        <td style="padding-left:5px;"><a href="javascript:selcolor(document.form1.bgcolor)"><IMG src="images/icon_color.gif" border="0" align="absmiddle"></a></td>
                                    </tr>
                                    </table>
                                </div>
								</td>
							</TR>
							<TR>
								<th><span>투명색 사용여부</span></th>
								<TD class="td_con1"><input type=radio name="bgclear" value="N" id="idx_bgclear1" <?=$bgclear_checked["N"]?> <?=($shopbgtype=="N" || $shopbgtype=="I"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgclear1">투명색 사용안함</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="bgclear" value="Y" id="idx_bgclear2" <?=$bgclear_checked["Y"]?> <?=($shopbgtype=="N" || $shopbgtype=="I"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgclear2">투명색 사용함</label></td>
							</TR>
							</table>
                            </div>
							</td>
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
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td id="idx_bgimage" style="background-Color:<?=($shopbgtype=="I"?"#0099CC":"#EAE9E4")?>;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD width="100%"><div class="point_title">배경 이미지로 설정</div></TD>
						</TR>
						<tr>
							<td>
                            <div class="table_style01">
							<table cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<th><span>배경 이미지</span></th>
								<TD class="td_con1"  ><input type=file name="bgimage" <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><br>
								* 등록 가능한 이미지는 파일 확장자 <span class="font_orange">GIF(gif)</span> 만 가능하며 용량은 <span class="font_orange">최대 150KB</span> 까지 가능합니다.
								<?php
									if(file_exists($imagepath.$image_name)){
								?>										
										<br /><img src="<?=$imagepath.$image_name?>" style="width:200px;border:1px solid #CCCCCC" />
								<?php
									}
								?>
								</TD>
							</TR>
							<TR>
								<th><span>배경 고정 여부</span></th>
								<TD class="td_con1"><input type=radio name="bgimagefreeze" value="Y" id="idx_bgimagefreeze1" <?=$bgimagefreeze_checked["Y"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagefreeze1">배경 고정 안함(배경 이미지 따라다닙니다.)</label>
								<input type=radio name="bgimagefreeze" value="N" id="idx_bgimagefreeze2" <?=$bgimagefreeze_checked["N"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagefreeze2">배경 고정함(배경 이미지 고정)</label>
								</TD>
							</TR>
							<TR>
								<th><span>배경 출력 시작 위치</span></th>
								<TD class="td_con1"><select name="bgimagelocat" class="select" <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>>
									<option value="A" <?=$bgimagelocat_seleced["A"]?>>맨위 - 좌측 </option>
									<option value="B" <?=$bgimagelocat_seleced["B"]?>>맨위 - 중앙</option>
									<option value="C" <?=$bgimagelocat_seleced["C"]?>>맨위 - 우측</option>
									<option value="D" <?=$bgimagelocat_seleced["D"]?>>가운데 - 좌측</option>
									<option value="E" <?=$bgimagelocat_seleced["E"]?>>가운데 - 중앙</option>
									<option value="F" <?=$bgimagelocat_seleced["F"]?>>가운데 - 우측</option>
									<option value="G" <?=$bgimagelocat_seleced["G"]?>>맨아래 - 좌측</option>
									<option value="H" <?=$bgimagelocat_seleced["H"]?>>맨아래 - 중앙</option>
									<option value="I" <?=$bgimagelocat_seleced["I"]?>>맨아래 - 우측</option>
									</select></TD>
							</TR>
							<TR>
								<th><span>배경 반복 설정</span></th>
								<TD class="td_con1"><input type=radio name="bgimagerepet" value="A" id="idx_bgimagerepet1" <?=$bgimagerepet_checked["A"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet1">전체반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="bgimagerepet" value="B" id="idx_bgimagerepet2" <?=$bgimagerepet_checked["B"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet2">수평반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="bgimagerepet" value="C" id="idx_bgimagerepet3" <?=$bgimagerepet_checked["C"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet3">수직반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="bgimagerepet" value="D" id="idx_bgimagerepet4" <?=$bgimagerepet_checked["D"]?> <?=($shopbgtype=="N" || $shopbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet4">반복안함</label>
								</TD>
							</TR>
							</table>
                            </div>
							</td>
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
			<tr>
				<td height="30"></td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">복사방지 기능 설정<span>투프레임 타입으로 선택시 상단에는 복사방지 기능이 동작하지 않습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TR>
                        <th style="width:300"><span>마우스 오른쪽 버튼 사용여부</span></th>
						<TD class="td_con1"  ><input type=radio name="mousekeyright" value="N" id="idx_mousekeyright1" <?=$mousekey_checked[0]["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyright1">사용 가능함</label>
						<input type=radio name="mousekeyright" value="Y" id="idx_mousekeyright2" <?=$mousekey_checked[0]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyright2">사용 불가함</label>
						</TD>
					</TR>
					<TR>
						<th><span>마우스 드래그 사용여부</span></th>
						<TD class="td_con1"><input type=radio name="mousekeydrag" value="N" id="idx_mousekeydrag1" <?=$mousekey_checked[1]["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeydrag1">사용 가능함</label>
						<input type=radio name="mousekeydrag" value="Y" id="idx_mousekeydrag2" <?=$mousekey_checked[1]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeydrag2">사용 불가함</label>
						</TD>
					</TR>
					<TR>
						<th><span>마우스 이미지 위 도구창 출력여부</span></th>
						<TD class="td_con1"><input type=radio name="mousekeyover" value="N" id="idx_mousekeyover1" <?=$mousekey_checked[2]["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyover1">출력 가능함</label>
						<input type=radio name="mousekeyover" value="Y" id="idx_mousekeyover2" <?=$mousekey_checked[2]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyover2">출력 불가함</label>
						</TD>
					</TR>
					<TR>
						<th><span>키보드 사용여부(Ctrl, 펑션키)</span></th>
						<TD class="td_con1"><input type=radio name="mousekeyboard" value="N" id="idx_mousekeyboard1" <?=$mousekey_checked[3]["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyboard1">사용 가능함</label>
						<input type=radio name="mousekeyboard" value="Y" id="idx_mousekeyboard2" <?=$mousekey_checked[3]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_mousekeyboard2">사용 불가함</label>
						</TD>
					</TR>
					</table>
                    </div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>

			</form>
            <tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>쇼핑몰의 레이아웃 및 기타기능 설정</span></dt>
							<dd>
								- 쇼핑몰 전체 가로사이즈를 조절할 수 있으며, 미입력시 템플릿에 적용된 사이즈로 출력됩니다.<br>
								- 본문 메뉴 출력 및 위치조절은 ▲▽ 이용하여 조절할 수 있습니다. □ 체크하지 않은 메뉴는 본문에 출력되지 않습니다.<br>
								- 쇼핑몰 배경 설정은 색상코드 및 이미지 구분하여 사용할 수 있습니다.<br>
								<b>&nbsp;&nbsp;</b>이미지 용량은 최대 150KByte까지 가능하며, 확장자는 gif만 가능합니다.<br>
								- 복사 방지기능은 상품복사 또는 소스복사를 차단시 이용할 수 있습니다.<br>
								<b>&nbsp;&nbsp;</b>단, 프레임설정에서 투프레임 타입 사용시 상단에는 복사방지 기능이 작동되지 않습니다.<br>
								- 원프레임에서 디자인 한 후 투프레임으로 사용할 경우 상하좌우 라인이 정확히 일치하지 않을 수 있습니다.<br>
								- 좌우정렬을 변경하면 기존 디자인에 변화가 있을 수 있습니다.<br>
								- 상품의 특성이나 쇼핑몰에 변화를 줄 때 좌우정렬 및 디자인을 변경하면서 사용하실 수 있습니다.
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			<!-- 내용 종료 -->
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

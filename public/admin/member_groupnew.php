<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$max=50;

//exdebug($_POST);

$type = $_POST["type"];
$mode = $_POST["mode"];
$group_code = $_POST["group_code"];
$group_name = $_POST["group_name"];
$group_description = $_POST["group_description"];
$group_level = $_POST["group_level"];
/*
$group_deli_free=$_POST["group_deli_free"]+0;
$group_addreserve = $_POST['$group_addreserve']?$_POST['$group_addreserve']:0;
$group_ordercnt_s = $_POST['group_ordercnt_s']?$_POST['group_ordercnt_s']:0;
$group_ordercnt_e = $_POST['group_ordercnt_e']?$_POST['group_ordercnt_e']:0;
$group_orderprice_s = $_POST['group_orderprice_s']?$_POST['group_orderprice_s']:0;
$group_orderprice_e = $_POST['group_orderprice_e']?$_POST['group_orderprice_e']:0;
$group_couponcode = $_POST['group_couponcode']?substr($_POST['group_couponcode'],0,-1):"";
*/
$group_ap_s = $_POST['group_ap_s']?$_POST['group_ap_s']:0;
$group_ap_e = $_POST['group_ap_e']?$_POST['group_ap_e']:0;

$groupimg=$_FILES["groupimg"];

## 회원가입시 자동 지정 등급
$reg_group=$_POST["reg_group"];

$imagepath = $Dir.DataDir."shopimages/grade/";
$no_img = $Dir."images/no_img.gif";

if ($type=="insert" || $type=="modify") {
	$group_addreserve = trim($_POST[group_addreserve]).$_POST[reserve_add_type];
}

if(ord($reg_group)==0 && $type!="reg_group"){
	$reg_group=$_shopdata->group_code;
}


if ($type=="insert") {
	$sql = "SELECT CAST(MAX(SUBSTR(group_code,3,2)) AS int)+1 as cnt, COUNT(*) as count FROM tblmembergroup ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		if($row->count>=$max){
			pmysql_free_result($result);
			alert_go("등급은 최대 {$max}개 까지만 등록가능합니다.",-1);
		}else {
			$cnt=sprintf('%02d',$row->cnt);
			$count=$row->count;
		}
		pmysql_free_result($result);
	}
	if(ord($cnt)==0 || $count==0) $cnt="01";

	$group_code=str_pad($group_code.$cnt,4,'0',STR_PAD_LEFT);

	if (ord($groupimg["name"])) {
		 $ext = strtolower(pathinfo($groupimg['name'],PATHINFO_EXTENSION));
		if ($ext!="gif") {
			alert_go('등급이미지는 gif파일만 등록이 가능합니다.',-1);
		} else if ($groupimg["size"]==0 || $groupimg["size"] > 153600) {
			alert_go("정상적인 파일이 아니거나 파일 용량이 너무 큽니다.\\n\\n다시 확인 후 등록하시기 바랍니다.",-1);
		}
		$uploaded_img="groupimg_{$group_code}.gif";
		move_uploaded_file ($groupimg["tmp_name"], $imagepath.$uploaded_img);
		chmod($imagepath.$uploaded_img,0666);
	}

	$sql = "INSERT INTO 
				tblmembergroup
					(
						group_code,
						group_name,
						group_description,
						group_level,
						group_ap_s, 
                        group_ap_e
					) 
				VALUES 
					(
						'{$group_code}', 
						'{$group_name}', 
						'{$group_description}', 
						{$group_level}, 
						{$group_ap_s}, 
						{$group_ap_e}
					)
	";
	pmysql_query($sql,get_db_conn());
    //echo $sql."<br>";
	$onload="<script>window.onload=function(){ alert('회원등급 등록이 완료되었습니다.');}</script>";

	$log_content = "## 회원등급생성 - $group_code $group_level $group_name $group_ap_s $group_ap_e";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

} else if ($type=="modify" && $mode=="result" && strlen($group_code)==4) {
	
    $group_code2=substr($group_code,2,2);
	if (ord($groupimg["name"])) {
		if (strtolower(pathinfo($groupimg['name'],PATHINFO_EXTENSION))!="gif") {
			alert_go('등급이미지는 gif파일만 등록이 가능합니다.',-1);
		} else if ($groupimg["size"]==0 || $groupimg["size"] > 153600) {
			alert_go("정상적인 파일이 아니거나 파일 용량이 너무 큽니다.\\n\\n다시 확인 후 등록하시기 바랍니다.",-1);
		}
		if (file_exists($imagepath."groupimg_{$group_code}.gif")) {
			unlink ($imagepath."groupimg_{$group_code}.gif");
		}
		$uploaded_img="groupimg_{$group_code}.gif";
		move_uploaded_file ($groupimg["tmp_name"], $imagepath.$uploaded_img);
		chmod($imagepath.$uploaded_img,0666);
	}
	$sql = "UPDATE tblmembergroup SET ";
	$sql.= "group_code		= '".str_pad($group_code2,4,'0',STR_PAD_LEFT)."', ";
	$sql.= "group_name		= '{$group_name}', ";
	$sql.= "group_description='{$group_description}', ";
	$sql.= "group_level	= {$group_level}, ";
	/*
    $sql.= "group_deli_free	= {$group_deli_free}, ";
	$sql.= "group_addreserve = '{$group_addreserve}', ";
	$sql.= "group_orderprice_s = {$group_orderprice_s}, ";
	$sql.= "group_orderprice_e = {$group_orderprice_e}, ";
	$sql.= "group_ordercnt_s = {$group_ordercnt_s}, ";
	$sql.= "group_ordercnt_e = {$group_ordercnt_e}, ";
	$sql.= "group_couponcode = '{$group_couponcode}' ";
    */
	$sql.= "group_ap_s = {$group_ap_s}, ";
	$sql.= "group_ap_e = {$group_ap_e} ";
	$sql.= "WHERE group_code = '{$group_code}' ";
	pmysql_query($sql,get_db_conn());
    //echo $sql."<br>";
	/*
	$sql = "UPDATE tblproductcode SET group_level='{$group_level}' ";
	$sql.= "WHERE group_code = '{$group_code}' ";
	pmysql_query($sql,get_db_conn());
	
	$sql = "UPDATE tblboardadmin SET group_level='{$group_level}' ";
	$sql.= "WHERE group_code = '{$group_code}' ";
	pmysql_query($sql,get_db_conn());
    */
	$log_content = "## 회원등급변경 - $group_code $group_level $group_name $group_ap_s $group_ap_e";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	if ($group_code!=$group_code2) {
		$sql = "UPDATE tblmember SET group_code = '".str_pad($group_code2,4,'0',STR_PAD_LEFT)."' ";
		$sql.= "WHERE group_code = '{$group_code}' ";
		pmysql_query($sql,get_db_conn());
        /*
		$sql = "UPDATE tblproductcode SET group_code = '".str_pad($group_code2,4,'0',STR_PAD_LEFT)."' ";
		$sql.= "WHERE group_code = '{$group_code}' ";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblboardadmin SET group_code = '".str_pad($group_code2,4,'0',STR_PAD_LEFT)."' ";
		$sql.= "WHERE group_code = '{$group_code}' ";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblproductgroupcode SET group_code = '".str_pad($group_code2,4,'0',STR_PAD_LEFT)."' ";
		$sql.= "WHERE group_code = '{$group_code}' ";
		pmysql_query($sql,get_db_conn());
        */
	}

	$onload="<script>window.onload=function(){ alert('회원등급 수정이 완료되었습니다.');}</script>";
	$type='';
	$mode='';
	$group_code='';
    $group_couponcode = "";
} else if ($type=="delete" && strlen($group_code)==4) {
	$sql = "DELETE FROM tblmembergroup WHERE group_code = '{$group_code}' ";
	pmysql_query($sql,get_db_conn());
	//$sql = "DELETE FROM tblproductgroupcode WHERE group_code = '{$group_code}' ";
	//pmysql_query($sql,get_db_conn());
	$sql = "UPDATE tblmember SET group_code='' WHERE group_code = '{$group_code}' ";
	pmysql_query($sql,get_db_conn());
	if($reg_group==$group_code){
		$sql = "UPDATE tblshopinfo SET group_code=NULL ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
	}
	if (file_exists($imagepath."groupimg_{$group_code}.gif")) {
		unlink ($imagepath."groupimg_{$group_code}.gif");
	}
    /*
	$sql = "SELECT productcode FROM tblproductgroupcode GROUP BY productcode ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$group_check_code[]=$row->productcode;
	}
	pmysql_free_result($result);

	if(count($group_check_code)>0) {
		$sql = "UPDATE tblproduct SET group_check='N' ";
		$sql.= "WHERE group_check='Y' ";
		$sql.= "AND productcode NOT IN ('".implode("','", $group_check_code)."') ";
		pmysql_query($sql,get_db_conn());
	}
    */
	$log_content = "## 회원등급삭제 - $group_code";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('해당 등급 삭제가 완료되었습니다.');}</script>";
	$type='';
	$group_code='';
} else if ($type=="imgdel" && strlen($group_code)==4) {
	unlink ($imagepath."groupimg_{$group_code}.gif");
	$onload="<script>window.onload=function(){ alert('해당등급 이미지 삭제가 완료되었습니다.');}</script>";
	$type='';
	$group_code='';
} else if ($type=="reg_group") {
	$sql = "UPDATE tblshopinfo SET ";
	if(ord($reg_group)==0) $sql.= "group_code = NULL ";
	else $sql.= "group_code = '{$reg_group}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('신규 회원 가입시의 회원등급 등록이 완료되었습니다.');}</script>";
	$type='';
	DeleteCache("tblshopinfo.cache");

    $log_content = "## 신규가입 회원등급변경 - $reg_group";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

if(ord($type)==0) $type="insert";

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if (document.form1.group_name.value.length==0) {
		alert("등급명을 입력하세요");
		document.form1.group_name.focus();
		return;
	}

	if(type=="modify") {
		document.form1.mode.value="result";
        /*
        var grade_couponcd = '';
        $("input[name='couponcd[]']").each(function(){
            //alert($(this).val());
            grade_couponcd += $(this).val() + '^';
        });
        $("input[name='group_couponcode']").val(grade_couponcd);
        */
	}
	document.form1.type.value=type;
	document.form1.submit();
}

function GroupSend(type,code) {
	if (type=="delete") {
		if (!confirm("해당 등급을 삭제하시겠습니까?")) {
			return;
		}
	}
	if (type=="imgdel") {
		if (!confirm("해당 등급 이미지를 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form2.type.value=type;
	document.form2.group_code.value=code;
	document.form2.submit();
}

$(document).ready(function(){
	if($("#memberGroupType").val() == 'modify'){
		var position = $("#scrollAutoMove").offset();
		$( 'html, body' ).animate( { scrollTop: (parseInt(position.top)-(20)) }, 200 );
	}
})
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원등급설정 &gt;<span>회원등급 등록/수정/삭제</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">회원등급 등록/수정/삭제</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원등급 신규등록/수정/삭제를 하실 수 있으며 등급별 권한설정이 가능합니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">회원가입시 등급 지정<span>신규회원가입시 선택된 등급으로 자동 가입됩니다.</span></div>
				</td>
			</tr>
			<form name=form3 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type value="reg_group">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>신규회원 가입시</span></th>
					<td><select name=reg_group style="width:350px" class="select">
						<option value="">선택등급 없음
<?php
						$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_level";
						$result = pmysql_query($sql,get_db_conn());
						while($row = pmysql_fetch_object($result)){
							echo "<option value=\"{$row->group_code}\"";
							if($reg_group==$row->group_code) echo " selected";
							echo ">{$row->group_name}</option>\n";
						}
?>
						</select> 에 자동으로 가입됩니다. 
					</td>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
            <tr><td height="10"></td></tr>
			<tr>
				<td align=center><a href="javascript:document.form3.submit();"><img src="images/botteon_save.gif" border="0" vspace="5"></a></td>
			</tr>
			</form>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등록된 회원등급 목록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width="30" />
                    <col width="100" />
					<col width="70" />
					<col width="70" />
                    <col width="" />
					<col width="70" />
					<col width="70" />
                    <col width="100" />
                    <col width="80" />
                    <col width="60" />
                    <col width="60" />
				</colgroup>
				<TR>
					<th>No</th>
					<th>등급명</th>
					<th>등급레벨</th>
					<th>등급이미지</th>
					<th>등급설명</th>
					<th>누적 활동포인트</th>
					<th>회원수</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$sql = "SELECT COUNT(*) as cnt, group_code FROM tblmember ";
				$sql.= "WHERE group_code != '' GROUP BY group_code ";
				$result=pmysql_query($sql,get_db_conn());
                //echo $sql."<br>";
				while($row=pmysql_fetch_object($result)) {
					$group_cnt[$row->group_code] = $row->cnt;
				}
				pmysql_free_result($result);
                //print_r($group_cnt)."<br>";

				$sql = "SELECT * FROM tblmembergroup order by group_level";
				$result = pmysql_query($sql,get_db_conn());
                //echo $sql."<br>";
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$i++;
					$group_code_num=$row->group_code;
					//$group_deli_free=$row->group_deli_free;
					$group_reserve_addtype=strpos($row->group_addreserve,"%")?"%":"";
                    $group_addreserve=str_replace("%","",$row->group_addreserve);
                    /*
					if($group_deli_free){
						$group_deli_free_str = "<font style = 'color:#077CCD'>배송비무료</font>";
					}else{
						$group_deli_free_str = "배송비정책";
					}
                    */
                    $group_img = $imagepath."groupimg_".$row->group_code.".gif";
					echo "<tr>\n";
					echo "	<td>{$i}</td>\n";
					echo "	<td><span class=\"font_orange\"><b>{$row->group_name}</b></span></td>\n";
					echo "	<td><span class=\"font_orange\"><b>{$row->group_level}</b></span></td>\n";
                    echo "	<td><img src=\"{$group_img}\" align=absmiddle width=50 onerror=\"this.src='{$no_img}'\"></td>\n";
					echo "	<td><NOBR>&nbsp;{$row->group_description}</NOBR></td>\n";
                    echo "	<td><NOBR>&nbsp;".number_format($row->group_ap_s)." P ~ ".number_format($row->group_ap_e)." P</td>\n";
					echo "	<td>".number_format($group_cnt[$group_code_num])."명</td>\n";
					echo "	<td><a href=\"javascript:GroupSend('modify','{$row->group_code}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
					echo "	<td><a href=\"javascript:GroupSend('delete','{$row->group_code}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
				}
				pmysql_free_result($result);
				if ($i==0) {
					echo "<tr><td colspan=\"8\" align=\"center\">등록된 회원등급이 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub" id = 'scrollAutoMove'>회원등급 등록/수정</div>
				</td>
			</tr>
<?php
			if($type=="modify" && strlen($group_code)>=2) {
				$sql = "SELECT * FROM tblmembergroup WHERE group_code = '{$group_code}' ";
				$result = pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name=$row->group_name;
					$group_description=$row->group_description;
                    $group_ap_s=$row->group_ap_s;
                    $group_ap_e=$row->group_ap_e;
					$group_level=$row->group_level;
					//$checked['group_deli_free'][$row->group_deli_free]="checked";
				}
				pmysql_free_result($result);

                $coup_temp = explode("^", $group_couponcode);
                $subwhere = "and coupon_code in ('".implode("','", $coup_temp)."')";

                $coup_sql = "SELECT coupon_code, coupon_name FROM tblcouponinfo WHERE 1=1 $subwhere";
                //echo $coup_sql;
                $coup_result = pmysql_query($coup_sql,get_db_conn());
                while($coup_row = pmysql_fetch_array($coup_result)){
                    $thisCoupon[] = $coup_row;
                }
                pmysql_free_result( $bProductResult );
                
			} else {
				$group_name='';
				$group_description='';
				$group_level='';
                $group_addreserve = 0;
                $group_orderprice_s = 0;
                $group_orderprice_e = 0;
                $group_ordercnt_s = 0;
                $group_ordercnt_e = 0;
			}
?>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=group_code value="<?=$group_code?>">
			<tr>
				<td>





				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=139>
				<col width=>
				<TR>
					<th><span>등급명</span></th>
					<td><input type=text name=group_name value="<?=$group_name?>" maxlength=30 style="width:200px;" class="input"></td>
				</TR>
				<TR>
					<th><span>등급레벨</span></th>
					<td>
						<select name="group_level">
							<?for($i=1;$i<=100;$i++){
								$level_qry="select group_level, group_name from tblmembergroup where group_level='".$i."'";
								$level_result=pmysql_query($level_qry);
								$level_data=pmysql_fetch_array($level_result);
								
								if($level_data[group_level] && $level_data[group_level]!=$group_level){
								?>
									<optgroup label="<?=$i."-".$level_data[group_name]?>"></optgroup>
								<?}else{
									if($level_data['group_level'] == $i){
										$level_name		= "-".$level_data[group_name];
										$level_select	= "selected";
									}else{
										$level_name		= "";
										$level_select	= "";
									}	
								?>
									<option value="<?=$i?>" <?=$level_select?>><?=$i.$level_name?></option>
								<?}?>
							<?}?>
						</select>
						
					</td>
				</TR>
				<TR>
					<th><span>등급설명</span></th>
					<td><input type=text name=group_description value="<?=$group_description?>" maxlength=100 style="width:450" class="input">50자 이내</td>
				</TR>
                <TR>
					<th><span>등급조건</span></th>
					<td>
                        <span class="font_orange"><B>누적 활동포인트</B></span></label>
                        <input type=text name=group_ap_s value="<?=$group_ap_s?>" style="text-align:right" maxlength=8 size=8 class="input"> P ~ 
                        <input type=text name=group_ap_e value="<?=$group_ap_e?>" style="text-align:right" maxlength=8 size=8 class="input"> P
                    </td>
				</TR>
				<!-- <TR>
					<th><span>등급혜택</span></th>
					<td>
                        <span class="font_orange"><B>지급쿠폰</B></span>&nbsp;&nbsp;<a href="javascript:layer_open('layer2','gradeCoupon');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-bottom:4px;'/></a>
                        <input type="hidden" name="group_couponcode" class="input" value="<?=$group_couponcode?>">
                        
                        <div style="margin-top:0px; margin-bottom: 0px;">
                            <table border=1 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="checkProduct">									
                            <?foreach($thisCoupon as $k=>$v){?>	
                                <tr align="center">
                                    <td style='border:0px' align="left">
                                        <?=$v[coupon_name]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:gradeCouponDel('<?=$v[coupon_code]?>');" border="0" style="cursor: hand;vertical-align:middle;" />
                                        <input type='hidden' name='couponcd[]' value='<?=$v[coupon_code]?>'>
                                    </td>
                                </tr>
                            <?}?>
                            </table>
                        </div>
                    </td>
				</TR> -->
				<TR>
					<th><span>등급이미지</span></th>
					<TD class="td_con1">
					<input type=file name=groupimg style="width:50%;"><br />		
					* 권장크기 : 80*40 픽셀 [가로*세로])<br><span class="font_orange">* 150KB 이하의 GIF(gif)이미지만 가능합니다.</span>
					<?php if(file_exists($imagepath."groupimg_{$group_code}.gif")){?>
					<BR><BR><img src="<?=$imagepath?>groupimg_<?=$group_code?>.gif" align=absmiddle>&nbsp;&nbsp; | &nbsp;&nbsp;<A HREF="javascript:GroupSend('imgdel','<?=$group_code?>');"><img src="images/icon_del1.gif" border=0 align=absmiddle></A>
					<?php }?></td>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<?php if($type=="insert"){?>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_make.gif" border="0" vspace="3"></a></td>
			</tr>
			<?php }else if($type=="modify"){?>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/btn_edit1.gif" border="0" vspace="3"></a></td>
			</tr>
			<?php }?>
			</form>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=group_code>
			</form>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>회원등급 기본정보 관리</span></dt>
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
<input type = 'hidden' id = 'memberGroupType' value = '<?=$type?>'>







<style type="text/css">
	.layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100;}
	.layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
	.layer .pop-layer {display:block;}

	.pop-layer {display:none; position: absolute; top: 50%; left: 50%; width: 900px; height:500px;  background-color:#fff; border: 5px solid #3571B5; z-index: 10; overflow-y: scroll;}	
	.pop-layer .pop-container {padding: 20px 25px;}
	.pop-layer p.ctxt {color: #666; line-height: 25px;}
	.pop-layer .btn-r {
			/*width: 100%; margin:10px 0 20px; padding-top: 10px; border-top: 1px solid #DDD; text-align:right;*/
			position: fixed; margin-left: 843px; margin-top: -35;
	}

	a.cbtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #304a8a; background-color:#3f5a9d; font-size:13px; color:#fff; line-height:25px;}	
	a.cbtn:hover {border: 1px solid #091940; background-color:#1f326a; color:#fff;}
	
	/*
	li.prListOn { position:relative; float:left; margin-right:15px; margin-bottom:5px; width:100px; height: 150px;}
	li.prListOn:before {display:block; width:1px; height:100%; content:""; background:#dbdbdb; position:absolute; top:0px; left:105px;}
    */
</style>

<!-- 쿠폰조회 레이어팝업 S -->
<input type="hidden" name="listMode" id="listMode" value=""/>
<div class="layer">
    <div class="bg"></div>
    <div id="layer2" class="pop-layer" style='width:1000px'>
        <div class="btn-r" style='margin-left:942px'>
            <a href="#" class="cbtn">Close</a>
        </div>
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <p class="ctxt mb20" style="font-size:15px; font-weight: 700;">쿠폰 선택
                    <div>
                        <input type="text" name="s_keyword" id="s_keyword" value="" style="width: 250px;"/>
                        <a href="javascript:couponListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
                    </div>
                </p>
                <div id="couponList">
                    
                </div>
                <!--// content-->
            </div>
        </div>
    </div>
</div>
<!-- 쿠폰조회 레이어팝업 E -->

<script type="text/javascript">
<!--
function layer_open(el,onMode){

	var temp = $('#' + el);
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
	switch(onMode){
		case 'gradeCoupon' :
			$('#listMode').val('gradeCoupon');
			break;
		default :
			$('#listMode').val('');
			break;
	}
	
	if(bg){
		$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
	}else{
		temp.fadeIn();
	}

	layerResize(el);

	temp.find('a.cbtn').click(function(e){
		if(bg){
			$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			outLayer();
		}else{
			temp.fadeOut();
			outLayer();
		}
		e.preventDefault();
	});

	$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
		$('.layer').fadeOut();
		outLayer();
		e.preventDefault();
	});

}

function layerResize(el){
	var temp = $('#' + el);
	// 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
	else temp.css('top', '0px');
	if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
	else temp.css('left', '0px');
	
	//console.log(temp.outerHeight());
}

function outLayer(){
	$("#s_keyword").val("");
	$("#couponList").html("");
	$('#listMode').val("");
	//$("#checkProduct").html("");
}

function couponListSearch(){
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"member_groupnew_couponlistPost_v3.php",
		{
			s_keyword:s_keyword,
			listMode:listMode
		},
		function(data){
			$("#couponList").html(data);
			layerResize('layer2');
		}
	);
}

function gradeCoupon(prname,prcode){
	var upList = true;
	var appHtml = "";
    
	$("input[name='couponcd[]']").each(function(){
		if($(this).val() == prcode){
			alert('쿠폰이 중복되었습니다.');
			upList = false;
			return upList;
		}else{
        }
	});
	if(upList){
		appHtml= "<tr align=\"center\">\n";
		appHtml+= "	<td style='border:0px' align=\"left\">"+prname+"&nbsp;&nbsp;<img src=\"images/icon_del1.gif\" onclick=\"javascript:gradeCouponDel('"+prcode+"');\" border=\"0\" style=\"cursor: hand;vertical-align:middle;\" />\n";
		appHtml+= "		<input type='hidden' name='couponcd[]' value='"+prcode+"'>\n";
		appHtml+= "	</td>\n";
		appHtml+= "</tr>\n";
		$("#checkProduct").append(appHtml);
	}
}

function gradeCouponDel(prcode){
	if(confirm('해당 쿠폰을 삭제 하시겠습니까?')){
		$("input[name='couponcd[]']").each(function(){
			if($(this).val() == prcode){
				$(this).parent().parent().remove();
			}
		});
	}
}

function T_GoPage(block,gotopage){
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"member_groupnew_couponlistPost_v3.php",
		{
			listMode:listMode,
			s_keyword:s_keyword,
			block:block,
			gotopage:gotopage
		},
		function(data){
			$("#couponList").html(data);
			layerResize('layer2');
		}
	);
}

//-->
</script>
<?=$onload?>
<?php 
include("copyright.php");

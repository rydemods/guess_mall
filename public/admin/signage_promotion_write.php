<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "signage";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$pidx=$_REQUEST["pidx"];
$no=$_REQUEST['no']; 
$mode=$_REQUEST['mode']?$_REQUEST['mode']:"ins";
$itemCount=(int)$_REQUEST["itemCount"];

$imagepath      = $cfg_img_path['signage_event'];

$filedata       = new FILE($imagepath);
$errmsg = $filedata->chkExt();
if($errmsg==''){
	$up_file = $filedata->upFiles();
}

switch($mode){
	case "del" :
				$csel_qry="select * from tblsignage_promotion_content where promotion_no='".$no."'";
				$csel_result=pmysql_query($csel_qry);
				while($csel_data=pmysql_fetch_array($csel_result)){
					#기본이미지는 삭제안시킴
					//if($csel_data["per_img"]) @unlink($imagepath.$csel_data["per_img"]);
					if($csel_data["next_img"]) @unlink($imagepath.$csel_data["next_img"]);
					#기본이미지는 삭제안시킴
					//if($csel_data["s_per_img"]) @unlink($imagepath.$csel_data["s_per_img"]);
					if($csel_data["s_next_img"]) @unlink($imagepath.$csel_data["s_next_img"]);
				}
				
				$won_img=pmysql_fetch("select won_img from tblsignage_promotion where no='".$no."'");
				if($won_img) @unlink($imagepath.$won_img);
					
				$del_qry="delete from tblsignage_promotion where no='".$no."'";
				pmysql_query($del_qry);
				$cdel_qry="delete from tblsignage_promotion_content where promotion_no='".$no."'";
				pmysql_query($cdel_qry);
				
				echo "<script>alert('삭제되었습니다.');</script>";
				echo "<script>document.location.href='signage_promotion_list.php';</script>";

				break;
	case "ins_submit" : 	
				$mt = pmysql_escape_string($_POST["mtitle"]); 
				$s_date = $_REQUEST["s_date"];
				$e_date = $_REQUEST["e_date"];
				$re_pare = $_REQUEST["re_pare"];
				$re_won = $_REQUEST["re_won"];
				$viewyn = $_REQUEST["viewyn"];

				$plistname = pmysql_escape_string($_POST["plistname"]); $pl = explode(",", $plistname); 
				$pwonper = $_POST["pwonper"];							$pw = explode(",", $pwonper); 
				$ppercent = $_POST["ppercent"];							$pp = explode(",", $ppercent); 
				$pppidx = $_POST["pppidx"];								$pidxs = explode(",", $pppidx);

				$mnsql = "select no from tblsignage_promotion order by no desc limit 1";
				$mnres = pmysql_query($mnsql);
				$tempx = 1;
				while($mnrow = pmysql_fetch_object($mnres)){
					if($tempx <= $mnrow->no){
						$tempx = $mnrow->no+1;								
					}							
				}
				$ins_qry="insert into tblsignage_promotion (no, mtitle, s_date, e_date, re_pare, re_won, viewyn, regdt, won_img) values ('".$tempx."', '".$mt."','".$s_date."','".$e_date."','".$re_pare."','".$re_won."','".$viewyn."',now(), '".$up_file["won_img"][0]["v_file"]."')";
				if(pmysql_query($ins_qry)){
					$i=1;
					for($aa=0;count($pl)>$aa;$aa++){
						$cins_qry="insert into tblsignage_promotion_content (promotion_no, listname, wonper, percent, per_img, next_img, s_per_img, s_next_img, sort) values ('".$tempx."','".$pl[$aa]."','".$pw[$aa]."','".$pp[$aa]."','".$up_file["per_img"][$aa]["v_file"]."','".$up_file["next_img"][$aa]["v_file"]."','".$up_file["s_per_img"][$aa]["v_file"]."','".$up_file["s_next_img"][$aa]["v_file"]."','".$i."')";
						pmysql_query($cins_qry);
						$i++;
					}
				}
				echo "<script>alert('등록되었습니다.');</script>";
				echo "<script>document.location.href='signage_promotion_list.php';</script>";

				break; 
	
	case "mod_submit" :
				$mt = pmysql_escape_string($_POST["mtitle"]); 
				$s_date = $_REQUEST["s_date"];
				$e_date = $_REQUEST["e_date"];
				$re_pare = $_REQUEST["re_pare"];
				$re_won = $_REQUEST["re_won"];
				$viewyn = $_REQUEST["viewyn"];

				$plistname = pmysql_escape_string($_POST["plistname"]); $pl = explode(",", $plistname); 
				$pwonper = $_POST["pwonper"];							$pw = explode(",", $pwonper); 
				$ppercent = $_POST["ppercent"];							$pp = explode(",", $ppercent); 
				$pppidx = $_POST["pppidx"];								$pidxs = explode(",", $pppidx);

				$upd_qry="update tblsignage_promotion set mtitle='".$mt."', s_date='".$s_date."', e_date='".$e_date."', re_pare='".$re_pare."', re_won='".$re_won."', viewyn='".$viewyn."' ";
				if($up_file["won_img"][0]["v_file"]){
					$won_img=pmysql_fetch("select won_img from tblsignage_promotion where no='".$no."'");
					if($won_img) @unlink($imagepath.$won_img);
					$upd_qry.=", won_img='".$up_file["won_img"][0]["v_file"]."'";
				}
				
				$upd_qry.=" where no='".$no."'";

				if(pmysql_query($upd_qry)){
					
					$i=1;
					list($total_count)=pmysql_fetch("select count(no) from tblsignage_promotion_content where promotion_no='".$no."'");
					if(count($pl)<$total_count){
						$csel_qry="select * from tblsignage_promotion_content where promotion_no='".$no."' and sort > '".count($pl)."'";
						$csel_result=pmysql_query($csel_qry);
						while($csel_data=pmysql_fetch_array($csel_result)){
							if($csel_data["per_img"]) @unlink($imagepath.$csel_data["per_img"]);
							if($csel_data["next_img"]) @unlink($imagepath.$csel_data["next_img"]);
						}
						$del_qry="delete from tblsignage_promotion_content where promotion_no='".$no."' and sort > '".count($pl)."'";
						pmysql_query($del_qry);
					}
					for($aa=0;count($pl)>$aa;$aa++){
						$sel_qry="select * from tblsignage_promotion_content where promotion_no='".$no."' and sort='".$i."'";
						$sel_result=pmysql_query($sel_qry);
						$sel_num=pmysql_num_rows($sel_result);
						$sel_data=pmysql_fetch_array($sel_result);
						if($sel_num){
							$cupd_qry="update tblsignage_promotion_content set listname='".$pl[$aa]."', wonper='".$pw[$aa]."', percent='".$pp[$aa]."'";
							if($up_file["per_img"][$aa]["v_file"]){
								$cupd_qry.=", per_img='".$up_file["per_img"][$aa]["v_file"]."'";
								#기본이미지는 삭제안시킴
								//if($sel_data["per_img"]) @unlink($imagepath.$sel_data["per_img"]);
							}
							if($up_file["next_img"][$aa]["v_file"]){
								$cupd_qry.=", next_img='".$up_file["next_img"][$aa]["v_file"]."'";
								if($sel_data["next_img"]) @unlink($imagepath.$sel_data["next_img"]);
							}
							if($up_file["s_per_img"][$aa]["v_file"]){
								$cupd_qry.=", s_per_img='".$up_file["s_per_img"][$aa]["v_file"]."'";
								#기본이미지는 삭제안시킴
								//if($sel_data["s_per_img"]) @unlink($imagepath.$sel_data["s_per_img"]);
							}
							if($up_file["s_next_img"][$aa]["v_file"]){
								$cupd_qry.=", s_next_img='".$up_file["s_next_img"][$aa]["v_file"]."'";
								if($sel_data["s_next_img"]) @unlink($imagepath.$sel_data["s_next_img"]);
							}
							$cupd_qry.=" where promotion_no='".$no."' and sort='".$i."'";
					
							pmysql_query($cupd_qry);
						}else{
							$cins_qry="insert into tblsignage_promotion_content (promotion_no, listname, wonper, percent, per_img, next_img, sort) values ('".$no."','".$pl[$aa]."','".$pw[$aa]."','".$pp[$aa]."','".$up_file["per_img"][$aa]["v_file"]."','".$up_file["next_img"][$aa]["v_file"]."','".$i."')";
							pmysql_query($cins_qry);
						}
						$i++;
					}
					
				}
				echo "<script>alert('수정되었습니다.');</script>";
				echo "<script>document.location.href='signage_promotion_list.php';</script>";

				break; 

	case "mod" :
				$msql = "SELECT * FROM tblsignage_promotion WHERE no = '{$no}'";
				$mres = pmysql_query($msql);
				$mrow = pmysql_fetch_array($mres);

				$select["re_pare"][$mrow["re_pare"]]="selected";
				$select["re_won"][$mrow["re_won"]]="selected";
				$select["viewyn"][$mrow["viewyn"]]="selected";

				break;

}


?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
 
<script language="JavaScript">
function tr_remove(){
	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	if(itemCount=="1"){
		alert("더이상 삭제할수없습니다.");
		return;
	}
	document.eventform.itemCount.value = itemCount;
	$(".table_style01 [name=promotable]:last").remove();
	
}
function chkfrm()	{

    if ( $("#mtitle").val().trim() === "" ) {
        alert("메인 타이틀을 입력해 주세요.");
        $("#mtitle").val("").focus();
        return false;
    }


    if ( $("input[name='s_date']").val().trim() === "" ) {
        alert("이벤트 시작일을 입력해 주세요.");
        return false;
    }

    if ( $("input[name='e_date']").val().trim() === "" ) {
        alert("이벤트 마감일을 입력해 주세요.");
        return false;
    }


	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	var mode = document.eventform.mode.value;
	if(mode=="ins"){  
		document.eventform.mode.value = "ins_submit";
	}else if(mode=="mod"){
		document.eventform.mode.value = "mod_submit";
	} 

	//promo_seq
	for(var i=1;i<=itemCount;i++){ 
		for(var ii=0;ii<8;ii++){
			var itemname
			var hiddenname
			switch(ii){
				case 0 : itemname = ".item"+i+" [name=listname]";	
						hiddenname = document.eventform.plistname;						
						break;
				case 1 : itemname = ".item"+i+" [name=wonper]";	
						hiddenname = document.eventform.pwonper;
						break;
				case 2 : itemname = ".item"+i+" [name=percent]";	
						hiddenname = document.eventform.ppercent;
						break;
				case 3 : itemname = ".item"+i+" [name=per_img]";	
						hiddenname = document.eventform.pper_img;
						break;
				case 4 : itemname = ".item"+i+" [name=next_img]";

						hiddenname = document.eventform.pnext_img;
						break;
				case 5 : itemname = ".item"+i+" [name=s_per_img]";	
						hiddenname = document.eventform.s_pper_img;
						break;
				case 6 : itemname = ".item"+i+" [name=s_next_img]";

						hiddenname = document.eventform.s_pnext_img;
						break;
				case 7 : itemname = ".item"+i+" [name=ppidx]";	
						hiddenname = document.eventform.pppidx;
						break;
			}						
			if(hiddenname.value==""){
				hiddenname.value =$(itemname).val();
			}else{ 
				hiddenname.value = hiddenname.value+","+$(itemname).val();
			}	
		}
	}

}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디지털사이니즈 &gt; <span>프로모션 관리</span></p></div></div>
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
			<?php include("menu_signage.php"); ?>
			</td>

			<td></td>

			<td valign="top">

	<div class="title_depth3">프로모션 <?if($mode=="ins"){echo "등록";}else{echo "수정";} ?>
		<?//if($mode=="ins"){?>
		<a href="#"><img align="right" class="tr_remove" src="../admin/images/botteon_del.gif" align="right" alt="삭제하기" onclick="javascript:tr_remove()"></a>
		<a href="#"><img align="right" id="tr_add" src="../admin/images/btn_badd2.gif" alt="추가하기"></a>
		<?//}?>
	</div>



<form name="eventform" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="return chkfrm();">
	<input type="hidden" name="plistname">
	<input type="hidden" name="pwonper">
	<input type="hidden" name="ppercent">
	<input type="hidden" name="pper_img">
	<input type="hidden" name="pnext_img">
	<input type="hidden" name="s_pper_img">
	<input type="hidden" name="s_pnext_img">
	<input type="hidden" name="pppidx">
	<input type="hidden" name="itemCount">
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="no" value="<?=$no?>">
	<input type="hidden" name="pidx" value="<?=$pidx?>">
	
	<!-- 테이블스타일01 -->
	<div class="table_style01 pt_20" style="position:relative">
		<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
		<table cellpadding=0 cellspacing=0 border=0 width=100%>
		
		<tr> 
			<th><span>메인 타이틀</span></th>
			<td><input type="text" name="mtitle" id="mtitle" style="width:50%" value="<?=$mrow['mtitle']?>" alt="타이틀" /></td>
		</tr>
		
		<tr>
			<th><span>이벤트기간</span></th>
			<td>
				<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=s_date value="<?=$mrow['s_date']?>" class="input_bd_st01">
				~
				<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=e_date value="<?=$mrow['e_date']?>" class="input_bd_st01">
			</td>
		</tr>

		<tr style="display:none">
			<th><span>재참여기간</span></th>
			<td>
				<select name="re_pare">
					<option value="0" <?=$select["re_pare"]["0"]?>>==== 선택하세요 ====</option>
					<option value="1" <?=$select["re_pare"]["1"]?>>1일</option>
					<option value="7" <?=$select["re_pare"]["7"]?>>7일</option>
					<option value="30" <?=$select["re_pare"]["30"]?>>30일</option>
				</select>
				* 미등록시 이벤트 기간동안 1회만 참여가능합니다.
			</td>
		</tr>

		<tr style="display:none">
			<th><span>재당첨기간</span></th>
			<td>
				<select name="re_won">
					<option value="0" <?=$select["re_won"]["0"]?>>==== 선택하세요 ====</option>
					<option value="1" <?=$select["re_won"]["1"]?>>1일</option>
					<option value="7" <?=$select["re_won"]["7"]?>>7일</option>
					<option value="30" <?=$select["re_won"]["30"]?>>30일</option>
				</select>
				* 미등록시 이벤트 기간동안 등록된 당첨수량만큼만 당첨됩니다.
			</td>
		</tr>

		<tr>
			<th><span>당첨혜택 이미지</span></th>
			<td>
				<input type="file" name="won_img[]" id='won_img' alt="썸네일 이미지" />
				<?
					if($mrow['won_img']){
				?>
					<span><br><img src="<?=$imagepath?><?=$mrow['won_img']?>" style="height:30px;" class="img_view_sizeset"></span>
				<?
					}
				?>
			</td>
		</tr>

		
		<tr>
			<th><span>노출</span></th>
			<td>
				<select name="viewyn" >
					<option value="1" <?=$select["viewyn"]["1"]?>>노출</option>
					<option value="0" <?=$select["viewyn"]["0"]?>>비노출</option>
				</select>
			</td>
		</tr>
		
		</table>
		&nbsp;
		<div>
			<font color=red>#당첨수량 0개, 확률 100% 항목 <b>1개 필수</b> </font>
		</div>
		<div>
			<font color=red>#당첨수량 0개, 확률 100%로 설정된 항목은 다른항목이 당첨안됫을경우 마지막에 당첨되는(꽝) 항목입니다.</font>
		</div>
		<!--기획전들-->
		<?if($mode=="ins"){?>
			<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
				<tr>
					<th><span>당첨명</span></th>
					<td>
						<input type="text" name="listname" id="listname" style="width:50%" value="<?=$row['listname']?>" alt="타이틀" />
					</td>
				</tr>						
				<tr>
					<th><span>당첨수량</span></th>
					<td>
						<input type="text" name="wonper" id="wonper" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['wonper']?>" alt="타이틀" />개
					</td>
				</tr>
				<tr>
					<th><span>확률</span></th>
					<td>
						<input type="text" name="percent" id="percent" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['percent']?>" alt="타이틀" />%
					</td>
				</tr> 
				<tr>
					<th><span>이미지(룰렛)</span></th>
					<td>
						<input type="file" name="per_img[]" id='per_img' alt="썸네일 이미지" />
						<?
							if($row['per_img']){
						?>
							<span name="imgper"><br><img src="<?=$imagepath?><?=$row['per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(룰렛)</span></th>
					<td>
						<input type="file" name="next_img[]" id='next_img' alt="썸네일 이미지" />
						<?
							if($row['next_img']){
						?>
							<span name="imgnext"><br><img src="<?=$imagepath?><?=$row['next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(복권)</span></th>
					<td>
						<input type="file" name="s_per_img[]" id="per_img" alt="썸네일 이미지" />
						<?
							if($row['s_per_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(복권)</span></th>
					<td>
						<input type="file" name="s_next_img[]" id="next_img" alt="썸네일 이미지" />
						<?
							if($row['s_next_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<input type="hidden" name="ppidx" value="1"/>
			</table> 
		<?}else if($mode=="mod"){ 
		$qry="select * from tblsignage_promotion_content where promotion_no='".$no."' ORDER by sort ASC "; 
		$res=pmysql_query($qry);
		$cnt=0;
		while($row=pmysql_fetch_array($res)){ $cnt++;?>
			<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item<?=$cnt?>">			
				<tr>
					<th><span>당첨명</span></th>
					<td>
						<input type="text" name="listname" id="listname" style="width:50%" value="<?=$row['listname']?>" alt="타이틀" />
					</td>
				</tr>						
				<tr>
					<th><span>당첨수량</span></th>
					<td>
						<input type="text" name="wonper" id="wonper" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['wonper']?>" alt="타이틀" />개
					</td>
				</tr>
				<tr>
					<th><span>확률</span></th>
					<td>
						<input type="text" name="percent" id="percent" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['percent']?>" alt="타이틀" />%
					</td>
				</tr> 
				<tr>
					<th><span>이미지(룰렛)</span></th>
					<td>
						<input type="file" name="per_img[]" id='per_img' alt="썸네일 이미지" /><br>
						<?
							if($row['per_img']){
						?>
							<span name="imgper"><img src="<?=$imagepath?><?=$row['per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(룰렛)</span></th>
					<td>
						<input type="file" name="next_img[]" id='next_img' alt="썸네일 이미지" /><br>
						<?
							if($row['next_img']){
						?>
							<span name="imgnext"><img src="<?=$imagepath?><?=$row['next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(복권)</span></th>
					<td>
						<input type="file" name="s_per_img[]" id="per_img" alt="썸네일 이미지" />
						<?
							if($row['s_per_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(복권)</span></th>
					<td>
						<input type="file" name="s_next_img[]" id="next_img" alt="썸네일 이미지" />
						<?
							if($row['s_next_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				
				<input type="hidden" name="ppidx" value="<?=$row['no']?>"> 
				
			</table> 
			
		<?  }
		} 
		if($cnt == 0  and $mode != "ins" ){ ?> 
			<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
				<tr>
					<th><span>당첨명</span></th>
					<td>
						<input type="text" name="listname" id="listname" style="width:50%" value="<?=$row['listname']?>" alt="타이틀" />
					</td>
				</tr>						
				<tr>
					<th><span>당첨수량</span></th>
					<td>
						<input type="text" name="wonper" id="wonper" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['wonper']?>" alt="타이틀" />개
					</td>
				</tr>
				<tr>
					<th><span>확률</span></th>
					<td>
						<input type="text" name="percent" id="percent" style="width:90px;ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?=$row['percent']?>" alt="타이틀" />%
					</td>
				</tr> 
				<tr>
					<th><span>이미지(룰렛)</span></th>
					<td>
						<input type="file" name="per_img[]" id="per_img" alt="썸네일 이미지" />
						<?
							if($row['per_img']){
						?>
							<span name="imgper"><br><img src="<?=$imagepath?><?=$row['per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(룰렛)</span></th>
					<td>
						<input type="file" name="next_img[]" id="next_img" alt="썸네일 이미지" />
						<?
							if($row['next_img']){
						?>
							<span name="imgper"><br><img src="<?=$imagepath?><?=$row['next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(복권)</span></th>
					<td>
						<input type="file" name="s_per_img[]" id="per_img" alt="썸네일 이미지" />
						<?
							if($row['s_per_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_per_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>당첨후 팝업 이미지(복권)</span></th>
					<td>
						<input type="file" name="s_next_img[]" id="next_img" alt="썸네일 이미지" />
						<?
							if($row['s_next_img']){
						?>
							<span name="simgper"><br><img src="<?=$imagepath?><?=$row['s_next_img']?>" style="height:30px;" class="img_view_sizeset"></span>
						<?
							}
						?>
					</td>
				</tr>

				<input type="hidden" name="ppidx" value="1"/>
			</table> 
			<?}?>
		<div id="add_div"></div>
	</div>
	<div style="width:100%;text-align:center">
		<?if($mode=="mod"){?>
			<input type="image" src="images/btn_edit2.gif">
		<?}else{?>
			<input type="image" src="../admin/images/btn_confirm_com.gif">
		<?}?>
		
		<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='signage_promotion_list.php'">
	</div>
</form>
 
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<form name="delform" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<input type="hidden" name="ppidx" />
<input type="hidden" name="mode" value="mod" />
<input type="hidden" name="pidx" value="<?=$pidx?>" />
</form>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript">
$(document).ready(function(){
	$("#tr_add").click(function(){
		var lastItemNo = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
		document.eventform.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			var newItem = $(".table_style01 [name=promotable]:last").clone();
			newItem.removeClass();
			var xxx = $(".table_style01 [name=promotable]:last [name=ppidx]").val();
			newItem.addClass("item"+(parseInt(lastItemNo)+1));
			newItem.appendTo('.table_style01'); 			
			$(".table_style01 [name=promotable]:last [name=ppidx]").attr('value', parseInt(xxx)+1);	

			
			var optemp = "<option value='"+(parseInt(lastItemNo)+1)+"'>"+(parseInt(lastItemNo)+1)+"</option>";
			$(".table_style01").find(".display_seq").append(optemp);
			
			$(".table_style01 [name=promotable]:last [name=listname]").val(""); 
			$(".table_style01 [name=promotable]:last [name=wonper]").val(""); 
			$(".table_style01 [name=promotable]:last [name=percent]").val(""); 
			$(".table_style01 [name=promotable]:last [name=per_img]").val(""); 
			$(".table_style01 [name=promotable]:last [name=next_img]").val(""); 
			$(".table_style01 [name=promotable]:last [name=s_per_img]").val(""); 
			$(".table_style01 [name=promotable]:last [name=s_next_img]").val(""); 
			$(".table_style01 [name=promotable]:last [name=imgper]").empty();
			$(".table_style01 [name=promotable]:last [name=imgnext]").empty();
			$(".table_style01 [name=promotable]:last [name=simgper]").empty();
			$(".table_style01 [name=promotable]:last [name=simgnext]").empty();

		}else{ 
			alert("20개까지 등록할 수 있습니다.");
			return;   
		}
	}); 
	 
	$(".img_view_sizeset").on('mouseover',function(){
		$("#img_view_div").offset({top:($(document).scrollTop()+200)});
		$("#img_view_div").find('img').attr('src',($(this).attr('src')));
		$("#img_view_div").find('img').css('display','block');
		


	});

	$(".img_view_sizeset").on('mouseout',function(){
		$("#img_view_div").find('img').css('display','none'); 
	});	
	
	
});

function del_prmo(t){		
	if(confirm("삭제하시겠습니까?")){
		document.delform.ppidx.value=t;
		document.delform.submit();
	}
}

</script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<?=$onload?>
<?php 
include("copyright.php");

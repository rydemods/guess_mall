<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../css/admin.css">
<?php // hspark
$Dir="../";
//header("Content-Type: text/html; charset=UTF-8");
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

#######################접속권한 체크##############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
?>
<?
#########post로 값은 값들을 각각 변수로 설정해줍니다############
extract($_POST);										
//type,code,company_name,dacom_code,inicis_code,deli_url
########################################################

############배송업체 등록 부분############
if($type == "regist"){
	$regist_sql = "insert into tbldelicompany";
	$regist_sql .= " (code,dacom_code,inicis_code,company_name,deli_url) ";
	$regist_sql .= " values('{$code}','{$dacom_code}','{$inicis_code}','{$company_name}','{$deli_url}')";
	if($regist_result = pmysql_query($regist_sql,get_db_conn())){
		echo "<script>alert('신규 배송업체가 등록되었습니다');</script>";
	}else{
		echo "<script>alert('신규 배송업체가 등록에 실패하였습니다. 관리자에게 문의해주세요');</script>";
		//echo $regist_sql;
	}
}
######################################

############배송업체 수정 부분############
if($type == "modify"){
	
	//$pre_code = trim($pre_code);
	$modify_sql = "update tbldelicompany";
	$modify_sql .= " set code = '{$code}', dacom_code = '{$dacom_code}',inicis_code = '{$inicis_code}',company_name = '{$company_name}', deli_url = '{$deli_url}'  ";
	$modify_sql .= " where code = '{$pre_code}' ";

	if($modify_result = pmysql_query($modify_sql,get_db_conn())){
		$type = "0";
		echo "<script>alert('해당 배송업체 정보가 수정되었습니다');</script>";
	}else{
		echo "<script>alert('배송업체 정보가 수정되지 않았습니다. 관리자에게 문의해주세요');</script>";
	}
	
}
######################################

############배송업체 삭제 부분############
if($type == "delete"){
	//echo "<script> alert('delete'); </script>";
	$delete_sql = " delete from tbldelicompany ";
	$delete_sql .= " where code = '{$pre_code}' ";
	if($delete_result = pmysql_query($delete_sql,get_db_conn())){
		echo "<script> alert ('해당 배송업체가 삭제되었습니다');</script>";
	}else{
		echo "<script> alert ('배송업체 삭제가 되지 않았습니다. 관리자에게 문의해주세요');</script>";
	}
}
######################################

############배송업체 정보 불러오기#########
$sql = "select * from tbldelicompany order by company_name";
$result = pmysql_query($sql,get_db_conn());
######################################
?>

<style>/*배송업체 등록 폼 레이어 팝업스타일로 지정합니다*/
#layer{
	z-index:1000;
    position:absolute;
    top:0px;
    left:0px;
    width:100%;
    background-color:rgba(0,0,0,0.8);
}
#layer_regist{
	z-index:1001;
    position:fixed;
    text-align:center;
    left:10%;
    top:10%;
    width:500px;
    height:200px;
    background-color:#FFFFFF;
    border:3px solid #87cb42;        
}
.layer_input{
	width:100px;
}
</style>

<script src="../js/jquery-1.10.1.js"></script>
<script>
//$("input[name^='type']").val('0');
//alert($("input[name^='type']").val());
$(document).ready(function(){
	$(".deli_company_config").click(function(){
		if($(this).attr("mode")=="modify"){
			$(this).parent().parent().children().find("input").attr("readonly",false);
			$(this).parent().parent().children().find("#deli_code").focus();
			$(this).parent().parent().next().children().css("display","block");
		}
		if($(this).attr("mode")=="delete"){
			if(confirm('해당 업체를 삭제하시겠습니까??')){
				$(this).parent().parent().find("form").next().val("delete");
				$(this).parent().parent().find("form").submit();
			}
		}
	});

	$(".deli_company_modify2").click(function(){
		if($(this).attr("mode") == "modify"){
			$(this).parent().parent().parent().prev().find("form").next().val("modify");
			$(this).parent().parent().parent().prev().find("form").submit();
		}
		if($(this).attr("mode") == "cancel"){
			$(this).parent().parent().css("display","none");
		}
	});
});
function regist_open(type){
	if(type == 'open') {
		$('#layer').attr('style','visibility:visible');
			$('#layer').height(jQuery(document).height());
    }else if(type == 'close'){
         $('#layer').attr('style','visibility:hidden');
    }
}
function regist_company(type){
	if(type == 'regist'){
		$("#regist_company_form").submit();
	}else if(type == 'close'){
		$("#layer").attr('style','visibility:hidden');
	}
}
</script>

<div class="table_style02">
	<div class="title_depth3_sub">
		배송업체 설정
	</div>
	<TABLE cellSpacing=0 cellPadding=0 width="100%">
        <col width="128" />
        <col width="128" />
        <!-- <col width="128" />
		<col width="128" /> -->
		<col width="512" />
		<col width="60" />
		<col width="60" />
		
		<TR>
			<th>code</th>
			<th>company_name</th>
			<!-- <th>dacom_code</th>
			<th>inicis_code</th> -->
            <th>deli_url</th>
			<th>수정</th>
			<th>삭제</th>
        </TR>
		
		<?while($row=pmysql_fetch_object($result)){//배송업체 정보를 뿌려준다?>
		<tr>
			<form name="modify_form" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="type" autocomplete="off" value="">
			<input type="hidden" name="pre_code" value="<?=$row->code?>">
			<td><input type="text" readonly name="code" id="deli_code" class="input" maxlength="3" value='<?=trim($row->code)?>'></td>
			<td><input type="text" readonly name="company_name" class="input" value='<?=$row->company_name?>'></td>
			<!-- <td><input type="text" readonly name="dacom_code" class="input"  value='<?=$row->dacom_code?>'></td>
			<td><input type="text" readonly name="inicis_code" class="input" value='<?=$row->inicis_code?>'></td> -->
			<td><input type="text" readonly name="deli_url" size="70" class="input" value='<?=$row->deli_url?>'></td>
			<td><img src="images/btn_edit.gif" class="deli_company_config" mode="modify"></td>
			<td><img src="images/btn_del.gif" class="deli_company_config" mode="delete"></td>
			</form>
		</tr>
		<tr class="modify">
			<td colspan=7 style="display:none">
				<center>
					<input type="button" class="deli_company_modify2" mode="modify" value="수정">
					<input type="button" class="deli_company_modify2" mode="cancel" value="취소">
				</center>
			</td>
		</tr>
		<?}?>
	
	</table>
		<center>
			<br>
			
			<a href="#" onClick="regist_open('open')"><img src="image/button/bu_input_gray2.gif"></a>
			<a href="#" onClick="window.close()"><img src="image/button/bu_close.gif"></a>
			
		</center>
</div>

<div id="layer" style="visibility:hidden">
	<div id="layer_regist" class="table_style02">
		
		<span style="font-family: Nanum Gothic,Malgun Gothic;color: #0764b3;font-size: 15px;font-weight: bold;padding-left:7px;"><br>배송업체 신규 등록</span>
	
		<TABLE cellSpacing=0 cellPadding=0 style="margin-top:10px" >
		<col width="100px" />
        <col width="100px" />
        <!-- <col width="100px" />
		<col width="100px" /> -->
		<col width="300px" />
			<TR>
				<th>code</th>
				<th>company_name</th>
				<!-- <th>dacom_code</th>
				<th>inicis_code</th> -->
				<th>deli_url</th>
			</TR>

			<tr>
				<form id="regist_company_form" name="regist_company_form" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<td><input type="text" name="code" size="10" maxlength="3" class="input" ></td>
				<td><input type="text" name="company_name" size="20" class="input"></td>
				<!-- <td><input type="text" name="dacom_code" class="layer_input"></td>
				<td><input type="text" name="inicis_code" class="layer_input"></td> -->
				<td><input type="text" name="deli_url" size="48" class="input"></td>
				<input type="hidden" name="type" autocomplete="off" value="regist">
				</form>
			</tr>
			
		</table>
		<center style="margin-top:30px;">
			<input type="button" value="등록" onclick="regist_company('regist')">&nbsp;&nbsp;<input type="button" value="취소" onclick="regist_company('close')">
		</center>
	</div>
</div>

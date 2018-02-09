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

$max=10;

$type = $_POST["type"];
$mode = $_POST["mode"];
$group_no=$_POST["group_no"];
$group_name = $_POST["group_name"];
$group_code = $_POST["group_code"];
$group_description = $_POST["group_description"];

if ($type=="insert") {

	$sql = "INSERT INTO 
				tblcompanygroup
					(
						group_name,
						group_description,
						regdt,
						group_code
					) 
				VALUES 
					(
						'{$group_name}', 
						'{$group_description}', 
						now(),
						'{$group_code}'
					)
	";
	$sql.= "RETURNING group_no ";
	
	$result = pmysql_query( $sql, get_db_conn() );
	backup_save_sql( $sql );
	if( $row = pmysql_fetch_object( $result ) ){
		$row->group_no;
		$s_qry="select no as price_no,group_productcode from tblcompanyprice where group_type='N' order by no asc limit 1";
		$s_result=pmysql_query($s_qry);
		while($s_data=pmysql_fetch_object($s_result)){
			if($s_data->price_no){
				$c_qry="update tblcompanyprice set group_no='".$row->group_no."',group_type='Y' where no = '".$s_data->price_no."' and group_productcode =  '".$s_data->group_productcode."'";
				$p_qry="update tblcompanygroup set group_productcode = '".$s_data->group_productcode."' where group_no = '".$row->group_no."'";
				pmysql_query($c_qry,get_db_conn());
				pmysql_query($p_qry,get_db_conn());
			}
		}
	}
	//echo $sql."<br>";
	//exdebug($c_qry);
	//exdebug($p_qry);

		$onload="<script>window.onload=function(){ alert('제휴사 등록이 완료되었습니다.');}</script>";
		$log_content = "## 제휴사생성 - $group_no $group_name ";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

} else if ($type=="modify" && $mode=="result") {
	
	$sql = "UPDATE tblcompanygroup SET ";
	$sql.= "group_name		= '{$group_name}', ";
	$sql.= "group_description='{$group_description}', ";
	$sql.= "group_code='{$group_code}' ";
	$sql.= "WHERE group_no = '{$group_no}' ";
	pmysql_query($sql,get_db_conn());
    //echo $sql."<br>";
	
	$log_content = "## 제휴사변경 - $group_no $group_name ";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	
	$onload="<script>window.onload=function(){ alert('제휴사 수정이 완료되었습니다.');}</script>";
	$type='';
	$mode='';
	$group_no='';
    $group_couponcode = "";
} else if ($type=="delete") {
	//삭제 기능 넣을 경우 관련 제휴사 회원들도 초기화 시켜야함

	$sql = "DELETE FROM tblcompanygroup WHERE group_no = '{$group_no}' ";
	pmysql_query($sql,get_db_conn());

	//제휴사 기본 그룹으로 변경
	$m_sql="UPDATE tblmember set cooper_yn='N',company_group='0',company_code='' where company_group= '{$group_no}'";
	pmysql_query($m_sql,get_db_conn());

	//제휴사 상품정보 삭제
	$d_sql="UPDATE tblcompanyprice set group_no='0', group_type='N' WHERE group_no = '{$group_no}'";
	pmysql_query($d_sql,get_db_conn());

	$onload="<script>window.onload=function(){ alert('제휴사 삭제가 완료되었습니다.');}</script>";
	$type='';
	$group_no='';
}

if(ord($type)==0) $type="insert";

include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	var regId = /^[a-zA-Z0-9]/;
	var myregexp = /[가-힣]/; // 한글
	var gcode = document.form1.group_code;

	if (document.form1.group_name.value.length==0) {
		alert("제휴사명을 입력하세요");
		document.form1.group_name.focus();
		return;
	}
	if (gcode.value.length==0) {
		alert("제휴사코드를 입력하세요");
		gcode.focus();
		return;
	}else{
		if (gcode.value.length!=8) {
			alert("제휴사 코드는 8자리입니다");
			gcode.focus();
			return;
		}else{
			if(!regId.test(gcode.value)) {
				alert('영문과 숫자로 입력하세요.');
				gcode.focus();
				return;
			}
		}
	}
	if(type=="modify") {
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}

function GroupSend(type,code) {
	if (type=="delete") {
		if (!confirm("해당 제휴사를 삭제하시겠습니까?\n!!!제휴사별 상품 금액은 삭제되며 제휴사회원은 일반회원으로됩니다!!!")) {
			return;
		}
	}
	
	document.form2.type.value=type;
	document.form2.group_no.value=code;
	document.form2.submit();
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 제휴사 관리 &gt;<span>제휴사 정보 관리</span></p></div></div>
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
					<div class="title_depth3">제휴사 정보 관리</div>
					
                </td>
            </tr>
			<tr><td height="8"></td></tr>
          
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width="30" />
                   	<col width="70" />
                    <col width="" />
					<col width="80" />
                    <col width="60" />
                    <col width="60" />
				</colgroup>
				<TR>
					<th>No</th>
					<th>제휴사명</th>
					
					<th>제휴사설명</th>
					
					<th>회원수</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$sql = "SELECT COUNT(*) as cnt, company_group FROM tblmember ";
				$sql.= "WHERE cooper_yn = 'Y' AND member_out != 'Y' GROUP BY company_group ";
				$result=pmysql_query($sql,get_db_conn());
                //echo $sql."<br>";
				while($row=pmysql_fetch_object($result)) {
					$group_cnt[$row->company_group] = $row->cnt;
				}
				pmysql_free_result($result);
			if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
//				exdebug($sql);
			}
				$sql = "SELECT * FROM tblcompanygroup order by group_no";
				$result = pmysql_query($sql,get_db_conn());
                //echo $sql."<br>";
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$i++;
					$group_code_num=$row->group_no;
					
					echo "<tr>\n";
					echo "	<td>{$i}</td>\n";
					echo "	<td><span class=\"font_orange\"><b>{$row->group_name}</b></span></td>\n";
					echo "	<td><NOBR>&nbsp;{$row->group_description}</NOBR></td>\n";
                    echo "	<td>".number_format($group_cnt[$group_code_num])."명</td>\n";
					echo "	<td><a href=\"javascript:GroupSend('modify','{$row->group_no}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";

//					if($group_code_num=="1"){
//					echo "	<td><img src=\"images/btn_del1.gif\" border=\"0\"></td>\n";
//					}else{
					echo "	<td><a href=\"javascript:GroupSend('delete','{$row->group_no}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
//					}

					echo "</tr>\n";
				}
				pmysql_free_result($result);
				if ($i==0) {
					echo "<tr><td colspan=\"8\" align=\"center\">등록된 제휴사가 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub" id = 'scrollAutoMove'>제휴사 정보 등록/수정</div>
				</td>
			</tr>
<?php
			if($type=="modify") {
				$sql = "SELECT * FROM tblcompanygroup WHERE group_no = '{$group_no}' ";
				$result = pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name=$row->group_name;
					$group_code=$row->group_code;
					$group_description=$row->group_description;
					$group_productcode=$row->group_productcode;
//                    $group_orderprice_s=$row->group_orderprice_s;
//                    $group_orderprice_e=$row->group_orderprice_e;
//                    $group_ordercnt_s=$row->group_ordercnt_s;
//                    $group_ordercnt_e=$row->group_ordercnt_e;
//                    $group_couponcode = $row->group_couponcode;

//					$group_reserve_addtype=strpos($row->group_addreserve,"%")?"%":"";
//					$group_addreserve=str_replace("%","",$row->group_addreserve);

//					$group_level=$row->group_level;
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
				$group_code='';
				$group_productcode='';
//				$group_level='';
//                $group_addreserve = 0;
//                $group_orderprice_s = 0;
//                $group_orderprice_e = 0;
//                $group_ordercnt_s = 0;
//                $group_ordercnt_e = 0;
				$n_check = 'new';
			}
?>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=group_no value="<?=$group_no?>">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=139>
				<col width=>
				<TR>
					<th><span>제휴사명</span></th>
					<td><input type=text name=group_name value="<?=$group_name?>" maxlength=30 style="width:200px;" class="input"></td>
				</TR>

				<TR>
					<th><span>제휴사 코드</span></th>
					<td><input type=text name=group_code value="<?=$group_code?>" maxlength=8 style="width:200px" class="input"> * 2자리 영문 제휴사 약자+6자리 숫자</td>
				</TR>

				<TR>
					<th><span>제휴사 설명</span></th>
					<td><input type=text name=group_description value="<?=$group_description?>" maxlength=55 style="width:450" class="input"> * 50자 이내</td>
				</TR>
				<?if($group_productcode !=''){?>
				<TR>
					<th><span>제휴사 가격 필드</span></th>
					<td><?=$group_productcode?></td>
				</TR>
				<?}?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<?php if($type=="insert"){?>
				<?if($i == 9 || $n_check = 'new'){ ?>
					<tr>
						<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/btn_confirm_com.gif" border="0" vspace="3"></a></td>
					</tr>
				<?}?>
			<?php }else if($type=="modify"){?>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/btn_edit1.gif" border="0" vspace="3"></a></td>
			</tr>
			<?php }?>
			</form>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=group_no>
			</form>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
<!--
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							
						</dl>
					</div>
				</td>
			</tr>
-->
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
	/*if($("input[name='relationProduct[]']").length > 4){
		alert('관련상품은 5개까지 등록이 가능합니다.');
		upList = false;
		//return upList;
	}*/
    
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
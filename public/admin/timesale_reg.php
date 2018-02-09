<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$block=$_REQUEST['blockval'];
$gotopage=$_REQUEST['gotoval'];
$sno=$_REQUEST['sno'];
$board='event_admin_ns';
$mode=($_REQUEST['mode'])?$_REQUEST['mode']:'ins';

$qry="select *,to_char(edate,'YYYY-MM-DD') as edt, to_char(sdate,'YYYY-MM-DD') as sdt from tbl_timesale_list where sno='".$sno."'";
$res=pmysql_query($qry);
$time_row=pmysql_fetch_array($res);
pmysql_free_result($res);

if($sno){
	$checked['view_type'][$time_row['view_type']]='checked';
	$checked['rolling_type'][$time_row['rolling_type']]='checked';
}else{
	$checked['view_type'][0]='checked';
	$checked['rolling_type'][0]='checked';
}

$imagepath=$cfg_img_path['timesale'];

$p_imagepath = $cfg_img_path['product'];

//상품 정보 쿼리
$pdt_qry="select * from view_tblproduct where productcode='".$time_row['productcode']."'";
$pdt_res=pmysql_query($pdt_qry);
$pdt_row=pmysql_fetch_array($pdt_res);
pmysql_free_result($pdt_res);

?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
<link rel="stylesheet" href="../css/community.css">

<script language="JavaScript">


	$(function(){
		$(".img_view_sizeset").on('mouseover',function(){
			$("#img_view_div").find('img').attr('src',($(this).attr('src')));
			$("#img_view_div").find('img').css('display','block');
		});

		$(".img_view_sizeset").on('mouseout',function(){
			$("#img_view_div").find('img').css('display','none');
		});

		//카테고리 검색관련 - 페이지에맞게 아이디 수정필요함.
		$(".searchcate").click(function(){

			var pdtcode = $("#code_a").val()+$("#code_b").val()+$("#code_c").val()+$("#code_d").val();
			var pdtname = $("#proname").val();
			/*
			$.ajax({
				type: "GET",
				url: "./product_search.php",
				data: "pdtcode="+pdtcode+"&pdtname="+pdtname,
				dataType:"html",  //타입은 여러가지 존재
				success: function(result) {
					$("#pdt_search_div").html(result);
					$("#pdt_search_div").css("display","block");
					$(".pdt_search_result").dblclick(function(){

						//페이지별 수정필요 부분
						$("#pdt_name").val($(this).find(".name_span").html());
						$("#price").val($(this).find(".price_span").html());
						$("#pdt_code").val($(this).find(".pdt_code_val").val());
						$("#pdt_img").html($(this).find(".img_span").html());
						$("#pdt_search_div").css("display","none");
						//페이지별 수정필요 부분
					});
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
				}
			});
			*/
			$.ajaxSetup ({
				cache: false
			});
			$("#pdt_search_div").load("./product_search.php?pdtcode="+pdtcode+"&pdtname="+pdtname,function(){

				$("#pdt_search_div").css("display","block");

				$(".pdt_search_result").dblclick(function(){

					//페이지별 수정필요 부분
					$("#pdt_name").val($(this).find(".name_span").html());
					$("#price").val($(this).find(".price_span").html());
					$("#pdt_code").val($(this).find(".pdt_code_val").val());
					$("#pdt_img").html($(this).find(".img_span").html());
					$("#pdt_search_div").css("display","none");
					//페이지별 수정필요 부분
				});
			});
		});

		$(".clzcate").click(function(){
			$("#pdt_search_div").css("display","none");

		});
		//카테고리 검색종료

	});

	function chkform(mode){

		var chkinput=0;
		$("input[type=text]:not(input[name=proname])").each(function(){
			if($(this).val()==''){

				alert("["+$(this).attr('alt')+"] 필수입력사항");
				$(this).focus();
				chkinput=1;
				return false;
			}
		});

		if(chkinput==0){
			if(mode=='ins'){

				$("input[type=file]").each(function(){
					if($(this).val()==''){
						alert("["+$(this).attr('alt')+"] 필수입력사항");
						chkinput=1;
						return false;
					}
				});
			}
		}

		if(chkinput==0){
			return true;
		}else{
			return false;
		}
	}

	function del_confirm(cno,sno){

		if(confirm('삭제하시겠습니까?')){
			document.location.href="market_event_ins.php?mode=re_del&c_num="+cno+"&sno="+sno+"&board=event_admin_ns";
		}
	}

	function GoPage(block,gotopage) {
		document.form_paging.block.value=block;
		document.form_paging.gotopage.value=gotopage;
		document.form_paging.submit();
	}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>타임세일 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">

<div class="title_depth3">타임세일 등록/수정</div>

<form name="eventform" method="post" action="timesale_ins.php" enctype="multipart/form-data" onsubmit="return chkform('<?=$mode?>');">

	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="sno" value="<?=$sno?>">
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20" style="position:relative">
					<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품선택</span></th>
							<td>
							<div style="position:relative;">
								<? include("../cateSearch.php") ?>
							</div>
							</td>
						</tr>
						<tr>
							<th><span>선택상품</span></th>
							<td>
								<span id="pdt_img">
								<?if($sno>0){?>
									<?if(is_file($p_imagepath.$pdt_row['tinyimage'])){ ?>
									<img src="<?=$p_imagepath?><?=$pdt_row['tinyimage']?>" width="50px" height="50px">
									<?}elseif(is_file($Dir.$pdt_row['tinyimage'])){ ?>
									<img src="<?=$Dir?><?=$pdt_row['tinyimage']?>" width="50px" height="50px">
									<?}?>
								<?}else{?>
								 상단에서 등록할 상품을 선택해 주세요.
								<?}?>
								</span>
								<input type="text" readonly name="pdt_name" id="pdt_name" style="width:200px;border:0px;" alt="선택상품" value="<?=$pdt_row['productname']?>"></span>
							    <input type="hidden" name="pdt_code" id="pdt_code"  value="<?=$time_row['productcode']?>"  />
							</td>
						</tr>
						<tr>
							<th><span>이벤트명</span></th>
							<td><input type="text" name="title" id="title" style="width:50%" value="<?=$time_row['title']?>" alt="이벤트명" /></td>
						</tr>
						<tr>
							<th><span>PC버전 메인 & 모바일 이미지 (609*388)</span></th>
							<td >
							<input type="file" name="rolling_file[]" alt="롤링 이미지"/>
							<?
								if($time_row['rolling_v_img']){
							?>
								<br><img src="<?=$imagepath?><?=$time_row['rolling_v_img']?>" style="height:30px;" class="img_view_sizeset">
							<?
								}
							?>
							</td>
						</tr>
						<tr>
							<th><span>PC버전 본문 이미지 (779*506) </span></th>
							<td>
							<input type="file" name="view_file[]" alt="본문 이미지" />
							<?
								if($time_row['view_v_img']){
							?>
								<br><img src="<?=$imagepath?><?=$time_row['view_v_img']?>" style="height:30px;" class="img_view_sizeset">
							<?
								}
							?>
							</td>
						</tr>
						<tr>
							<th><span>시작일</span></th>
							<td><input type="text" name="sdate" OnClick="Calendar(event)"  value="<?=$time_row['sdate']?>" alt="시작일" /><span style="color:red;">달력 선택 후 시간,분,초를 정확한 형식으로 입력해주세요. ex) <?=date('Y-m-d H:i:s')?></span></td>
						</tr>
						<tr>
							<th><span>종료일</span></th>
							<td><input type="text" name="edate" OnClick="Calendar(event)"  value="<?=$time_row['edate']?>" alt="종료일" /><span style="color:red;">달력 선택 후 시간,분,초를 정확한 형식으로 입력해주세요. ex) <?=date('Y-m-d H:i:s')?></span></td>
						</tr>
						<tr>
							<th><span>판매가(정가)</span></th>
							<td><input type="text" name="price" id="price" value="<?=$time_row['price']?>" alt="판매가" /> 원</td>
						</tr>
						<tr>
							<th><span>특가(할인가)</span></th>
							<td><input type="text" name="s_price" id="s_price" value="<?=$time_row['s_price']?>" alt="특가" /> 원</td>
						</tr>
						<tr>
							<th><span>판매수량 (실재고수량)</span></th>
							<td><input type="text" name="ea" id="ea" value="<?=$time_row['ea']?>" alt="판매수량" /> </td>
						</tr>
						<tr>
							<th><span>판매노출수량</span></th>
							<td><input type="text" name="add_ea" id="add_ea" value="<?=$time_row['add_ea']?>" alt="추가판매수량" /><span style="color:red;">[정확한 수량 표시를 위하여 총 수량에도 더해집니다.]</span> </td>
						</tr>
						<tr>
							<th><span>타임세일노출</span></th>
							<td>
								<input type="radio" name="view_type" value="0" alt="타임세일노출" <?=$checked['view_type'][0]?>/> 비노출
								<input type="radio" name="view_type" value="1" alt="타임세일노출" <?=$checked['view_type'][1]?>/> 노출
							</td>
						</tr>
						<tr>
							<th><span>롤링 노출</span></th>
							<td>
								<input type="radio" name="rolling_type" value="0" alt="롤링 노출"  <?=$checked['rolling_type'][0]?>/> 비노출
								<input type="radio" name="rolling_type" value="1" alt="롤링 노출"  <?=$checked['rolling_type'][1]?>/> 노출
							</td>
						</tr>
					</table>
				</div>
				<div style="width:100%;text-align:center">
					<input type="image" src="../admin/images/btn_confirm_com.gif">
					<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='timesale_list.php?gotopage=<?=$gotopage?>&block=<?=$block?>'">
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
<script language="javascript">

</script>
<?=$onload?>
<?php
include("copyright.php");

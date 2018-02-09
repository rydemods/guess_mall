<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#exdebug($_POST);

$s_curtime=strtotime("$s_year-$s_month-$s_day");
$s_curdate=date("Ymd",$s_curtime);
$e_curtime=strtotime("$e_year-$e_month-$e_day 23:59:59");
$e_curdate=date("Ymd",$e_curtime)."235959";

//exdebug($s_curtime);
//exdebug($e_curtime);

list($qna_email, $qna_hp) = pmysql_fetch("Select email, mobile From tblmember Where id='".$_ShopInfo->getMemid()."' ", get_db_conn());

$sql = "SELECT  *
        FROM    tblboard
        WHERE   1=1
        AND     mem_id='".$_ShopInfo->getMemid()."'
        AND     board = 'qna'
        AND     writetime >= ".$s_curtime." AND writetime <= ".$e_curtime."
        ORDER BY writetime desc
        ";
$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
#exdebug($sql);
?>
<script type="text/javascript">
<!--
$(document).ready(function(){

    $('.btn-qna-detail').click(function(){
        var num = $(this).data('num');
        var modify_pdtimg       = $(this).next().next().val();
        var modify_brandname    = $(this).next().next().next().val();
        var modify_productname  = $(this).next().next().next().next().val();
        var modify_title        = $(this).next().next().next().next().next().val();
        var modify_content      = $(this).next().next().next().next().next().next().val();
        var modify_sms_send     = $(this).next().next().next().next().next().next().next().val();
        var modify_hp           = $(this).next().next().next().next().next().next().next().next().val();
        var modify_email_send   = $(this).next().next().next().next().next().next().next().next().next().val();
        var modify_email        = $(this).next().next().next().next().next().next().next().next().next().next().val();
        var modify_secret       = $(this).next().next().next().next().next().next().next().next().next().next().next().val();
        var modify_passwd       = $(this).next().next().next().next().next().next().next().next().next().next().next().next().val();
        var modify_answer       = $(this).next().next().next().next().next().next().next().next().next().next().next().next().next().val();
        //console.log(modify_secret);

        //Layer 에 값 채우기
        $(".modify_info img").attr({"src":modify_pdtimg});
        $("#qna-brandname").html("["+modify_brandname+"]");
        $("#qna-productname").html(modify_productname);
        $("#inp_writer").val(modify_title);
        $("#inp_content").val(modify_content);
        $("#qna-num").val(num);

        if(modify_answer != "") {
            // 답변글이 있으면
            $("#qna_answer").show();
            $("#inp_answer").html(modify_answer);
            $(".btn_wrap a").html("확인");

            var close = "javascript:LayerClose();";
            $(".btn_wrap a").attr({"onclick":close});
        } else {
            // 답변글이 없으면
            $("#qna_answer").hide();
            $("#inp_answer").html(null);
            $(".btn_wrap a").html("수정하기");

            var close = "javascript:ModifyAjaxQna();";
            $(".btn_wrap a").attr({"onclick":close});
        }

        if(modify_sms_send == "1") {
            $("#phone_chk").prop( 'checked', true);
            $("#inp_hp").val(modify_hp);
        } else {
             $("#phone_chk").prop( 'checked', false);
            $("#inp_hp").val(null);
       }

        if(modify_email_send == "1") {
            $("#email_chk").prop( 'checked', true);
            $("#inp_email").val(modify_email);
        } else {
            $("#email_chk").prop( 'checked', false);
            $("#inp_email").val(null);
        }

        if(modify_secret == "0") {
            $("#view").prop( 'checked', true);
            $("#no-view").prop( 'checked', false);
            $("#chk_secret").hide();
        }
        if(modify_secret == "1") {
            $("#view").prop( 'checked', false);
            $("#no-view").prop( 'checked', true);
            $("#inp_passwd").val(modify_passwd);
            $("#chk_secret").show();
        }

        // Layer 창 보이기
        $('.pop-qna-detail').fadeIn();
    });

    $("#view").click(function(){
        $("#chk_secret").hide();
        $("#inp_passwd").val(null);
    });

    $("#no-view").click(function(){
        $("#chk_secret").show();
    });

});

// 상품문의 수정
function ModifyAjaxQna() {
    //alert('modify');

    var up_subject      = $('#inp_writer').val(); // 제목
    var up_content      = $('#inp_content').val(); // 내용
    var up_hp           = $('#inp_hp').val();
    var up_email        = $('#inp_email').val();
    var up_is_secret    = $('input[name="view-type"]:checked').val(); //공개여부
    var up_passwd       = $('#inp_passwd').val(); // 비밀번호
    var num             = $('#qna-num').val(); //게시판 번호
    var up_sms_send     = "0";
    var up_email_send   = "0";

    if(up_hp != "") {
        $("#phone_chk").prop( 'checked', true);
        up_sms_send = "1";
    }

    if(up_email != "") {
        $("#email_chk").prop( 'checked', true);
        up_email_send = "1";
    }

    if( $("#phone_chk").is(":checked") == true ) up_sms_send = "1";
    if( $("#email_chk").is(":checked") == true ) up_email_send = "1";
    //console.log(up_sms_send);

    if(up_sms_send == "1") {
        if(up_hp == "") {
            alert("답변받을 휴대폰번호를 입력해 주십시오.");
            $("#inp_hp").focus();
            return true;
        }
    }

    if(up_email_send == "1") {
        if(up_email == "") {
            alert("답변받을 이메일 주소를 입력해 주십시오.");
            $("#inp_email").focus();
            return true;
        }
    }

    if(up_is_secret == "1") {
        if(up_passwd == "") {
            alert("비밀번호를 입력해 주십시오.");
            $("#inp_passwd").focus();
            return true;
        }
    }

    if( confirm("수정하시겠습니까?")) {
        $.ajax({
            type: "POST",
            url: "ajax_modify_qna.php",
            data: {
                num : num,
                up_subject : up_subject,
                up_content : up_content,
                up_sms_send : up_sms_send,
                up_hp : up_hp,
                up_email_send : up_email_send,
                up_email : up_email,
                up_is_secret : up_is_secret,
                up_passwd : up_passwd
            }
        }).done( function( data ) {
            location.reload();
        });
    }
}

function LayerClose() {
    $('.layer-dimm-wrap').fadeOut();
}
//-->
</script>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

<?
 ?>

 <div id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">상품 문의</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_box_border">
						<h3>상품문의</h3>
					</div>

					<!-- 게시판 목록 -->
					<div class="myboard mt-50">
						<div class="order_right">
							<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
							<div class="total">총 <?=number_format($t_count)?>건</div>
							<div class="date-sort clear">
								<div class="type month">
									<p class="title">기간별 조회</p>
								<?
									if(!$day_division) $day_division = '1MONTH';

								?>
								<?foreach($arrSearchDate as $kk => $vv){?>
									<?
										$dayClassName = "";
										if($day_division != $kk){
											$dayClassName = '';
										}else{
											$dayClassName = 'on';
										}
									?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
								<?}?>
								</div>
								<div class="type calendar">
									<p class="title">일자별 조회</p>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$strDate1?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
									<span>-</span>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$strDate2?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
								</div>
								<button type="button" class="btn-go" onClick="javascript:CheckForm();"><span>검색</span></button>
							</div>
						    </form>
						</div>
						<table class="th_top">
							<caption></caption>
							<colgroup>
								<col style="width:5%">
								<col style="width:auto">
								<col style="width:35%">
								<col style="width:10%">
								<col style="width:10%">
								<col style="width:8%">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">NO.</th>
									<th scope="col">상품정보</th>
									<th scope="col">제목</th>
									<th scope="col">작성일</th>
									<th scope="col">공개여부</th>
									<th scope="col">답변</th>
								</tr>
							</thead>
							<tbody>
<?
		$cnt=0;
		if ($t_count > 0) {
			while($row=pmysql_fetch_object($result)) {

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
				$reg_date = date("Y.m.d", $row->writetime);

                // 상품 정보
                $sub_sql  = "   SELECT  b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand, b.tinyimage, b.deli,
                                        b.soldout, b.deli_price, c.brandname
                                FROM	tblboard a
                                JOIN	tblproduct b on a.pridx = b.pridx
                                JOIN    tblproductbrand c on b.brand = c.bridx
                                WHERE   1=1
                                AND	    a.pridx = " . $row->pridx . "
                                AND     b.display = 'Y'
                            ";
                $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

                list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."'");

                $qna_anwser = "";
                if($qnaCount > 0){
                    $a_status	= "완료";
                    list($qna_anwser)=pmysql_fetch("SELECT comment FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."'");
                } else {
                    $a_status	= "대기";
                }

                $secret_img = "";
                if($row->is_secret == "0") {
                    $is_secret	= "공개";
                    $secret_img = "<span></span>";
                }
                if($row->is_secret == "1") {
                    $is_secret	= "비공개";
                    $secret_img = "<span><img src='../static/img/icon/icon_lock.png' alt='비공개' class='vl-m'></span>";
                }

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $sub_row->tinyimage);
?>
								<tr class="bold">
									<td><?=$number?></td>
									<td class="goods_info">
										<a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$sub_row->productcode?>">
											<img src="<?=$file?>" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[<?=$sub_row->brandname?>]</li>
												<li><?=$sub_row->productname?></li>
											</ul>
										</a>
									</td>
									<td class="ta-l">
                                        <a href="javascript:void(0)" class="btn-qna-detail" data-num='<?=$row->num?>'><?=strcutMbDot($row->title, 35)?></a> <?=$secret_img?>
                                        <input type=hidden name='modify_pdtimg' value="<?=$file?>">
                                        <input type=hidden name='modify_brandname' value="<?=$sub_row->brandname?>">
                                        <input type=hidden name='modify_productname' value="<?=$sub_row->productname?>">
                                        <input type=hidden name='modify_title' value="<?=$row->title?>">
                                        <input type=hidden name='modify_content' value="<?=strip_tags($row->content)?>">
                                        <input type=hidden name='modify_sms_send' value="<?=$row->sms_send?>">
                                        <!-- <input type=hidden name='modify_hp' value="<?=str_replace("-", "", $qna_hp)?>">-->
                                       <input type=hidden name='modify_hp' value="<?=$row->hp?>">
										<input type=hidden name='modify_email_send' value="<?=$row->email_send?>">
                                       <!--   <input type=hidden name='modify_email' value="<?=$qna_email?>">-->
                                        <input type=hidden name='modify_email' value="<?=$row->email?>">
                                        <input type=hidden name='modify_secret' value="<?=$row->is_secret?>">
                                        <input type=hidden name='modify_passwd' value="<?=$row->passwd?>">
                                        <input type=hidden name='modify_answer' value="<?=$qna_anwser?>">
                                    </td>
									<td><?=$reg_date?></td>
									<td><?=$is_secret?></td>
									<td><?=$a_status?></td>
								</tr>
<?
		$cnt++;
		}
	} else {
?>
								<tr>
									<td colspan="7">내역이 없습니다.</td>
								</tr>
<?
	}
?>
							</tbody>
						</table>
						<div class="list-paginate mt-20"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
					</div>
					<!-- // 게시판 목록 -->

				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->



<!-- 상품문의 상세팝업 -->
<div class="layer-dimm-wrap pop-qna-detail"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner w800">
		<h3 class="layer-title">HOT<span class="type_txt1">;T</span> 상품문의</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<table class="th_left">
				<caption>1:1 문의 작성/상세보기</caption>
				<colgroup>
					<col style="width:100px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">상품</th>
						<td colspan="3" class="goods_info modify_info">
							<a href="javascript:void(0)">
								<img src="../static/img/test/@mypage_main_order1.jpg" alt="마이페이지 상품 썸네일 이미지">
								<ul class="bold">
									<li id="qna-brandname">[나이키]</li>
									<li id="qna-productname">루나에픽 플라이니트 MEN 신발 러닝</li>
								</ul>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_writer">제목 <span class="required">*</span></label></th>
						<td colspan="3">
							<input type="text" id="inp_writer" title="제목 입력자리" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_content">문의내용 <span class="required">*</span></label></th>
						<td colspan="3">
							<textarea id="inp_content" cols="30" rows="10" style="width:100%"></textarea>
						</td>
					</tr>
					<tr id="qna_answer" style="display:none;">
						<th scope="row"><label for="inp_content">답변내용 <span class="required">*</span></label></th>
						<td colspan="3">
							<textarea id="inp_answer" cols="30" rows="10" style="width:100%" readonly></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="phone_chk">휴대폰 답변</label><input id="phone_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
						<td>
							<input type="text" placeholder="하이픈(-) 없이 입력" title="휴대폰 번호" style="width:240px" id="inp_hp" class="chk_only_number" maxlength="11">
						</td>
						<th><label for="email_chk">이메일 답변</label><input id="email_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
						<td>
							<input type="text" title="이메일 아이디 입력자리" style="width:240px" id="inp_email">
						</td>
					</tr>
					<tr>
						<th scope="row">공개여부</th>
						<td colspan="3">
							<input type="radio" name="view-type" id="view" value="0" class="radio-def" checked="">
							<label for="view">공개</label>
							<input type="radio" name="view-type" id="no-view" value="1" class="radio-def">
							<label for="no-view">비공개</label>
						</td>
					</tr>
					<!-- // [D]비공개 시 노출 -->
					<tr id="chk_secret" style="display:none;">
						<th scope="row">비밀번호 <span class="required">*</span></th>
						<td colspan="3"><input type="text" placeholder="영문, 대소문자, 숫자 조합 6~12자리" title="영문, 대소문자, 숫자 조합 6~12자리" id="inp_passwd"></td>
					</tr>
				</tbody>
			</table>
			<div class="btn_wrap">
                <input type='hidden' id='qna-num' value='' >
                <a href="#" class="btn-type1" onclick='javascript:ModifyAjaxQna();'>수정하기</a>
            </div>
		</div>
	</div>
</div>
<!-- // 상품문의 상세팝업 -->

<div id="create_openwin" style="display:none"></div>

<? include($Dir."admin/calendar_join.php");?>
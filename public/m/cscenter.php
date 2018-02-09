<?
include_once('outline/header_m.php');
$csMenu	= $_REQUEST['csMenu'];
if (!$csMenu) $csMenu = 'notice';
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(boardnum,block,gotopage,board) {
	if(!block){
		var block = 0;
	}
	location.href="board_view.php?board="+board+"&boardnum="+boardnum+"&block="+block+"&gotopage="+gotopage;
}

function faq(faq_type) {
	/*$(".js-faq-accordion > li ").removeClass('hide');
	if (faq_type != '')
	{
		$(".js-faq-accordion > li ").each(function () {
			if($(this).attr('alt') != faq_type) {
				$(this).addClass('hide');
			}
		});
	}*/
	document.faqForm.faq_type.value = faq_type;
	document.faqForm.faq_search.value = '';
	document.faqForm.submit();
}

function faqSearchSubmit() {
	document.faqForm.submit();
}

</SCRIPT>

	<!-- <div class="sub-title">
		<h2>CS CENTER</h2>
		<a class="btn-prev" href="javascript:;" onclick='history.back(-1); return false;'><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div> -->

	<div class="js-tab-component cs-center-tab">
		<!-- <div class="content-tab">
			<div class="js-menu-list">
				<div class="js-tab-line"></div>
				<ul>
					<li class="js-tab-menu<?if ($csMenu == 'notice') {?> on<?}?>"><a href="javascript:;"><span>공지사항</span></a></li>
					<li class="js-tab-menu<?if ($csMenu == 'faq') {?> on<?}?>"><a href="javascript:;"><span>FAQ</span></a></li>
					<li class="js-tab-menu<?if ($csMenu == 'personal') {?> on<?}?>"><a href="javascript:;"><span>1:1상담</span></a></li>
				</ul>
			</div>
		</div> -->

		<!-- [D] 기존 내용 hide -->
		<div class="js-tab-content cs-notice-wrap hide">
			<table class="th-top">
				<caption>공지사항 리스트</caption>
				<colgroup>
					<col style="width:45px">
					<col style="width:auto">
					<col style="width:90px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">제목</th>
						<th scope="col">등록일</th>
					</tr>
				</thead>
				<tbody>
		<?php
				$sql = "SELECT num, writetime as date, title as subject, content FROM tblboard ";
				$sql.= "WHERE board='notice' ";
				$sql.= "ORDER BY date DESC ";
				$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$date = date("Y-m-d",$row->date);
					$re_date="-";

		?>
					<tr>
						<td><?=$number?></td>
						<td class="subject"><a href="javascript:ViewNotice('<?=$row->num?>','<?=$paging->block?>','<?=$paging->gotopage?>','notice')"><?=strip_tags($row->subject)?></a></td>
						<td><?=$date?></td>
					</tr>
		<?php
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
		?>
					<tr height="30"><td colspan="3" align="center">공지사항이 없습니다.</td></tr>
		<?php
				}
		?>
				</tbody>
			</table>
			<form name=idxform method=get action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="board" value="notice" />
			<input type="hidden" name="block" />
			<input type="hidden" name="gotopage" />
			</form>
			<div class="paginate">
				<div class="box">
					<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
				</div>
			</div>
		</div><!-- //공지사항 -->
<?
		$faq_type		= $_GET[faq_type];
		$faq_search	= $_GET[faq_search];

		if($faq_type!='')$faq_on[$faq_type]="class='on'";
		else $faq_on_total="class='on'";
		$where[]=" b.secret='1'";
		if ($faq_search) $where[]=" a.faq_title LIKE '%{$faq_search}%'";
		if ($faq_type) $where[]=" a.faq_type = '{$faq_type}'";

		$sql = "select * from tblfaq a left join tblfaqcategory b on (a.faq_type=b.num) ";
		if($where)$sql.="where".implode(" and ",$where);
		$sql.=" order by a.sort asc, a.date asc";
		$result = pmysql_query($sql,get_db_conn());
		$t_count = pmysql_num_rows($result);
		$cnt=0;

		##카테고리 쿼리
		$cate_qry="select * from tblfaqcategory where secret='1' order by sort_num";
		$cate_result=pmysql_query($cate_qry);
?>
		<div class="js-tab-content faq-wrap hide">
			<h4 class="title">고객님들의 자주 묻는 질문을 확인하세요!</h4>
			<ul class="category-box">
				<li><a href="javascript:faq('');"<?if ($faq_type =='') {?> class="on"<?}?>>전체</a></li>
<?php
			$cate_cnt	= 0;
			while($cate_data = pmysql_fetch_object($cate_result)){
?>
				<li><a href="javascript:faq('<?=$cate_data->num?>');"<?if($faq_type == $cate_data->num) : ?> class="on"<?endif;?>><?=$cate_data->faq_category_name?></a></li>
<?
				$cate_cnt++;
			}
			if (($cate_cnt%3) > 0 && ($cate_cnt%3) < 3) {
				for($k=0;$k < ($cate_cnt%3);$k++) {
?>
				<li><span>&nbsp;</span></li>
<?
				}
			}
?>
			</ul>
			<form name=faqForm class="search-def" method=get action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="csMenu" value="faq" />
			<input type="hidden" name="faq_type" value="<?=$faq_type?>" />
				<fieldset>
					<legend>자주묻는 질문 검색</legend>
					<div class="input-cover"><input type="search" name="faq_search" title="검색어 입력자리" value="<?=$faq_search?>"></div>
					<button class="btn-def" type="button" onClick="javascript:faqSearchSubmit();"><span>검색</span></button>
				</fieldset>
			</form>
<?php if($t_count > 0 ) :?>
			<ul class="js-faq-accordion">
<?php
while($data = pmysql_fetch_object($result)){
?>
				<li class='faq_<?=$data->no?>' alt='<?=$data->faq_type?>'>
					<dl>
						<dt class="js-faq-accordion-menu"><?=$data->faq_title?></dt>
						<dd class="js-faq-accordion-content"><?=nl2br($data->faq_content)?></dd>
					</dl>
				</li>
<?}?>
			</ul>
<? else : ?>
		<!-- 내역 없는경우 -->
		<div class="none-ment margin">
			<p>검색한 내용이 없습니다.</p>
		</div><!-- //내역 없는경우 -->
<?	endif; ?>
		</div><!-- //FAQ -->
		<!-- //[D] 기존 내용 hide -->

		<div class="js-tab-content cs-qna-wrap cs">
			<!-- <h3 class="title">고객님의 궁금하신 사항을 신속하게 답변해 드리겠습니다.</h3> -->
<?
if(strlen($_MShopInfo->getMemid())==0) {
?>
			<div class="mt-30">
				<div class="none-ment">
					<p>1:1문의를 위해서는 로그인이 필요합니다.</p>
				</div>
				<div class="btnwrap pb-20">
					<div class="box">
						<a class="btn-def" href="login.php">로그인</a>
					</div>
				</div>
			</div>
<?} else {?>
			<form name=form1 method=post action="cscenter.ajax.php" enctype='multipart/form-data'>
			<input type=hidden name=mode value="write_exe">
			<input type='hidden' name='chk_mail' >
			<input type='hidden' name='chk_sms' >
			<!-- 사진 업로드 작성 -->
			<!-- <div class="form_photo_write">
				<section class="with-select">
					<h3>상담유형</h3>

					<div class="select-def">
						<select name="head_title">
						<?
							foreach($arrayCustomerHeadTitle as $k => $v){
						?>
							<option value = "<?=$k?>"><?=$v?></option>
						<?
							}
						?>
						</select>
					</div>
				</section>
				<section class="with-select">
					<h3>관련상품</h3>
					<div class="select-def">
						<select name="productcode">
<?

									//관련상품
									$op_sql = "SELECT op.* FROM tblorderproduct op LEFT JOIN tblorderinfo oi ON op.ordercode=oi.ordercode WHERE id='".$_ShopInfo->getMemid()."' AND op.op_step < 44 ";
									$op_sql.= "ORDER BY ordercode DESC , vender, productcode ASC ";
									$op_result=pmysql_query($op_sql,get_db_conn());
									$op_total=pmysql_num_rows($op_result);
									//exdebug($op_sql);
									$op_list	="";
									if ($op_total > 0 ) {
?>
									<option value="">구매한 상품을 선택 해주세요</option>

<?
										while($op_row=pmysql_fetch_object($op_result)) {
?>
									<option value="<?=$op_row->productcode?>"><?="[".substr($op_row->ordercode,0,4)."-".substr($op_row->ordercode,4,2)."-".substr($op_row->ordercode,6,2)."] ".$op_row->productname."(".$op_step[$op_row->op_step].")"?></option>

<?
										}

										if ($_pdata->productcode =='') $sel_product_text	= "구매하신 상품을 선택해주세요.";
									} else {
?>
									<option value="">구매상품 없음</option>

<?
									}
									pmysql_free_result($op_result);
?>
						</select>
					</div>
				</section>
				<section class="write-content">
					<h3>제목</h3>
					<div><input type="text" name="up_subject" size="40" id="qna-subject" class="w100-per" title="제목을 입력하세요."></div>
				</section>
				<section class="write-content">
					<h3>내용</h3>
					<textarea placeholder="내용을 입력하세요" name="up_content"  title="문의내용을 입력하세요."></textarea>
				</section>
				<section class="write-upload">
					<h3>이미지등록</h3>
					<ul>
						<li>
							<label>
								<span>이미지등록</span><input type="hidden" name="v_up_filename[0]" value="" class="vi-image"><input type="file" name="up_filename[0]" class="add-image">
								<div class="image_preview" style='display:none;position:absolute;top:0;left:0;width:100%;height:100%;'>
									<img src="#" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
									<a href="#" class="delete-btn">
										<button type="button"></button>
									</a>
								</div>
							</label>
						</li>
					</ul>
					<p class="note">파일명: 한글,영문,숫자 / 용량: 5M이하 / 파일형식: GIF,JPG</p>
				</section>
				<section class="phone">
					<h3>이메일</h3>
					<div><input type="text" class="w100-per" name="up_email" id="email-inp" title="이메일을 입력해 주세요."></div>
					<div class="replay-sms">
						<label for="send-email">이메일로 답변받음</label>
						<input type="checkbox" name="chk_mail" id="send-email" value = 'Y'>
					</div>
				</section>
				<section class="phone">
					<h3>핸드폰번호</h3>
					<div class="tel-input">
						<div class="select-def">
							<select name="hp0">
								<option value="010">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
							</select>
						</div>
						<div><input type="tel" id="join-tel" name="hp1"></div>
						<div><input type="tel" name="hp2"></div>
					</div>
					<div class="replay-sms">
						<label for="send-sms">SMS 수신</label>
						<input type="checkbox" name="chk_sms" id="send-sms" class="checkbox-def" value = 'Y'>
					</div>
				</section>
				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="javascript:CheckForm();">등록</a>
						<a class="btn-def" href="javascript:location.href='<?=$Dir.MDir?>cscenter.php?csMenu=personal';">취소</a>
					</div>
				</div>
			</div> -->
			<!-- // 사진 업로드 작성 -->

			<section class="top_title_wrap">
				<h2 class="page_local">
					<a href="javascript:history.back();" class="prev"></a>
					<span>1:1문의</span>
					<a href="/m/shop.php" class="home"></a>
				</h2>
			</section>
			<!-- 1:1문의 글쓰기 -->
			<div class="mypage_sub write_qna">
				<div class="order_table">
					<table class="my-th-left form_table">
						<colgroup>
							<col style="width:30%;">
							<col style="width:70%;">
						</colgroup>
						<tbody>
							<tr>
								<th>상담유형<span class="required">*</span></th>
								<td>
									<select class="select_def" class="required_value" name="head_title" label="상담유형">
									<?
										foreach($arrayCustomerHeadTitle as $k => $v){
									?>
										<option value = "<?=$k?>"><?=$v?></option>
									<?
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<th>제목<span class="required">*</span></th>
								<td><input type="text" name="up_subject" id="qna-subject" title="제목"></td>
							</tr>
							<tr>
								<th>내용<span class="required">*</span></th>
								<td><textarea class="required_value" name="up_content" id="qna-content" title="내용" label="내용"></textarea></td>
							</tr>
							<tr>
								<th>휴대폰번호</th>
								<td><input type="tel" id="hp" name="hp" title="휴대폰번호을 입력해 주세요." placeholder="하이픈(-) 없이 입력"></td>
							</tr>
							<tr>
								<th>이메일</th>
								<td><input type="email" name="up_email" id="email-inp" title="이메일을 입력해 주세요."></td>
							</tr>
							<tr>
								<th>첨부파일</th>
								<td>
									<div class="upload_file">
										<input class="upload-name" value="" disabled="disabled">
										<input type="hidden" name="v_up_filename[0]" value=" class="vi-image">
										<input type='hidden' name='ori_filename' value="">
										<label for="ex_filename" class="btn-def">파일첨부</label>
										<input type="file" id="ex_filename" name="up_filename[0]" class="upload-hidden">
									</div>

									<!-- <ul class="upload_photo_list clear">
										<li>
											<label>
												<input type="hidden" name="v_up_filename[0]" value="" class="vi-image"><input type="file" name="up_filename[0]" class="add-image">
												<div class="image_preview" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;">
													<img src="#" style="position:absolute;top:0;left:0;width:100%;height:100%;">
													<a href="#" class="delete-btn">
														<button type="button"></button>
													</a>
												</div>
											</label>
										</li>
										<li>
											<label>
												<input type="hidden" name="v_up_filename[1]" value="" class="vi-image"><input type="file" name="up_filename[1]" class="add-image">
												<div class="image_preview" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;">
													<img src="#" style="position:absolute;top:0;left:0;width:100%;height:100%;">
													<a href="#" class="delete-btn">
														<button type="button"></button>
													</a>
												</div>
											</label>
										</li>
									</ul> -->
								</td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.order_table -->

				<a class="btn-point" href="javascript:CheckForm();">문의하기</a>
			</div><!-- //.mypage_sub -->
			<!-- // 1:1문의 글쓰기 -->
			</form>

<?}?>
		</div><!-- //1:1문의 -->
	</div><!-- //.js-tab-component -->

	<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<script type="text/javascript">
    if (!('url' in window) && ('webkitURL' in window)) {
        window.URL = window.webkitURL;
    }

    $(document).ready(function() {
		$('.add-image').on('change', function( e ) {
			ext = $(this).val().split('.').pop().toLowerCase();
			if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
				resetFormElement($(this));
				window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
			} else {
				blobURL = window.URL.createObjectURL(e.target.files[0]);
				$(this).parents("li").find('.image_preview img').attr('src', blobURL);
				$(this).parents("li").find('.vi-image').val('');
				$(this).parents("li").find('.image_preview').show();
			}
		});

		$('.image_preview a').bind('click', function() {
			if( confirm('삭제 하시겠습니까?') ){
				resetFormElement($(this).parents("li").find('.add-image'));
				$(this).parents("li").find('.vi-image').val('');
				$(this).parent().hide();
			}
			return false;
		});

		//파일첨부 파일명 설정
	    $("#up_filename").change(function(){
	        var filename = $("#up_filename").val();
			$(".upload-name").text(filename);
			$("input[name=ori_filename]").val(getFilename(filename));
	    });
	});

  	//파일명 추출
    function getFilename(filename) {

    	var fileValue = filename.split("\\");
    	var fileName = fileValue[fileValue.length-1]; // 파일명

    	return fileName;
    }

	function resetFormElement(e) {
		e.wrap('<form>').closest('form').get(0).reset();
		e.unwrap();
	}


	function CheckForm() {
		var procSubmit = true;
		$(".required_value").each(function(){
			if(!$(this).val()){
				if($(this).attr('label') == "상담유형"){
					alert($(this).attr('label')+"을 선택해 주세요");
				}else{
					alert($(this).attr('label')+"을 입력해 주세요");
				}
				$(this).focus();
				procSubmit = false;
				return false;
			}
		});

		//휴대폰 번호가 있으면 답변 여부가 Y 아니면 N
		if($("input[name=hp]").val() != ""){
			$("input[name=chk_sms]").val("Y");
		}else{
			$("input[name=chk_sms]").val("N");
		}
		//이메일이 있으면 답변 여부가 Y 아니면 N
		if($("input[name=up_email]").val() != ""){
			$("input[name=chk_mail]").val("Y");
		}else{
			$("input[name=chk_mail]").val("N");
		}

		if(procSubmit){
			if( confirm('등록하시겠습니까?') ){
				document.form1.target="processFrame";
				document.form1.submit();
			} else {
				return;
			}
		}else{
			return false;
		}
	}
</script>

<? include_once('outline/footer_m.php'); ?>
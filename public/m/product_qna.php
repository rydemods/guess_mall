<?php
include_once('outline/header_m.php');
$productcode     = $_GET['productcode'];   // 상품 productcode
$pridx				= $_GET['pridx'];   // 상품 pridx

$sql = "SELECT * FROM tblboard WHERE board='qna' AND pridx='{$pridx}'  "; //AND is_secret = '0'
if ($qnasetup->use_reply != "Y") $sql.= "AND pos = 0 AND depth = 0 ";
$sql.= "ORDER BY thread,pos";
//echo $sql;
$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
<script>
	function PerDel(obj, num) {
		if( confirm('삭제하시겠습니까?') ){
			//document.form_exe.mode.value="del_exe";
			//document.form_exe.num.value=num;
			//document.form_exe.target="processFrame";
			//document.form_exe.submit();

			var passwd  = $(obj).next().val();
			//alert(passwd);
			//return;
			$.ajax({
				type: "POST",
				url: "../board/board.php",
				data: {
					'pagetype' : 'delete',
					'exec' : 'delete',
					'board' : 'qna',
					mode : 'delete_ajax',
					up_passwd : passwd,
					num : num
				}
			}).done( function( data ){
				alert('정상적으로 삭제되었습니다.');
				location.reload();
				//console.log( data );
			});

		} else {
			return;
		}
	}
	function GoPage(block,gotopage) {
		document.form2.block.value=block;
		document.form2.gotopage.value=gotopage;
		document.form2.submit();
	}

	function chkLoginWriteLink() {
		var mem_id = '<?=$_ShopInfo->getMemid()?>';

		if ( mem_id === "" ) {
			alert("로그인이 필요합니다.");
			location.href = '/m/login.php?chUrl=<?=urlencode("/m/product_qna_write.php?productcode={$productcode}&pridx={$pridx}")?>';
			return false;
		} else {
			location.href = '/m/product_qna_write.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>';
			return true;
		}
	}
</script>
<!-- 내용 -->
<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=productcode value="<?=$productcode?>">
<input type=hidden name=pridx value="<?=$pridx?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<main id="content">

	<div class="sub-title">
		<h2>상품 Q&#38;A</h2>
		<a class="btn-prev" href="productdetail.php?productcode=<?=$productcode?>"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>

	<p class="goods-qna-note">
		고객님의 문의에 최대한 빨리 답변 드리도록 하겠습니다.<br>
		질문에 대한 답변은 마이페이지에서도 확인 하실 수 있습니다.
	</p>

	<!-- Q&A 리스트 -->
	<div class="goods-qna-list">
		<h3>고객님이 작성해 주신 상품 질문 (<strong><?=number_format($t_count)?></strong>)</h3>
<?
		if ($t_count == 0) {
?>

		<!-- 내역 없는경우 -->
		<div class="none-ment">
			<p>내역이 없습니다.</p>
		</div><!-- //내역 없는경우 -->
<?
		} else {
?>
		<ul class="js-goods-qna-accordion">
<?
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());

		while($row=pmysql_fetch_object($result)) {
			$date = date( "Y-m-d" , $row->writetime);

			list($productcode, $tinyimage)=pmysql_fetch("SELECT productcode, tinyimage FROM tblproduct WHERE pridx = '".$row->pridx."'");

			list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."'");
			$countStr = "";
			if($qnaCount > 0){
				$a_status	= "답변완료";
			} else {
				$a_status	= "답변 전";
			}

			$qna_reply_sql = "SELECT * FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."' order by num desc";
			$qna_reply_res = pmysql_query($qna_reply_sql,get_db_conn());
?>
			<li>
				<dl>
					<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
						<button type="button" title="펼쳐보기">
							<p class="thumb"></p>
							<span class="qna-title"><?=$row->title?><?if($row->is_secret == '1') {?><img class="ico-lock" src="./static/img/icon/ico_lock_close.png" alt="내가 쓴 비밀글"><?}?></span>
							<span class="qna-id"><?=substr($row->mem_id,0, -2)."**"?></span>
							<span class="box">
								<span class="qna-date"><?=$date?></span>
								<span class="qna-condition"><strong><?=$a_status?></strong></span>
							</span>
						</button>
					</dt>
					<dd class="js-goods-qna-accordion-content">
						<p class="qna-question">
							<?if( $row->is_secret == '0' ||  $_ShopInfo->getmemid() == $row->mem_id ) {?>
							<!-- a href="../m/productdetail.php?productcode=<?=$productcode?>"><img src="<?=getProductImage($Dir.DataDir.'shopimages/product/',$tinyimage)?>" alt=""></a><br><br -->
							<?=nl2br($row->content)?>
							<?} else {?>
							비밀글입니다.
							<?}?>
							<?if( $_ShopInfo->getmemid() == $row->mem_id ) {?>
                                <br><br>
							<?if($qnaCount == 0){?>
								<button class="btn-function" type="button" onClick="javascript:location.href='product_qna_write.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>&qna_num=<?=$row->num?>'"><span>수정</span></button>
							<?}?>
								<button class="btn-function" type="button" onClick="javascript:PerDel(this,'<?=$row->num?>');"><span>삭제</span></button>
								<input type='hidden' name='modify_passwd' value='<?=$row->passwd?>' >
							<?}?>
						</p>
					<?if( $row->is_secret == '0' ||  $_ShopInfo->getmemid() == $row->mem_id ) {?>
						<?while($qna_reply_row = pmysql_fetch_object($qna_reply_res)){?>
						<p class="qna-answer"><?=nl2br($qna_reply_row->comment)?></p>
						<?}?>
					<?}?>
					</dd>
				</dl>
			</li>
<?
		}
?>
		</ul>
<?
	}
?>

<?
		if ($t_count > 0) {
?>
		<div class="list-paginate mt-10 mb-30">
			<?echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;?>
		</div>
<?
		}
?>

		<div class="btnwrap">
			<div class="box">
				<a class="btn-def" href="javascript:chkLoginWriteLink();">글쓰기</a>
			</div>
		</div>
	</div>
	<!-- // Q&A 리스트 -->

	<!-- Q&A 유의사항 -->
	<dl class="goods-qna-note">
		<dt>유의사항</dt>
		<dd>상품 Q&#38;A에 문의하신 내용과 답변을 확인하실 수 있습니다.</dd>
		<dd>고객님께서 작성하긴 내용은 관리자의 답변이 등록된 이후에는 수정이 불가능하며 삭제만 가능합니다.</dd>
		<dd>상품과 관련 없는 내용, 비방, 광고, 불건전한 내용의 글은 사전동의 없이 삭제될 수 있습니다.</dd>
	</dl>
	<!-- // Q&A 유의사항 -->

</main>
<!-- // 내용 -->

<? include_once('outline/footer_m.php'); ?>
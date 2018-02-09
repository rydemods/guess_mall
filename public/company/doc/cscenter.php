<?php 

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$homeNotice = arrayBoardLoop('notice_home', 3);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko" >

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="XNGOLF" />
	<meta name="keywords" content="" />

	<title>엑스넬스 코리아</title>

	<link rel="stylesheet" href="../css/c_xngolf.css" />
	<!--<script type="text/javascript" src="../css/select_type01.js" ></script>-->
	<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
</head>	
<script type="text/javascript">
$(document).ready(function(){
	
	$(".faq_list li").click(function(e){
		var answerIndex = $(".faq_list li").index(this);
		$(".faq_list li div.answer").eq(answerIndex).toggle();
	});
});
</script>
<div class="main_wrap">
		
<?php include "../outline/header.php"; ?>


	<div class="sub_top_wrap sub_pos_cs"></div>
	<div class="container960 mb_50">
		<img src="../img/common/cs_img01.jpg" alt="" />
		<div class="cs_menu_wrap">
			<div class="left">
				<p class="title">공지사항 <a href="notice.php" class="more_btn"></a></p>
				<ul class="list">
		<?foreach($homeNotice as $noticeKey => $noticeVal){?>
					<li><a href="notice_view.php?num=<?=$noticeVal['num']?>"><?=strcutDot($noticeVal['title'], 30)?></a></li>
		<?}?>
				</ul>
			</div>
			<div class="right">
				<a href="mailto:xnells@xnells.com"><img src="../img/common/cs_mtm_icon.gif" alt="일대일문의" /></a><a href="location01.php"><img src="../img/common/cs_local_icon.gif" alt="오시는 길" /></a>
			</div>
		</div>
		<div class="faq_wrap">
			<p class="title">자주하는 질문</p>
			<ul class="faq_list">
				<li><a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 
					</div>
				</li>
				
				<li><a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 
					</div>
				</li>
				<li><a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 
					</div>
				</li>
				<li>
					<a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 그렇게 합시다 
					</div>
				</li>
				<li><a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 
					</div>
				</li>
				<li><a href="javascript:;">FAQ목록 대표적으로 몇개만 지정해서 들어갑니다</a>
					<div class="answer hide">
						그렇게 합시다 
					</div>
				</li>
			</ul>
		</div>
	</div>


<?php include "../outline/footer.php"; ?>

</div>

</html>
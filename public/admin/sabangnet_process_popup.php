<style>
	.sabangnet_pop_close{
		padding-top:20px;
		margin:0px auto;
		text-align:center;
	}
	.sabangnet_pop_msg{
		margin:0px auto;
		text-align:center;
	}
</style>
<script src="../js/jquery.js"></script>
<script>
	$(document).ready(function () {
		$('.pop_close').click(function () {
			$('#dialog-overlay, #dialog-box', parent.document).hide();
			return false;
		});
		$(window).resize(function () {
			if (!$('#dialog-box').is(':hidden')) popup();
		});
	});
</script>
<!-- 쇼핑카트 -->
<body style = 'background:#fff;'>
	<div class="sabangnet_pop_msg">
		데이터 불러오는중...<br>
		<img src = '../img/ajax-loader.gif'>
	</div>

	<div class="sabangnet_pop_close">
		<a href = "javascript:;" class = 'pop_close'>닫기</a>
	</div>
</body>

<?php
$Dir = '../../';
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata.php");

?>

<html>
<head>
    <link rel="stylesheet" href="<?=$Dir?>static/css/common.css">
    <link rel="stylesheet" href="<?=$Dir?>static/css/component.css">
    <link rel="stylesheet" href="<?=$Dir?>static/css/content.css">
    <script type="text/javascript" src="<?=$Dir?>static/js/jquery-1.12.0.min.js"></script>
	<title>test-frame</title>
</head>
<body>
<script>
    $(document).ready(function(){
        $('.layer-detail-coupon').show();
    });
</script>
<div class="layer-dimm-wrap layer-detail-coupon" >
    <div class="dimm-bg"></div>
    <div class="layer-inner layer-coupon" style='height:500px;' >
        <h3 class="layer-title"></h3>
        <button type="button" class="btn-close">창 닫기 버튼</button>
        <iframe src='../coupon_layer.php' style='width:100%; height:100%; border:0;'></iframe>
    </div>
</div>
</div>
</body>
</html>
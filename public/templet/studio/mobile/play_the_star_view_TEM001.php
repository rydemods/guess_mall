<?php include ($Dir.TempletDir."studio/mobile/navi_TEM001.php"); ?>

<!-- 플레이더스타 리스트 -->
<div class="studio-play-list">
    <ul>
        <?=$list_html?>
    </ul>

    <div class="paginate">
        <div class="box">
                <?php echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page; ?>
        </div>
    </div>
</div>
<!-- // 플레이더스타 리스트 -->


<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>" >
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<script type="text/javascript">
    function GoPage(block,gotopage) {
        document.form2.block.value=block;
        document.form2.gotopage.value=gotopage;
        document.form2.submit();
    }
</script>



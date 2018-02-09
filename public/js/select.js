$(document).ready(function(){
$('select.select').each(function(){
    var $this = $(this), numberOfOptions = $(this).children('option').length;
  
    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled"></div>');

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());
  
    var $list = $('<ul ></ul>', {
        'class': 'select-options'
    }).insertAfter($styledSelect);
  
    for (var i = 0; i < numberOfOptions; i++) {
        $('<li ></li>', {
            text: $this.children('option').eq(i).text(),
            rel: $this.children('option').eq(i).val()
        }).appendTo($list);
    }
  
    var $listItems = $list.children('li');
  
    $styledSelect.click(function(e) {
        e.stopPropagation();
        $('div.select-styled.active').each(function(){
            $(this).removeClass('active').next('ul.select-options').hide();
        });
        $(this).toggleClass('active').next('ul.select-options').toggle();
    });
  
    $listItems.click(function(e) {
        e.stopPropagation();
        $styledSelect.text($(this).text()).removeClass('active');
        $this.val($(this).attr('rel'));
        $list.hide();
        //console.log($this.val());
    });
  
    $(document).click(function() {
        $styledSelect.removeClass('active');
        $list.hide();
    });

});

//페이지 카테고리 용
$('select.cate_select').each(function(){
    var $this = $(this), numberOfOptions = $(this).children('option').length;
  
    $this.addClass('cate_select-hidden'); 
    $this.wrap('<div class="cate_select"></div>');
    $this.after('<div class="cate_select-styled"></div>');

    var $styledSelect = $this.next('div.cate_select-styled');
	// selected 추가 2015 11 09 유동혁
	var selectItem = {};
	$this.children('option').prop( 'selected',function( i, val){
		if( val ){
			selectItem = $(this);
		}
	});
	if( selectItem.length > 0){
		$styledSelect.text(selectItem.text());
	} else {
		$styledSelect.text($this.children('option').eq(0).text());
	}
  
    var $list = $('<ul ></ul>', {
        'class': 'cate_select-options'
    }).insertAfter($styledSelect);
  
    for (var i = 0; i < numberOfOptions; i++) {
        $('<li ></li>', {
            text: $this.children('option').eq(i).text(),
            rel: $this.children('option').eq(i).val()
        }).appendTo($list);
    }
  
    var $listItems = $list.children('li');
  
    $styledSelect.click(function(e) {
        e.stopPropagation();
        $('div.cate_select-styled.active').each(function(){
            $(this).removeClass('active').next('ul.cate_select-options').hide();
        });
        $(this).toggleClass('active').next('ul.cate_select-options').toggle();
    });
  
    $listItems.click(function(e) {
        //e.stopPropagation(); 임시 주석 2015 11 06 유동혁 
        $styledSelect.text($(this).text()).removeClass('active');
        $this.val($(this).attr('rel'));
        $list.hide();
        //console.log($this.val());
    });
  
    $(document).click(function() {
        $styledSelect.removeClass('active');
        $list.hide();
    });

});

});
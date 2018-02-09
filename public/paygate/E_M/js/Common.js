<!--

document.onmousedown=function(e)
{
	if( typeof(e)!="undefined" )
	{
		click(e);
	}
	else
	{
		click();
	}
}

document.onkeydown=function(e)
{
	if( typeof(e)!="undefined" )
	{
		keypressed(e);
	}
	else
	{
		keypressed();
	}
}

function click(e)
{
	if(e==null)
	{
		if( (event.button==2) || (event.button==3) )
		{
			alert("오른쪽 버튼은 사용하실 수없습니다");
		}
	}
	else
	{
		if( (e.button==2) || (e.button==3) )
		{
			alert("오른쪽 버튼은 사용하실 수없습니다");
		}
	}
}
	
function keypressed(e)
{
	if(e==null)
	{
		if( event.keyCode == 123 || event.keyCode == 17 )
		{
			event.returnValue = false;
		}
	}
	else
	{
		if( e.which == 17 )
		{
			e.returnValue = false;
		}
	}
}
	
//<![CDATA[
function OrtChange(){
	if( window.orientation == 90 || window.orientation == -90 ){
		$('body').addClass('horizontal');
        }
	else{
		$('body').removeClass('horizontal');
	}

	$(window).bind("orientationchange", function(event){
		if(event.orientation == "portrait"){
			$('body').removeClass('horizontal');
		}       
		else{
			$('body').addClass('horizontal');
		}       
	});
};
//]]>

-->

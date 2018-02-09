//<![CDATA[

function Flash(Url,Width,Height){
 document.writeln("<object type='application/x-shockwave-flash' data='"+Url+"' width='"+Width+"' height='"+Height+"'>");
 document.writeln("<param name='movie' value='"+Url+"'/>");
 document.writeln("<param name='quality' value='high'/>");
 document.writeln("<param name='wmode' value='transparent'>");
 document.writeln("</object>");
}

//]]>


		 function setPng24(obj) {
			obj.width=obj.height=1;
			obj.className=obj.className.replace(/\bpng24\b/i,'');
			obj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
			obj.src='';
			return '';
		}


	function mainMenu(obj)
		{
			obj.src=obj.src.replace(".gif", "_on.gif");
		}



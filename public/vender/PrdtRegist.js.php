<?
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
function ACodeSendIt(f,obj) {
	if(obj.value.length>0) {
		f.code_b_name.value = "";
		f.code_c_name.value = "";
		f.code_d_name.value = "";
		f.code_a_name.value = obj.text;
		f.category_view.value = f.code_a_name.value;

		if(obj.ctype=="X") {
			f.code.value = obj.value+"000000000";
		} else {
			f.code.value = obj.value;
		}

		burl = "product_register.ctgr.php?code=" + obj.value;
		curl = "product_register.ctgr.php";
		durl = "product_register.ctgr.php";
		BCodeCtgr.location.href = burl;
		CCodeCtgr.location.href = curl;
		DCodeCtgr.location.href = durl;
	}
}

function sectSendIt(f,obj,x) {
	if(obj.value.length>0) {
		if(x == 2) {
			f.code_c_name.value = "";
			f.code_d_name.value = "";
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000000";
			} else {
				f.code.value = obj.value;
			}
			durl = "product_register.ctgr.php";
			f.code_b_name.value = obj.text;
			f.category_view.value = f.code_a_name.value + " > " + f.code_b_name.value;
			url = "product_register.ctgr.php?code="+obj.value;
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			f.code_d_name.value = "";
			f.code_c_name.value = obj.text;
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000";
			} else {
				f.code.value = obj.value;
			}
			f.category_view.value = f.code_a_name.value + " > " + f.code_b_name.value + " > " + f.code_c_name.value;
			url = "product_register.ctgr.php?code="+obj.value;
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value = obj.value;
			f.code_d_name.value = obj.text;
			f.category_view.value = f.code_a_name.value + " > " + f.code_b_name.value + " > " + f.code_c_name.value + " > " + f.code_d_name.value;
		}
	}
}

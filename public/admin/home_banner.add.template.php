<?
	if($_GET[banner_type] == 'image'){
?>
										<tr>
											<th><span>이미지</span></th>
											<td class='td_con1'><INPUT type='file' size='38' name='banner_img[]' style='width:100%'></td>
											<th><span>순서</span></th>
											<td class='td_con1'>
												<INPUT type='text' size='5' name='banner_sort[]'>
												<INPUT type='hidden' name='banner_mode[]' value = 'ins'>
											</td>
										</tr>
										<tr>
											<th><span>링크</span></th>
											<td class='td_con1' colspan='3'>
												<INPUT type='text' size='5' name='banner_link[]' style='width:90%'>
												<a href = 'javascript:;' class = 'CLS_btnDelRows'><img src = '../img/button/btn_bbs_del.gif' align = 'absmiddle' width = '60'></a>
												<INPUT type='hidden' name='banner_no[]' value = ''>
											</td>
										</tr>
										<tr>
											<th><span>노출</span></th>
											<td class="td_con1">
												<select name='banner_hidden[]'>
													<option value = '1'>노출</option>
													<option value = '0'>미노출</option>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan = '4' class = 'CLS_blank'></td>
										</tr>
<?
	}else{
?>
										<tr>
											<th><span>타이틀 기본 이미지</span></th>
											<td class='td_con1'><INPUT type='file' size='38' name='banner_img_title_on[]' style='width:100%'></td>
											<th><span>타이틀 선택 이미지</span></th>
											<td class='td_con1'><INPUT type='file' size='38' name='banner_img_title_out[]' style='width:100%'></td>
										</tr>
										<tr>
											<th><span>내용 이미지</span></th>
											<td class='td_con1'><INPUT type='file' size='38' name='banner_img[]' style='width:100%'></td>
											<th><span>순서</span></th>
											<td class='td_con1'>
												<INPUT type='text' size='5' name='banner_sort[]'>
												<INPUT type='hidden' name='banner_mode[]' value = 'ins'>
											</td>
										</tr>
										<tr>
											<th><span>링크</span></th>
											<td class='td_con1' colspan='3'>
												<INPUT type='text' size='5' name='banner_link[]' style='width:90%'>
												<a href = 'javascript:;' class = 'CLS_btnDelRows'><img src = '../img/button/btn_bbs_del.gif' align = 'absmiddle' width = '60'></a>
												<INPUT type='hidden' name='banner_no[]' value = ''>
											</td>
										</tr>
										<tr>
											<th><span>노출</span></th>
											<td class="td_con1">
												<select name='banner_hidden[]'>
													<option value = '1'>노출</option>
													<option value = '0'>미노출</option>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan = '4' class = 'CLS_blank'></td>
										</tr>

<?
	}
?>
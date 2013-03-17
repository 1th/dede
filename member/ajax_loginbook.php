<?php
/**
 * @version        $Id: ajax_loginsta.php 1 8:38 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if($myurl == '') exit('');
?>
<table class="table-show" style="width:97%;">
	<colgroup>
		<col style="width:15%;">
		<col style="width:35%;">
		<col style="width:15%;">
		<col style="width:35%;">
	</colgroup>
	<tr>
		<td class="tab-title"><span class="color_red">*</span>姓名:</td>
		<td><input type="text" name="truename" value="<?php echo $cfg_ml->M_EXT['uname']; ?>"/></td>
		<td class="tab-title"><span class="color_red">*</span>手机号：</td>
		<td><input type="text" name="telphone" value="<?php echo $cfg_ml->M_LoginID; ?>" /></td>
	</tr>
	<tr>
		<td class="tab-title">
			<span class="color_red">*</span>证件号
		</td>
		<td>
			<select name="idtype" id="idtype">
				<option value="身份证" <?php if ($cfg_ml->M_EXT['idtype'] =='身份证') {echo 'selected="selected"';} ?>>身份证</option>
				<option value="军官证" <?php if ($cfg_ml->M_EXT['idtype'] =='军官证') {echo 'selected="selected"';} ?>>军官证</option>
				<option value="护照"   <?php if ($cfg_ml->M_EXT['idtype'] =='护照') {echo 'selected="selected"';} ?>>护照</option>
			</select>
			<input type="text" name="idno" value="<?php echo $cfg_ml->M_EXT['idno']; ?>"/>
		</td>
		<td class="tab-title">邮箱:</td>
		<td><input type="text" name="email" value="<?php echo $cfg_ml->M_EMAIL; ?>" /></td>
	</tr>
</table>
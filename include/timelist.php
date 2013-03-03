<?php
require_once ("common.inc.php");
$line=$dsql->getone("select bj from #@__line where aid='$artid'");
if ($line['bj']){
	echo $line['bj'];
} else{
	echo '[]';
}

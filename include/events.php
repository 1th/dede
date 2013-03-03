<?php
	include ('./connector/base_connector.php');
	require_once ("common.inc.php");

	$bj=$dsql->getone("select bj from #@__line where aid='$aid'");
		var_dump($bj);
		if(ifbjj($bj['bj'])=='按日历报价')
		{
		$get_c_str = new get_c_str;
		$pdate=explode('<event>',$bj['bj']);
        array_shift($pdate);
		foreach($pdate as $date)
        {
		$sdate=$get_c_str -> get_str($date,'<start_date><![CDATA[',']]></start_date>'); //开始时间
        $edate=$get_c_str -> get_str($date,'<end_date><![CDATA[',']]></end_date>'); //结束时间
		$jg=$get_c_str -> get_str($date,'<text><![CDATA[',']]></text>'); //价格
		
		$sdate1=explode(' ',$sdate);
		$edate1=explode(' ',$edate);
		
		if($sdate1[0]==$edate1[0])
		{ //单天报价
		$yes .='<event>
<start_date><![CDATA['.$sdate1[0].' 00:00]]></start_date>
<end_date><![CDATA['.$sdate1[0].' 00:00]]></end_date>
<text><![CDATA['.$jg.']]></text>
</event>';
		}
		else
		{// 多天报价
		
		//格式时间
$t1=strtotime($sdate1[0]);
$t2=strtotime($edate1[0]);

for($i=1;$i<60;$i++)
{
if($t1>$t2) break;
$yes .='<event>
<start_date><![CDATA['.$sdate1[0].' 00:00]]></start_date>
<end_date><![CDATA['.$sdate1[0].' 00:00]]></end_date>
<text><![CDATA['.$jg.']]></text>
</event>';
$t1=$t1+86400; //增加一天
$sdate1[0]=date('Y-m-j',$t1);
 
}
		
		}
		
		}
		

	    $start="<?xml version='1.0' encoding='utf-8' ?><data>";
		$end=$yes.'</data>';
		$out = new OutputWriter($start, $end);
	
		
		$out->output();
		
		}
		else if(ifbjj($bj['bj'])=='按星期报价')
		{
		
		 $start="<?xml version='1.0' encoding='utf-8' ?>";
		 $end='<data><event>
<start_date><![CDATA[1900-04-12 00:00]]></start_date>
<end_date><![CDATA[1900-04-12 00:05]]></end_date>
<text><![CDATA[980]]></text>
<id><![CDATA[1334233639011]]></id>
</event>
</data>';
		$out = new OutputWriter($start, $end);	
		$out->output();
		
		}
		else
		{
		
		
		$start="<?xml version='1.0' encoding='utf-8' ?>";
		 $end='<data><event>
<start_date><![CDATA[1900-04-12 00:00]]></start_date>
<end_date><![CDATA[1900-04-12 00:05]]></end_date>
<text><![CDATA[]]></text>
<id><![CDATA[1334233639011]]></id>
</event>
</data>';
		$out = new OutputWriter($start, $end);	
		$out->output();
		}
?>
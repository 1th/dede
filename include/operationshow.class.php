<?php

if(!defined('DEDEINC')) exit('Request Error!');

require_once(DEDEINC.'/dedetemplate.class.php');



class operationshow
{
    var $dsql;
    var $tpl;
	var $orderid;

    function __construct($orderid)
    {
        if ($GLOBALS['cfg_mysql_type'] == 'mysqli' && function_exists("mysqli_init"))
        {
            $dsql = $GLOBALS['dsqli'];
        } else {
            $dsql = $GLOBALS['dsql'];
        }

        $this->dsql = $dsql;
        $this->tpl = new DedeTemplate();
	    $this->orderid = $orderid;
        if($GLOBALS['cfg_tplcache']=='N')
        {
            $this->tpl->isCache = false;
        }
    }



    //设置模板
    //如果想要使用模板中指定的pagesize，必须在调用模板后才调用 SetSource($sql)
    function SetTemplate($tplfile)
    {
        $this->tpl->LoadTemplate($tplfile);
    }

    function SetTemplet($tplfile)
    {
        $this->tpl->LoadTemplate($tplfile);
    }



    //设置/获取文档相关的各种变量
    function SetVar($k,$v)
    {
        global $_vars;
        if(!isset($_vars[$k]))
        {
            $_vars[$k] = $v;
        }
    }

    function GetVar($k)
    {
        global $_vars;
        return isset($_vars[$k]) ? $_vars[$k] : '';
    }


    //获得当前网址
    function GetCurUrl()
    {
        if(!empty($_SERVER["REQUEST_URI"]))
        {
            $nowurl = $_SERVER["REQUEST_URI"];
            $nowurls = explode("?",$nowurl);
            $nowurl = $nowurls[0];
        }
        else
        {
            $nowurl = $_SERVER["PHP_SELF"];
        }
        return $nowurl;
    }

	function getLine() {
		return $this->dsql->GetOne("select * from `#@__line_order` where buyid='{$this->orderid}'");
	}

    //显示数据
    function Display()
    {
        $this->tpl->SetObject($this);
        $this->tpl->Display();
    }

    //保存为HTML
    function SaveTo($filename)
    {
        $this->tpl->SaveTo($filename);
    }
}
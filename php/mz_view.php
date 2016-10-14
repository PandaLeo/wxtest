<?php
$page_name='详情';
include_once("global.php");
include_once("header.php");
$note_id=$_GET["note_id"];
$class_id=$_GET["class_id"];
$_par = parse_url(urldecode($url_out));
parse_str($_par['query'],$_query);

$_SESSION["referer"]=$url_now;

if($rmid == $_COOKIE["user_id"])
{
	//只修改推荐人ID,否则就变成自已推荐自已
	$path_array = explode("_",$_query["path"]);
	$rmid_array_num = count($path_array)-2;
	$rmid = $path_array[$rmid_array_num];
}
else
{
	$_query["depth"] = $_query["depth"]+1;
	$_query["path"] ? $_query["path"] = $_query["path"].$rmid."_" : $_query["path"]="_";
}


if(!$_query["lineid"]) $_query["lineid"] = time().$_COOKIE["user_id"];

$url_out = $_par['scheme']."://".$_par['host']."/".$_par['path'].'?'.http_build_query($_query);
if($_query["state"]=="") $url_out=$url_out."&state=repeat";


$mydb = new DbClass;
$mydb->DbLogon();
$class_id && $class_name=get_dict_name("note_class",$class_id);

$filed_arr = explode(",","note_title,note_content,wx_title,wx_remark,wx_photo,add_time,repeat_num,view_num");
$note_info = $mydb->DbQuery("select note_title,note_content,wx_title,wx_remark,wx_photo,to_char(add_time,'yyyy-mm-dd HH24:mi') add_time,repeat_num,view_num from sys_note where note_id=$note_id",$filed_arr);

$timestamp = time();
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url_jssdk = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$signature = sha1("jsapi_ticket=".$wx_config["00"]["js_ticket"]."&noncestr=GZOgQmEI9hKgyCLg&timestamp=$timestamp&url=$url_jssdk");
?>

<script src="js/b.js" type="text/javascript"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<body class="c-list note">
    <header><a class="back" href="javascript:history.back()" title="返回"></a>详情</header>
    
    <article class="c">
        <a href=""><img src="images/home-r2.jpg"></a>
        <div class="essay">
        	<h3><?=$note_info[0]["note_title"]?></h3>
            <time><?=$note_info[0]["add_time"]?></time>
            <div class="essay-con">
                <?=$note_info[0]["note_content"]?>
            </div>
        </div>
    </article>


<?php
include_once("footer.php");
?>


<script>

$.get('user_record.php?module=note&id=<?=$note_id?>&action=view&rmid=<?=$rmid?>&depth=<?=$_query["depth"]?>&lineid=<?=$_query["lineid"]?>&path=<?=$_query["path"]?>');
setInterval("$.get('user_record.php?module=note&id=<?=$note_id?>&action=stop&ct_num=20&rmid=<?=$rmid?>');", 20000);

wx.config({    
debug: false,    
appId: '<?=$wx_config["00"]["appid"]?>',    
timestamp: <?=$timestamp?>,    
nonceStr: 'GZOgQmEI9hKgyCLg',    
signature: '<?=$signature?>',    
jsApiList: [    
'checkJsApi',    
'onMenuShareTimeline',    
'onMenuShareAppMessage',
'onMenuShareQQ',
'onMenuShareWeibo'
]    
});
wx.ready(function () {
    var shareData = {
		title: '<?=str_replace(array("\r\n", "\r", "\n"), "", $note_info[0]["wx_title"])?>',
		desc: '<?=str_replace(array("\r\n", "\r", "\n"), "", $note_info[0]["wx_remark"])?>',
		link: '<?=$url_out?>',
		imgUrl: '<?php echo dirname(urldecode($url_now))."/admin/".$note_info[0]["wx_photo"];?>',
		success: function (){$.get('user_record.php?module=note&id=<?=$note_id?>&action=repeat&rmid=<?=$rmid?>&depth=<?=$_query["depth"]?>&lineid=<?=$_query["lineid"]?>&path=<?=$_query["path"]?>');}
	};
	wx.onMenuShareAppMessage(shareData);
	wx.onMenuShareTimeline(shareData);
	wx.onMenuShareQQ(shareData);
	wx.onMenuShareWeibo(shareData);
});
</script>

<script type="text/javascript">

//微信去掉下方刷新栏
if(navigator.userAgent.indexOf('MicroMessenger') >= 0){
	document.addEventListener('WeixinJSBridgeReady', function() {
		//WeixinJSBridge.call('hideToolbar');
	});
}
</script>
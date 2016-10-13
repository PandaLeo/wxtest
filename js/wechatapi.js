/**
 * Created by Administrator on 2016/10/13.
 */
function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  decodeURI(r[2]); return null;
}
var appid = '';
var timestamp = '';
var signature = '';
var out_url = '';
var id = GetQueryString('mz_id');
$(function (){
    $.ajax({
        url:"../mz_view.php?mz_id="+id,
        type:'get',
        data:{},
        cache:false,
        async:false,
        dataType:'json',
        success : function(data){
            // 返回数据
            appid =data.appid;
            timestamp =data.timestamp;
            signature =data.signature;
            out_url =data.out_url;
        },
        error : function() {
            // view("异常！");
            alert("异常！");
        }
    });
});

wx.config({
    debug: false,
    appId: appid,
    timestamp: timestamp,
    nonceStr: 'GZOgQmEI9hKgyCLg',
    signature: signature,
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
        title: '测试',
        desc: '测试描述',
        link: out_url,
        imgUrl: '/123.jpg',
        success: function (){}
    };
    wx.onMenuShareAppMessage(shareData);
    wx.onMenuShareTimeline(shareData);
    wx.onMenuShareQQ(shareData);
    wx.onMenuShareWeibo(shareData);
});
//微信去掉下方刷新栏
if(navigator.userAgent.indexOf('MicroMessenger') >= 0){
    document.addEventListener('WeixinJSBridgeReady', function() {
        //WeixinJSBridge.call('hideToolbar');
    });
}




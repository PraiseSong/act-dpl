/**
 * Created with JetBrains WebStorm.
 * User: qizhuq
 * Date: 4/24/13
 * Time: 2:12 PM
 * To change this template use File | Settings | File Templates.
 */
KISSY.use("node", function (S){
	var D = S.DOM;
    var html = D.create('<div id="J_demo" style="position: fixed;left:0;bottom:0;-webkit-border-radius: 0 5px 0 0; text-align: center;z-index:999999999;background: rgba(0, 0, 0, .6);color:#fff;width:200px;height:40px;line-height:40px;font-size:15px;"></div>');
    S.one(window).on("resize", function (){
        resize();
    });
    function resize(){
        S.one("#J_demo") && S.one("#J_demo").remove();
        S.one("body").append(html);
        var w = S.one(window).width();
        var h = S.one(window).height();
        S.one("#J_demo").html("当前视口尺寸："+w+" * "+h);
    }
    resize();
})
/**
 * Created with JetBrains WebStorm.
 * User: qizhuq
 * Date: 5/8/13
 * Time: 4:48 PM
 * To change this template use File | Settings | File Templates.
 */
$('.thumbnail').click(function (e){
    e.preventDefault();
    var dpl = $(this);
    var id = dpl.attr("data-id");
    var type = dpl.attr("data-type");
    updateLoading('正在加载模版资源...');
    showPop($(this).attr("title"), id);

    getHTML(type, id, function (){
        getCSS(type, id, function (){
            getTMS(type, id, function (){
                getJS(type, id, function (){
                    showDpl();
                });
            });
        });
    });
});

function getHTML(type, id, callback){
    $.get("dpl/"+id+"/self.html?t="+new Date().getTime(), function (html){
        updateLoading("已获取html模版...");

        var content = appendContent(html);
        $('#html').html(content.html());
        content.remove();

        callback && callback();
    });
}

function getCSS(type, id, callback){
    $.get("dpl/"+id+"/self.css?t="+new Date().getTime(), function (css){
        updateLoading("已获取CSS源码...");

        var content = appendContent(css);
        $('#css').html(content.html());
        content.remove();

        callback && callback();
    });
}

function getTMS(type, id, callback){
    $.get("dpl/"+id+"/self.tms?t="+new Date().getTime(), function (tms){
        updateLoading("已获取TMS代码...");

        var content = appendContent(tms);
        $('#tms').html(content.html());
        content.remove();

        callback && callback();
    });
}

function getJS(type, id, callback){
    $.get("dpl/"+id+"/self.script?t="+new Date().getTime(), function (js){
        updateLoading("已获取JS代码...");

        if(!js.trim() || js.trim().length <= 0){
            js = "无javascript代码";
        }
        var content = appendContent(js);
        $('#js').html(content.html());
        content.remove();

        callback && callback();
    });
}

function updateLoading(txt){
    $('#J-loading').html(txt || '').show();
}

function showDpl(){
    $('#dpl-main').show();
    $('#J-loading').hide();
    $('#J-copy-css-tms').css("visibility", "visible");
}

function showPop(title, id){
    $('#modal h3').html(title);
    $('#modal').modal('show');
    $('#J_modal_tab a:first').tab('show');
    $("#J-copied-tip").css("visibility", "hidden");
    id && $('#J-preview').attr("href", "dpl/"+id+"/preview.html");
}

function appendContent(content){
    if($('#J-tem').get(0)){
        $('#J-tem').remove();
    }

    content = content.replace(/\</g, "&lt;").replace(/\>/g, "&gt;");

    var content = $('body').append('<div id="J-tem"><textarea style="width:90%;display: block;margin:0 auto;height: 400px;">'+content+'</textarea></div>');

    return $('#J-tem');
}


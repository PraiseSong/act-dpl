/**
 * Created with JetBrains WebStorm.
 * User: qizhuq
 * Date: 5/8/13
 * Time: 4:48 PM
 * To change this template use File | Settings | File Templates.
 */
var currentDpl = null;
$('.thumbnail').click(function (e){
    e.preventDefault();
    var dpl = $(this);
    currentDpl = dpl;
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

$('#J-update').click(function (e){
    e.preventDefault();
    if(!currentDpl){return;}
    var author = currentDpl.attr("data-author");
    var id = currentDpl.attr("data-id");
    var confirm = window.confirm("亲，您确认更新 "+author+" 创建的这个模版");
    if(confirm){
        insetTip("更新中...", "loading");
        var html = $.trim($('#html textarea').val());
        var css = $.trim($('#css textarea').val());
        var js = $.trim($('#js textarea').val());
        var tms = $.trim($('#tms textarea').val());
        $.ajax("update_tem.php", {
            type: "post",
            dataType: "json",
            data: "id="+id+"&html="+encodeURIComponent(html)+"&tms="+encodeURIComponent(tms)+"&js="+encodeURIComponent(js)+"&css="+encodeURIComponent(css)+"",
            success: function (data){
                if(data.msg){
                    return insetTip(data.msg, "error");
                }else{
                    return insetTip("更新成功，建议预览确认一下", "success");
                }
            }
        });

        function insetTip(text, type){
            $('#J-update').parent().find(".tip").remove();
            var html = "";

            if(type === "success"){
                html = "<span class=\"label label-success tip\" style=\"margin-right: 5px;\">"+text+"</span>";
            }else if(type === "error"){
                html = "<span class=\"label label-important tip\" style=\"margin-right: 5px;\">"+text+"</span>";
            }else if(type === "loading"){
                html = "<span class=\"label tip\" style=\"margin-right:5px;\">"+text+"</span>";
            }
            $('#J-update').before(html);
        }
    }
});

$('#J-delete').click(function (e){
    e.preventDefault();
    if(!currentDpl){return;}
    var author = currentDpl.attr("data-author");
    var id = currentDpl.attr("data-id");
    var confirm = window.confirm("亲，您确认更新 "+author+" 创建的这个模版");
    if(confirm){
        insetTip("删除中...", "loading");
        $.ajax("delete.php", {
            type: "post",
            data: "id="+id+"",
            success: function (data){
                if(data === "no"){
                    return insetTip("操作失败", "error");
                }else{
                    setTimeout(function (){location.reload();}, 2000);
                    return insetTip("OK,2秒后自动刷新", "success");
                }
            }
        });

        function insetTip(text, type){
            $('#J-delete').parent().find(".tip").remove();
            var html = "";

            if(type === "success"){
                html = "<span class=\"label label-success tip\" style=\"margin-left: 5px;float: left;\">"+text+"</span>";
            }else if(type === "error"){
                html = "<span class=\"label label-important tip\" style=\"margin-left: 5px;float: left;\">"+text+"</span>";
            }else if(type === "loading"){
                html = "<span class=\"label tip\" style=\"margin-left:5px;float: left;\">"+text+"</span>";
            }
            $('#J-update').after(html);
        }
    }
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
    $('#J-update').parent().find(".tip").remove();
}

function appendContent(content){
    if($('#J-tem').get(0)){
        $('#J-tem').remove();
    }

    content = content.replace(/\</g, "&lt;").replace(/\>/g, "&gt;");

    var content = $('body').append('<div id="J-tem"><textarea style="width:90%;display: block;margin:0 auto;height: 400px;">'+content+'</textarea></div>');

    return $('#J-tem');
}


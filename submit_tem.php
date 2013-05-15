<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

$dpl_dir = "dpl";

$action = @$_POST['action'];
$name = @$_POST['name'];
$en_name = @$_POST['en_name'];
$tms = @$_POST['tms'];
$html = @$_POST['html'];
$js = @$_POST['js'];
$css = @$_POST['css'];
$author = @$_POST['author'];
$type = @$_POST['type'];
$thumb = @$_POST['thumb'];

$created_tem_dir = false;

if(!$js){
  $js = "该模版没有js代码";
}

function getCategories(){
    global $dpl_dir;
    $handle = fopen($dpl_dir.'/'."categories.md", 'r');
    $buffers = array();
    $len = filesize($dpl_dir.'/'."categories.md");
    if((int)$len < 2){
        return $buffers;
    }
    if ($handle) {
        while (($buffer = fgets($handle, $len)) !== false) {
           if(strlen(trim($buffer)) > 2){
            $type = preg_split("/\s/", $buffer);
            array_push($buffers, json_encode(array("name"=>$type[1], "id"=>$type[0])));
           }
        }
        if (!feof($handle)) {
            echo "靠！发生了怪异的事情，找颂赞吧";
        }
        fclose($handle);
    }
    return $buffers;
}
?>

<?php
    //新增分类
    if($action == "add_type"){
      $type_id = @$_POST['id'];
      if(!$name){
         exit("缺少新增分类的name");
      }
      if(!$type_id){
        exit("缺少新增分类的id");
      }
      foreach(getCategories() as $k => $category){
        $class = "";
        if($k === 0){
          $class = "active";
        }
        $category = json_decode($category);
        if($category->name === $name){
            exit("分类名称已存在");
        }else if($category->id === $type_id){
            exit("分类id已存在");
        }
      }

      $cate_handle = fopen($dpl_dir."/categories.md", 'a');
      $writed = fwrite($cate_handle, "\n".$type_id." ".$name);

      if($writed){
        exit("success");
      }else{
        exit("新增分类失败");
      }

      exit;
    }

    //检查模版文件夹是否重名
    if($action == "check_tem_name"){
      if(is_dir($dpl_dir."/".$en_name)){
          echo "yes";
      }else{
          echo "no";
      }
      exit;
    }

    //生成随机文件名
    if($action == "generate_temName"){
      $date = new DateTime();
      exit(md5($date->format('Y-m-d H:i:s')));
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>提交模版-ACT-DPL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="js/jquery.js"></script>
    <style type="text/css">
        body{
            padding: 10px;
        }
        form .table{
            width: 550px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<?php
  if($action && $action === "add"):
?>
<?php
  //写入当前模版目录
  $tem_dir = $dpl_dir."/".$en_name;
  if(!is_writable($dpl_dir.'/README.md')){
      $created_tem_dir = false;
      echo "<div class=\"alert alert-error\">文件目录：$dpl_dir 没有写入权限</div>";
  }else if(is_dir($tem_dir)){
    $created_tem_dir = false;
    $config_handle = fopen($tem_dir."/self.config", 'r');
    $config = fread($config_handle, filesize($tem_dir."/self.config"));
    $config = json_decode($config);

    echo "<div class=\"alert alert-error\">模版目录：$tem_dir 已经存在。
          <p>这个模版由 {$config->author} 创建于 {$config->time}，你找他吧</p>
          </div>";
  }else{
    $created_tem_dir = mkdir($tem_dir, 0777);
    if($created_tem_dir){
      echo "<div class=\"alert alert-success\">创建模版目录 $tem_dir 成功</div>";
    }else{
      echo "<div class=\"alert alert-error\">创建模版目录 $tem_dir 失败</div>";
    }
  }

  //写入模版信息
  if($created_tem_dir){
    $config = array("name"=>$name, "en_name"=>$en_name, "author"=>$author, "type"=>$type, "time"=>@date("Y-m-d H:i:m"), "thumb"=>$thumb);
    $tem_config = fopen($tem_dir."/self.config", 'w+');
    $writed = fwrite($tem_config, json_encode($config));
    if($writed>0){
      echo "<div class=\"alert alert-success\">写入模版配置文件成功</div>";
    }else{
      echo "<div class=\"alert alert-error\">写入模版配置文件失败</div>";
    }
  }

  //写入html
  if($created_tem_dir){
      $tem_html = fopen($tem_dir."/self.html", 'w+');
      $writed = fwrite($tem_html, $html);
      if($writed>0){
        echo "<div class=\"alert alert-success\">已保存HTML代码</div>";
      }else{
        echo "<div class=\"alert alert-error\">保存HTML代码失败</div>";
      }
  }

  //写入js
  if($created_tem_dir){
    $tem_js = fopen($tem_dir."/self.script", 'w+');
    $writed = fwrite($tem_js, $js);
    if($writed>0){
      echo "<div class=\"alert alert-success\">已保存JavaScript代码</div>";
    }else{
      echo "<div class=\"alert alert-error\">保存JavaScript代码失败</div>";
    }
  }

  //写入css
    if($created_tem_dir){
      $tem_css = fopen($tem_dir."/self.css", 'w+');
      $writed = fwrite($tem_css, $css);
      if($writed>0){
        echo "<div class=\"alert alert-success\">已保存CSS代码</div>";
      }else{
        echo "<div class=\"alert alert-error\">保存CSS代码失败</div>";
      }
    }

  //写入tms
  if($created_tem_dir){
    $tem_tms = fopen($tem_dir."/self.tms", 'w+');
    $writed = fwrite($tem_tms, $tms);
    if($writed>0){
      echo "<div class=\"alert alert-success\">已保存TMS代码</div>";
    }else{
      echo "<div class=\"alert alert-error\">保存TMS代码失败</div>";
    }
  }

  //写入预览页面
  if($created_tem_dir){
      $head_handle = fopen($dpl_dir."/head.php", 'r');
      $footer_handle = fopen($dpl_dir."/footer.php", 'r');
      $head = fread($head_handle, filesize($dpl_dir."/head.php"));
      $footer = fread($footer_handle, filesize($dpl_dir."/footer.php"));
      $preview = fopen($tem_dir."/preview.html", 'w+');
      $writed = fwrite($preview, $head.$html."<style type=\"text/css\">".$css."</style>"."<script type=\"text/javascript\">".$js."</script>".$footer);
      if($writed>0){
        echo "<div class=\"alert alert-success\">已生成预览页面</div>";
      }else{
        echo "<div class=\"alert alert-error\">生成预览页面失败</div>";
      }
  }
?>
<a href="<?php echo basename(__FILE__); ?>" class="btn btn-primary">再来一个模版</a>
<?php

?>
<?php
  else:
?>
<form class="form-horizontal" method="post" action="<?php echo basename(__FILE__); ?>">
    <input type="hidden" value="add" name="action" />
    <table class="table table-bordered">
      <caption>感谢您提交模版</caption>
        <tbody>
          <tr>
             <td>模版名称： </td>
             <td><input type="text" name="name" placeholder="模版名称" required></td>
          </tr>
          <tr>
              <td>模版文件夹(英文)名称： </td>
              <td><input type="text" name="en_name" placeholder="模版文件夹(英文)名称" pattern="[a-z|A-Z|-|0-9]+" required><a href="javascript:void(0)" id="J-generate-temName">亲，给我来个名称</a></td>
          </tr>
          <tr>
              <td>缩略图： </td>
              <td><input type="text" name="thumb" placeholder="模版缩略图" required>请提供图片地址</td>
          </tr>
         <tr>
              <td>模版分类：</td>
              <td>
               <div id="J-types">
                 <?php
                     foreach(getCategories() as $k => $category){
                       $class = "";
                       if($k === 0){
                         $class = "active";
                       }
                       $category = json_decode($category);
                      echo "<label class=\"radio\"><input type=\"radio\" name=\"type\" value=\"{$category->id}\" required />{$category->name}</label>";
                     }
                 ?>
               </div>
               <p><a href="javascript:void(0)" class="btn btn-mini btn-primary" id="J-add-type">新增分类</a></p>
              </td>
          </tr>
          <tr>
               <td>TMS源码： </td>
               <td><textarea rows="10" name="tms" required></textarea></td>
          </tr>
          <tr>
                <td>HTML源码： </td>
                <td><textarea rows="10" name="html" required></textarea></td>
          </tr>
          <tr>
               <td>JS源码： </td>
               <td><textarea rows="10" name="js"></textarea></td>
          </tr>
          <tr>
               <td>CSS源码： </td>
               <td><textarea rows="10" name="css" required></textarea></td>
          </tr>
          <tr>
            <td>模版作者： </td>
            <td><input type="text" name="author" placeholder="模版作者" required></td>
          </tr>
          <tr><td></td><td><button type="submit" class="btn btn-inverse" id="J-submit">提交</button></td></tr>
        </tbody>
    </table>
</form>
<div class="modal hide fade" id="modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>一个模版分类即将诞生！</h3>
    </div>
    <div class="modal-body">
        <input type="text" placeholder="分类名称" id="J-newType-name">
        <input type="text" placeholder="分类id" id="J-newType-id">
    </div>
    <div class="modal-footer">
        <span id="J-result"></span>
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
        <a href="#" class="btn btn-primary" id="J-save">保存</a>
    </div>
</div>
<?php endif; ?>
<script src="js/bootstrap.min.js"></script>
<script>
$("#J-add-type").click(function (e){
    e.preventDefault();
    $('#modal').modal('show').on("shown", function (){
        $('#J-newType-name').focus();
    });
});
$('#J-save').click(function (e){
    e.preventDefault();
    var name = $.trim($('#J-newType-name').val());
    var id = $.trim($('#J-newType-id').val());

    if(!name){
      $('#J-newType-name').focus();
      return alert("请输入模版分类名称");
    }
    if(!id){
      $('#J-newType-id').focus();
      return alert("请输入模版分类id");
    }

    if(id && !/[a-z|A-Z]/g.test(id)){
      $('#J-newType-id').focus();
      return alert("id必须是英文字符");
    }

    $.ajax("submit_tem.php", {
        type: "post",
        data: "action=add_type&name="+name+"&id="+id+"",
        success: function (data){
            data = $.trim(data);
            $('#J-result').attr("class", null);
            if(data !== "success"){
              $('#J-result').addClass("alert alert-error").html(data);
            }else if(data === "success"){
              $('#J-result').addClass("alert alert-success").html("新增分类成功");
              $('#J-types').append("<label class=\"radio\"><input type=\"radio\" name=\"type\" value=\""+id+"\" required />"+name+"</label>");
              setTimeout(function (){
                $('#modal').modal('hide');
              }, 2000);
            }
        }
    });
});

$('#J-submit').click(function (e){
  var types = $('[name=type]');
  if(types.length <= 0){
      $('#J-types').find('.alert-error').remove();
      $('#J-types').prepend("<div class=\"alert alert-error\">如果没有模版分类，你就新增一个</div>");
      window.scroll(0, 0);
      return false;
  }
});

$('input[name=en_name]').blur(function (){
  var val = $.trim($(this).val());
  var node = $(this);
  $(this).val(val);
  $.ajax("submit_tem.php", {
      type: "post",
      data: "action=check_tem_name&en_name="+val+"",
      success: function (data){
          data = $.trim(data);
          node.parent().find('.alert').remove();
          if(data === "yes"){
              node.after("<div class=\"alert alert-error\" style=\"margin-top:5px;\">模版文件夹重命啦！</div>");
              node.focus();
          }
      }
  });
});

$('#J-generate-temName').click(function (e){
  $.ajax("submit_tem.php", {
      type: "post",
      data: "action=generate_temName",
      success: function (data){
          data = $.trim(data);
          if(data){
              $('input[name=en_name]').val(data);
          }
      }
  });
});
</script>
</body>
</html>
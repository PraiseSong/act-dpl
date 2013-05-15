<?php
  ini_set("display_errors", "1");
  error_reporting(E_ALL);

  $dpl_dir = "dpl";

  $tems_config = array();

  function getDir($dir) {
  	$dirArray = array();
  	if (false != ($handle = opendir ( $dir ))) {
  		$i=0;
  		while ( false !== ($file = readdir ( $handle )) ) {
  			//去掉"“.”、“..”以及带“.xxx”后缀的文件
  			if ($file != "." && $file != ".."&&!strpos($file,".")) {
  				$dirArray[$i]=$file;
  				$i++;
  			}
  		}
  		//关闭句柄
  		closedir ( $handle );
  	}
  	return $dirArray;
  }

  if(count(getDir($dpl_dir)) >= 1){
    foreach(getDir($dpl_dir) as $k => $tem_dir){
        $config_handle = fopen($dpl_dir.'/'.$tem_dir."/self.config", 'r');
        $config = fread($config_handle, filesize($dpl_dir.'/'.$tem_dir."/self.config"));
        $config = json_decode($config);
        array_push($tems_config, $config);
    }
  }

  function getCategories(){
    global $dpl_dir;
    $handle = fopen($dpl_dir.'/'."categories.md", 'r');
    $buffers = array();
    if ($handle) {
        while (($buffer = fgets($handle, filesize($dpl_dir.'/'."categories.md"))) !== false) {
           $type = preg_split("/\s/", $buffer);
           array_push($buffers, json_encode(array("name"=>$type[1], "id"=>$type[0])));
        }
        if (!feof($handle)) {
            echo "靠！发生了怪异的事情，找颂赞吧";
        }
        fclose($handle);
    }
    return $buffers;
  }
?>
<!DOCTYPE html>
<html>
<head>
    <title>ACT-DPL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="js/jquery.js"></script>
    <link rel="stylesheet" href="codemirror-3.12/lib/codemirror.css" />
    <link href="css/dpl.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="Zeroclipboard-1.1.7/ZeroClipboard.min.js"></script>
    <script type="text/javascript">
        var copyCssHtml;
        var copyCssTms;
        $(document).ready(function (){
            copyCssTms = new ZeroClipboard( $("#J-copy-css-tms"), { moviePath: "zeroclipboard-1.1.7/ZeroClipboard.swf", useNoCache:true });
            copyCssTms.on('noFlash', function (client) {
                alert("亲，您的浏览器没有安装flash，拷贝功能是基于flash的，你懂得！");
            });
            copyCssTms.addEventListener('mousedown',function() {
                var tms = "<!--tms-->\n"+$('#tms textarea').val()+"\n\n";
                var css = "<!--css-->\n<style type=\"text/css\">\n"+$('#css textarea').val()+"\n</style>\n\n";
                copyCssTms.setText(tms+css);
            });
            copyCssTms.addEventListener('complete',function() {
                $("#J-copied-tip").css("visibility", "visible");
                setTimeout(function (){
                    $('#modal').modal('hide');
                }, 2500);
            });
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">
            <div class="aside">
                <ul class="nav nav-list">
                    <li class="nav-header">模版分类</li>
                    <?php
                      foreach(getCategories() as $k => $category){
                        $class = "";
                        if($k === 0){
                          $class = "active";
                        }
                        $category = json_decode($category);
                       echo "<li><a href=\"#{$category->id}\">{$category->name}</a></li>";
                      }
                    ?>
                </ul>
            </div>
            <a href="submit_tem.php" class="btn btn-primary submit-tem" target="_blank">提交一个模版</a>
        </div>
        <div class="span10">
            <div id="content">
                <?php
                  foreach(getCategories() as $k => $category){
                    $class = "";
                    if($k === 0){
                      $class = "active";
                    }
                    $category = json_decode($category);
                   echo "<div id=\"{$category->id}\" name=\"{$category->id}\">";
                   echo   "<ul class=\"breadcrumb\"><li>{$category->name}</li></ul>";
                   echo   "<ul class=\"thumbnails\">";

                   foreach($tems_config as $k => $config){
                       if($config->type == $category->id){
                           echo "<li class=\"span4\">
                                    <a href=\"#\" class=\"thumbnail\" title=\"{$config->name}\" data-type=\"{$config->type}\" data-id=\"{$config->en_name}\">
                                        <img data-src=\"holder.js/360x270\" alt=\"360x270\" src=\"{$config->thumb}\" />
                                    </a>
                                 </li>";
                       }
                   }

                   echo   "</ul>";
                   echo "</div>";
                  }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="modal hide fade" id="modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Modal header</h3>
    </div>
    <div class="modal-body">
        <p id="J-loading">
            正在加载模版资源...
        </p>
        <div id="dpl-main">
            <ul class="nav nav-tabs" id="J_modal_tab">
                <li><a href="#html" data-toggle="tab">HTML</a></li>
                <li><a href="#css" data-toggle="tab">CSS</a></li>
                <li><a href="#tms" data-toggle="tab">TMS</a></li>
                <li><a href="#js" data-toggle="tab">JS</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="html">
                    正在获取中...
                </div>
                <div class="tab-pane" id="css">CSS</div>
                <div class="tab-pane" id="tms">TMS</div>
                <div class="tab-pane" id="js">该模版没有JS功能</div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <span class="label label-success" id="J-copied-tip">拷贝成功</span>
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
        <a href="#" class="btn btn-primary" id="J-copy-css-tms">Copy CSS & TMS</a>
    </div>
</div>

<script src="js/bootstrap.min.js"></script>
<script src="js/dpl.js?v=1"></script>
</body>
</html>
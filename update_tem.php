<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

$dpl_dir = "dpl";

$en_name = @$_POST['en_name'];
$tms = @$_POST['tms'];
$html = @$_POST['html'];
$js = @$_POST['js'];
$css = @$_POST['css'];
$author = @$_POST['author'];
$type = @$_POST['type'];
$thumb = @$_POST['thumb'];
$date = @date("Y-m-d H:i:m");
$id = @$_POST['id'];
$log = @$_POST['log'];

if(!$id){
    echo json_encode(array("msg"=>"亲，缺少模版id"));
    exit;
}

if(!$log){
    $log = "系统修改于";
}

if(!$author){
  $author = "系统";
}

if(!$thumb){
  $thumb = "该模版没有tms代码";
}

$tem_dir = $dpl_dir."/".$id;

if(!is_writable($dpl_dir.'/README.md')){
      exit("文件目录：$dpl_dir 没有写入权限");
}else if(is_dir($tem_dir)){
    $config_path = $tem_dir."/self.config";
    $html_path = $tem_dir."/self.html";
    $js_path = $tem_dir."/self.script";
    $css_path = $tem_dir."/self.css";
    $tms_path = $tem_dir."/self.tms";
    $preview_path = $tem_dir."/preview.html";

    $result = array();

    copy($html_path, $html_path.".".$date.".bak");
    copy($js_path, $js_path.".".$date.".bak");
    copy($css_path, $css_path.".".$date.".bak");
    copy($tms_path, $tms_path.".".$date.".bak");
    copy($preview_path, $preview_path.".".$date.".bak");

    $remove_html = @unlink($html_path);
    $remove_js = @unlink($js_path);
    $remove_css = @unlink($css_path);
    $remove_preview = @unlink($preview_path);
    $remove_tms = @unlink($tms_path);

    //写入html
    if($remove_html){
          $tem_html = fopen($tem_dir."/self.html", 'w+');
          $writed = fwrite($tem_html, $html);
          if($writed>0){
            array_push($result, "html:yes");
          }else{
            array_push($result, "html:no");
          }
    }

    //写入js
    if($remove_js){
          $tem_js = fopen($tem_dir."/self.script", 'w+');
          $writed = fwrite($tem_js, $js);
          if($writed>0){
            array_push($result, "js:yes");
          }else{
            array_push($result, "js:no");
          }
    }

    //写入css
    if($remove_css){
          $tem_css = fopen($tem_dir."/self.css", 'w+');
          $writed = fwrite($tem_css, $css);
          if($writed>0){
            array_push($result, "css:yes");
          }else{
            array_push($result, "css:no");
          }
    }

    //写入tms
    if($remove_tms){
          $tem_tms = fopen($tem_dir."/self.tms", 'w+');
          $writed = fwrite($tem_tms, $tms);
          if($writed>0){
            array_push($result, "tms:yes");
          }else{
            array_push($result, "tms:no");
          }
    }

    //写入预览页面
    if($remove_preview){
          $head_handle = fopen($dpl_dir."/head.php", 'r');
          $footer_handle = fopen($dpl_dir."/footer.php", 'r');
          $head = fread($head_handle, filesize($dpl_dir."/head.php"));
          $footer = fread($footer_handle, filesize($dpl_dir."/footer.php"));
          $preview = fopen($tem_dir."/preview.html", 'w+');
          $writed = fwrite($preview, $head.$html."<style type=\"text/css\">".$css."</style>"."<script type=\"text/javascript\">".$js."</script>".$footer);
          if($writed>0){
            array_push($result, "preview:yes");
          }else{
            array_push($result, "preview:no");
          }
    }

    //写入changelog
    $changelog_handle = fopen($tem_dir."/changelog.md", 'a');
    $writed = fwrite($changelog_handle, "\n".$log."->".$date."->".$date.".bak");

    if($writed){
      array_push($result, "changelog:yes");
    }else{
      array_push($result, "changelog:no");
    }

    echo json_encode($result);
}
 ?>
<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

$dpl_dir = "dpl";

$id = @$_POST['id'];

if(!$id){
    echo "no";
    exit;
}

function remove_dir_with_files($dirPath){
  if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
  }
  $files = glob($dirPath . '*', GLOB_MARK);
  foreach ($files as $file) {
      if (is_dir($file)) {
          remove_dir_with_files($file);
      } else {
          unlink($file);
      }
  }
  return rmdir($dirPath);
}

$tem_dir = $dpl_dir."/".$id;

if(!is_writable($dpl_dir.'/README.md')){
      exit("文件目录：$dpl_dir 没有写入权限");
}else if(is_dir($tem_dir)){
    $remove = @remove_dir_with_files($tem_dir);

    if($remove){
        echo "yes";
    }else{
        echo "no";
    }
    exit;
}else{
    echo json_encode(array("msg"=>"亲，模版不存在"));
    exit;
}
 ?>
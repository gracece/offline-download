<!DOCTYPE HTML> <html lang="en"> <head>
    <meta charset="UTF-8">
    <link media="all" rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" type="text/css" />
    <script src="./bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <title>Remote Download</title>
    <style type="text/css">
      body{
        font-family:Microsoft YaHei;
      }

    </style> 
  </head> 
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a href="http://sysucs.org" class="brand">sysucs.org</a>
        </div>
      </div>
    </div>

    <br />
    <br />
    <br />

    <div class="container">
      <header>
        <h1 onclick="reloadPage()">离线下载？</h1>
      </header>

      <div  id="downloading" class="alert alert-info">
        <p>欢迎使用！采用wget后台下载的方法实现简单的离线下载功能，wget更多使用方法请查看  <a href="wget.pdf"><strong>wget使用说明</strong></a></p>
        <p><b>注意：</b>如果文件48小时内未被下载，则将被自动删除!</p>
      </div>

      <form  onSubmit="return downloading()" method="post">      
        <h3><code style="font-size:18px;">sysu@sysucs.org ~ $ wget -b -nc --restrict-file-names=nocontrol -P ./download &nbsp  </code>
        </h3>
        <input id="url" name="url" type="text" style="width:700px;" />
        <button type="submit" class="btn">Enter</button>
      </form>

      <?php 
      writelist("download");
      ?>
    </div>

    <script type="text/javascript">
      function reloadPage()
        {
          window.location.reload();
        }

      function downloading(){    
        var UU =document.getElementById("url");
        if(UU == " ")
        {
          alert("地址为空！");
          return false;
        }
        else   return true;
       }
</script>

  </body>
</html>
<?php

function secondToDate($second) {
if (!$second) {
return ; 
}
if ($second >= 24 * 3600) {
echo floor($second / (24 * 3600)).'<b>d</b> ';
$second %= 24 * 3600;
}
if ($second >= 3600) {
echo floor($second / 3600).'<b>h</b>';
$second %= 3600;
}
if ($second >= 60) {
echo floor($second / 60).'<b>m</b>';
$second %= 60;
}
if ($second > 0) {
echo floor($second).'<b>s</b>';
}
}

function writelist($Spath){
echo' <table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th style="text-align:center;">#</th>
      <th>名称</th>
      <th style="width:80px;">文件大小</th>
      <th style="width:90px;">下载时间</th>
      <th style="width:100px;">有效时间</th> 
    </tr>
  </thead>
  <tbody>
    ';
    date_default_timezone_set("Asia/Shanghai");
    if(is_dir($Spath)){
        $path = scandir($Spath);
        for($a=0,$index=0;$a<count($path);$a++){
            if($path[$a]=='.'||$path[$a]=='..') continue;
            $longPath="./".$Spath."/".$path[$a];
            $size = filesize($longPath)/1024/1024;
            $size = number_format($size,2);
            //两位小数
            $filedownloadtime = filectime($longPath);
            $effecttime =172800-( time()- $filedownloadtime) ;
            if ($effecttime <0)
                unlink($longPath);
            $downtime_fomat = date('m/d H:i',$filedownloadtime);
            //格式化显示
            $index++;
    echo '<tr>
      <td style="text-align:center;width:60px;">'.$index.'</td>
      <td><a target="_blank" title="点击下载" href='.$longPath.'>'.$path[$a]."</a></td>
      <td>".$size."MB</td>
      <td>".$downtime_fomat."</td>
      <td>";
        secondToDate($effecttime);
        echo "</td>
    </tr>
    ";

    }
    }
    }

    function safePost($str)
    {
    $val = !empty($_POST["$str"]) ? $_POST[$str]:null;
    return $val;

    }

    $url = safePost("url");
    $name = safePost("filename");
    $allow_type=array("wmv","iso","xls","xlsx","exe","cpp","pdf","gif","mp3","mp4","zip","rar","doc","docx","mov","ppt","pptx","txt","7z","jpeg","jpg","JPEG","png");
    //允许的文件类型
    $torrent = explode(".",$url);
    $file_end = end($torrent);
    $file_end = strtolower($file_end);
    if(in_array($file_end,$allow_type))
    {
    shell_exec("wget -b -nc --restrict-file-names=nocontrol -P ./download ".escapeshellarg($url)); 
    // -b 后台下载
    // -nc 文件已经存在时不覆盖
    // --restrict-file-names 解决中文地址导致文件名乱码的问题
    // -P 保存路径
    echo "<script type='text/javascript'>
      alert('正在后台下载，请耐心等待！sorry,暂时不能提供下载进度=。= 请通过文件大小来判断');
    reloadPage();
    </script>"; 
    }
    else if($file_end != null)
    {
    echo '<div class="alert alert-danger"> 类型: '. $file_end . '<br />';
        echo"<button class='close' data-dismiss='alert'>x</button>";
    echo "这个文件类型不允许！";
    echo "允许的文件有：</br>";
    foreach($allow_type as $xxx)
    echo $xxx . "、 ";
    echo "</br> 如有需要，请与管理员联系！</div>";
    }

    ?>

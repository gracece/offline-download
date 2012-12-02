<!DOCTYPE HTML> <html lang="en"> <head>
    <meta charset="UTF-8">
    <link media="all" rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" type="text/css" />
    <script src="./bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <title>Offline Download</title>
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
          <a href="http://gmy.asia" class="brand">gmy</a>
          <a href="http://gracece.net" class="brand">grace</a>
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
        <p><b>注意：</b>文件有效期为48小时，请及时下载，到期将会自动删除！</p>
      </div>

      <form  onSubmit="return downloading()" method="post">      
        <h3><code style="font-size:18px;">sysu@sysucs.org ~ $ wget -b -nc --restrict-file-names=nocontrol -P ./download &nbsp  </code>
        </h3>
        <input id="url" name="url" type="text" placeholder="请把文件的下载地址粘贴到这里,然后回车即可。" style="width:700px;" />
        <button type="submit" class="btn">Enter</button>
      </form>
      <div>
        <?php 
        writelist("download");
        ?>
      </div>
    </div>
        <script type="text/javascript">
        function info(){
            alert ("还没写好！");
      }

function reloadPage(){
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
var xtime = new Array();
var num = document.getElementById("xnumber").innerHTML;
document.getElementById("xnumber").innerHTML = "";
function timeRemain(){
    for(var i=1;i<=num;i++){
        var temp = xtime[i];
        var h = Math.floor( temp / 3600);
        temp %= 3600;
        var m = Math.floor( temp / 60);
        if(m<10) m = '0' + m.toString();
        else m = m.toString();
        temp %= 60;
        var s = temp;
        if(s<10) s = '0' + s.toString();
        else s = s.toString();
        document.getElementById("x"+i.toString()).innerHTML = (h.toString()+':'+m+':'+s);
  }     
  for(var i=1;i<=num;i++){
      xtime[i]--;
  }
  setTimeout("timeRemain()",1000);  
}

for(var j = 1;j<=num;j++){
    xtime[j] = document.getElementById("x"+j.toString()).innerHTML;
}
//alert("good");
timeRemain();
</script>

  </body>
</html>
<?php

        function secondToDate($second,$iid) {
            echo '<swan id="x'.$iid.'">'.$second.'</swan>'; 
        }

        function sortFileByDate($dir)
        {
            if(is_dir($dir))
            {
                $scanArray=scandir($dir);
                $finalArray = array();
                for($i=0; $i<count($scanArray);$i++)
                {
                    if($scanArray[$i]!="."&&$scanArray[$i]!="..")
                    {
                        $finalArray[$scanArray[$i]]=filectime($dir."/".$scanArray[$i]); 
                    }
                }
                arsort($finalArray);
                return($finalArray);
                //返回数组，key为文件名，value为文件时间
            }
            else 
                echo "sorry,".$dir."is not a dir";

        }

        function writelist($Spath){
            echo' <table class="table table-striped table-bordered">
                <thead>
                <tr>
                <th style="text-align:center;">#</th>
                <th>名称(按下载时间排序)</th>
                <th style="width:80px;">文件大小</th>
                <th style="width:90px;">下载时间</th>
                <th style="width:80px;">有效时间</th> 
                <th style="width:45px;">操作</th>
                </tr>
                </thead>
                <tbody> '; 

            date_default_timezone_set("Asia/Shanghai");
            $sortedPath = sortFileByDate($Spath);
            $index = 0;
            while ($element =each($sortedPath))
            {
                $longPath = "./".$Spath."/".$element['key'];
                $size =filesize($longPath)/1024/1024;
                $size = number_format($size,2);
                //两位小数
                $filedownloadtime = $element['value']; 
                $effecttime =172800-( time()- $filedownloadtime) ;
                //有效时间为两天 48*60*60 = 172800, 有效时间为0则删除 
                if ($effecttime <0)
                    unlink($longPath);
                $downtime_fomat = date('m/d H:i',$filedownloadtime);
                //格式化显示
                $index++;

                echo '<tr>
                    <td style="text-align:center;width:60px;">'.$index.'</td>
                    <td><a target="_blank" title="点击下载" href='.$longPath.'>'.$element['key']."</a></td>
                    <td>".$size."MB</td>
                    <td>".$downtime_fomat."</td>
                    <td>";
                secondToDate($effecttime,$index);
                echo "</td>
                    <td><input class='btn btn-danger' type='submit' value='delete' onclick='info()' style='padding:0 0 ;' /></td>
                    </tr>
                    ";

            }
            echo "<p id='xnumber'>".$index."</p>";
            // echo "<div>Copyright &copy;gmy.asia & gracece.net</div>";
        }

        function safePost($str)
        {
            $val = !empty($_POST["$str"]) ? $_POST[$str]:null;
            return $val;

        }

        $url = safePost("url");
        $name = safePost("filename");
        $allow_type=array("wmv","deb","iso","xls","xlsx","exe","cpp","pdf","gif","mp3","mp4","zip","rar","doc","docx","mov","ppt","pptx","txt","7z","jpeg","jpg","JPEG","png");
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

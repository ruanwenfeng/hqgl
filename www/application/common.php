<?php
// 应用公共文件


\think\Route::bind('index');

function create_password($pw_length = 4){
    $chars = '123456789';
    $password = '';
    for ( $i = 0; $i < $pw_length; $i++ )
    {
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}
function generate_username( $length = 6 ) {
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}
function isArray($arg){
    return gettype([]) === gettype($arg);
}
function isString($arg){
    return gettype("") === gettype($arg);
}
function isNumber($arg){
    return gettype(12) === gettype($arg);
}

function create_guid($namespace = '') {
    $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
//    $guid = '{' .
//        substr($hash, 0, 8) .
//        '-' .
//        substr($hash, 8, 4) .
//        '-' .
//        substr($hash, 12, 4) .
//        '-' .
//        substr($hash, 16, 4) .
//        '-' .
//        substr($hash, 20, 12) .
//        '}';
    return $guid = $hash;
}


function create_xls($data,$filename='simple.xls'){

    ini_set('max_execution_time', '0');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header ('Cache-Control: cache, must-revalidate');
    header ('Pragma: public');
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

function excelData($datas,$titlename,$title,$filename){
    $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
    $str .="<table border=1><head>".$titlename."</head>";
    $str .= $title;
    foreach ($datas  as $key=> $rt )
    {
        $str .= "<tr>";
        foreach ( $rt as $k => $v )
        {
            $str .= "<td>{$v}</td>";
        }
        $str .= "</tr>\n";
    }
    $str .= "</table></body></html>";
    header( "Content-Type: application/vnd.ms-excel; name='excel'" );
    header( "Content-type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=".$filename );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Expires: 0" );
    exit( $str );
}

function sendMail($smtpemailto,$code){

    $smtpserver = "smtp.163.com";//SMTP服务器
    $smtpserverport =25;//SMTP服务器端口

    $smtpusermail = "m18252779796_2@163.com";//SMTP服务器的用户邮箱

    $smtpuser = "m18252779796_2";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
    $smtppass = "wozai123";//SMTP服务器的用户密码
    $mailtitle = '测试';//邮件主题

    //$code = generate_username(5);
    //session('pwd_code',['verify_code'=>$code,'verify_time'=>time() + 60*10]);
    $mailcontent = "验证码：$code";//邮件内容
    $mailtype = "html";
    $smtp = new \Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    return $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
}


function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }
    $o = "";
    foreach ( $post_data as $k => $v )
    {
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);
    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: "));
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
}


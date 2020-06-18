
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>monitor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>

<?php

/*

create file sites.json
[
  {
    "site": "http://my.test.dvinaland.ru/",
    "email": "dathim@gmail.com"
  },
  {
    "site": "https://dvinaland.ru/",
    "email": "dathim@gmail.com"
  },
  {
    "site": "https://dvinaldand.ru/",
    "email": "dathim@gmail.com"
  }
]
*/


$json = file_get_contents("sites.json");

function get_http_response_code($domain1) {
    $header = @get_headers($domain1);
    if (is_array($header)){
        $rep_code = explode(" ",$header[0]); 
        return $rep_code[1];
    } else {
        return 504;
    }
}

function sendMail($to, $subject, $body, $from_email,$from_name){
    $headers  = "MIME-Version: 1.0 \n" ;
    $headers .= "From: " .
    "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
    "<".$from_email."> \n";
    $headers .= "Reply-To: " .
    "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
    "<".$from_email."> \n";
    $headers .= "Content-Type: text/plain;charset=ISO-2022-JP \n";
    $body = mb_convert_encoding($body, "ISO-2022-JP","AUTO");
    $sendmail_params  = "-f$from_email";
    mb_language("ja");
    $subject = mb_convert_encoding($subject, "ISO-2022-JP","AUTO");
    $subject = mb_encode_mimeheader($subject);
    $result = mail($to, $subject, $body, $headers, $sendmail_params);
    return $result;
}

$site_array =json_decode($json, TRUE);

echo "<table class='table table-striped table-sm'>";
foreach($site_array as $a){

	echo "<tr><td>";
	echo $a['site'];
	echo "</td><td>";
    $resp =get_http_response_code($a['site']) ;
    if ($resp == 200){
        echo $resp;
    }  else {
        echo "<b>{$resp}</b>";
        $body = $a['site'] ." resp:" . $resp;
        if($a['email'] != ''){
            echo " mail:";
            $result = sendMail($a['email'],$a['site'] , $body, "noreplay@sass.test" ,"dathim-monitor");
            print_r( $result );
        }
       
    }
    $resp='';
    echo "</td></tr>";
}
echo "</table>";
?>
</body>
</html>
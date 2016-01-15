<?php
/*
VICIDIAL RECORDINGS DOWNLOADER
AUTHOR: ARIES LALUCES

### PLACE THIS ON CLIENT SERVERS ###  
 
20151202 - First
20160116 - Improvements and code cleanup

  
*/                          

$client    = ereg_replace("[^0-9]","",$_GET['client_ip']); 
$file_name = ereg_replace("[^-a-zA-Z0-9\_]","",$_GET['file_name']) . '.gsm'; 
                                               
$cwd = getcwd() . "/$client"; 

if (!file_exists("$cwd")) {
     system("mkdir -m 0777 $cwd");    
}
exec("rm -f $cwd/*.mp3");

$arr_server_details = array(
    "Infin8|http://192.168.1.9/recorder/infin8",
    "PraBel|http://192.168.1.9/recorder/prabel",
    "MAV|http://192.168.1.9/recorder/mav",
    "iConcept|http://192.168.1.9/recorder/iconcept",
    "LanBPO|http://192.168.1.9/recorder/lanbpo",
    "RSC|http://192.168.1.9/recorder/rsc",
    "e-Solutions|http://192.168.1.9/recorder/esolutions",
    "JSG|http://192.168.1.9/recorder/jsg"    
);

foreach ($arr_server_details as $server_detail) {
    $arr_server_details = explode("|",$server_detail);

    $server_name = $arr_server_details[0];
    $server_path = $arr_server_details[1]; 

    $url = "$server_path/$file_name";    
    if (url_exist($url)){
        $baseName = basename($file_name);              
        $mp3Name  = str_replace('.gsm', '.mp3', $baseName);	
        $mp3Path  = "$cwd/$mp3Name";
        exec("/usr/bin/sox -t gsm $url $mp3Path");        
        stream_file($mp3Path);
    } else {
        continue;
    }
}


//=====================FUNCTIONS==============================

function stream_file($filePath) {
    // check that file exists and is readable
    $err = 'File not found';
    if (file_exists($filePath) && is_readable($filePath)) {
        // get the file size and send the http headers
        $fileSize = filesize($filePath);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename=' . basename($filePath));
        header('Content-Transfer-Encoding: binary');

        $file = @ fopen($filePath, 'rb');
        if ($file) {
                // stream the file and exit the script when complete
                fpassthru($file);
                exit;
        } else {
                echo $err;
        }
    } else {
        echo $err;
    }
}    

// function borrowed from net
function url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
} 
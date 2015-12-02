<?php
/*
VICIDIAL RECORDINGS DOWNLOADER
AUTHOR: ARIES LALUCES

### PLACE THIS ON CLIENT SERVERS ###  
 
20151202 - First

  
*/                                                  


$client    = ereg_replace("[^0-9]","",$_GET['client_ip']); 
$file_name = ereg_replace("[^-a-zA-Z0-9\_]","",$_GET['file_name']) . '.gsm'; 

$http_path      = dirname($_SERVER['PHP_SELF']);
$server_address = $_SERVER['SERVER_ADDR']; 
                                                
$cwd       = getcwd(); 
$rec_paths = array(
'/recorder/esolutions'
); 

foreach ($rec_paths as $rec_path) {  

	if (!file_exists("$cwd/$client")) {
		system("mkdir -m 0777 $cwd/$client");    
	} 

	// CLEANUP FIRST
	exec("rm -f $cwd/$client/*.mp3");	
    
echo "Searching for $rec_path/$file_name...\n";  
    if(file_exists("$rec_path/$file_name")){
        $baseName = basename($file_name);
        $dirName  = dirname($file_name);        
        $mp3Name  = str_replace('.gsm', '.mp3', $baseName);	
        $mp3Path  = "$cwd/$client/$mp3Name";
        exec("/usr/bin/sox -t gsm $rec_path/$file_name $mp3Path");	
    }      
}

$err = 'File not found';


// check that file exists and is readable
if (file_exists($mp3Path) && is_readable($mp3Path)) {
	// get the file size and send the http headers
	$size = filesize($mp3Path);
	header('Content-Type: application/octet-stream');
	header('Content-Length: '.$size);
	header('Content-Disposition: attachment; 
	filename='.basename($mp3Path));
	header('Content-Transfer-Encoding: binary');

	$file = @ fopen($mp3Path, 'rb');
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


 

?>

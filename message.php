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

			// $message = $message.$title_str."\\n";
			$message = $message."게시일: ".$date[0][0]."\\n";
			$message = $message.$whole_url."\\n";
			$message = $message."\\n";
		}
		

		// if($now_date=="2018-01-05"){
		// 	print_r($exp_tr[$i]);
		// 	echo '<br>';
		// }
		
	}

	// 2번부터 맨 위에 공지 글부터 나오는것 같은데?


	if($message==""){
		$message="최근 14일간 올라온 공지사항이 없습니다.";
	}

	return $message;
}



$data = json_decode(file_get_contents('php://input'));
// print_r($data);
// print($data["content"]);

// print($data->content);
// echo "\n";
// print($data->content == "button1");
// echo "\n";

if($data->content == "공지사항 확인"){
	$message = rtn_message();

	echo <<< EOD
{
	"message":{
		"text": "$message"
	},
	"keyboard":{
		"type": "buttons",
		"buttons" : ["알림 시작하기", "도움말", "직접 소통하기"]
	}
}
EOD;

}
if($data->content == "도움말"){
	echo <<< EOD
{
	"message":{
		"text": "소프트웨어학부 제2대 학생회 [프리]입니다. \\n프리의 플러스친구는 소프트웨어학부의 최근 14일간 공지사항을 버튼 하나로 자동으로 알려주는 기능을 하고 있습니다. \\n또한, 학생회에 질문하거나 전달하고 싶으신 말씀이 있으면 여기에 남겨주세요.",
		"photo":{
			"url": "http://52.79.76.13/kakao_auto/pree1.jpg",
			"width": 600,
			"height": 600
		},
		"message_button":{
			"label": "소프트 학생회 페이스북",
			"url": "http://www.facebook.com/softwarekw/"
		}
	},
	"keyboard":{
		"type": "buttons",
		"buttons" : ["알림 시작하기", "도움말", "직접 소통하기"]
	}
}
EOD;
}

if($data->content == "직접 소통하기"){
	echo <<< EOD
{
	"message":{
		"text": "소프트웨어학부 제2대 학생회 [프리]입니다. \\n전달하고자 하시는 말씀이 있으시면 여기에 메세지를 남겨주세요. 최대한 빠르게 확인하여 답변 드리도록 하겠습니다."
	},
	"keyboard":{
		"type": "text"
	}
}
EOD;
}
?>
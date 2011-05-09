<?php

	include ('../functions/functionList.php');
	
	//if(!isset($_POST['month'])){
	//	echo 'path est vide';
	//  exit;
	//}
	//if(!isset($_POST['day'])){
	//	echo 'path est vide';
	//  exit;
	//}
	//if(!isset($_POST['year'])){
	//	echo 'path est vide';
	//  exit;
	//}
	

	function DateSearch($day, $month, $year) {
		
		if( !is_int($day) OR !is_int($month) OR !is_int(year)){
			echo 'kicked';
			exit ;
		}
		
		if ($month > 0 AND $month < 12)
			if ($month%2 == 0)
				if ($day < 1 OR $day > 31){
					echo 'date not correct';
					exit;
				}
			elseif($month == 2)
				if (year%4 == 0)
					if ($day > 29 OR $day < 0){
						echo 'date not correct';
						exit;
					}
				else
					if ($day > 28 OR $day < 0){
						echo 'date not correct';
						exit;
					}
			else
				if ($day > 30 OR $day < 0){
					echo 'date not correct';
					exit;
				}
				
						
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE dc:created >= DATE '". $year . '-' . $month . '-' . $day ."'")->SendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->Output();
		}
	}
?>


	<?php 
		//James Page 
		//This function is for loading JSON into index from the trail.json file


		header('Content-Type: application/json');
		$aResult = array();

		if( !isset($_POST['functionname'])){
			$aResult['error'] = 'No Function Given!';
		}

		if( !isset($aResult['error'])){
			switch($_POST['functionname']){
				case 'load':
					$stateFile = fopen("../simulations/trial.json", "r") or die("Unable to open file");
					$jsonText = fread($stateFile, filesize("../simulations/trial.json"));
					$jsonText = json_encode(json_decode($jsonText));
					$times = json_encode($jsonText);
					fclose($stateFile);
					$aResult['result'] = $times;
			}
		}

		echo json_encode($aResult);
	?>


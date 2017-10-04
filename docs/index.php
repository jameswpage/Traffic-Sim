<!-- James Page		9/19/2017 -->
<!-- This site will be for visualizing traffic waves -->


<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<title>Traffic Simulation</title>
	</head>
	<body>
		<!--LOAD JSON FOR PATH OF FIRST CAR -->
		<?php
			$stateFile = fopen("./../simulations/trial.json", "r") or die("Unable to open file");
			$jsonText = fread($stateFile, filesize("./../simulations/trial.json"));
			$jsonText = json_encode(json_decode($jsonText));
			$times = json_encode($jsonText);
			fclose($stateFile);
		?>
		<script type="text/javascript">
			var times_json = <?php echo $times;?>;
			var times_table = JSON.parse(times_json);
		</script>

		<link rel = "stylesheet" type = "text/css" href = "./../static/traffic/style.css"/>


		<!--SITE CONTENT BEGINS HERE-->

		<h1><center>Traffic Simulator</center></h1>

		<!-- FORM FOR SUBMITTING NOW PATH OF FRONT CAR -->
		<div id = 'form'>
			<h3>Head Car Action</h3>
			<form id = "mpath" name = "mpath" action = './../cgitest.cgi' method="POST">

				Total Time (in ms): <input type = "text" name = "ttime" value = "30000">
				Number of Cars (1-10): <input type = "text" name = "carnum" value = "10">
				Initial Speed (fps): <input type = "text" name = "initspeed" value = "60"><br>
				<br>

				<!--extraRowTemplate will be repeated for every change in the accleration of the
					head car -->
				<div id = 'container'>
				<div class = "extraRowTemplate" name = "extraRowTemplate">
					Change:
					<select name="change">
					<option value="acc">Acceleration</option>
					<option value="dec">Deceleration</option>
					</select>

					Start time: <input type = "text" name="starttime">
					End time: <input type = "text" name="endtime">
					Amount (in fps): <input type = "text" name="amount"> 
				</div>

				</div>

				<div style = "height: 10px; width: 100%"></div>
				<div style = "margin: 0 auto; width: 20%; ">
					<a href = "#" id = "addRow" class = "plus-icon">+ Add Row +</a><br/>
				</div>

				<input type="submit" value="Load Head Car">
			</form>
		</div>



		<!--FORM VALIDATION-->
		<script type="text/javascript">

			function validateForm(form){
				
				var ttime = document.querySelector('input[name=ttime]');
				var carnum = document.querySelector('input[name=carnum]');
				var initspeed = document.querySelector('input[name=initspeed]');
				var change = document.querySelectorAll('select[name=change]');
				var starttime = document.querySelectorAll('input[name=starttime]');
				var endtime = document.querySelectorAll('input[name=endtime]');
				var amount = document.querySelectorAll('input[name=amount]');

				if(ttime.value == '' || ttime.value > 60000){
					alert("Total Time must be under 1 minute");
					return false;
				}
				if(carnum.value == '' || carnum.value > 10 || carnum.value < 1){
					alert("Must have between 1-10 cars");
					return false;
				} 
				if(parseInt(carnum.value) != carnum.value){
					alert("Cannot have fractions of cars");
					return false;
				}
				if(initspeed.value == '' || initspeed.value > 120 || initspeed.value < 0){
					alerts("Cars travel between 0 and 120 fps");
					return false;
				}

				var current_min = 0;
				for(var i = 0; i < change.length; i++){
					if(starttime[i].value == '' || endtime[i].value == '' || amount[i].value == ''){
						alert("Time and Amount fields must be filled out");
						return false;
					}
					if(parseInt(starttime[i].value) < current_min){
						console.log(starttime[i].value);
						console.log(current_min);
						alert("Overlapping or negative intervals");
						return false;
					}
					if(parseInt(endtime[i].value) < starttime[i].value){
						alert("End-time cannot be less than Start-time");
						return false;
					}
					current_min = endtime[i].value;
				}
				alert("here");
				return true;
			}
		</script>

		<!--javascript for adding row to simulation form as well as submitting form data to 
			cgi file to generate json-->

		<script type="text/javascript">
			$(document).ready(function(){
				//generate an extra row to change path of head car
				$('#addRow').click(function() {
					$('<div/>', {
						'class': 'extraRow', html: newRow()
					}).hide().appendTo('#container').slideDown('slow');
				});

				$("#form").on('click', '#close', function(){
					$(this).closest('.extraRow').remove()
				});

				//
  				var $form = $('form');
  				var $button = document.querySelector('input[type=submit]');
   				$form.submit(function(){
   					if(!validateForm(this)){
   						//returning false means because form is not valid
   						return false;
   					}
   					var url = $(this).attr('action');
   					var formData = $(this).serialize();
   			
      				$.post(url, formData, function(response){
            			alert('responding');
            			//agax call re loads times_table variable with newly created trail.json file
            			$.ajax({
		   					type: 'POST',
		   					url: './load_json.php',
		   					dataType: 'json',
		   					data: {'functionname': 'load'},
		   					success: function(obj, textstatus){
		   						alert('success');
		   						if(!('error' in obj)){
		   							times_json = obj.result;
									times_table = JSON.parse(JSON.parse(times_json));
									setInitPostion();
		   						}
		   						else{
		   							//if there is a problem, it is sent to the console
		   							console.log(obj.error);
		   						}
		   					},
		   					error: function(jqXHR, textStatus, errorThrown){
		   						console.log(jqXHR);
		   						console.log(textStatus);
		   						console.log(errorThrown);
		   					}
	   					});
      				});
      				//return false so that url is not change when submit is clicked
   					return false;
   				}); 
			});
			//function for adding a new row, may be changed so that data is easier to retrieve from form and also so that duplicated items can be distinguished
			function newRow(){
				var len = $('.extraRow').length;
				var $html = $('.extraRowTemplate').clone();
				$('<button id = "close" class = "minus-icon">X</button><br>').appendTo($html);
				return $html.html();
			}

		</script>


		<!-- <p>
			<button onclick="myMove()">Run Simulation</button>
		</p>  -->

		<!--style of animation found in CSS-->
		<div>
			<div id = "map">
				<p id = "menu">
					<button onclick="myMove()">Run Simulation</button>
					<button class = "pause">Pause</button>
					<button class = "reset">Reset</button>
				</p> 
				<div id ="road">
				</div>
			</div>
		</div>


		<!--CODE FROM HERE IS FOR GENERATING THE LOCATION OF THE CARS ON THE ROAD-->

		<!--this code is for generating the initial placement of the cars-->
		<script>
		
			//variables for displaying the the car and road style
			var length = 1178;
			var norm = 1.6;

			//initialize inner html
			// var text = "";

			//initialize variables for setting initial postions of car divs
			var car_arr;
			var car_num;
			var lastcar;
			var init_pos;

			setInitPostion();

			//position road	
			document.getElementById("road").style.height = (norm * 13 * 3) + "px";
			document.getElementById("road").style.top = (100 - (norm*13*1.3)) + "px";

			//this function sets the position of the cars in the simulation
			function setInitPostion(){
				//initialize inner html
				var text = "";

				//get number of cars from number of keys in times_table json dictionary
				car_arr = Object.keys(times_table[0]);
				car_num = car_arr.length - 2;
				lastcar = 'Car' + car_num;
				console.log(car_num);
				
				//initialize the initial position of the head car (so that the last car is at the left most part of the 
				//road)
				init_pos = times_table[0][lastcar];

				//add html for first car because it must exist (and none of the others need to)
				text += "<div id =\"Car1\" style = \"left:" + norm*(init_pos) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px; background-color: blue\"></div>";

				//for loop up to the number of cars to add a div for each car
				for(var i = 2; i <= car_num; i++){
					var curcar = 'Car' + i;
					text += "<div id =\"" + curcar + "\" style = \"left:" + norm*(init_pos - times_table[0][curcar]) +"px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px;" +
						"background-color: blue\"></div>";
				}
			
				//add divider to the road and set the html 
				text += "<p style=\"margin-top: " + (norm*1.5*13 - 2.5) + "px; border-top: 5px dashed #FFFFFF;\">"
				document.getElementById("road").innerHTML = text;

			}
		</script>


		<!--move cars using setInterval when Run Simulation is Clicked -->
		<script>
			var id;
			function myMove() {
				setInitPostion();
				clearInterval(id);
			  	var car1 = document.getElementById("Car1"); 
	
			  	//This value must match the value in style.css
			  	var i = 0;
			  	var time_interval = 10;
			  	var total_time = times_table.length;
			  	var isPaused = false;
			  	var isCrash = false;
			  	id = setInterval(move_cars, time_interval);
			  	function move_cars() {
			    	if (!times_table[i]) {
			      		clearInterval(id);
			    	} else {
			      		car1.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos)))%(1200-norm*13) + 'px'; 
			      		var prevcar = car1;
			      		for(var j = 2; j <= car_num; j++){
			      			var curcarname = 'Car' + j;
			      			var curcar = document.getElementById(curcarname);
			      			curcar.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i][curcarname]))%(1200-norm*13)) + 'px';
			      			if(collision($(curcar), $(prevcar))){
			      				i += total_time;
			      				break;
			      			}
			      			prevcar = curcar;
			      		}
			      		if (!isPaused){
			      			i += time_interval;
			      		}
			    	}
			  	}

			  	//collision test function taken from stack overflow user BC.
			  	function collision($div1, $div2){
			  		var x1 = $div1.offset().left;
				    var w1 = $div1.outerWidth(true);
				    var r1 = x1 + w1;
				    var x2 = $div2.offset().left;
				    var w2 = $div2.outerWidth(true);
				    var r2 = x2 + w2;
				        
				    if (r1 < x2 || x1 > r2) return false;
				    $div1.css("background-color",  "red");
				    $div2.css("background-color",  "red");
				    return true;
			  	}

			  	//design for pause button, this toggles the isPaused variable, which stops incrementing
			  	//the while loop index in setinterval
			  	$('.pause').on('click', function(e){
			  		e.preventDefault();
			  		isPaused = !isPaused;
			  		if(isPaused){
			  			$(this).html('Play');
			  			$('.reset').show();
			  		}
			  		else{
			  			$(this).html('Pause');
			  			$('.reset').hide();
			  		}

			  	});

			  	//reset button appears when paused is pressed
			  	$('.reset').on('click', function(e){
			  		clearInterval(id);
			  		$('.pause').html('Pause');
			  		setInitPostion();
			  		$(this).hide();
			  	}).hide();
			}
		</script>
	</body>
</html>
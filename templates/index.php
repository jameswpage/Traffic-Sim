<!-- James Page		9/19/2017 -->
<!-- This site will be for visualizing traffic waves -->


<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<title>Traffic Simulation</title>
	</head>
	<body>
		<!--LOAD JSON FOR PATH OF FIRST CAR -->
		<?php
			$stateFile = fopen("../simulations/trial.json", "r") or die("Unable to open file");
			$jsonText = fread($stateFile, filesize("../simulations/trial.json"));
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
			<form id = "mpath" name = "mpath" action = './../cgitest.cgi' method="POST" onsubmit = "return validateForm(this)">

				Total Time (in ms): <input type = "text" name = "ttime">
				Number of Cars (1-10): <input type = "text" name = "carnum">
				Initial Speed (fps): <input type = "text" name = "initspeed"><br>
				<br>

				<!--extraRowTemplate will be repeated for every change in the accleration of the
					head car -->
				<p class = "extraRowTemplate" name = "extraRowTemplate">
					Change:
					<select name="change">
					<option value="acc">Acceleration</option>
					<option value="dec">Deceleration</option>
					</select>

					Start time: <input type = "text" name="starttime">
					End time: <input type = "text" name="endtime">
					Amount (in fps): <input type = "text" name="amount">
					<br>
				</p>

				<div id = 'container'></div>

				<a href="#" id = "addRow"><i class="icon-plus-sign"></i>Add Change</a><br>

				<input type="submit" value="Load Head Car">
			</form>
		</div>

		<!--FORM VALIDATION-->
		<!--I am currently working on two ways to validate the form, neither are working correctly right now-->

		<script type="text/javascript">
			var button = document.querySelector('input[type=submit]')

			button.addEventListener('click', function onClick(event) {
			  var ttime = document.querySelector('input[name=ttime]')
			  var carnum = document.querySelector('input[name=carnum]')
			  var initspeed = document.querySelector('input[name=initspeed]')
			  var change = document.querySelector('select[name=change]')
			  var starttime = document.querySelector('input[name=starttime]')
			  var endtime = document.querySelector('input[name=endtime]')
			  var amount = document.querySelector('input[name=amount]')

			  console.info('ttime', ttime.value)
			  console.info('carnum', carnum.value)
			  console.info('initspeed', initspeed.value)
			  console.info('change', change.value)
			  console.info('starttime', starttime.value)
			  console.info('endtime', endtime.value)
			  console.info('amount', amount.value)
			  
			  event.preventDefault()
			})
		</script>

		<script type="text/javascript">
			function validateForm(form){
				
				var ttime = document.querySelector('input[name=ttime]');
				var carnum = document.querySelector('input[name=carnum]');
				var initspeed = document.querySelector('input[name=initspeed]');
				var change = document.querySelector('select[name=change]');
				var starttime = document.querySelector('input[name=starttime]');
				var endtime = document.querySelector('input[name=endtime]');
				var amount = document.querySelector('input[name=amount]');

				console.info('ttime', ttime.value);
				console.info('carnum', carnum.value);
				console.info('initspeed', initspeed.value);
				console.info('change', change.value);
				console.info('starttime', starttime.value);
				console.info('endtime', endtime.value);
				console.info('amount', amount.value);

				var tt = document.forms[0].ttime.value;
				var cn = document.forms[0].carnum.value;
				var is = document.forms[0].initspeed.value;
				var cha = document.forms[0].change.value;
				var sta = document.forms[0].starttime.value;
				var eta = document.forms[0].endtime.value;
				var ama = document.forms[0].amount.value;

				console.log(tt);
				console.log(cn);
				console.log(is);
				console.log(cha);
				console.log(sta);
				console.log(eta);
				console.log(ama);
				
				
				return false;
			}
		</script>

		<!--javascript for adding row to simulation form as well as submitting form data to 
			cgi file to generate json-->

		<script type="text/javascript">
			$(document).ready(function(){
				//generate an extra row to change path of head car
				$('<div>', 
					{'class' : 'extraRow', html: newRow()
				}).appendTo('#container');
				$('#addRow').click(function() {
					$('<div/>', {
						'class': 'extraRow', html: newRow()
					}).hide().appendTo('#container').slideDown('slow');
				});

				//
  				var $form = $('form');
  				var $button = document.querySelector('input[type=submit]');
  				//$form.submit(function(){}
   				$button.addEventListener('click', function(){
   					//one way to validate form
   					if(!validateForm($form)){
   						console.log("here");
   						//returning false means because form is not valid
   						return false;
   					}
   					var url = $($form).attr('action');
   					var formData = $($form).serialize();
   			
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
				return $html.html();
			}

		</script>


		<p>
			<button onclick="myMove()">Run Simulation</button>
		</p> 

		<!--style of animation found in CSS-->
		<div id = "map">
			<div id ="road">
			</div>
		</div>


		<!--CODE FROM HERE IS FOR GENERATING THE LOCATION OF THE CARS ON THE ROAD-->

		<!--this code is for generating the initial placement of the cars-->
		<script>
			var length = 1178;
			//var norm = 1.0 * length / (times_table[(times_table.length - 1)].head_pos);
			var norm = 1.6
			var text = "";
			var init_pos = (times_table[0].Car10);
			text += "<div id =\"car1\" style = \"left:" + norm*(init_pos + 13) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car2\" style = \"left:" + norm*(init_pos - times_table[0].Car2) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car3\" style = \"left:" + norm*(init_pos - times_table[0].Car3) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car4\" style = \"left:" + norm*(init_pos - times_table[0].Car4) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car5\" style = \"left:" + norm*(init_pos - times_table[0].Car5) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car6\" style = \"left:" + norm*(init_pos - times_table[0].Car6) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car7\" style = \"left:" + norm*(init_pos - times_table[0].Car7) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car8\" style = \"left:" + norm*(init_pos - times_table[0].Car8) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car9\" style = \"left:" + norm*(init_pos - times_table[0].Car9) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<div id =\"car10\" style = \"left:" + norm*(init_pos - times_table[0].Car10) + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; top: " + (5*norm) + "px\"></div>";
			text += "<p style=\"height: " + (norm*13*1.5 - 2.5) + "px; border-bottom: 5px dashed #FFFFFF;\">"
			document.getElementById("road").innerHTML = text;
			document.getElementById("road").style.height = (norm * 13 * 3) + "px";
			document.getElementById("road").style.top = (100 - (norm*13*1.3)) + "px";
		</script>


		<!--move cars using setInterval when Run Simulation is Clicked -->
		<script>
			var id;
			function myMove() {
				clearInterval(id);
			  	var car1 = document.getElementById("car1"); 
			  	var car2 = document.getElementById("car2"); 
			  	var car3 = document.getElementById("car3"); 
			  	var car4 = document.getElementById("car4"); 
			  	var car5 = document.getElementById("car5"); 
			  	var car6 = document.getElementById("car6"); 
			  	var car7 = document.getElementById("car7"); 
			  	var car8 = document.getElementById("car8"); 
			  	var car9 = document.getElementById("car9"); 
			  	var car10 = document.getElementById("car10"); 
			  	//This value must match the value in style.css
			  	var i = 0;
			  	var time_interval = 10;
			  	id = setInterval(move_cars, time_interval);
			  	function move_cars() {
			    	if (!times_table[i]) {
			      		clearInterval(id);
			    	} else {
			      		car1.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos)))%(1200-norm*13) + 'px'; 
			      		car2.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car2))%(1200-norm*13)) + 'px';
			      		car3.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car3))%(1200-norm*13)) + 'px';
			      		car4.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car4))%(1200-norm*13)) + 'px';
			      		car5.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car5))%(1200-norm*13)) + 'px';
			      		car6.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car6))%(1200-norm*13)) + 'px';
			      		car7.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car7))%(1200-norm*13)) + 'px';
			      		car8.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car8))%(1200-norm*13)) + 'px';
			      		car9.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car9))%(1200-norm*13)) + 'px';
			      		car10.style.left = (norm*(parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i].Car10))%(1200-norm*13)) + 'px';
			      		i += time_interval;
			    	}
			  	}
			}
		</script>
	</body>
</html>
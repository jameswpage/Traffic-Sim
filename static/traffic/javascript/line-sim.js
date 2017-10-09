//this javascript file is for displaying the simulation as a line

		
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

			setLinPosition();

			//position road	
			document.getElementById("road").style.height = (norm * 13 * 3) + "px";
			document.getElementById("road").style.top = (100 - (norm*13*1.3)) + "px";

			//this function sets the position of the cars in the simulation
			function setLinPosition(){

				//set up map
				$("#map").css({
					"margin": "auto",
 					"position": 'relative',
  					'background': 'rgb(86,158,44)',
  					'width': '1200px',
  					'height': '200px',
  					'border-radius': '10px'});

				$('#road').css({
 					'width': '100%',
  					'position': 'absolute',
  					'background': 'rgb(71,71,71)',
  					'height': '60px',
  					'border-bottom-right-radius': '0px',
  					'border-bottom-left-radius': '0px'});

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
		
			var id;
			function myLinMove() {
				setLinPosition();
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
			  		setLinPosition();
			  		$(this).hide();
			  	}).hide();
			}
	
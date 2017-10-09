//This page is for siplaying the simulation as a circle


			var radius = 460.0;
			var margin = 40.0;
			function toCircleX(length){
				return (2*radius + margin - radius * (1 - Math.cos((length/radius))));
			}

			function toCircleY(length){
				return (margin + radius  - radius * Math.sin((length/radius)));
			}
		
		
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

			setCircPosition();

			//this function sets the position of the cars in the simulation
			function setCircPosition(){

				$("#map").css({
					"margin": "auto",
 					"position": 'relative',
  					'background': 'rgb(86,158,44)',
  					'width': '1000px',
  					'height': '1000px',
  					'border-radius': '10px'});

				$('#road').css({
 					'position': 'relative',
  					'width': '100%',
  					'height': '1000px',
  					'background': 'rgb(86,158,44)',
  					'margin-top': '0px',
  					'border-bottom-right-radius': '10px',
  					'border-bottom-left-radius': '10px'});
				//initialize inner html
				var text = "";
				//text += "<div class = \"outer-circ\"><div class = \"inner-circ\"></div></div>";
				text += "<div class = \"outer-circ\"><div class = \"line\"><div class = \"inner-circ\"></div></div></div>";

				//get number of cars from number of keys in times_table json dictionary
				car_arr = Object.keys(times_table[0]);
				car_num = car_arr.length - 2;
				lastcar = 'Car' + car_num;
				console.log(car_num);
				
				//initialize the initial position of the head car (so that the last car is at the left most part of the 
				//road)
				init_pos = times_table[0][lastcar];

				//add html for first car because it must exist (and none of the others need to)
				text += "<div id =\"Car1\" style = \"position: absolute; left:" + (toCircleX(init_pos + 
					parseFloat(norm*13/2.0)) - parseFloat(norm*13/2.0)) + "px; top: " + (toCircleY(init_pos + 
					parseFloat(norm*13/2.0)) -parseFloat(norm*6/2.0))+ "px; width: " + (13*norm) + "px; height: " + 
					(6*norm) + "px; transform: rotate(" +(90 - ((init_pos + parseFloat(norm*13/2.0))/(2*Math.PI * 460))*360) 
					+"deg); background-color: blue\"></div>";

				//for loop up to the number of cars to add a div for each car
				for(var i = 2; i <= car_num; i++){
					var curcar = 'Car' + i;
					var length = parseFloat(init_pos) - parseFloat(times_table[0][curcar]) + parseFloat(norm*13/2.0);
					var left = toCircleX(length) - parseFloat(norm*13/2.0);
					var top = toCircleY(length) - parseFloat(norm*6/2.0);
					text += "<div id =\"" + curcar + "\" style = \"position: absolute; left:" + left +"px; top : " + top + "px; width: " + (13*norm) + "px; height: " + (6*norm) + "px; transform: rotate(" +(90 - (length/(2*Math.PI * 460))*360) +"deg); background-color: blue\"></div>";
				}

				document.getElementById("road").innerHTML = text;

			}

		
			var id;
			function myCircMove() {
				setCircPosition();
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
			    		var c1_length = parseFloat(times_table[i].head_pos) + parseFloat(init_pos) + parseFloat($(car1).css("width"))/2.0;
			      		car1.style.left = ((toCircleX(parseFloat(c1_length))) - (parseFloat($(car1).css("width"))/2.0)) + 'px';
			      		car1.style.top = ((toCircleY(parseFloat(c1_length))) - (parseFloat($(car1).css("height"))/2.0)) + 'px'; 
			      		car1.style.transform = "rotate(" + (90 - (c1_length/(2*Math.PI * 460))*360) + "deg)";
			      		var prevcar = car1;
			      		for(var j = 2; j <= car_num; j++){
			      			var curcarname = 'Car' + j;
			      			var length = parseFloat(times_table[i].head_pos) + parseFloat(init_pos) - parseFloat(times_table[i][curcarname]);
			      			var curcar = document.getElementById(curcarname);
			      			var car_wid = ($(curcar).css("width"));
			      			var car_height = ($(curcar).css("height"));
			      			length = length + (parseFloat(car_wid)/2.0);

			      			curcar.style.left = (toCircleX(length) - (parseFloat(car_wid)/2.0)) + 'px';
			      			curcar.style.top = (toCircleY(length) - (parseFloat(car_height)/2.0)) + 'px';
			      			curcar.style.transform = "rotate(" + (90 - (length/(2*Math.PI * 460))*360) + "deg)";
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
				    var y1 = $div1.offset().top;
				    var h1 = $div1.outerHeight(true);
				    var w1 = $div1.outerWidth(true);
				    var b1 = y1 + h1;
				    var r1 = x1 + w1;
				    var x2 = $div2.offset().left;
				    var y2 = $div2.offset().top;
				    var h2 = $div2.outerHeight(true);
				    var w2 = $div2.outerWidth(true);
				    var b2 = y2 + h2;
				    var r2 = x2 + w2;

				    if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) return false;
   
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
			  		setCircPosition();
			  		$(this).hide();
			  	}).hide();
			}

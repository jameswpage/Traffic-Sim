James Page
9/27/2017

This project was an attempt to simulate traffic waves using very basic assumptions about
how drivers repond to certain situations. It assumes that every driver has a reaction time
time of about .25sec, meaning they wont respond to a brake in the car ahead of them in 
before that time. the position is calculated based on the velocity, and the velocity based on
the acceleration of the car, which is what directly changes based on certain events.

In order to use the program, git clone it. the one change that you will need to make is to 
set the path in cgitest.cgi to your computer/servers python.exe. Once this has been done,
simply host the site on your local machine (I use xampp). 

trail.json is overwritten every time new input is entered. 

Currently the number of cars is set to 10, and the program does not allow for a different number
(because of hard coded javascript). 

In the future I want to analyze the generated traffic patterns to tell how loyal they are to 
actual traffic (or at least the best models). Perhaps this will involve some machine learning. 

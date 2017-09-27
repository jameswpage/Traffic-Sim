#!C:/Users/James Page.JamesPage-THINK/Anaconda3/python.exe

import cgi
import cgitb; cgitb.enable()  # for troubleshooting
import Simulation_3

print("Content-type: text/html\n\n")



form = cgi.FieldStorage()
print(form)

time = form.getvalue("ttime")
carnum = form.getvalue("carnum")
initspeed = form.getvalue("initspeed")
change = form.getvalue("change")
start = form.getvalue("starttime")
end = form.getvalue("endtime")
amount = form.getvalue("amount")

#print(change, start, end, amount)

Simulation_3.createAndRun(time, carnum, initspeed, change, start, end, amount)


		


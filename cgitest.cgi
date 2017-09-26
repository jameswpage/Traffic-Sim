#!C:/Users/James Page.JamesPage-THINK/Anaconda3/python.exe

import cgi
import cgitb; cgitb.enable()  # for troubleshooting
import Simulation_3

print("Content-type: text/html\n\n")



form = cgi.FieldStorage()
print(form)

change = form.getvalue("change")
start = form.getvalue("starttime")
end = form.getvalue("endtime")
amount = form.getvalue("amount")

#print(change, start, end, amount)

Simulation_3.createAndRun(30000, change, start, end, amount)


print("""<?php
			$stateFile = fopen("../simulations/trial.json", "r") or die("Unable to open file");
			$jsonText = fread($stateFile, filesize("../simulations/trial.json"));
			$jsonText = json_encode(json_decode($jsonText));
			$times = json_encode($jsonText);
			fclose($stateFile);
		?>
		<script type="text/javascript">
			var times_json = <?php echo $times;?>;
			var times_table = JSON.parse(times_json);
		</script>""")
		


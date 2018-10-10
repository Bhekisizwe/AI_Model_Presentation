<?php
use UserClasses\BusinessLayer\ManageSession;

session_start();
require __DIR__."\\..\\vendor\autoload.php";
$manageSession=new ManageSession();
if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
if(isset($_SESSION["staffNumber"])){
    ?>
    	<!doctype html>
		<html lang="en">
  		<head>
        <!-- Required meta tags -->
    	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="dist/themes/default/style.min.css" />
        <!-- <link rel="stylesheet" href="/html/css/bootstrap-treeview.css">-->
        <title>AI Model Results Presentation</title>
        </head>
        <body style="background-color:#ccccff">
        <div class="containter-fluid" align="center">
        <h3 class="h3">AI Model Results Presentation</h3>
            	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]		
            	<?php if($_SESSION["userRoleName"]!="Admin"){
            	?>
            		[<a href='normalmenu.php' class='active'><b>Back to User Menu</b></a>]<p>
            	<?php 
            	}
            	else{            	
            	?>
            		[<a href='adminmenu.php' class='active'><b>Back to Administrator Menu</b></a>]<p>
            	<?php
            	}
            	?>	
            
            <img src="images/staticbackground.jpg" width="60%" height="60%" style="position:absolute;z-index:-1;top:25%;right:20%;bottom:0%">
            <div style="position:absolute;z-index:2;top:51%;right:28%" id="tonnage"><h3><b><font color="red">0t</font></b></h3></div>
          	<!-- <div style="position:absolute;top:42%;right:39%;bottom:10%">-->
            <svg viewBox="-650 -190 1500 1500">
                <line id="one" x1="100" y1="100" x2="40" y2="-100" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="two" x1="100" y1="100" x2="160" y2="-50" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="three" x1="100" y1="100" x2="-100" y2="100" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="four" x1="100" y1="100" x2="300" y2="100" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="five" x1="100" y1="100" x2="60" y2="250" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="six" x1="100" y1="100" x2="-100" y2="150" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="seven" x1="100" y1="100" x2="140" y2="290" style="stroke:rgb(255,255,0);stroke-width:5" />
                <line id="eight" x1="100" y1="100" x2="275" y2="263" style="stroke:rgb(255,255,0);stroke-width:5" />
   				
				<path id="temperature" d="M 100 100 L 160 100 A60 60 0 0 0 70 48" stroke="none" fill="#aaaa00" />
   				<path id="water" d="M 70 48 A60 60 0 0 0 70 152 L 100 100" stroke="none" fill="#000055" /> 
   				<path id="energy" d="M 70 152 A60 60 0 0 0 160 100 L 100 100" stroke="none" fill="#550000" /> 								
			</svg>
			<!-- </div> -->
			
	    	
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
		<script>
		$(document).ready(function(){
			var conn = new WebSocket('ws://localhost:8080');
			conn.onopen = function(e) {
				$("#json_data").html("Connection established!");
			};

			conn.onmessage = function(e) {
				e=JSON.parse(e.data);
				//console.log(e.water);
				html_str="<h3><b><font color='red'>"+e["tonnage"]+" t</font></b></h3>";				
			    $("#tonnage").html(html_str);
			    //extract the JSON parameters
			    water=parseFloat(e["water"]);
			    energy=parseFloat(e["energy"]);
			    temperature=parseFloat(e["temperature"]);
			    one=parseFloat(e["one"]);
			    two=parseFloat(e["two"]);
			    three=parseFloat(e["three"]);
			    four=parseFloat(e["four"]);
			    five=parseFloat(e["five"]);
			    six=parseFloat(e["six"]);
			    seven=parseFloat(e["seven"]);
			    eight=parseFloat(e["eight"]);
			    //change color intensity based on their values
			    var perc_arr_thresholds=new Array(0,10,20,30,40,50,60,70);
			    var water_arr_colors=new Array("#000022","#000044","#000066","#000088","#0000aa","#0000cc","#0000ff");
			    var temp_arr_colors=new Array("#222200","#444400","#666600","#888800","#aaaa00","#cccc00","#ffff00");
			    var energy_arr_colors=new Array("#220000","#440000","#660000","#880000","#aa0000","#cc0000","#ff0000");
			    for(var i=0;i<perc_arr_thresholds.length;i++){
					if(perc_arr_thresholds[i]<=water && water<perc_arr_thresholds[i+1]){
						//change water reactor fill color intensity
						$("#water").attr("fill",water_arr_colors[i]);
					}
					if(perc_arr_thresholds[i]<=energy && energy<perc_arr_thresholds[i+1]){
						//change water reactor fill color intensity
						$("#energy").attr("fill",energy_arr_colors[i]);
					}
					if(perc_arr_thresholds[i]<=temperature && temperature<perc_arr_thresholds[i+1]){
						//change water reactor fill color intensity
						$("#temperature").attr("fill",temp_arr_colors[i]);
					}					
			    }
			    //change the length of the radiating beams
			    //calculate the new x2 and y2 coordinates
			    $("#one").attr("x2",((60+1.4*one)*Math.cos(120/57.3)+100));
			    $("#one").attr("y2",((60+1.4*one)*Math.sin(120/57.3)+100));
			    $("#two").attr("x2",((60+1.4*two)*Math.cos(150/57.3)+100));
			    $("#two").attr("y2",((60+1.4*two)*Math.sin(150/57.3)+100));
			    $("#three").attr("x2",((60+1.4*three)*Math.cos(210/57.3)+100));
			    $("#three").attr("y2",((60+1.4*three)*Math.sin(210/57.3)+100));
			    $("#four").attr("x2",((60+1.4*four)*Math.cos(240/57.3)+100));
			    $("#four").attr("y2",((60+1.4*four)*Math.sin(240/57.3)+100));
			    $("#five").attr("x2",((60+1.4*five)*Math.cos(280/57.3)+100));
			    $("#five").attr("y2",((60+1.4*five)*Math.sin(280/57.3)+100));
			    $("#six").attr("x2",((60+1.4*six)*Math.cos(320/57.3)+100));
			    $("#six").attr("y2",((60+1.4*six)*Math.sin(320/57.3)+100));
			    $("#seven").attr("x2",((60+1.4*seven)*Math.cos(45/57.3)+100));
			    $("#seven").attr("y2",((60+1.4*seven)*Math.sin(45/57.3)+100));
			    $("#eight").attr("x2",((60+1.4*eight)*Math.cos(70/57.3)+100));
			    $("#eight").attr("y2",((60+1.4*eight)*Math.sin(70/57.3)+100));
			};
		});		

		</script>        
        </body>
        </html>
<?php
        $_SESSION["lastActive"]=time();
    }
    else{
        echo "<html><head><title></title><script>alert('Session Has Expired');window.location='index.html';</script></head><body></html>";  
        session_unset();
        session_destroy();
    }
?>
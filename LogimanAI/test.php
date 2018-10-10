<html><title>Random Data Generator</title>
</head>
<body>
	<div style="width:50%">            		        	
    	<div id='json_data'></div>	            	   		
    </div>

		<script src="/html/js/jquery-3.3.1.min.js"></script>
        <script src="/html/js/bootstrap.bundle.min.js"></script>
		<script>		
		$(document).ready(function(){
			var conn = new WebSocket('ws://localhost:8080');
			conn.onopen = function(e) {
				$("#json_data").html("Connection established!");
			};
			conn.onmessage = function(e) {
			    $("#json_data").html(e.data);
			};
			tonnage=0;
			function randomGenerator(){
				objLiteral={};
				objLiteral["water"]=Math.round(Math.random()*100*100)/100;
				objLiteral["energy"]=Math.round(Math.random()*100*100)/100;
				objLiteral["temperature"]=Math.round(Math.random()*100*100)/100;
				objLiteral["one"]=Math.round(Math.random()*100*100)/100;
				objLiteral["two"]=Math.round(Math.random()*100*100)/100;
				objLiteral["three"]=Math.round(Math.random()*100*100)/100;
				objLiteral["four"]=Math.round(Math.random()*100*100)/100;
				objLiteral["five"]=Math.round(Math.random()*100*100)/100;
				objLiteral["six"]=Math.round(Math.random()*100*100)/100;
				objLiteral["seven"]=Math.round(Math.random()*100*100)/100;
				objLiteral["eight"]=Math.round(Math.random()*100*100)/100;
				tonnage_increase=(Math.round(Math.random()*5*100)/100);	//increment tonnage 
				tonnage+=Math.floor(tonnage_increase);
				objLiteral["tonnage"]=tonnage;
				json_data=JSON.stringify(objLiteral);
				//alert(json_data);
				conn.send(json_data);
			}
			setInterval(randomGenerator, 1000);			
		});		

		</script>        
        </body>
        </html>
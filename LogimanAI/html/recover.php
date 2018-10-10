<?php
use UserClasses\BusinessLayer\ManageSession;

session_start();
require __DIR__."\\..\\vendor\autoload.php";
$manageSession=new ManageSession();
if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
if(isset($_SESSION["staffNumber"]) && $_SESSION["userRoleName"]=="Admin"){
    ?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>Password Recovery Screen</title>
  </head>
  <body style="background-color:#ccccff">
    <div align="center" class="container-fluid">
    	<h3 class="h3">Password Recovery Screen</h3><p>
    	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]
            	[<a href='adminmenu.php' class='active'><b>Back to Administrator Menu</b></a>]<p>	    	  		
    		<div class="form-group">
    			<form style="border:1px solid #888888;width:30%;background-color:#eeeeee" class="rounded" id="loginForm">
    				<input type="text" class="form-control" placeholder="Enter staff number" id="staffNumber" pattern="[a-zA-Z0-9]{4,}" title="Four or more Alpha-Numeric (i.e Letters and Digits only) characters necessary for the Staff Number" autocomplete="off" required><p>
		    		<p>	    		
		    		<input type="submit" id="passwordReset" class="btn btn-primary" value="RESET PASSWORD"><p>
		    		
		    		<p></p>    		
    			</form>    
	    	</div>
    	
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>   
    $(document).ready(function(){    	
 	    	$('#loginForm').submit(function (event){
 	    		var staffNumber=$('#staffNumber').val();    	    		
 	    		//send this data to the server API endpoint
 	    		//if(staffNumber)
 	    		//alert($('#loginForm')[0].checkValidity());
 	    		objData={"accountID":"",
				"roleID":0,
				"name":"",
				"surname":"",
				"staffNumber":"",
				"emailAddress":"",
				"passwordHash":"",							
				"accountState":0};								
				objData["staffNumber"]=staffNumber;	
				json_data=JSON.stringify(objData);
 	    		if($("#loginForm")[0].checkValidity()){ 
 	    			$("#passwordReset").val("RESETTING PASSWORD");
 	    			$("#passwordReset").prop("disabled",true);
 	    			//alert(staffNumber+"_"+password);   	    			    	
 	    			//alert(url);
 	    			$.ajax({
 						type: "POST",
 						contentType: "application/json",
 						url: "/login/reset",	
 						data:json_data,						
 						dataType: 'json',
 						cache: false,							
 						success: function (data) {  
						$("#passwordReset").val("EDIT PASSWORD");
	 	    			$("#passwordReset").prop("disabled",false);
						if(data["transactionStatus"]){
							alert("Password Successfully Reset and Emailed to User!");													
						}
						else{														    	
				    		alert("User account with that staff number does not exist!");
				    		$('#staffNumber').val("");
						}
 						},
 						error: function (e) {
 							$("#passwordReset").val("EDIT PASSWORD");
 		 	    			$("#passwordReset").prop("disabled",false);
 							alert("transaction failed to execute with error "+e);
 						}
			  });			
 	    				 
    		}
    		event.preventDefault();
    	});  	
    	
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
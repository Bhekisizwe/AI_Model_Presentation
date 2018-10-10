<?php
use UserClasses\BusinessLayer\ManageSession;

session_start();
require __DIR__."\\..\\vendor\autoload.php";
$manageSession=new ManageSession();
if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
if(isset($_SESSION["staffNumber"]) && $_SESSION["userRoleName"]=="Super Admin"){
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
        <!-- <link rel="stylesheet" href="css/bootstrap-treeview.css">-->
        <title>Create Administrator Accounts</title>
        </head>
        <body style="background-color:#ccccff">
        <div class="containter-fluid" align="center">
        	<h3 class="h3">Create Administrator Accounts</h3>
            	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]
            	[<a href='superadminmenu.php' class='active'><b>Back to Super Administrator Menu</b></a>]<p>
            
            	<div class="form-group">
    			<form style="border:1px solid #888888;width:30%;background-color:#eeeeee" class="rounded" id="accountForm">
    				<input type="hidden" id="accountID" value="">    				
    				<input type="text" class="form-control" placeholder="Enter name" id="name" pattern="[a-zA-Z\-\s]{2,}" title="Two or more {i.e Only Letters, Space and Hyphens allowed} characters necessary for the Name" autocomplete="on" required><p>
		    		<p>		    		
		    		<input type="text" class="form-control" placeholder="Enter surname" id="surname" pattern="[a-zA-Z\-\s]{2,}" title="Two or more {i.e Only Letters, Space and Hyphens allowed} characters necessary for the Surname" autocomplete="on" required><p>
		    		<input type="text" class="form-control" placeholder="Enter staff number" id="staffNumber" pattern="[0-9a-zA-Z]{4,}" title="Four or more Alpha-Numeric characters {i.e Only digits and letters allowed} necessary for the surburb" autocomplete="on" required><p>
		    		<p>		    		
		    		<input type="email" class="form-control" placeholder="Enter email address" id="emailAddress" title="please enter a valid email address" autocomplete="on" required><p>
		    		<p>		    		
		    		<label>Account State:</label><select id="accountState" required>
		    			<option value="" selected>--Select Account State--</option>
		    			<option value="0">In-Active</option>
		    			<option value="1">Active</option>		    			
		    		</select><p>	    		
		    		<input type="submit" id="addAccount" class="btn btn-primary" value="CREATE ADMIN ACCOUNT"><p>	
		    		
    			</form>
	    	
            </div>
        </div>
         <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="dist/jstree.min.js"></script>
        <!-- <script src="js/bootstrap-treeview.js"></script> -->       
        <script>
        $(document).ready(function(){ 
            var holder_add;
            var holder_edit;                            
            //$holder_add=$("#addLicense").detach();
            //$holder_edit=$("#editLicense").detach();
            
			$("#accountForm").on("submit",function(event){													
				//var data_arr=new Array();
				if($("#accountForm")[0].checkValidity()){					
					//initialise object literal.
					objData={"accountID":"",
							"name":"",
							"surname":"",
							"staffNumber":"",
							"emailAddress":"",							
							"accountState":0};
					objData["accountID"]=$("#accountID").val();
					objData["name"]=$("#name").val();	
					objData["surname"]=$("#surname").val();
					objData["staffNumber"]=$("#staffNumber").val();	
					objData["emailAddress"]=$("#emailAddress").val();
					objData["accountState"]=$("#accountState").val();							
					json_data=JSON.stringify(objData);
					
					if($("#addAccount").length>0){
						$('#addAccount').val("CREATING ADMIN ACCOUNT");
						$('#addAccount').prop('disabled', true);
						//we are adding
						//add admin account
						$.ajax({
							type: "GET",
							contentType: "application/json",
							url: "/adminaccount/"+objData["staffNumber"],							
							dataType: 'json',
							cache: false,							
							success: function (data) {								
								if(data["transactionStatus"]){									
									if(data["dataExistsStatus"]){
										//then we know that there is a duplicate Staff Number
										$('#addAccount').val("CREATE ADMIN ACCOUNT");
										$('#addAccount').prop('disabled', false);
										alert("The staff number entered already exists in the system,\n please try another one.");
									}
									else{
										//create the account
										$.ajax({
                						type: "POST",
                						contentType: "application/json",
                						url: "/adminaccount/add",	
                						data:json_data,						
                						dataType: 'json',
                						cache: false,							
                						success: function (data) {
                							$('#addAccount').val("CREATE ADMIN ACCOUNT");
            								$('#addAccount').prop('disabled', false);
											if(data["transactionStatus"]){
												alert("Administrator Account Successfully Created!");													
											}
											else{												
												if(data["errorAssocArray"]["errorCode"]=="0x19"){
											    	//session has expired
											    	alert(data["errorAssocArray"]["errorDescription"]);
											    	window.location="index.html";
										    	}
												else if(data["errorAssocArray"]["errorCode"]=="0x10"){
													alert(data["errorAssocArray"]["errorDescription"]);
												}
										    	else{								    	
										    		alert("transaction failed to execute"); 
										    	}	
											}
                						},
                						error: function (e) {
                							$('#addAccount').val("CREATE ADMIN ACCOUNT");
            								$('#addAccount').prop('disabled', false);
                							alert("transaction failed to execute with error"+e);
                						}
									  });
									}								    								    	
							    }
							    else{
							    	$('#addAccount').prop('disabled', false);
							    	if(data["errorAssocArray"]["errorCode"]=="0x19"){
								    	//session has expired
								    	alert(data["errorAssocArray"]["errorDescription"]);
								    	window.location="index.html";
							    	}
							    	else{								    	
							    		alert("transaction failed to execute"); 
							    	}	    
							    }
							},
							error: function (e) {
								$('#addAccount').val("CREATE ADMIN ACCOUNT");
								$('#addAccount').prop('disabled', false);
								alert("transaction failed to execute with error"+e);
							}
						});		
					}				
					
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
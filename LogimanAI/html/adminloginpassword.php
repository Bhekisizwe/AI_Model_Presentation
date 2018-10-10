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
        <!-- <link rel="stylesheet" href="css/bootstrap-treeview.css">-->
        <title>Edit Login Password</title>
        </head>
        <body style="background-color:#ccccff">
        <div class="containter-fluid" align="center">
        	<h3 class="h3">Edit Login Password</h3>
            	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]
            	[<a href='adminmenu.php' class='active'><b>Back to Administrator Menu</b></a>]<p>
            
            	<div class="form-group">
    			<form style="border:1px solid #888888;width:30%;background-color:#eeeeee" class="rounded" id="loginForm">
    				<input type="hidden" id="accountID" value="">
    				<input type="hidden" id="passwordHash" value=""> 
    				<input type="hidden" id="accountState" value="">       				
    				<div id="staffNumber" align="left"></div><p><p>   			  				
    				<div id="name" align="left"></div><p><p>		    		
		    		<div id="surname" align="left"></div><p><p>   			  				
    				<div id="emailAddress" align="left"></div><p><p>	    		
		    		<div id="accountStateWords" align="left"></div><p><p>   			  				
    				<div id="userroles" align="left"></div><p><p>
    				<input type="password" id="oldPassword" placeholder="Enter current password" class="form-control" pattern="[a-zA-Z0-9]{8,}" title="Eight or more Alpha-numeric (i.e Letters and Digits only) characters necessary for the Password" autocomplete="off" required><p>	    		
    				<input type="password" id="newPassword1" placeholder="Enter new password" class="form-control" pattern="[a-zA-Z0-9]{8,}" title="Eight or more Alpha-numeric (i.e Letters and Digits only) characters necessary for the Password" autocomplete="off" required><p>
    				<input type="password" id="newPassword2" placeholder="Repeat new password" class="form-control" pattern="[a-zA-Z0-9]{8,}" title="Eight or more Alpha-numeric (i.e Letters and Digits only) characters necessary for the Password" autocomplete="off" required><p>
		    		<input type="submit" id="editPassword" class="btn btn-primary" value="EDIT PASSWORD"><p>	
		    		
    			</form>
	    	
            </div>
        </div>
         <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.md5.js"></script>
        <script src="dist/jstree.min.js"></script>
        <!-- <script src="js/bootstrap-treeview.js"></script> -->       
        <script>
        $(document).ready(function(){ 
            var name;
            var surname;
            var emailAddress;
            var staffNumber;            
            var roleID;                                                                          
            //$holder_add=$("#addLicense").detach();
            //$holder_edit=$("#editLicense").detach();
            $.ajaxSetup({cache: false});   
            //Search for user profile            				
		    <?php echo "var staffNumber=".$_SESSION["staffNumber"];?>;			    
		    //alert(staffNumber);
		    if(staffNumber!=""){
		    	$.ajax({
					type: "GET",
					contentType: "application/json",
					url: "/useraccount/"+staffNumber,							
					dataType: 'json',
					cache: false,							
					success: function (data) {							
						if(data["transactionStatus"]){								
							$("#accountID").val(data["accountID"]);
							name=data["name"];
							$("#name").html("Name: <b>"+data["name"]+"</b>");
							surname=data["surname"];
							$("#surname").html("Surname: <b>"+data["surname"]+"</b>");
							staffNumber=data["staffNumber"];
							$("#staffNumber").html("Staff Number: <b>"+data["staffNumber"]+"</b>");
							emailAddress=data["emailAddress"];
							$("#emailAddress").html("Email Address: <b>"+data["emailAddress"]+"</b>");
							$("#passwordHash").val(data["passwordHash"]);
							$("#accountState").val(data["accountState"]);
							if(data["accountState"]==1){
								$("#accountStateWords").html("Account State: <b>Active</b>");								
							}
							else if(data["accountState"]==0){
								$("#accountStateWords").html("Account State: <b>Inactive</b>");
							}
							roleID=data["roleID"];
							$("#userroles").html("User Role: <b>"+data["userRoleName"]+"</b>");																													
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
					    		alert("Transaction execution failed");	
					    	}	
						}
					},
					error: function (e) {
						alert("Error:transaction failed to execute with error "+e);
					}
		    	});	
		    }
		    else{			    	
		    	//alert("Please enter a valid staff number");				
		    }		    
            
			$("#loginForm").on("submit",function(event){													
				//var data_arr=new Array();
				if($("#passwordHash").val()==$.md5($("#oldPassword").val())){
					if($("#newPassword1").val()==$("#newPassword2").val()){
						if($("#loginForm")[0].checkValidity()){					
							//initialise object literal.
							objData={"accountID":"",
									"roleID":0,
									"name":"",
									"surname":"",
									"staffNumber":"",
									"emailAddress":"",
									"passwordHash":"",							
									"accountState":0};
							objData["accountID"]=$("#accountID").val();
							objData["name"]=name;	
							objData["surname"]=surname;						
							objData["emailAddress"]=emailAddress;
							objData["accountState"]=$("#accountState").val();
							objData["passwordHash"]=$("#newPassword1").val();
							objData["roleID"]=roleID;
														
							json_data=JSON.stringify(objData);
							
							if($("#editPassword").length>0){
								$('#editPassword').val("UPDATING PASSWORD");
								$('#editPassword').prop('disabled', true);
								//we are updating!!!												
								//update the account
								$.ajax({
								type: "POST",
								contentType: "application/json",
								url: "/login/update",	
								data:json_data,						
								dataType: 'json',
								cache: false,							
								success: function (data) {
									$('#editPassword').val("EDIT PASSWORD");
									$('#editPassword').prop('disabled', true);
									//alert(data["transactionStatus"]);
									if(data["transactionStatus"]){
										alert("User Password Successfully Updated!");
										location.reload();													
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
									$('#editPassword').val("EDIT PASSWORD");
									$('#editPassword').prop('disabled', true);
									alert("transaction failed to execute with error "+e);
								}
							  });
							}								    								    	
							
						}
					}
					else{
						alert("New Password and Repeated Password do not match");
						$("#newPassword1").val("");
						$("#newPassword2").val("");
					}
				}
				else{
					alert("Incorrect current password");
					$("#oldPassword").val("");
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
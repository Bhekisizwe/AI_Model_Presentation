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
        <title>Edit Administrator Accounts</title>
        </head>
        <body style="background-color:#ccccff">
        <div class="containter-fluid" align="center">
        	<h3 class="h3">Edit Administrator Accounts</h3>
            	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]
            	[<a href='superadminmenu.php' class='active'><b>Back to Super Administrator Menu</b></a>]<p>
            
            	<div class="form-group">
    			<form style="border:1px solid #888888;width:30%;background-color:#eeeeee" class="rounded" id="accountForm">
    				<input type="hidden" id="accountID" value="">
    				<input type="hidden" id="passwordHash" value="">    				
    					<div id="populateSelection"></div><p><p>
    				<div id="staffNumber" align="left"></div><p><p>   			  				
    				<input type="text" class="form-control" placeholder="Enter name" id="name" pattern="[a-zA-Z\-\s]{2,}" title="Two or more Alpha {i.e Only Letters, Space and Hyphens allowed} characters necessary for the Name" autocomplete="on" required><p>
		    		<p>		    		
		    		<input type="text" class="form-control" placeholder="Enter surname" id="surname" pattern="[a-zA-Z\-\s]{2,}" title="Two or more Alpha {i.e Only Letters, Space and Hyphens allowed} characters necessary for the Surname" autocomplete="on" required>
		    		<p><p>		    				    		
		    		<input type="email" class="form-control" placeholder="Enter email address" id="emailAddress" title="please enter a valid email address" autocomplete="on" required><p>
		    		<p>		    		
		    		<label id="labelState">Account State:</label><select id="accountState" required>
		    			<option value="" selected>--Select Account State--</option>
		    			<option value="0">In-Active</option>
		    			<option value="1">Active</option>		    			
		    		</select><p>	    		
		    		<input type="submit" id="editAccount" class="btn btn-primary" value="EDIT ADMIN ACCOUNT"><p>	
		    		
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
            var prev_account_state; 
            $("#name").hide();
			$("#surname").hide();
			$("#staffNumber").hide();
			$("#emailAddress").hide();					
			$("#accountState").hide(); 
			$("#labelState").hide();
			$("#editAccount").hide();                                                  
            //$holder_add=$("#addLicense").detach();
            //$holder_edit=$("#editLicense").detach();
            //read all admin accounts and populate the form
            url="/adminaccount";            
        	var jqxhr=$.getJSON(    
		    url,    			    
		    function(data,status){    			    	
		    	//alert(data["staffNumber"]);    			        
		    }).done(function(data){		    
		    	//successfully executed transaction	
		    	var html_str="<label>Select Administrator Profile: </label><p><select id='admin_selector'><option value=''>Select Administrator</option>";
		    	for(var i=0;i<data.length;i++){			    	
		    		html_str+="<option value='"+data[i]['staffNumber']+"'>"+data[i]['name']+" "+data[i]['surname']+" "+data[i]['staffNumber']+"</option>";	
		    		//alert(html_str);
		    	}
		    	html_str+="</select><p>";
		    	$("#populateSelection").append(html_str);	    	  			    	    			    	
		    }).fail(function(data){
		    	alert("Error:transaction failed");    			    	
		    });	

		    $("#populateSelection").on("change","select",function(){
			    var staffNumber=$("#admin_selector").val();
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
								$("#name").val(data["name"]);
								$("#surname").val(data["surname"]);
								$("#staffNumber").html("Staff Number: <b>"+data["staffNumber"]+"</b>");
								$("#emailAddress").val(data["emailAddress"]);
								$("#passwordHash").val(data["passwordHash"]);
								$("#accountState").val(data["accountState"]);
								prev_account_state=data["accountState"];
								$("#name").show();
								$("#surname").show();
								$("#staffNumber").show();
								$("#emailAddress").show();					
								$("#accountState").show();	
								$("#labelState").show();
								$("#editAccount").show(); 											
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
							alert("Error:transaction failed to execute with error "+e);
						}
			    	});	
			    }
			    else{			    	
			    	$("#name").hide();
					$("#surname").hide();
					$("#staffNumber").hide();
					$("#emailAddress").hide();					
					$("#accountState").hide();
					$("#labelState").hide();
					$("#editAccount").hide(); 
			    }	    	
		    }); 
            
			$("#accountForm").on("submit",function(event){													
				//var data_arr=new Array();
				if($("#accountForm")[0].checkValidity()){					
					//initialise object literal.
					objData={"accountID":"",
							"roleID":2,
							"name":"",
							"surname":"",
							"staffNumber":"",
							"emailAddress":"",
							"passwordHash":"",							
							"accountState":0};
					objData["accountID"]=$("#accountID").val();
					objData["name"]=$("#name").val();	
					objData["surname"]=$("#surname").val();						
					objData["emailAddress"]=$("#emailAddress").val();
					objData["accountState"]=$("#accountState").val();
					objData["passwordHash"]=$("#passwordHash").val();
					if($("#accountState").val()==prev_account_state){
						objData["dataExistsStatus"]=true;
					}
					else objData["dataExistsStatus"]=false;							
					json_data=JSON.stringify(objData);
					
					if($("#editAccount").length>0){
						$('#editAccount').val("UPDATING ADMIN ACCOUNT");
						$('#editAccount').prop('disabled', true);
						//we are updating!!!												
						//update the account
						$.ajax({
						type: "POST",
						contentType: "application/json",
						url: "/adminaccount/update",	
						data:json_data,						
						dataType: 'json',
						cache: false,							
						success: function (data) {
							$('#editAccount').val("EDIT ADMIN ACCOUNT");
							$('#editAccount').prop('disabled', false);
							//alert(data["transactionStatus"]);
							if(data["transactionStatus"]){
								alert("Administrator Account Successfully Updated!");
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
							$('#editAccount').val("EDIT ADMIN ACCOUNT");
							$('#editAccount').prop('disabled', false);
							alert("transaction failed to execute with error "+e);
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
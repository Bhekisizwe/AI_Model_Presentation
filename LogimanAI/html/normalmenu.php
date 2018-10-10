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
        <title>User Menu</title>
        </head>
        <body style="background-color:#ccccff">
        <div class="containter-fluid" align="center">
        	<h3 class="h3">User Menu</h3><p>
            	<i><div style="font-family:arial;font-size:14pt">Logged in as:<b><?php echo $_SESSION["name"]." ".$_SESSION["surname"]; ?></b></div></i><p>
            	[<a href='logout.php' class='active'><b>Logout</b></a>]
		 
		<p>
            <div style="width:20%">            	
            	<div id="tree" align="left">
    		  		 <ul>       	                    
                      	                 
                      	<li id="login_password">Login Password Management</li>
                      	
                      	<li id="view_AI_model">View AI Model Results Presentation</li>                      	
                      </ul> 
            	</div>            	   		
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
        	$("#tree").jstree({"plugins":["sort"]});
        	$("#tree").on("select_node.jstree", function(e,data) {
            	var id=data.selected[0];
            	//alert(id);
            	switch(id){
            	case "login_password":
					window.location="userloginpassword.php";
                	break;            
            	case "view_AI_model":
            		window.location="viewAImodel.php";
            	}
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

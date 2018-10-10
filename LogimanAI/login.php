<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use UserClasses\BusinessLayer\LoginCredentials;
use UserClasses\BusinessObjects\UserAccountBO;
use UserClasses\BusinessLayer\ManageSession;
    
    //Check If User Exists in System
    $app->get('/login/checkexistence/{staffnumber_password}', function (Request $request, Response $response, array $args) {
        //create objects
        $loginObj=new LoginCredentials();
        $userAccountBO=new UserAccountBO();
        $urlString=$args["staffnumber_password"];        
        $login_cred_arr=explode("_", $urlString);
        $userAccountBO->setStaffNumber($login_cred_arr[0]);
        $userAccountBO->setPasswordHash($login_cred_arr[1]);        
        if($loginObj->findUserAccountMatch($userAccountBO)){
            //match found. Set SESSION variable and start session
            //set Transaction Status to true            
            $_SESSION["staffNumber"]=$userAccountBO->getStaffNumber();
            $user_arr=$loginObj->listUserAccount($userAccountBO);            
            $_SESSION["roleID"]=$user_arr["roleID"];
            $_SESSION["userRoleName"]=$user_arr["userRoleName"];
            $_SESSION["name"]=$user_arr["name"];
            $_SESSION["surname"]=$user_arr["surname"];
            $_SESSION["lastActive"]=time();
            $userAccountBO->setUserRoleName($user_arr["userRoleName"]);
            $userAccountBO->setTransactionStatus(true);
            $userAccountBO->setDataExistsStatus(true);
            $userAccountBO->setPasswordHash("");
        }
        else {
            $userAccountBO->setPasswordHash("");
            $userAccountBO->setTransactionStatus(true);
            $userAccountBO->setDataExistsStatus(false);
        }       
        $arr=$userAccountBO->getArray();
        $arr_json=json_encode($arr);
        //destroy objects
        unset($loginObj);
        unset($userAccountBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body);        
    }); 
    
    //View User Account Profile
    $app->get('/login/{staffnumber}', function (Request $request, Response $response, array $args) {
        //create objects
        $loginObj=new LoginCredentials();
        $userAccountBO=new UserAccountBO();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $staffNumber=$args["staffnumber"];
            $userAccountBO->setStaffNumber($staffNumber);
            $user_account_arr=$loginObj->listUserAccount($userAccountBO);
            $userAccountBO->set($user_account_arr);
            $userAccountBO->setTransactionStatus(true);
            $arr=$userAccountBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userAccountBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userAccountBO->set($arr_error);
            $arr=$userAccountBO->getArray();
        }       
        
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($loginObj);
        unset($userAccountBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    }); 
        
    //Update Password
    $app->post('/login/update', function (Request $request, Response $response, array $args) {
        //create objects
        $loginObj=new LoginCredentials();
        $userAccountBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){            
            $userAccountBO->set($form_data);  
            $userAccountBO->setAdminStaffNumber($_SESSION["staffNumber"]);
            $userAccountBO->setTransactionStatus($loginObj->updateUserPassword($userAccountBO));
            $userAccountBO->setPasswordHash("");
            $arr=$userAccountBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userAccountBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userAccountBO->set($arr_error);
            $userAccountBO->setPasswordHash("");
            $arr=$userAccountBO->getArray();
        }        
        $arr_json=json_encode($arr);
        //destroy objects
        unset($loginObj);
        unset($userAccountBO);    
        unset($manageSession);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
    
    //Reset Password
    $app->post('/login/reset', function (Request $request, Response $response, array $args) {
        //create objects
        $loginObj=new LoginCredentials();
        $userAccountBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data        
        $userAccountBO->set($form_data);        
        $userAccountBO->setTransactionStatus($loginObj->resetUserPassword($userAccountBO));
        $userAccountBO->setPasswordHash("");
        $arr=$userAccountBO->getArray();      
        $arr_json=json_encode($arr);
        //destroy objects
        unset($loginObj);
        unset($userAccountBO);        
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
?>

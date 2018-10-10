<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use UserClasses\BusinessLayer\UserAccounts;
use UserClasses\BusinessObjects\UserAccountBO;
use UserClasses\BusinessLayer\ManageSession;
    //View User Account
    $app->get('/useraccount/{staffnumber}', function (Request $request, Response $response, array $args) {
        //Create Objects
        $user=new UserAccounts();
        $userBO=new UserAccountBO();
        $staffNumber=$args["staffnumber"];
        $arr=array();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $userBO->setStaffNumber($staffNumber);
            $user_arr=$user->listUserAccount($userBO);
            $userBO->setTransactionStatus(true);
            $userBO->set($user_arr);
            $arr=$userBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userBO->set($arr_error);
            $arr=$userBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($user);
        unset($userBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
    
    //Check User Account Exists
    $app->get('/useraccount/checkexistence/{staffnumber}', function (Request $request, Response $response, array $args) {
        //Create Objects
        $user=new UserAccounts();
        $userBO=new UserAccountBO();
        $staffNumber=$args["staffnumber"];
        $arr=array();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $userBO->setStaffNumber($staffNumber);
            $userBO->setDataExistsStatus($user->checkUserAccountExists($userBO));
            $userBO->setTransactionStatus(true);
            $arr=$userBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userBO->set($arr_error);
            $arr=$userBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($user);
        unset($userBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
        
    //Add
    $app->post('/useraccount/add', function (Request $request, Response $response, array $args) {
        //Create Objects
        $user=new UserAccounts();
        $userBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $userBO->set($form_data);
            $userBO->setAdminStaffNumber($_SESSION["staffNumber"]);            
            $userBO->setTransactionStatus($user->addUserAccount($userBO));
            if(!$userBO->getTransactionStatus()){
                $arr_error["errorAssocArray"]=$user->getUserAccountBO()->getErrorAssocArray();
                $userBO->set($arr_error);
            }
            $arr=$userBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userBO->set($arr_error);
            $arr=$userBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($user);
        unset($userBO);       
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
        
    //Update
    $app->post('/useraccount/update', function (Request $request, Response $response, array $args) {
        //Create Objects
        $user=new UserAccounts();
        $userBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $userBO->set($form_data);
            $userBO->setActionCode("0xA100");
            $userBO->setAdminStaffNumber($_SESSION["staffNumber"]);
            $userBO->setTransactionStatus($user->updateUserAccount($userBO));
            if(!$userBO->getTransactionStatus()){
                $arr_error["errorAssocArray"]=$user->getUserAccountBO()->getErrorAssocArray();
                $userBO->set($arr_error);
            }
            $arr=$userBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $userBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $userBO->set($arr_error);
            $arr=$userBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($user);
        unset($userBO);         
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
?>

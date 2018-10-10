<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use UserClasses\BusinessLayer\AdminAccounts;
use UserClasses\BusinessObjects\UserAccountBO;
use UserClasses\BusinessLayer\ManageSession;
    //View All
    $app->get('/adminaccount', function (Request $request, Response $response, array $args) {
        //Create Objects
        $admin=new AdminAccounts();
        $adminBO=new UserAccountBO();
        $arr=$adminBO->getArray(); 
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){  
            $arr=array(); 
            $admin_arr=$admin->listAllAdminAccounts();
            for($i=0;$i<count($admin_arr);$i++){
                $adminBO->set($admin_arr[$i]);
                $adminBO->setTransactionStatus(true);
                $arr[]=$adminBO->getArray();
            }
            $_SESSION["lastActive"]=time();
        }
        else{
            $adminBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $adminBO->set($arr_error);
            $arr=$adminBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($admin);
        unset($adminBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
    
    //Check Staff Number exists
    $app->get('/adminaccount/{staffnumber}', function (Request $request, Response $response, array $args) {
        //Create Objects
        $admin=new AdminAccounts();
        $adminBO=new UserAccountBO();
        $staffNumber=$args["staffnumber"];
        $arr=array();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $adminBO->setStaffNumber($staffNumber);           
            $adminBO->setDataExistsStatus($admin->checkUserAccountExists($adminBO));
            $adminBO->setTransactionStatus(true);
            $arr=$adminBO->getArray();  
            $_SESSION["lastActive"]=time();
        }
        else{
            $adminBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $adminBO->set($arr_error);
            $arr=$adminBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($admin);
        unset($adminBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
        
    //Add
    $app->post('/adminaccount/add', function (Request $request, Response $response, array $args) {
        //Create Objects
        $admin=new AdminAccounts();
        $adminBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data 
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $adminBO->set($form_data);
            $adminBO->setAdminStaffNumber($_SESSION["staffNumber"]);
            $adminBO->setRoleID(2);
            $adminBO->setTransactionStatus($admin->addUserAccount($adminBO));
            if(!$adminBO->getTransactionStatus()){  
                $arr_error["errorAssocArray"]=$admin->getUserAccountBO()->getErrorAssocArray();
                $adminBO->set($arr_error);
            }
            $arr=$adminBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $adminBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $adminBO->set($arr_error);
            $arr=$adminBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($admin);
        unset($adminBO); 
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body);         
    });
        
    //Update
    $app->post('/adminaccount/update', function (Request $request, Response $response, array $args) {
        //Create Objects
        $admin=new AdminAccounts();
        $adminBO=new UserAccountBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $adminBO->set($form_data);
            $adminBO->setActionCode("0xA100");
            $adminBO->setAdminStaffNumber($_SESSION["staffNumber"]);
            $adminBO->setTransactionStatus($admin->updateUserAccount($adminBO));
            if(!$adminBO->getTransactionStatus()){
                $arr_error["errorAssocArray"]=$admin->getUserAccountBO()->getErrorAssocArray();
                $adminBO->set($arr_error);
            }
            $arr=$adminBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $adminBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $adminBO->set($arr_error);
            $arr=$adminBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($admin);
        unset($adminBO); 
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });

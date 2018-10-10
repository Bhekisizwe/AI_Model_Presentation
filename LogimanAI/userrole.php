<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use UserClasses\BusinessLayer\UserRole;
use UserClasses\BusinessObjects\UserRoleBO;
use UserClasses\BusinessLayer\ManageSession;
    //View All User Roles, Activity Names, and Report Column Names
    $app->get('/userrole', function (Request $request, Response $response, array $args) {
        //Create Objects
        $role=new UserRole();
        $roleBO=new UserRoleBO();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){           
            $list_roles_arr=$role->listAllUserRoles();
            $list_activity_arr=$role->listAllActivities();
            $list_reportcol_arr=$role->listAllColumns();
            $roleBO->set($list_roles_arr);
            $roleBO->set($list_activity_arr);
            $roleBO->set($list_reportcol_arr);
            $roleBO->setTransactionStatus(true);
            $arr=$roleBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $roleBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $roleBO->set($arr_error);
            $arr=$roleBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($role);
        unset($roleBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
    
    //Check if User Role exists
    $app->get('/userrole/checkexistence/{userrolename}', function (Request $request, Response $response, array $args) {
        //Create Objects
        $role=new UserRole();
        $roleBO=new UserRoleBO();
        $userRoleName=$args["userrolename"];
        $role_arr["userRole2DArray"][0]["userRoleName"]=$userRoleName;
        $arr=array();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $roleBO->set($role_arr);
            $roleBO->setDataExistsStatus($role->checkUserRoleExists($roleBO));
            $roleBO->setTransactionStatus(true);
            $arr=$roleBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $roleBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $roleBO->set($arr_error);
            $arr=$roleBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($role);
        unset($roleBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
    
    //View Access Rights
    $app->get('/userrole/{roleid}', function (Request $request, Response $response, array $args) {
        //Create Objects
        $role=new UserRole();
        $roleBO=new UserRoleBO();
        $roleID=$args["roleid"];
        $role_arr["userRole2DArray"][0]["roleID"]=$roleID;
        $arr=array();
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $roleBO->set($role_arr);
            $access_arr=$role->listUserAccessRights($roleBO);
            $roleBO->setTransactionStatus(true);
            $roleBO->set($access_arr);
            $arr=$roleBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $roleBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $roleBO->set($arr_error);
            $arr=$roleBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($role);
        unset($roleBO);
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
    });
    
    //Add
    $app->post('/userrole/add', function (Request $request, Response $response, array $args) {
        //Create Objects
        $role=new UserRole();
        $roleBO=new UserRoleBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $roleBO->set($form_data);
            $roleBO->setStaffNumber($_SESSION["staffNumber"]);
            $roleBO->setTransactionStatus($role->addUserRole($roleBO));           
            $arr=$roleBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $roleBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $roleBO->set($arr_error);
            $arr=$roleBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($role);
        unset($roleBO);  
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
        
    //Update
    $app->post('/userrole/update', function (Request $request, Response $response, array $args) {
        //Create Objects
        $role=new UserRole();
        $roleBO=new UserRoleBO();
        //Return Associative Array
        $form_data=json_decode($request->getBody()->getContents(),TRUE);  //get client form data
        $manageSession=new ManageSession();
        if(isset($_SESSION["lastActive"])) $manageSession->determineSessionValidity(time());
        if(isset($_SESSION["staffNumber"])){
            $roleBO->set($form_data);            
            $roleBO->setStaffNumber($_SESSION["staffNumber"]);
            $roleBO->setTransactionStatus($role->updateUserRole($roleBO));
            $arr=$roleBO->getArray();
            $_SESSION["lastActive"]=time();
        }
        else{
            $roleBO->setTransactionStatus(false);
            //write Error Code and Description
            $arr_err=array();
            $arr_err["errorCode"]="0x19";
            $arr_err["errorDescription"]="Session has expired";
            $arr_error["errorAssocArray"]=$arr_err;
            $roleBO->set($arr_error);
            $arr=$roleBO->getArray();
        }
        $arr_json=json_encode($arr);
        //destroy objects
        unset($manageSession);
        unset($role);
        unset($roleBO);  
        $body=$response->getBody();
        $body->write($arr_json);
        return $response->withHeader("Content-Type", "application/json;charset=UTF-8")
        ->withBody($body); 
        
    });
?>

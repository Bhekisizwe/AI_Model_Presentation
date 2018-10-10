<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\ {
    DataLayer\UserRoleDL,
    BusinessObjects\UserRoleBO
};
use UserClasses\BusinessObjects\ActivityLogBO;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserRole
{
    private $accessRightsObj;
    private $userRole;
    private $activityLog;
    private $activityBO;

    /**
     */
    public function __construct()
    {
        $this->accessRightsObj=new AccessRights();    
        $this->userRole=new UserRoleDL();
        $this->activityLog=new ActivityLog();
        $this->activityBO=new ActivityLogBO();
    }
    
    public function __destruct(){
        $this->accessRightsObj=null;
        $this->userRole=null;
        $this->activityLog=null;
        $this->activityBO=null;
    }
    
    public function listAllUserRoles():array {       
        $data=array();
        try {
            $arr=$this->userRole->retrieveAllUserRoles($data);
        } catch (\Exception $e) {
            $class_name="UserRole";
            $method_name="listAllUserRoles";
            $this->errObj->logErrors($e,null,$class_name, $method_name);
        }                
        return $arr;
    }
    
    public function listAllActivities():array {
        $data=array();
        try {
            $arr=$this->userRole->retrieveAllActivities($data);
        } catch (\Exception $e) {
            $class_name="UserRole";
            $method_name="listAllActivities";
            $this->errObj->logErrors($e,null,$class_name, $method_name);
        }        
        return $arr;
    }
    
    public function listAllColumns():array {
        $data=array();
        try {
            $arr=$this->userRole->retrieveAllColumns($data);
        } catch (\Exception $e) {
            $class_name="UserRole";
            $method_name="listAllColumns";
            $this->errObj->logErrors($e,null,$class_name, $method_name);
        }        
        return $arr;
    }
    
    public function listUserAccessRights(UserRoleBO $data):array {
        if(isset($data)){
            $arr=$this->accessRightsObj->listUserRights($data); 
            return $arr;
        }
        else return NULL;
    }
    
    public function checkUserRoleExists(UserRoleBO $data):bool {
        if(isset($data)){
            try {
                $arr=$data->getArray();
                $status_message=$this->userRole->dataExists($arr);
            } catch (\Exception $e) {
                $class_name="UserRole";
                $method_name="checkUserRoleExists";
                $this->errObj->logErrors($e,null,$class_name, $method_name);
            }
        
            return $status_message;
        }
        else return false;
    }
    
    public function addUserRole(UserRoleBO $data):bool {
        if(isset($data)){
            try {
                $arr=$data->getArray();
                $status_message=$this->userRole->create($arr);
                if($status_message){
                    $arr_data=array();
                    $arr_data["taskArray2D"][0]["taskName"]="User Roles";
                    $arr_data["transactionName"]="Adding User Role information to Database";
                    $arr_data["activityAction"]=1;      //create
                    $arr_data["staffNumber"]=$data->getStaffNumber();
                    $this->activityBO->set($arr_data);
                    $this->activityLog->addActivityData($this->activityBO);
                }
            } catch (\Exception $e) {
                $class_name="UserRole";
                $method_name="addUserRole";
                $this->errObj->logErrors($e,null,$class_name, $method_name);
            }            
            return $status_message;
        }
        else return false;
    }
    
    public function updateUserRole(UserRoleBO $data):bool {
        if(isset($data)){            
            $status_message=$this->accessRightsObj->updateUserAccessRights($data);
            if($status_message){
                $arr_data=array();
                $arr_data["taskArray2D"][0]["taskName"]="User Roles";
                $arr_data["transactionName"]="Updating User Role information in Database";
                $arr_data["activityAction"]=2;      //update
                $arr_data["staffNumber"]=$data->getStaffNumber();
                $this->activityBO->set($arr_data);
                $this->activityLog->addActivityData($this->activityBO);
            }
            return $status_message;
        }
        else return false;
    }
    
    public function checkUserAuthorization(UserRoleBO $data,string $accessRight,string $activityName):bool {
        if(isset($data) && $accessRight!="" && $activityName!=""){
            $arr=$this->listUserAccessRights($data);    //get user access rights for the specific Role  
            if(count($arr)>0){
                foreach($arr["activityRights2DArray"] as $value){
                    if($value["activityName"]==$activityName){
                        //we will only enter here once
                        $arr_data=explode(" ",$value["activityRights"]);
                        if(array_search($accessRight, $arr_data)===false){
                            //we do not have access rights here
                            $status_message=false;
                            break;  //exit loop
                        }
                        else{
                            $status_message=true;   //we have access rights
                            break;  //exit loop
                        }
                    }
                    else $status_message=false;
                }
            }
            else $status_message=false;            
            return $status_message;
        }
        else return false;
    }
   
}


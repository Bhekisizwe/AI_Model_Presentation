<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\ {
    BusinessObjects\UserRoleBO,
    DataLayer\UserRoleDL
};

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class AccessRights
{
    private $userRole;
    private $errObj;
    
    public function __construct(){
        $this->userRole=new UserRoleDL();  
        $this->errObj=new ErrorLog();        
    }
    
    public function __destruct(){
        $this->userRole=null;    
        $this->errObj=null;
    }
    
    public function listUserRights(UserRoleBO $data):array {
        if(isset($data)){
            $arr=$data->getArray();
            try {
                $arr_2D=$this->userRole->searchData($arr);
            } catch (\Exception $e) {            
                $class_name="AccessRights";
                $method_name="listUserRights";
                $this->errObj->logErrors($e,null,$class_name, $method_name);
            }            
            return $arr_2D;
        }
        else return NULL;
    }
    
    public function updateUserAccessRights(UserRoleBO $data):bool {
        if(isset($data)){
            $arr=$data->getArray();
            try {
                $status_message=$this->userRole->update($arr);
            } catch (\Exception $e) {
                $class_name="AccessRights";
                $method_name="updateUserAccessRights";
                $this->errObj->logErrors($e,null,$class_name, $method_name);
            }            
            return $status_message;
        }
        else return false;
    }
}


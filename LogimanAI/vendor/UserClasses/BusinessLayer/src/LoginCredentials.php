<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\BusinessObjects\UserAccountBO;
use UserClasses\DataLayer\UserAccountDL;
use UserClasses\DataLayer\LoginCredentialsDL;
use UserClasses\BusinessObjects\ActivityLogBO;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class LoginCredentials extends UserAccounts
{
    private $sender;
    private $err;
    private $loginCredentialsDL;
    private $userAccounts;
    private $activityLog;
    private $activityBO;

    /**
     */
    public function __construct()
    {
        parent::__construct();
        $this->sender=new Email();
        $this->err=new ErrorLog();
        $this->loginCredentialsDL=new LoginCredentialsDL();
        $this->userAccounts=new UserAccounts();
        $this->activityLog=new ActivityLog();
        $this->activityBO=new ActivityLogBO();
    }

    /**
     */
    function __destruct()
    {
        $this->sender=null;
        $this->err=null;
        $this->loginCredentialsDL=null;
        $this->userAccounts=null;
        $this->activityLog=null;
        $this->activityBO=null;
        parent::__destruct();
    }
    
    public function findUserAccountMatch(UserAccountBO $data):bool {
        if(isset($data))  {
            try {
                $data->setPasswordHash(md5($data->getPasswordHash()));
                $arr=$data->getArray();
                $status_message=$this->loginCredentialsDL->findLoginCredentialsMatch($arr);  
            } catch (\Exception $e) {
                $class_name="LoginCredentials";
                $method_name="findUserAccountMatch";
                $this->err->logErrors($e,null,$class_name, $method_name);
            }
            return $status_message;
        }
        else return false;
    }
    
    public function updateUserPassword(UserAccountBO $data):bool {
        if(isset($data)){           
            $data->setActionCode("0xA101");  //update password              
            $status_message=$this->userAccounts->updateUserAccount($data);
            if($status_message){
                $arr_data=array();
                $arr_data["taskArray2D"][0]["taskName"]="User Accounts";
                $arr_data["transactionName"]="Updating User Password for Staff Number:".$data->getAdminStaffNumber();
                $arr_data["activityAction"]=2;      //update
                $arr_data["staffNumber"]=$data->getAdminStaffNumber();
                $this->activityBO->set($arr_data);
                $this->activityLog->addActivityData($this->activityBO);
            }
            return $status_message;
        }
        else return false;
    }
    
    public function resetUserPassword(UserAccountBO $data):bool {
        if(isset($data)){            
            $arr=$this->userAccounts->listUserAccount($data);   //retrieve user profile data
            $data->set($arr);       //set UserAccount data object with this data
            $data->setActionCode("0xA102");  //reset password action code
            $status_message=$this->userAccounts->updateUserAccount($data);          
            return $status_message;
        }
        else return false;
    }
}


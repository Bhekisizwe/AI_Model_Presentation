<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\ {
    BusinessObjects\UserAccountBO,
    DataLayer\UserAccountDL,
    BusinessObjects\SystemLicenseBO
};
use UserClasses\BusinessObjects\ActivityLogBO;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserAccounts
{
    private $sender;
    private $err;
    private $userAccountDL;
    private $userAccountBO; 
    private $activityLog;
    private $activityBO;

    /**
     * @return Ambigous <NULL, \UserClasses\BusinessObjects\UserAccountBO>
     */
    public function getUserAccountBO()
    {
        return clone $this->userAccountBO;      
    }

    /**
     */
    public function __construct()
    {
        $this->sender=new Email();   
        $this->err=new ErrorLog();
        $this->userAccountDL=new UserAccountDL();
        $this->userAccountBO=new UserAccountBO();
        $this->activityLog=new ActivityLog();
        $this->activityBO=new ActivityLogBO();
    }

    /**
     */
    function __destruct()
    {
        $this->sender=null;
        $this->err=null;
        $this->userAccountDL=null;
        $this->userAccountBO=null;
        $this->activityLog=null;
        $this->activityBO=null;
    }
    
    public function listUserAccount(UserAccountBO $data):array {
        if(isset($data)){
            try {
                $arr=$data->getArray();
                $arr_result=$this->userAccountDL->searchData($arr);        
            } catch (\Exception $e) {
                $class_name="UserAccounts";
                $method_name="listUserAccount";
                $this->err->logErrors($e,null,$class_name, $method_name);
            } 
            return $arr_result;
        }
        else return NULL;
    }
    
    public function checkUserAccountExists(UserAccountBO $data):bool {
        if(isset($data)){
            try {
                $arr=$data->getArray();
                $status_message=$this->userAccountDL->dataExists($arr);
            } catch (\Exception $e) {
                $class_name="UserAccounts";
                $method_name="checkUserAccountExists";
                $this->err->logErrors($e,null,$class_name, $method_name);
            }
            return $status_message;
        }
        else return false;
    }
    
    public function addUserAccount(UserAccountBO $data):bool {
        if(isset($data)){
            try {
                $password_text=$this->generateRandomPassword(); //generate random password.                
                $data->setPasswordHash(md5($password_text));
                $arr=$data->getArray();
                if(!$this->checkIfActiveAccLimitReached()){
                    $status_message=$this->userAccountDL->create($arr);
                    if($status_message){
                        //Email new user
                        $arr["passwordHash"]=$password_text;
                        $arr_email=$this->generateEmailMessage($arr,"add");
                        $this->sender->sendEmail($arr_email);
                        $arr_data=array();
                        $arr_data["taskArray2D"][0]["taskName"]="User Accounts";
                        $arr_data["transactionName"]="Adding User Account to Database";
                        $arr_data["activityAction"]=1;      //create
                        $arr_data["staffNumber"]=$data->getAdminStaffNumber();
                        $this->activityBO->set($arr_data);
                        $this->activityLog->addActivityData($this->activityBO);
                    }
                    else throw new \Exception("Failed to Add user account!");
                }          
                else {                    
                    $err_arr=array();
                    $err_arr["errorDescription"]="License Limit Reached. Cannot add more active users than the license limit allows.";
                    $err_arr["errorCode"]="0x10";
                    $this->userAccountBO->setErrorAssocArray($err_arr);
                    throw new \Exception("License Limit Reached");
                }
            } catch (\Exception $e) {
                $status_message=false;
                $class_name="UserAccounts";
                $method_name="addUserAccount";
                $this->err->logErrors($e,null,$class_name, $method_name);
            } 
            return $status_message;
        }
        else return false;
    }
    
    public function updateUserAccount(UserAccountBO $data):bool {
        if(isset($data)){
            try {
                if($data->getActionCode()=="0xA100"){
                    //updates User Profile Only. Passwords are copied as is.
                    if(($data->getDataExistsStatus() || $data->getAccountState()==0) || !$this->checkIfActiveAccLimitReached()){
                        $arr=$data->getArray();
                        $status_message=$this->userAccountDL->update($arr);
                        if($status_message){
                            //Email user notifying them of updated profile
                            $arr["passwordText"]="";
                            $arr_email=$this->generateEmailMessage($arr,"update");
                            $this->sender->sendEmail($arr_email);
                            $arr_data=array();
                            $arr_data["taskArray2D"][0]["taskName"]="User Accounts";
                            $arr_data["transactionName"]="Updating User Account in Database";
                            $arr_data["activityAction"]=2;      //update
                            $arr_data["staffNumber"]=$data->getAdminStaffNumber();
                            $this->activityBO->set($arr_data);
                            $this->activityLog->addActivityData($this->activityBO);
                        }
                        else throw new \Exception("Failed to update user account!");
                    }
                    else{
                        $err_arr=array();
                        $err_arr["errorDescription"]="License Limit Reached. Cannot add more active users than the license limit allows.";
                        $err_arr["errorCode"]="0x10";
                        $this->userAccountBO->setErrorAssocArray($err_arr);
                        throw new \Exception("License Limit Reached");
                    }
                } 
                if($data->getActionCode()=="0xA101"){
                    //updates passwords only. Passwords are digested using MD5 Hash Algorithm
                    $password_text=$data->getPasswordHash();  //get unecrypted password.
                    $data->setPasswordHash(md5($password_text)); //reset password as encrypted.
                    $arr=$data->getArray();
                    $status_message=$this->userAccountDL->update($arr);
                    if($status_message){
                        //Email user notifying them of updated password
                        $arr["passwordText"]=$password_text;
                        $arr_email=$this->generateEmailMessage($arr,"update");
                        $this->sender->sendEmail($arr_email);
                    }
                    else throw new \Exception("Failed to update password!");
                }
                if($data->getActionCode()=="0xA102"){
                    //resets password only. Passwords are reset and digested using MD5 Hash Algorithm
                    $password_text=$this->generateRandomPassword();  //generate unecrypted password.
                    $data->setPasswordHash(md5($password_text)); //reset password as encrypted.
                    $arr=$data->getArray();
                    $status_message=$this->userAccountDL->update($arr);
                    if($status_message){
                        //Email user notifying them of password reset
                        $arr["passwordText"]=$password_text;
                        $arr_email=$this->generateEmailMessage($arr,"update");
                        $this->sender->sendEmail($arr_email);
                    }
                    else throw new \Exception("Failed to reset password!");
                }
            } catch (\Exception $e) { 
                $status_message=false;
                $class_name="UserAccounts";
                $method_name="updateUserAccount";
                $this->err->logErrors($e,null,$class_name, $method_name);
            }  
            return $status_message;
        }
        else return false;
    }
    
    protected function checkIfActiveAccLimitReached():bool {
        $license=new License();        
        $arr=$license->listLicenseData(new SystemLicenseBO());  //retrieve license data        
        try {
            $num_of_active_accounts=$this->userAccountDL->searchForActiveAccounts($arr);
            ($num_of_active_accounts>=$arr["licenseLimit"])?$status_message=true:$status_message=false;
        } catch (\Exception $e) {   
            $status_message=false;
            $class_name="UserAccounts";
            $method_name="checkIfActiveAccLimitReached";
            $this->err->logErrors($e,null,$class_name, $method_name);
        }        
        finally {
            unset($license);
        }
        return $status_message;        
    }
    
    protected function generateRandomPassword():string {
        $arr_alphabet=range("A","Z");   //create a Capital letter Alphabetical array
        $arr_digits=range(1,9);         //create array of digits 1 through 9
        $random_password="";
        for($i=0;$i<10;$i++){
            ($i<=4)?$random_password.=$arr_alphabet[rand(0,25)]:$random_password.=$arr_digits[rand(0,8)];            
        }
        return $random_password;
    }  
    
    protected function generateEmailMessage(array $arr=null,string $action):array {
        //generate email array
        if(isset($arr)){
            $arr_email=array();
            switch($action){
                case "add":                    
                    $arr_email["to"]=$arr["emailAddress"];
                    $arr_email["subject"]="Your System Login Credentials";                    
                    $body="Dear ".$arr["name"]." ".$arr["surname"]."<p>";
                    $body.="Your user account has been successfully created by the Administrator.<br>";
                    $body.="Please access the maintenance system website at <a href='https://rmssoft.co.za/html' target='_new'>Rail Maintenance System</a>.<br>";  
                    $body.="Your login credentials for this website are as follows:<p>";
                    $body.="Username (Staff Number): <b>".$arr["staffNumber"]."</b><br>";
                    $body.="Password: <b>".$arr["passwordHash"]."</b><p>";
                    $body.="Please login into the website and update your login password!<p>";
                    $body.=" By Order (Gqunsu Engineering Pty Ltd)";
                    $arr_email["body"]=$body;                   
                    break;
                case "update":
                    $arr_email["to"]=$arr["emailAddress"];
                    $body="Dear ".$arr["name"]." ".$arr["surname"]."<p>";
                    switch($arr["actionCode"]){
                        case "0xA100":
                            $arr_email["subject"]="Your Profile Details Have Been Updated";
                            $body.="Your user account details have been successfully updated by the Administrator.<br>";
                            $body.="Please access the maintenance system website at <a href='https://rmssoft.co.za/html' target='_new'>Rail Maintenance System</a>.<br>";
                            $body.="Please login into the website and view the changes to your profile.<p>";
                            break;
                        case "0xA101":
                            $arr_email["subject"]="Your Password Has Been Updated";
                            $body.="Your password has been successfully updated in the system.<br>";
                            $body.="Please access the maintenance system website at <a href='https://rmssoft.co.za/html' target='_new'>Rail Maintenance System</a>.<br>";
                            $body.="Login into the website to test your new password which is stated below:<p>";
                            $body.="New Password: <b>".$arr["passwordText"]."</b><p>";
                            break;
                        case "0xA102":
                            $arr_email["subject"]="Your Password Has Been Reset";
                            $body.="Your password has been successfully reset in the system.<br>";
                            $body.="Please access the maintenance system website at <a href='https://rmssoft.co.za/html' target='_new'>Rail Maintenance System</a>.<br>";
                            $body.="Login into the website to update your password which is stated below:<p>";
                            $body.="New Password: <b>".$arr["passwordText"]."</b><p>";
                    }                    
                    $body.=" By Order (Gqunsu Engineering Pty Ltd)";
                    $arr_email["body"]=$body;                    
            }
            return $arr_email; 
        }
        else return NULL;       
    }   
}


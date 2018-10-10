<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\ {
    BusinessObjects\SystemLicenseBO,
    DataLayer\SystemLicenseDL
};

use PHPUnit\Util\ErrorHandler;
use UserClasses\BusinessObjects\ActivityLogBO;

/**
 *
 * @author Bheki Mthethwa
 *
 */
class License
{
    private $sender;
    private $activityLog;
    private $activityBO;
    
    /**
     */
    public function __construct()
    {
        $this->sender=new Email();
        $this->activityLog=new ActivityLog();
        $this->activityBO=new ActivityLogBO();
    }
    
    public function __destruct(){
        $this->sender=null;
        $this->activityLog=null;
        $this->activityBO=null;
    }
    
    //return: Array of SystemLicenseBO objects
    public function listLicenseData(SystemLicenseBO $data=null):array {
        if(isset($data)) {
            $licenseDL=new SystemLicenseDL();
            $err_handler=new ErrorLog();
            $arr=$data->getArray(); //convert Object Into array
            try{
                $results_arr=$licenseDL->searchData($arr);
            }
            catch (\Exception $e){
                $class_name="License";
                $method_name="listLicenseData";
                //Log Exception here in errors.txt file
                $err_handler->logErrors($e,NULL,$class_name, $method_name);
            }
            finally{
                //destroy object
                unset($licenseDL);
                unset($err_handler);                
            }           
            return $results_arr;
        }
        else return NULL;
    }
    //return: Boolean
    public function checkLicenseExists(SystemLicenseBO $data=null):bool {
        if(isset($data)) {
            $licenseDL=new SystemLicenseDL();
            $err_handler=new ErrorLog();
            $arr=$data->getArray(); //convert Object Into array
            //print_r($arr);
            try{
                $status_message=$licenseDL->dataExists($arr);  //submit data for checking duplicate
            }
            catch (\Exception $e){
                $class_name="License";
                $method_name="checkLicenseExists";
                //Log Exception here in errors.txt file
                $err_handler->logErrors($e,NULL,$class_name, $method_name);
            }
            finally{
                //destroy object
                unset($licenseDL);
                unset($err_handler);
            }            
            return $status_message;
        }
        else return false;
    }
    //return: Boolean
    public function addLicense(SystemLicenseBO $data=null):bool {
        if(isset($data)) {
            $licenseDL=new SystemLicenseDL();
            $err_handler=new ErrorLog();
            $arr=$data->getArray(); //convert Object Into array
            try{
                $status_message=$licenseDL->create($arr);  //submit data for creation 
                if($status_message){
                    $arr_data=array();
                    $arr_data["taskArray2D"][0]["taskName"]="Super Admin Functions";
                    $arr_data["transactionName"]="Adding System License Information to Database";
                    $arr_data["activityAction"]=1;      //create
                    $arr_data["staffNumber"]=$data->getStaffNumber();
                    $this->activityBO->set($arr_data);
                    $this->activityLog->addActivityData($this->activityBO);                    
                }
            }
            catch (\Exception $e){
                $class_name="License";
                $method_name="addLicense";
                //Log Exception here in errors.txt file
                $err_handler->logErrors($e,NULL,$class_name, $method_name);
            }
            finally{
                //destroy object
                unset($licenseDL);
                unset($err_handler);
            }            
            //print_r($arr);                      
            return $status_message;
        }
        else return false;
    }
    //return: Boolean
    public function updateLicense(SystemLicenseBO $data=null):bool {
        if(isset($data)) {
            $licenseDL=new SystemLicenseDL();
            $err_handler=new ErrorLog();
            $arr=$data->getArray(); //convert Object Into array
            try{
                $status_message=$licenseDL->update($arr);  //submit data for updating
                if($status_message){
                    $arr_email=$this->generateEmailMessage();
                    $this->sender->sendEmail($arr_email);
                    $arr_data=array();
                    $arr_data["taskArray2D"][0]["taskName"]="Super Admin Functions";
                    $arr_data["transactionName"]="Updating System License Information in Database";
                    $arr_data["activityAction"]=2;      //update
                    $arr_data["staffNumber"]=$data->getStaffNumber();
                    $this->activityBO->set($arr_data);
                    $this->activityLog->addActivityData($this->activityBO); 
                }
                else {
                    throw new \Exception("Update of system license information was unsuccessful");
                }
            }
            catch (\Exception $e){
                $class_name="License";
                $method_name="updateLicense";
                //Log Exception here in errors.txt file
                $err_handler->logErrors($e,NULL,$class_name, $method_name);
            }
            finally{
                //destroy object
                unset($licenseDL);
                unset($err_handler);
            }
            //print_r($arr);            
            return $status_message;
        }
        else return false;
    }
    //return: Array of Email message to be sent
    private function generateEmailMessage():array {
        //generate email array
        $arr_email=array();
        $arr_email["to"]="bmthethwa@gqunsueng.co.za";
        $arr_email["subject"]="License Details Alert";
        //extract company name
        $data=new SystemLicenseBO();
        $arr_results=$this->listLicenseData($data);
        $body="Dear Super Administrator<p>";
        $body.="The licensing information, from ".$arr_results["companyName"]." based in";
        $body.=" ".$arr_results["postalAddressArray"]["surburb"]." in ".$arr_results["postalAddressArray"]["city"]." has been updated<p>";
        $body.=" By Order (Gqunsu Engineering Pty Ltd)";
        $arr_email["body"]=$body;
        unset($data);
        return $arr_email;
    }    
}




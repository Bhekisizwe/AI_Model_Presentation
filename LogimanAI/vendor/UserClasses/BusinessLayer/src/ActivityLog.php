<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\ {
    BusinessObjects\ActivityLogBO,
    DataLayer\ActivityLogDL,
    BusinessObjects\UserAccountBO
};
use Mpdf\Mpdf;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class ActivityLog
{
    private $sender;
    private $err;
    private $dirname;
    private $activityLogDL;   
    private $userAccountBO;

    /**
     */
    public function __construct()
    {
        $this->sender=new Email();
        $this->err=new ErrorLog();
        $this->activityLogDL=new ActivityLogDL();        
        $this->userAccountBO=new UserAccountBO();
        $this->dirname=__DIR__."\\..\\..\\..\\..\\ActivityLog";
    }

    /**
     */
    function __destruct()
    {
        $this->sender=null;
        $this->err=null;
        $this->activityLogDL=null;        
        $this->userAccountBO=null;
    }
    
    public function searchForActivityReports(ActivityLogBO $data):array {
        if(isset($data)){
            try {
                //$arr_2D=array();
                $arr=$data->getArray();
                $arr_2D=$this->activityLogDL->searchData($arr);
            } catch (\Exception $e) {
                $class_name="ActivityLog";
                $method_name="searchForActivityReports";
                $this->err->logErrors($e,null,$class_name, $method_name);
            } 
            return $arr_2D;
        }
        else return NULL;
    }
    
    public function listAllActivityNames():array {        
    try {            
            $arr_2D=$this->activityLogDL->retrieveAllActivityTasks();
        } catch (\Exception $e) {
            $class_name="ActivityLog";
            $method_name="listAllActivityNames";
            $this->err->logErrors($e,null,$class_name, $method_name);
        }
        return $arr_2D;            
    }
    
    public function addActivityData(ActivityLogBO $data):bool {
        if(isset($data)){
            try {
                $user_arr=array();
                $user_arr["staffNumber"]=$data->getStaffNumber();
                $this->userAccountBO->set($user_arr);
                $userAccounts=new UserAccounts();
                $arr_results=$userAccounts->listUserAccount($this->userAccountBO);
                $data->setModifiedBy($arr_results["accountID"]);
                $arr=$data->getArray();
                $status_message=$this->activityLogDL->create($arr);
                if(!$status_message) throw new \Exception("Failed to add an Activity event to the database");
            } catch (\Exception $e) {
                $status_message=false;
                $class_name="ActivityLog";
                $method_name="addActivityData";
                $this->err->logErrors($e,null,$class_name, $method_name);
            }
            finally {
                unset($userAccounts);
            }
            return $status_message;
        }
        else return false;
    }
    
    private function deleteFile(string $filename):bool {
        ($filename!="")?$status=unlink($this->dirname."\\".$filename):$status=false;
        return $status;
    }
    
    private function checkFileExists(string $filename):bool {
        ($filename!="")?$status=file_exists($this->dirname."\\".$filename):$status=false;
        return $status;
    }
    
    private function checkDirectoryExists():bool {
        return file_exists($this->dirname);    
    }
    
    private function createDirectory():bool {
        return mkdir($this->dirname);   
    }
    
    public function generatePDFActivityReport(ActivityLogBO $data):bool {
        if(isset($data)){
            $arr=array();
            $userAccountBO=new UserAccountBO();
            $arr[0]="";
            $arr[1]="Created";
            $arr[2]="Updated";
            $arr[3]="Deleted";
            $filename=$data->getStaffNumber().".pdf";
            //First check if Directory Exists, create it if necessary    
            if(!$this->checkDirectoryExists()) $this->createDirectory();
            //Check if file exists in Directory and delete it
            if($this->checkFileExists($filename)) $this->deleteFile($filename);
            //retrive data to use to generate report
            $arr_2D=$this->searchForActivityReports($data);
            //create HTML code using this data
            $html_string="<html><head><title></title></head><body>";
            $html_string.="<h3>This is the Activity Logs Report for the period ".$data->getStartDate()." to ".$data->getEndDate()."</h3>";
            $html_string.="<table align='center' width='100%'>";
            $html_string.="<thead><tr><th>Transaction Name</th><th>Activity Action</th><th>Task Name</th>";
            $html_string.="<th>Modification Timestamp</th><th>Modified by</th><th>Staff Number</th></tr></thead>";
            for($i=0;$i<count($arr_2D);$i++){
                $html_string.="<tr><td>".$arr_2D[$i]["transactionName"]."</td><td>".$arr[$arr_2D[$i]["activityAction"]]."</td>";
                $html_string.="<td>".$arr_2D[$i]["taskArray2D"][0]["taskName"]."</td><td>".$arr_2D[$i]["dateModified"]." ".$arr_2D[$i]["timeModified"]."</td>";
                $html_string.="<td>".$arr_2D[$i]["name"]." ".$arr_2D[$i]["surname"]."</td><td>".$arr_2D[$i]["staffNumber"]."</td></tr>";
            }               
            $html_string.="</table></body></html>";
            //pass HTML to mPDF class for PDF conversion
            try {
                $mpdf=new Mpdf();
                $stylesheet=file_get_contents($this->dirname."\\..\\styles\\styling.css");
                $mpdf->WriteHTML($stylesheet,1);
                $mpdf->WriteHTML(utf8_encode($html_string),2);
                $actual_file_name=$this->dirname."\\".$filename;
                //write PDF file to disk
                $mpdf->Output($actual_file_name,\Mpdf\Output\Destination::FILE);
                //Email PDF file to user.
                if($this->checkFileExists($filename)){
                    //if file exists on disk, email the user                    
                    $userAccountBO->setStaffNumber($data->getStaffNumber());
                    $arr_data=$this->getEmailRecepient($userAccountBO);
                    $arr_email=$this->generateEmailMessage($arr_data);
                    $this->sender->sendEmail($arr_email);
                }
                else throw new \Exception("Cannot email a file that does not exist");
            } catch (\Exception $e) {
                $class_name="ActivityLog";
                $method_name="generatePDFActivityReport";
                $this->err->logErrors($e,null,$class_name, $method_name);
            }
            finally{
                unset($mpdf);
                unset($userAccountBO);
            }
            return true;
        }else return false;
    }
    
    private function getEmailRecepient(UserAccountBO $data):array {        
        if(isset($data)){
            $userAccounts=new UserAccounts();
            //extract email address using staffNumber of user.            
            $arr_result=$userAccounts->listUserAccount($data);
            unset($userAccounts);
            return $arr_result;
        }
        else return NULL;
    }
    
    private function generateEmailMessage(array $arr):array {
        //generate email array
        $arr_email=array();
        $arr_email["to"]=$arr["emailAddress"];
        $arr_email["subject"]="Activity Log Report";       
        $body="Dear Sir/Madam<p>";
        $body.="Please find attached the requested ActivityLog report. <p>";        
        $body.=" By Order (Gqunsu Engineering Pty Ltd)";
        $arr_email["body"]=$body;
        $arr_email["file"]=$this->dirname."\\".$arr["staffNumber"].".pdf";
        unset($data);
        return $arr_email;
    }
}


<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;
use Exception;
use Error;
/**
 *
 * @author Bheki Mthethwa
 *
 */
class ErrorLog
{
    private $dir_path;
    
    public function __construct(){
        //Construct object by initialising the class attributes
        $this->dir_path=__DIR__."\\..\\..\\..\\..\\err\\errors.txt";
    }
    
    //check if File exists
    //return Boolean
    private function doesLogFileExist(string $filepath):bool {
        return file_exists($filepath);
    }
    
    //create Error Log file
    //return void
    private function createLogFile(string $filepath) {
        if(!file_exists($filepath)){
            $file_pointer=fopen($filepath,'w');  //create file
            fclose($file_pointer);  //close file
        }
    }
    
    //log the software errors and exceptions inside the Log file
    //return void
    public function logErrors(Exception $e=NULL,Error $err=NULL,string $class_name,string $method_name) {
        if(!$this->doesLogFileExist($this->dir_path)){
            //file does not exist so we need to create the file
            $this->createLogFile($this->dir_path);
        }
        //determine Date and Time of error based on South African TimeZone
        $dateObj=new \DateTime("now",new \DateTimeZone("Africa/Harare"));
        $date_time_str=$dateObj->format("Y-m-d H:i:s");
        //Open the error log file for appending errors
        $file_point=fopen($this->dir_path,'a');
        
        if(isset($e)){
            //Log Exceptions here
            $lineNo=$this->determineNextLineNumber();   //determine next line number
            $str_err=$lineNo.".) Oops, error in Class";
            $str_err.=" ".$class_name." within method ".$method_name." has occured at ".$date_time_str;
            $str_err.=" .Exception message is ".$e->getMessage()."\r\n ####End of Line#### \r\n";
            fwrite($file_point,$str_err);
        }
        if(isset($err)){
            //Log errors here
            $lineNo=$this->determineNextLineNumber();  //determine next line number
            $str_err=$lineNo.".) Oops, error in Class";
            $str_err.=" ".$class_name." within method ".$method_name." has occured at ".$date_time_str;
            $str_err.=" .Error message is ".$err->getMessage()."\r\n ####End of Line#### \r\n";
            fwrite($file_point,$str_err);
        }
        //close error logs file
        fclose($file_point);
    }
    
    private function determineNextLineNumber():int {
        file_exists($this->dir_path)?$lineNo=(count(file($this->dir_path))/2)+1:$lineNo=1;
        return (int) $lineNo;
    }
}


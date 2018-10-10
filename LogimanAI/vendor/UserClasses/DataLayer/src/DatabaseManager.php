<?php
namespace UserClasses\DataLayer;

use UserClasses\BusinessLayer\ErrorLog;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
abstract class DatabaseManager
{
    private $host="127.0.0.1";
    private $user="wheel";
    private $passwd="induction";
    private $dbname="logimanai";
    
    public function dbConnect(){
        $connector=new \mysqli($this->host,$this->user,$this->passwd,$this->dbname); 
        if(isset($connector->connect_error)){
            try{
                throw new Error("failed to connect to MySQL Database");    
            }
            catch(\Error $err){
                $class_name="DatabaseManager";
                $method_name="dbConnect";
                $err_handler=new ErrorLog();
                //log error
                $err_handler->logErrors(NULL,$err,$class_name,$method_name);                
            }
            finally{
                unset($err_handler);
            }
            return NULL;
        }
        else return $connector;
    }
    
    public function dbClose($connector){
        if($connector instanceof \mysqli) $connector->close();    
    }
}


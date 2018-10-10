<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use UserClasses\BusinessObjects\UserAccountBO;
use UserClasses\DataLayer\AdminAccountDL;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class AdminAccounts extends UserAccounts
{
    private $sender;
    private $err;
    private $adminAccountDL;
    
    /**
     */
    public function __construct()
    {        
        parent::__construct();
        $this->sender=new Email();
        $this->err=new ErrorLog();
        $this->adminAccountDL=new AdminAccountDL();
        
    }

    /**
     */
    function __destruct()
    {
        $this->sender=null;
        $this->err=null;
        $this->adminAccountDL=null;
        parent::__destruct();
    }   
    
    public function listAllAdminAccounts():array {        
        try {
            $arr=$this->adminAccountDL->retrieveAllAdmin();
        } catch (\Exception $e) {
            $class_name="AdminAccounts";
            $method_name="listAllAdminAccounts";
            $this->err->logErrors($e,null,$class_name, $method_name);
        }
        return $arr;
    }   
}


<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class ManageSession
{
    private $sessionLimit=1800;  //delete session if user is inactive for more than 5 minutes
    
    /**
     * @param number $sessionLimit
     */
    public function setSessionLimit($sessionLimit)
    {
        $this->sessionLimit = $sessionLimit;
    }

    public function determineSessionValidity(int $currentTime=0){
        if($currentTime>0){
            //calculate time between sessions  
            if(($currentTime-$_SESSION["lastActive"])>$this->sessionLimit){
                $this->destroySession();
            }
        }
        else {
            //do nothing
        }
    }
    
    private function destroySession(){
        session_unset();
        session_destroy();
    }
}


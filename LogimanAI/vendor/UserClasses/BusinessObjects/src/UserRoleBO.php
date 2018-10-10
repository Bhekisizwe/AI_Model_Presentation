<?php
declare(strict_types=1);
namespace UserClasses\BusinessObjects;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserRoleBO extends BusinessObject
{
    private $userRole2DArray;
    private $activityRights2DArray;
    private $columnVisibility2DArray;
    private $staffNumber;
    
    /**
     * @return mixed
     */
    public function getStaffNumber()
    {
        return $this->staffNumber;
    }

    /**
     * @param mixed $staffNumber
     */
    public function setStaffNumber($staffNumber)
    {
        $this->staffNumber = $staffNumber;
    }

    public function __construct(){
        $this->setObjectNumOfFields(8);
    }
    
    /**
     * @return mixed
     */
    public function getUserRole2DArray()
    {
        return $this->userRole2DArray;
    }

    /**
     * @return mixed
     */
    public function getActivityRights2DArray()
    {
        return $this->activityRights2DArray;
    }

    /**
     * @return mixed
     */
    public function getColumnVisibility2DArray()
    {
        return $this->columnVisibility2DArray;
    }

    /**
     * @param mixed $userRole2DArray
     */
    public function setUserRole2DArray($userRole2DArray)
    {
        $this->userRole2DArray = $userRole2DArray;
    }

    /**
     * @param mixed $activityRights2DArray
     */
    public function setActivityRights2DArray($activityRights2DArray)
    {
        $this->activityRights2DArray = $activityRights2DArray;
    }

    /**
     * @param mixed $columnVisibility2DArray
     */
    public function setColumnVisibility2DArray($columnVisibility2DArray)
    {
        $this->columnVisibility2DArray = $columnVisibility2DArray;
    }

    public function set(array $data){
        
        foreach($data as $key => $value){
            //make the Key case insensitive by making the string comparison to be lower case
            $key_lower=strtolower($key);
            switch($key_lower){  
                case "staffnumber":
                    $this->staffNumber=$value;
                    break;
                case "userrole2darray":
                    $this->userRole2DArray=$value;
                    break;
                case "activityrights2darray":
                    $this->activityRights2DArray=$value;
                    break;
                case "columnvisibility2darray":
                    $this->columnVisibility2DArray=$value;
                    break;
                case "transactionstatus":
                    $this->transactionStatus=$value;
                    break;
                case "actioncode":
                    $this->actionCode=$value;
                    break;
                case "errorassocarray":
                    $this->errorAssocArray=$value;
                    break;
                case "dataexistsstatus":
                    $this->dataExistsStatus=$value;
            }
        }
    }
    //override the getArray method in Parent class
    //return: Array
    public function getArray():array {
        //create array to contain business objects data
        $arr=array();
        $i=0;   //count of number of Object parameters read into array.
        foreach($this as $key => $value){
            if($i<$this->getObjectNumOfFields()){
                $arr[$key]=$value;
                $i++;
            }
        }
        return $arr;
    }
    //override the getArray method in Parent class
    public function setObjectParameters(array $arr) {
        $i=0;   //counter of number of objects read from array into object
        foreach ($this as $key => &$value){
            if($i<$this->getObjectNumOfFields()){
                $value=$arr[$key];
                $i++;
            }
        }
    }
    
    
    
}


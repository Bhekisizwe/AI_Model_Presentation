<?php
declare(strict_types=1);
namespace UserClasses\BusinessObjects;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class ActivityLogBO extends BusinessObject
{
    private $logID=0;
    private $taskArray2D;
    private $transactionName="";
    private $activityAction=0;
    private $modifiedBy=0;
    private $dateModified="";
    private $timeModified="";
    private $startDate="";
    private $endDate="";
    private $name="";
    private $surname="";
    private $staffNumber="";
    
    /**
     */
    public function __construct()
    {
        $this->setObjectNumOfFields(16);
    }

    /**
     * @return number
     */
    public function getLogID()
    {
        return $this->logID;
    }

    /**
     * @return mixed
     */
    public function getTaskArray2D()
    {
        return $this->taskArray2D;
    }

    /**
     * @return string
     */
    public function getTransactionName()
    {
        return $this->transactionName;
    }

    /**
     * @return number
     */
    public function getActivityAction()
    {
        return $this->activityAction;
    }

    /**
     * @return number
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @return string
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @return string
     */
    public function getTimeModified()
    {
        return $this->timeModified;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getStaffNumber()
    {
        return $this->staffNumber;
    }

    /**
     * @param number $logID
     */
    public function setLogID($logID)
    {
        $this->logID = $logID;
    }

    /**
     * @param mixed $taskArray2D
     */
    public function setTaskArray2D($taskArray2D)
    {
        $this->taskArray2D = $taskArray2D;
    }

    /**
     * @param string $transactionName
     */
    public function setTransactionName($transactionName)
    {
        $this->transactionName = $transactionName;
    }

    /**
     * @param number $activityAction
     */
    public function setActivityAction($activityAction)
    {
        $this->activityAction = $activityAction;
    }

    /**
     * @param number $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @param string $dateModified
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * @param string $timeModified
     */
    public function setTimeModified($timeModified)
    {
        $this->timeModified = $timeModified;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @param string $staffNumber
     */
    public function setStaffNumber($staffNumber)
    {
        $this->staffNumber = $staffNumber;
    }
    
    public function set(array $data){
        
        foreach($data as $key => $value){
            //make the Key case insensitive by making the string comparison to be lower case
            $key_lower=strtolower($key);
            switch($key_lower){
                case "logid":
                    $this->logID=$value;
                    break;
                case "taskarray2d":
                    $this->taskArray2D=$value;
                    break;
                case "transactionname":
                    $this->transactionName=$value;
                    break;
                case "activityaction":
                    $this->activityAction=$value;
                    break;
                case "modifiedby":
                    $this->modifiedBy=$value;
                    break;
                case "datemodified":
                    $this->dateModified=$value;
                    break;
                case "timemodified":
                    $this->timeModified=$value;
                    break;
                case "startdate":
                    $this->startDate=$value;
                    break;
                case "enddate":
                    $this->endDate=$value;
                    break;
                case "name":
                    $this->name=$value;
                    break;
                case "surname":
                    $this->surname=$value;
                    break;
                case "staffnumber":
                    $this->staffNumber=$value;
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


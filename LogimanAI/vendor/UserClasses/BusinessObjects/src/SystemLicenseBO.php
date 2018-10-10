<?php
declare(strict_types=1);
namespace UserClasses\BusinessObjects;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class SystemLicenseBO extends BusinessObject
{
       
    private $licenseID; //Primary Key from database    
    private $companyName;   //Name of client's company
    private $postalAddressArray;    //associative array containing the full address of the client
    private $licenseLimit;      //Maximum number of active users the specific license allows in the system
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
        $this->setObjectNumOfFields(9);    
    }
    
    public function set(array $data){
        
        foreach($data as $key => $value){
            //make the Key case insensitive by making the string comparison to be lower case
            $key_lower=strtolower($key);
            switch($key_lower){
                case "staffnumber":
                    $this->staffNumber=$value;
                    break;
                case "licenseid":
                    $this->licenseID=$value;
                    break;
                case "companyname":
                    $this->companyName=$value;
                    break;
                case "postaladdressarray":
                    $this->postalAddressArray=$value;
                    break;
                case "licenselimit":
                case "licensetype":
                    $this->licenseLimit=$value;
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
    /**
     * @return mixed
     */
    public function getLicenseID()
    {
        return $this->licenseID;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {        
        return $this->companyName;
    }

    /**
     * @return mixed
     */
    public function getPostalAddressArray()
    {
        return $this->postalAddressArray;
    }

    /**
     * @return mixed
     */
    public function getLicenseLimit()
    {
        return $this->licenseLimit;
    }   

    /**
     * @param mixed $licenseID
     */
    public function setLicenseID($licenseID)
    {
        $this->licenseID = $licenseID;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $postalAddressArray
     */
    public function setPostalAddressArray($postalAddressArray)
    {
        $this->postalAddressArray = $postalAddressArray;
    }

    /**
     * @param mixed $licenseLimit
     */
    public function setLicenseLimit($licenseLimit)
    {
        $this->licenseLimit = $licenseLimit;
    }
    
    
}


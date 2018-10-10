<?php
declare(strict_types=1);
namespace UserClasses\BusinessObjects;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserAccountBO extends BusinessObject
{
    private $accountID=0;
    private $roleID=0;
    private $name="";
    private $surname="";
    private $staffNumber="";
    private $emailAddress="";
    private $accountState=0;    //false
    private $passwordHash="";   //Message Digested or Hashed Password
    private $userRoleName="";
    private $adminStaffNumber;
    
    /**
     * @return mixed
     */
    public function getAdminStaffNumber()
    {
        return $this->adminStaffNumber;
    }

    /**
     * @param mixed $adminStaffNumber
     */
    public function setAdminStaffNumber($adminStaffNumber)
    {
        $this->adminStaffNumber = $adminStaffNumber;
    }

    /**
     */
    public function __construct()
    {
        $this->setObjectNumOfFields(14);
    }

    /**
     * @return number
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @return number
     */
    public function getRoleID()
    {
        return $this->roleID;
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
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return number
     */
    public function getAccountState()
    {
        return $this->accountState;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getUserRoleName()
    {
        return $this->userRoleName;
    }

    /**
     * @param number $accountID
     */
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }

    /**
     * @param number $roleID
     */
    public function setRoleID($roleID)
    {
        $this->roleID = $roleID;
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

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param number $accountState
     */
    public function setAccountState($accountState)
    {
        $this->accountState = $accountState;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @param string $userRoleName
     */
    public function setUserRoleName($userRoleName)
    {
        $this->userRoleName = $userRoleName;
    }
    
    public function set(array $data){
        
        foreach($data as $key => $value){
            //make the Key case insensitive by making the string comparison to be lower case
            $key_lower=strtolower($key);
            switch($key_lower){
                case "adminstaffnumber":
                    $this->adminStaffNumber=$value;
                    break;
                case "accountid":
                    $this->accountID=$value;
                    break;
                case "roleid":
                    $this->roleID=$value;
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
                case "emailaddress":
                    $this->emailAddress=$value;
                    break;
                case "accountstate":
                    $this->accountState=$value;
                    break;
                case "passwordhash":
                    $this->passwordHash=$value;
                    break;
                case "userrolename":
                    $this->userRoleName=$value;
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


<?php
declare(strict_types=1);
namespace UserClasses\BusinessObjects;

/**
 *
 * @author Bheki Mthethwa
 *
 */
abstract class BusinessObject
{
    //status of request processing
    protected $transactionStatus=False;
    //associative array of errors
    protected $errorAssocArray;
    //action code sent from the client {Edit or Add}
    protected $actionCode;
    //Flag signifying duplicate data
    protected $dataExistsStatus=false; 
    //Number of fields in Object
    private $objectNumOfFields=8;
        
    /**
     * @param number $objectNumOfFields
     */
    protected function setObjectNumOfFields($objectNumOfFields)
    {
        $this->objectNumOfFields = $objectNumOfFields;
    }

    /**
     * @return number
     */
    public function getObjectNumOfFields()
    {
        return $this->objectNumOfFields;
    }

    /**
     * @return boolean
     */
    public function getDataExistsStatus()
    {
        return $this->dataExistsStatus;
    }

    /**
     * @param boolean $dataExistsStatus
     */
    public function setDataExistsStatus($dataExistsStatus)
    {
        $this->dataExistsStatus = $dataExistsStatus;
    }

    /**
     * @return boolean
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * @return mixed
     */
    public function getErrorAssocArray()
    {
        return $this->errorAssocArray;
    }

    /**
     * @return mixed
     */
    public function getActionCode()
    {
        return $this->actionCode;
    }

    /**
     * @param boolean $transactionStatus
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     * @param mixed $errorAssocArray
     */
    public function setErrorAssocArray($errorAssocArray)
    {
        $this->errorAssocArray = $errorAssocArray;
    }

    /**
     * @param mixed $actionCode
     */
    public function setActionCode($actionCode)
    {
        $this->actionCode = $actionCode;
    }

    public function getArray():array{
        
    }
    
    public function setObjectParameters(array $arr) {
        
    }
}




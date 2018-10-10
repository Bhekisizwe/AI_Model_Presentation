<?php
declare(strict_types=1);
namespace UserClasses\DataLayer;



/**
 *
 * @author Bheki Mthethwa
 *        
 */
class SystemLicenseDL extends DatabaseManager implements DatabaseFunctionsInt
{

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::getPrimaryKey()
     */
    public function getPrimaryKey(array $data):int
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE License Data from Database*************/
            $query="SELECT * FROM SystemLicense WHERE LicenseID>0 LIMIT 1";
            $result=$connector->query($query);
            if($result){
                if($result->num_rows>0){
                    $arr=array();   //create array to store the result set data from the database
                    while($rows=$result->fetch_assoc()){
                        $arr[]=$rows;   //append Associative array of $rows to the end of the array $arr
                    }
                    $this->dbClose($connector);
                    return (int) $arr[0]["LicenseID"];
                }
                else return (int) $result->num_rows;
            }
            else{
                $this->dbClose($connector);
                return 0;
            }
        }
        else return 0;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::create()
     */
    public function create(array $data):bool
    {        
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            //successfully connected to database
            /***INSERT License Data into Database*******/
            $query="INSERT INTO SystemLicense";
            $query.=" (LicenseID,CompanyName,AddressType,AddressLine1,Surburb,City,Country,PostalCode,LicenseType)";
            $query.=" VALUES ('',?,?,?,?,?,?,?,?)";
            //prepare SQL script for execution
            $stmt=$connector->prepare($query);
            //bind parameters
            $company=$data["companyName"];
            $addr_arr=$data["postalAddressArray"];
            $license_type=$data["licenseLimit"];
            $stmt->bind_param("sisssssi",$company,$addr_arr['AddressType'],$addr_arr['AddressLine1'],$addr_arr['Surburb'],$addr_arr['City'],$addr_arr['Country'],$addr_arr['PostalCode'],$license_type);
            $status_message=$stmt->execute();
            $this->dbClose($connector); //Close Database Connection
            return $status_message;
        } 
        else return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::update()
     */
    public function update(array $data):bool
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /***UPDATE License Data in Database*******/
            $query="UPDATE SystemLicense SET ";
            $query.="CompanyName=?,AddressType=?,AddressLine1=?,Surburb=?,City=?,Country=?,PostalCode=?,";
            $query.="LicenseType=? WHERE LicenseID=?";
            $stmt=$connector->prepare($query);
            //bind parameters
            $company=$data["companyName"];
            $addr_arr=$data["postalAddressArray"];
            $license_type=$data["licenseLimit"];
            $licenseID=$data["licenseID"];
            $stmt->bind_param("sisssssii",$company,$addr_arr['AddressType'],$addr_arr['AddressLine1'],$addr_arr['Surburb'],$addr_arr['City'],$addr_arr['Country'],$addr_arr['PostalCode'],$license_type,$licenseID);
            $status_message=$stmt->execute();
            $this->dbClose($connector); //Close Database Connection
            return $status_message;
        }
        else return false;        
    }

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::dataExists()
     */
    public function dataExists(array $data):bool
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*****Check if License Data already exists in Database*******/
            $query="SELECT * FROM SystemLicense";
            $result=$connector->query($query);            
            if($result){
                ($result->num_rows>0)?$status_message=true:$status_message=false; 
                $this->dbClose($connector);
                return $status_message;
            }
            else{
                $this->dbClose($connector);
                return false;
            }
        }
        else return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::delete()
     */
    public function delete(array $data):bool
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            $query="DELETE FROM SystemLicense WHERE LicenseID>0";
            $status_message=$connector->query($query);
            $this->dbClose($connector);
            return $status_message;
        }
        else return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::searchData()
     */
    public function searchData(array $data):array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE License Data from Database*************/
            $query="SELECT * FROM SystemLicense WHERE LicenseID>0 LIMIT 1";
            $result=$connector->query($query);
            if($result){
                $arr=array();   //create array to store the result set data from the database
                while($rows=$result->fetch_assoc()){
                    $arr["licenseID"]=$rows["LicenseID"];
                    $arr["companyName"]=$rows["CompanyName"];
                    $arr["postalAddressArray"]["addressLine1"]=$rows["AddressLine1"];
                    $arr["postalAddressArray"]["surburb"]=$rows["Surburb"];
                    $arr["postalAddressArray"]["city"]=$rows["City"];
                    $arr["postalAddressArray"]["country"]=$rows["Country"];
                    $arr["postalAddressArray"]["postalCode"]=$rows["PostalCode"];
                    $arr["licenseLimit"]=$rows["LicenseType"];
                }
                $this->dbClose($connector);
                return $arr;
            }
            else{
                $this->dbClose($connector);
                return NULL;
            }
        }
        else return NULL;
    }
}


<?php
declare(strict_types=1);
namespace UserClasses\DataLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserAccountDL extends DatabaseManager implements DatabaseFunctionsInt
{

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::getPrimaryKey()
     */
    public function getPrimaryKey(array $data):int
    {}

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
            /***INSERT User Accounts Data into Database*******/
            $query="INSERT INTO UserAccounts";
            $query.=" (AccountID,RoleID,Name,Surname,StaffNumber,Email,Password,ActivityState) ";
            $query.="VALUES ('',?,?,?,?,?,?,?)";
            //prepare SQL script for execution
            $stmt=$connector->prepare($query);
            //bind parameters
            $roleID=$data["roleID"];
            $name=$data["name"];
            $surname=$data["surname"];
            $staffNumber=$data["staffNumber"];
            $emailAddress=$data["emailAddress"];
            $passwordHash=$data["passwordHash"];
            $accountState=$data["accountState"];
            $stmt->bind_param("isssssi",$roleID,$name,$surname,$staffNumber,$emailAddress,$passwordHash,$accountState);
            $status_message=$stmt->execute();
            $this->dbClose($connector);
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
            /***UPDATE User Account Data in Database*******/
            $query="UPDATE UserAccounts SET ";
            $query.="RoleID=?,Name=?,Surname=?,Email=?,Password=?,ActivityState=? WHERE AccountID=?";           
            $stmt=$connector->prepare($query);
            //bind parameters
            $roleID=$data["roleID"];
            $name=$data["name"];
            $surname=$data["surname"];           
            $emailAddress=$data["emailAddress"];
            $passwordHash=$data["passwordHash"];
            $accountState=$data["accountState"];
            $accountID=$data["accountID"];
            $stmt->bind_param("issssii",$roleID,$name,$surname,$emailAddress,$passwordHash,$accountState,$accountID);
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
            /*****Check if StaffNumber already exists in Database*******/
            $query="SELECT RoleID FROM UserAccounts WHERE StaffNumber=?";
            $stmt=$connector->prepare($query);
            $staffNumber=$data["staffNumber"];
            $stmt->bind_param("s",$staffNumber);
            $status=$stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($roleid);
            if($status){
                ($stmt->num_rows>0)?$status_message=true:$status_message=false;
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
            $query="DELETE FROM UserAccounts WHERE AccountID>1";
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
            /*********RETRIEVE User Account profile Data from Database*************/
            $query="SELECT UserAccounts.AccountID,UserAccounts.Name,UserAccounts.Surname,";
            $query.="UserAccounts.StaffNumber,UserAccounts.Email,UserAccounts.Password,UserAccounts.ActivityState,";
            $query.="UserRole.RoleID,UserRole.UserRoleName FROM UserAccounts,UserRole";
            $query.=" WHERE UserAccounts.StaffNumber=? AND UserRole.RoleID=UserAccounts.RoleID LIMIT 1";            
            $stmt=$connector->prepare($query);
            $staffNumber=$data["staffNumber"];
            $stmt->bind_param("s",$staffNumber);
            $status=$stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($accountID,$name,$surname,$staffNumber,$email,$passwordHash,$accountState,$roleID,$userRoleName);
            if($status){
                $arr=array();   //array to store result set
                while($stmt->fetch()){
                    $arr["accountID"]=$accountID; 
                    $arr["roleID"]=$roleID;
                    $arr["name"]=$name;
                    $arr["surname"]=$surname;
                    $arr["staffNumber"]=$staffNumber;
                    $arr["emailAddress"]=$email;    
                    $arr["passwordHash"]=$passwordHash;
                    $arr["accountState"]=$accountState;
                    $arr["userRoleName"]=$userRoleName;
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
    
    public function searchForActiveAccounts(array $data):int
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE Number of Active User Account profile Data from Database*************/
            $query="SELECT AccountID FROM UserAccounts WHERE ActivityState=1";
            $result=$connector->query($query);
            if($result){                
                return (int) $result->num_rows;
            }
            else{
                $this->dbClose($connector);
                return NULL;
            }
        }
        else return NULL;
    }
    
    public function searchUsingRoleID(array $data):array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE User Accounts Under a specific User Role from Database*************/
            $query="SELECT * FROM UserAccounts WHERE RoleID=?";
            $stmt=$connector->prepare($query);
            $roleID=$data["roleID"];
            $stmt->bind_param("i",$roleID);
            $status=$stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($accountID,$roleid,$name,$surname,$staffNumber,$email,$passwordHash,$accountState);
            if($status){
                $arr_2D=array();   //array to store result set
                while($stmt->fetch()){
                    $arr["accountID"]=$accountID;
                    $arr["roleID"]=$roleid;
                    $arr["name"]=$name;
                    $arr["surname"]=$surname;
                    $arr["staffNumber"]=$staffNumber;
                    $arr["emailAddress"]=$email;
                    $arr["passwordHash"]=$passwordHash;
                    $arr["accountState"]=$accountState;
                    $arr_2D[]=$arr; //append rows to end of 2D Array
                }
                $this->dbClose($connector);
                return $arr_2D;
            }
            else{
                $this->dbClose($connector);
                return NULL;
            }
        }
        else return NULL;
    }
}


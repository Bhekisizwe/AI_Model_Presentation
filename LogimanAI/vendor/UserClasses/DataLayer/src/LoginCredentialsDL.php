<?php
declare(strict_types=1);
namespace UserClasses\DataLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class LoginCredentialsDL extends UserAccountDL
{
    public function findLoginCredentialsMatch(array $data):bool{
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*****Check if Login Details Pair exist in Database*******/
            $query="SELECT RoleID FROM UserAccounts WHERE StaffNumber=? AND Password=? AND ActivityState=1";
            $stmt=$connector->prepare($query);
            $staffNumber=$data["staffNumber"];
            $passwordHash=$data["passwordHash"];
            $stmt->bind_param("ss",$staffNumber,$passwordHash);
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
}


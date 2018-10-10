<?php
namespace UserClasses\DataLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class UserRoleDL extends DatabaseManager implements DatabaseFunctionsInt
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
            /*********RETRIEVE UserRole Data from Database*************/
            $query="SELECT * FROM UserRole WHERE RoleID>0 ORDER BY RoleID Desc LIMIT 1";
            $result=$connector->query($query);
            if($result){
                if($result->num_rows>0){
                    $arr=array();   //create array to store the result set data from the database
                    while($rows=$result->fetch_assoc()){
                        $arr[]=$rows;   //append Associative array of $rows to the end of the array $arr
                    }
                    $this->dbClose($connector);
                    return (int) $arr[0]["RoleID"];
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
            /***INSERT UserRole, Access Rights and/Or Column Visibility Data into Database*******/
            $query="INSERT INTO UserRole";
            $query.=" (RoleID,UserRoleName) VALUES ('',?)";            
            //prepare SQL script for execution
            $stmt=$connector->prepare($query);
            //bind parameters
            $userrolename=$data["userRole2DArray"][0]["userRoleName"];            
            $stmt->bind_param("s",$userrolename);
            $status_message=$stmt->execute();
            if($status_message){
                //successfully added the UserRole. Let us get the Primary Key of this last added role
                $roleID=$this->getPrimaryKey($data);
                //let us insert the Access Rights for this role
                $query="INSERT INTO AccessRights (AccessID,RoleID,ActivityID,ActivityRights)";
                $query.=" VALUES('',?,?,?)";
                //prepare SQL script for execution
                $stmt=$connector->prepare($query);
                //bind parameters                
                for($i=0;$i<count($data["activityRights2DArray"]);$i++){
                    //$i is the row counter
                    $activityID=$data["activityRights2DArray"][$i]["activityID"];
                    $activityRights=$data["activityRights2DArray"][$i]["activityRights"];
                    $stmt->bind_param("iis",$roleID,$activityID,$activityRights);
                    $status_message=$stmt->execute();
                } 
                //insert column visibility for this role if the data exists
                $query="INSERT INTO PlanningReportColumns (PlanningID,RoleID,ColumnID,ColumnVisibility)";
                $query.=" VALUES('',?,?,?)";
                //prepare SQL script for execution
                $stmt=$connector->prepare($query);
                //bind parameters
                for($j=0;$j<count($data["columnVisibility2DArray"]);$j++){
                    //$j is the row counter
                    $columnID=$data["columnVisibility2DArray"][$j]["columnID"];
                    $columnVisibility=$data["columnVisibility2DArray"][$j]["columnVisibility"];
                    $stmt->bind_param("iii",$roleID,$columnID,$columnVisibility);
                    $status_message=$stmt->execute();
                } 
            }
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
            /***UPDATE Access Rights and/Or Column Visibility in Database*******/
            $query="UPDATE AccessRights SET ActivityRights=? WHERE AccessID=?";                   
            $stmt=$connector->prepare($query);
            //bind parameters
            for($i=0;$i<count($data["activityRights2DArray"]);$i++){
                //$i is the row counter
                $accessID=$data["activityRights2DArray"][$i]["accessID"];
                $activityRights=$data["activityRights2DArray"][$i]["activityRights"];
                $stmt->bind_param("ss",$activityRights,$accessID);
                $status_message=$stmt->execute();
            }
            //update column visibility for this role if the data exists
            $query="UPDATE PlanningReportColumns SET ColumnVisibility=? WHERE PlanningID=?";            
            //prepare SQL script for execution
            $stmt=$connector->prepare($query);
            //bind parameters
            for($j=0;$j<count($data["columnVisibility2DArray"]);$j++){
                //$j is the row counter
                $planningID=$data["columnVisibility2DArray"][$j]["planningID"];
                $columnVisibility=$data["columnVisibility2DArray"][$j]["columnVisibility"];
                $stmt->bind_param("ii",$columnVisibility,$planningID);
                $status_message=$stmt->execute();
            } 
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
            /*****Check if UserRole Name already exists in Database*******/
            $query="SELECT RoleID FROM UserRole WHERE UserRoleName=?";
            $stmt=$connector->prepare($query);
            $userRoleName=$data["userRole2DArray"][0]["userRoleName"];
            $stmt->bind_param("s",$userRoleName);            
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
            $arr_2D=array();
            /*********RETRIEVE Access Rights and Column Visibility from Database*************/
            $query="SELECT AccessRights.AccessID,AccessRights.ActivityID,AccessRights.ActivityRights,";
            $query.="Activity.ActivityName FROM AccessRights,Activity";
            $query.=" WHERE AccessRights.RoleID=? AND AccessRights.ActivityID=Activity.ActivityID";
            $stmt=$connector->prepare($query);
            $stmt->bind_param("i", $data["userRole2DArray"][0]["roleID"]);
            $status=$stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($accessID,$activityID,$activityRights,$activityName);   
            if($status){
                $arr_access=array();   //create array to store the result set data from the database                
                $i=0;       //row counter
                while($stmt->fetch()){
                    $arr_access["accessID"]=$accessID;   //append Associative array of $rows to the end of the array $arr
                    $arr_access["activityID"]=$activityID;
                    $arr_access["activityRights"]=$activityRights;
                    $arr_access["activityName"]=$activityName;
                    $arr_2D["activityRights2DArray"][$i++]=$arr_access;
                }
                //retrieve column visibility data
                $query="SELECT PlanningReportColumns.PlanningID,PlanningReportColumns.ColumnID,";
                $query.="PlanningReportColumns.ColumnVisibility,ReportColumns.ColumnName";
                $query.=" FROM PlanningReportColumns,ReportColumns WHERE PlanningReportColumns.RoleID=? AND";
                $query.=" ReportColumns.ColumnID=PlanningReportColumns.ColumnID ORDER BY ReportColumns.ColumnName Asc";
                $stmt=$connector->prepare($query);
                $stmt->bind_param("i", $data["userRole2DArray"][0]["roleID"]);
                $status_message=$stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($planningID,$columnID,$columnVisibility,$columnName);
                if($status_message){
                    $arr_visibility=array();   //create array to store the result set data from the database
                    $j=0;       //row counter
                    while($stmt->fetch()){
                        $arr_visibility["planningID"]=$planningID;   //append Associative array of $rows to the end of the array $arr
                        $arr_visibility["columnID"]=$columnID;
                        $arr_visibility["columnVisibility"]=$columnVisibility;
                        $arr_visibility["columnName"]=$columnName;
                        $arr_2D["columnVisibility2DArray"][$j++]=$arr_visibility;
                    }                  
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
    
    public function retrieveAllUserRoles(array $data):array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE ALL UserRoles from Database*************/
            $query="SELECT * FROM UserRole ORDER BY UserRoleName Asc";
            $result=$connector->query($query);
            if($result){
                $arr_2D=array();
                $i=0;       //row counter
                while($rows=$result->fetch_assoc()){  
                    $arr["roleID"]=$rows["RoleID"];
                    $arr["userRoleName"]=$rows["UserRoleName"];
                    $arr_2D["userRole2DArray"][$i++]=$arr;
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
    
    public function retrieveAllActivities(array $data):array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE ALL Activity Names from Database*************/
            $query="SELECT * FROM Activity ORDER BY ActivityName Asc";
            $result=$connector->query($query);
            if($result){                
                $arr_2D=array();
                $i=0;
                while($rows=$result->fetch_assoc()){
                    $arr["activityID"]=$rows["ActivityID"];   //append Associative array of $rows to the end of the array $arr
                    $arr["activityName"]=$rows["ActivityName"];
                    $arr_2D["activityRights2DArray"][$i++]=$arr;
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
    
    public function retrieveAllColumns(array $data):array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE ALL Column Names from Database*************/
            $query="SELECT * FROM ReportColumns ORDER BY ColumnName Asc";
            $result=$connector->query($query);
            if($result){                
                $arr_2D=array();
                $i=0;
                while($rows=$result->fetch_assoc()){  
                    $arr["columnID"]=$rows["ColumnID"];
                    $arr["columnName"]=$rows["ColumnName"];
                    $arr_2D["columnVisibility2DArray"][$i++]=$arr;
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


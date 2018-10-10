<?php
declare(strict_types=1);
namespace UserClasses\DataLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class ActivityLogDL extends DatabaseManager implements DatabaseFunctionsInt
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
            /*********RETRIEVE ActivityLogTasks from Database*************/
            $query="SELECT TaskID FROM ActivityLogTasks WHERE TaskName=?";
            $stmt=$connector->prepare($query);
            $taskName=$data["taskArray2D"][0]["taskName"];            
            $stmt->bind_param("s",$taskName);
            $status=$stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($taskID);
            if($status){
                $arr=array();   //array to store result set
                while($stmt->fetch()){
                    $arr["taskID"]=$taskID;                    
                }
                $this->dbClose($connector);
                return (int) $arr["taskID"];
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
            /***INSERT User Accounts Data into Database*******/
            $query="INSERT INTO ActivityLog";
            $query.=" (LogID,TaskID,TransactionName,Activity,ModifiedBy,DateModified,TimeModified) ";
            $query.="VALUES ('',?,?,?,?,DATE(NOW()),TIME(NOW()))";
            //prepare SQL script for execution
            $stmt=$connector->prepare($query);
            //bind parameters
            $taskID=$this->getPrimaryKey($data);
            $transactionName=$data["transactionName"];
            $activity=$data["activityAction"];
            $modifiedBy=$data["modifiedBy"];           
            $stmt->bind_param("isii",$taskID,$transactionName,$activity,$modifiedBy);
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
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::dataExists()
     */
    public function dataExists(array $data):bool
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \UserClasses\DataLayer\DatabaseFunctionsInt::delete()
     */
    public function delete(array $data):bool
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            $query="DELETE FROM ActivityLog WHERE LogID>0";
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
            /*********RETRIEVE Activity Log Data from Database*************/
            $query="SELECT ActivityLog.TransactionName,ActivityLog.Activity,";
            $query.="ActivityLog.DateModified,ActivityLog.TimeModified,";
            $query.="ActivityLogTasks.TaskName,UserAccounts.Name,UserAccounts.Surname,UserAccounts.StaffNumber FROM ActivityLogTasks,ActivityLog,UserAccounts";
            $query.=" WHERE (ActivityLog.DateModified>=DATE_FORMAT(?,'%Y-%m-%d') AND ActivityLog.DateModified<=DATE_FORMAT(?,'%Y-%m-%d'))";
            $query.=" AND (ActivityLog.TaskID=ActivityLogTasks.TaskID AND ActivityLog.TaskID=?) ";
            $query.="AND (UserAccounts.AccountID=ActivityLog.ModifiedBy)";
            $stmt=$connector->prepare($query);
            $startDate=$data["startDate"];
            $endDate=$data["endDate"];
            $arr_2D=array();    //array to store result sets.
            for($i=0;$i<count($data["taskArray2D"]);$i++){
                $taskID=$data["taskArray2D"][$i]["taskID"];
                $stmt->bind_param("ssi",$startDate,$endDate,$taskID);
                $status=$stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($transactionName,$activity,$dateModified,$timeModified,$taskName,$name,$surname,$staffNumber);
                if($status){
                    $arr=array();
                    while($stmt->fetch()){
                        $arr["transactionName"]=$transactionName;
                        $arr["activityAction"]=$activity;
                        $arr["dateModified"]=$dateModified;
                        $arr["timeModified"]=$timeModified;
                        $arr["taskArray2D"][0]["taskName"]=$taskName;
                        $arr["name"]=$name;
                        $arr["surname"]=$surname;
                        $arr["staffNumber"]=$staffNumber;
                        $arr_2D[]=$arr; //append Associative array to the end of the 2 Dimensional Array
                    }
                }else break;
            }
            $this->dbClose($connector);
            return $arr_2D;
        }
        else return NULL;          
    }
    
    public function retrieveAllActivityTasks():array
    {
        $connector=$this->dbConnect();  //connect to MariaDB database
        if(isset($connector)){
            /*********RETRIEVE ALL ActivityTasks from Database*************/
            $query="SELECT * FROM ActivityLogTasks ORDER BY TaskName Asc";
            $result=$connector->query($query);
            if($result){
                $arr_2D=array();
                $i=0;
                while($rows=$result->fetch_assoc()){
                    $arr["taskID"]=$rows["TaskID"];
                    $arr["taskName"]=$rows["TaskName"];
                    $arr_2D["taskArray2D"][$i++]=$arr;
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


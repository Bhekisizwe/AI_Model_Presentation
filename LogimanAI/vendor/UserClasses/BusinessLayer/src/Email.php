<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class Email
{     
    //return: Boolean
    public function sendEmail(array $arr=NULL):bool {
        //this function will actually do the email sending
        $err_handler=new ErrorLog();
        if(isset($arr)){
            $sender=new PHPMailer(true);
            $sender->From="systemadmin@gqunsueng.co.za";   
            $sender->FromName="AI Model System Administrator";
            $sender->isHTML(TRUE);
            $sender->Subject=$arr["subject"];           
            $sender->addAddress($arr["to"]);           
            $sender->SMTPAuth=True;
            $sender->Username="bmthethwa@gqunsueng.co.za";
            $sender->Password="Induction1";
            $sender->SMTPSecure="ssl";
            $sender->Host="mail.gqunsueng.co.za";
            $sender->Port=465;
            $sender->isSMTP();
            $sender->SMTPDebug=0;
            $sender->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            if(array_key_exists("file", $arr)){
                $sender->addAttachment($arr["file"]);
            }
            //$sender->Mailer="smtp";
            $sender->Body=$arr["body"];            
            $status_message=false;
            try{
                if($sender->send()){
                    $status_message=true;
                    //throw new Exception("Successfully Sent Email!!!");
                    throw new \Exception("Successfully Sent Email!!!");
                }
                else throw new \Exception("Failed to Send email");
            }
            catch (\Exception $e){
                $class_name="Email";
                $method_name="sendEmail";
                $err_handler->logErrors($e,NULL,$class_name,$method_name);               
            }
            finally {
                unset($err_handler);
                unset($sender);
            }
            return $status_message;
        }
        else return false;        
    }
}


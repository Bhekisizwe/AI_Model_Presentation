<?php
declare(strict_types=1);
session_start();
    //use UserClasses\BusinessLayer\DailyDistanceSetting;  
   
    require __DIR__.'/vendor/autoload.php';   
 
    $app = new \Slim\App(); 
        
    //Create, Edit and View Admin Accounts Task
    include 'adminaccount.php';
    
   //Create, Edit and View User Accountss Task
    include 'useraccount.php';
    
    //Edit and View login Task
    include 'login.php';    
    
    //View User Roles
    include 'userrole.php';
    
    $app->run();
     
?>
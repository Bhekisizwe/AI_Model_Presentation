<?php
namespace UserClasses\DataLayer;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
interface DatabaseFunctionsInt
{
    public function create(array $data):bool;
    
    public function update(array $data):bool;
    
    public function delete(array $data):bool;
    
    public function searchData(array $data):array;
    
    public function dataExists(array $data):bool;
    
    public function getPrimaryKey(array $data):int;
}


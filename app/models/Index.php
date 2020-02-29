<?php
/**
 * User: gergely.hajcsak
 * Date: 07/07/2015
 * Time: 15:18
 */

use Phalcon\Mvc\Model;
class Index extends BaseModel
{
    
    private $_connectionD;

    public function getMgm(){
    
	 
        ini_set('memory_limit','1024M');
        $di = \Phalcon\DI::getDefault();
        
        $this->_connectionD = new Database($di, $this->_db);

        $strDbQuery = 'SELECT name, value from mgm where channel = "ALL_CHANNEL" ';
        $arrParameters = array();
        $arrhotspotResultSet = $this->_connectionD->fetchAll($strDbQuery,$arrParameters);

        foreach($arrhotspotResultSet as $arrhotspotResultSetKeys => $arrhotspotResultSetValues){
        
            define($arrhotspotResultSetValues["name"], $arrhotspotResultSetValues["value"]);
        
        }
        
        return $arrhotspotResultSet;
    }
    
	public function confirmReferef($channelCode,$referer){

        $di = \Phalcon\DI::getDefault();
        
        $this->_connectionD = new Database($di, $this->_db);
			$strDbQuery = "
            SELECT
		          channel_code,
			   	  active,
				  accepted_referer
			FROM  channels
			WHERE channel_code = ?
            limit 1";

        $arrParameters = array($channelCode);
        $arrhotspotResultSet = $this->_connectionD->fetchAll($strDbQuery,$arrParameters);
		
		// only one record can be found otherwise it returns false
		if (count($arrhotspotResultSet)==1){
		
			$arrhotspotResultSet = $arrhotspotResultSet[0];
			
			// default return value
			// we return with true - as request is accepted - if we find a matching stored referral
			$isallowed = false;
			$message = "no matching HTTP referel found";
			
			//converting json to php array
			$allowedReferels = json_decode($arrhotspotResultSet["accepted_referer"]);
			
			//going throgh the array
			if (is_array($allowedReferels)){
			
				foreach($allowedReferels as $allowedReferelsKeys => $allowedReferelsValues){
				
					if ($allowedReferelsValues=="") {
					
						$message = "allow any";
						$isallowed = true;
						break;
					}
					
					// checks if any accepted referred can be found in the HTTP referral
					if (strstr($referer , $allowedReferelsValues)!=false){
					
						//if its found then set the return value true and finish function
						$message = "matching HTTP referel found";
						$isallowed = true;
						break;
					}
				}
			}else{
			
				// the accepted referal field value is not an array, rejecting
				$message = "no matching HTTP referel found";
				$isallowed = false;
			
			}
		
		}
		// not existing channel or not active channel, rejecting
		
		else {
		
		$message = "not existing channel or not active channel";
		$isallowed = false;
		
		}
		
		return $isallowed;
	}    

}





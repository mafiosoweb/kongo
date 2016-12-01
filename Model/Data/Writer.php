<?php 
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* File writer 
* @category Nostress 
* @package Nostress_Koongo
*/
namespace Nostress\Koongo\Model\Data;

class Writer extends \Nostress\Koongo\Model\AbstractModel 
{   		
	const DEF_OPEN_MODE = "w";
	
	public function saveData($data)
	{
	    $this->write($data);
    	    
	    if($this->getCompressFile())
	        $this->compress();
	}
	
	public function write($data)
	{
	    $fp = $this->openFile($this->getFullFilename());
	    fwrite($fp, $data);
	    $this->closeFile($fp);
	}
		
	protected function openFile($filename)
	{
	    $fp = @fopen($filename, self::DEF_OPEN_MODE);
        if ($fp===false) 
        {
            $e = error_get_last();
           $this->logAndException(__("Unable to open the file %1 (%2)", $filename, $e['message']));
        }
        return $fp;		
	}

	/**
    * Close file and reset file pointer
    */
    protected function closeFile($fp)
    {
        if (!$fp) 
        {
            return;
        }
        @fclose($fp);                
    }
    
    protected function compress()
    {
    	$zip = $this->getZipFilename();
    	$f = $this->getFullFilename();
    	
        if($this->helper->createZip(array($this->getFilename() => $this->getFullFilename()),$this->getZipFilename(),true))
        {          	
        	//$this->helper->deleteFile($this->getFullFilename());
        }
        
    }
}
?>
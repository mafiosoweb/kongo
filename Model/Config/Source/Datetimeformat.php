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
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Config source model - date time format
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Datetimeformat  extends \Nostress\Koongo\Model\Config\Source
{
	const SQL = "sql";
	const PHP = "php";
	const DATE_TIME = "date_time";
	const DATE = "date";
	const TIME = "time";
	
    const STANTDARD = "standard";
    const ISO8601 = "iso8601";
    const ATOM = "atom";
	const SLASH = "slash";
    const COOKIE = "cookie";
	const RFC822 = "rfc822";
	const RSS = "rss";
	const AT = "at";
	
    const STANTDARD_DATETIME = "Y-m-d H:i:s";
    const STANTDARD_DATE = "Y-m-d";
    const STANTDARD_TIME = "H:i:s";
    
    const STANTDARD_DATETIME_SQL = "%Y-%m-%d %H:%i:%s";
    const STANTDARD_DATE_SQL = "%Y-%m-%d";
    const STANTDARD_TIME_SQL = "%H:%i:%s";    
    
    protected $_formats = array();
    
    /**
     * @var \Magento\Cron\Model\ConfigInterface
     */
    protected $_config;
          
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;    
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager     
     * @param ConfigInterface $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig     
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
    		\Magento\Framework\ObjectManagerInterface $objectManager,
    		\Magento\Cron\Model\ConfigInterface $config,
    		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,   		    		    		
    		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
    		\Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {	
    	$this->_config = $config;
    	$this->_scopeConfig = $scopeConfig;
    	$this->_localeDate = $localeDate;
    	$this->_date = $date;
    }
    
    public function toOptionArray()
    {
        return array(
            array('value'=> self::STANTDARD, 'label'=> __('Standard (Y-m-d H:i:s)')),
            array('value'=>self::ISO8601, 'label'=> __('ISO  8601 (Y-m-dTH:i:sO)')),            
        	array('value'=>self::SLASH, 'label'=> __('Slash delimiter (Y/m/d H:M)')),
        	array('value'=>self::ATOM, 'label'=> __('ATOM,W3C (Y-m-d\TH:i:sP)')),
        	array('value'=>self::COOKIE, 'label'=> __('COOKIE (l, d-M-y H:i:s T)')),
        	array('value'=>self::RFC822, 'label'=> __('RFC822 (D, d M Y H:i:s O)')),       	
        	array('value'=>self::RSS, 'label'=> __('RSS (D, d M Y H:i:s O)')),
        	array('value'=>self::AT, 'label'=> __('@ (d.m.Y @ H:i:s)')),        	
        );
    }
    
    public function formatDatetime($timestamp,$format=self::STANDARD)
    {
    	$phpFormat = $this->getPhpFormat($format,self::DATE_TIME);
    	return $this->convertTimestamp($timestamp,$phpFormat);
    }
    
    public function formatDate($timestamp,$format=self::STANDARD)
    {
    	$phpFormat = $this->getPhpFormat($format,self::DATE);
    	return $this->convertTimestamp($timestamp,$phpFormat);
    }
    
    public function formatTime($timestamp,$format=self::STANDARD)
    {
    	$phpFormat = $this->getPhpFormat($format,self::TIME);
    	return $this->convertTimestamp($timestamp,$phpFormat);
    }
    
    public function getTime($timeString = null,$format = false)
    {
    	$time = $this->_getDateTime($timeString);
    	if($format)
    		$time = $this->formatTime($time,$format);
    
    	return $time;
    }
    
    public function getSqlFormat($format,$type)
    {
    	return $this->getFormat($format,$type,self::SQL);
    }
    
    public function getDate($timeString = null, $format = false) {
    	$time = $this->_getDateTime($timeString);
    	if ($format)
    		$time = $this->formatDate($time,$format);
    
    	return $time;
    }
    
    public function getDateTime($timeString = null, $format = false) {
    	$dateTime = $this->_getDateTime($timeString);
    
    	if ($format)
    		$dateTime = $this->formatDatetime($dateTime,$format);
    
    	return $dateTime;
    }
    
    public function getDayOfWeek($timeString = null)
    {
    	$time = strtotime($timeString);
    	$dayOfWeek = date('N', $time);
    	return $dayOfWeek;
    }
    
    protected function _getDateTime($timeString = null) {
    	$time = null;
    	if (!isset($timeString))
    		$time = $this->_localeDate->scopeTimeStamp();
    	else
    		$time = $this->_localeDate->scopeDate(null,$timeString,true)->getTimestamp(); 
    	//strtotime($timeString);
    
    	return $time;
    	//get time zone time
//     	return $this->timezone->timestamp($time);
//     	return $this->_localeDate->scopeDate(null,$time,true);
    }  
    
    protected function prepareFormats()
    {   	    	
    	$this->_formats = array(
	    	self::STANTDARD	=> array(
	    		self::PHP => array(self::DATE_TIME => self::STANTDARD_DATETIME,self::DATE => self::STANTDARD_DATE, self::TIME => self::STANTDARD_TIME),
	    		self::SQL => array(self::DATE_TIME => self::STANTDARD_DATETIME_SQL,self::DATE => self::STANTDARD_DATE_SQL, self::TIME => self::STANTDARD_TIME_SQL),
	    	),				
	    	self::ISO8601	=> array(
	    		self::PHP => array(self::DATE_TIME => \DateTime::ISO8601,self::DATE => self::STANTDARD_DATE, self::TIME => "H:i:sO"),
	    		self::SQL => array(self::DATE_TIME => "%Y-%m-%dT%T".$this->getTimeShift(),self::DATE => self::STANTDARD_DATE_SQL, self::TIME => self::STANTDARD_TIME_SQL.$this->getTimeShift()),
	    	),
	    	self::SLASH	=> array(
	    		self::PHP => array(self::DATE_TIME => "Y/m/d H:i",self::DATE => "Y/m/d", self::TIME => "H:i"),
	    		self::SQL => array(self::DATE_TIME => "%Y/%m/%d %H:%i",self::DATE => "%Y/%m/%d", self::TIME => "%H:%i"),
    		),
    		self::ATOM	=> array(
	    		self::PHP => array(self::DATE_TIME => \DateTime::ATOM,self::DATE => self::STANTDARD_DATE, self::TIME => "H:i:sP"),
	    		self::SQL => array(self::DATE_TIME => "%Y-%m-%dT%T".$this->getTimeShift("P"),self::DATE => self::STANTDARD_DATE_SQL, self::TIME => self::STANTDARD_TIME_SQL.$this->getTimeShift("P")),
	    	),
	    	self::COOKIE	=> array(
	    		self::PHP => array(self::DATE_TIME => \DateTime::COOKIE,self::DATE => "l, d-M-y", self::TIME => "H:i:s T"),
	    		self::SQL => array(self::DATE_TIME => "%W, %d-%b-%y %T ".$this->getTimeShift("T"),self::DATE => "%W, %d-%M-%y", self::TIME => self::STANTDARD_TIME_SQL." ".$this->getTimeShift("T")),
	    	),	
	    	self::RFC822	=> array(
	    		self::PHP => array(self::DATE_TIME => \DateTime::RFC822,self::DATE => "D, d M y", self::TIME => "H:i:s O"),
	    		self::SQL => array(self::DATE_TIME => "%a, %d %b %y %T ".$this->getTimeShift(),self::DATE => "%a, %d %M %y", self::TIME => self::STANTDARD_TIME_SQL." ".$this->getTimeShift()),
	    	),
	    	self::RSS	=> array(
	    		self::PHP => array(self::DATE_TIME => \DateTime::RSS,self::DATE => "D, d M Y", self::TIME => "H:i:s O"),
	    		self::SQL => array(self::DATE_TIME => "%a, %d %b %Y %T ".$this->getTimeShift(),self::DATE => "%a, %d %M %Y", self::TIME => self::STANTDARD_TIME_SQL." ".$this->getTimeShift()),
	    	),	
	    	self::AT	=> array(
	    		self::PHP => array(self::DATE_TIME => "d.m.Y @ H:i:s",self::DATE => "d.m.Y", self::TIME => self::STANTDARD_TIME),
	    		self::SQL => array(self::DATE_TIME => "%d.%m.%Y @ %H:%i:%s",self::DATE => "%d.%m.%Y", self::TIME => self::STANTDARD_TIME_SQL),
	    	),	
    	);
    }

    protected function getTimeShift($format = "O") 
    {
    	return $this->convertTimestamp($this->_localeDate->scopeTimeStamp(),$format);	
    }
    
    protected function getPhpFormat($format,$type)
    {
    	return $this->getFormat($format,$type);
    }
    
    protected function getFormat($format,$type,$platform = self::PHP)
    {
    	if(empty($this->_formats))
    		$this->prepareFormats();
    	
    	$formatBase = $this->_formats[self::STANTDARD][$platform];
    	if(isset($this->_formats[$format][$platform]))
    		$formatBase = $this->_formats[$format][$platform];

    	$result = $formatBase[self::DATE_TIME];
    	if(isset($formatBase[$type]))
    		$result = $formatBase[$type];
		return $result;
    }    
    
    
    
	protected function convertTimestamp($timestamp,$format)
	{
		$time = date(self::STANTDARD_DATETIME, $timestamp);
		if($format == self::STANTDARD_DATETIME)
			return $time;
		
		$timezone = $this->getTimezone();
		$datetime = new \DateTime($time,$timezone);
		return $datetime->format($format);		
	}
    
	protected function getTimezone($scopeType = null, $scopeCode = null)
	{
		$timezone = $this->_localeDate->getConfigTimezone($scopeType,$scopeCode);
		try
		{
			$tz = new \DateTimeZone($timezone);
		}
		catch (Exception $e)
		{
			$tz = new \DateTimeZone('Europe/Prague');
		}
		return $tz;
		
	}	
}
?>
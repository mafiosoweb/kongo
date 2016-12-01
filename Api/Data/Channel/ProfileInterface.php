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
* Interface for profile main class
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Api\Data\Channel;


interface ProfileInterface
{
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const PROFILE_ID = 'entity_id';	
	const STORE_ID = 'store_id';
	const NAME = 'name';
	const FILENAME = 'filename';
	const URL = 'url';
	const FEED_CODE = 'feed_code';
	const CONFIG = 'config';
	const STATUS = 'status';
	const MESSAGE = 'message';
	const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
	
	/**
	 * Get Profile ID
	 *
	 * @return int|null
	 */
	public function getProfileId();
	
	/**
	 * Get Store ID
	 *
	 * @return int|null
	 */
	public function getStoreId();
	
	/**
	 * Get profile name
	 *
	 * @return string
	 */
	public function getName();
	
	/**
	 * Get feed url
	 *
	 * @return string
	 */
	public function getUrl();
	
	/**
	 * Get feed code
	 *
	 * @return string
	 */
	public function getFeedCode();
	
	/**
	 * Get profile config
	 *
	 * @return string
	 */
	public function getConfig();
	
	/**
	 * Get profile status
	 *
	 * @return string|enum
	 */
	public function getStatus();
	
	/**
	 * Get message
	 *
	 * @return string
	 */
	public function getMessage();
	
	/**
	 * Get profile creation time
	 *
	 * @return string
	 */
	public function getCreationTime();
	
	/**
	 * Get feed update time
	 *
	 * @return string
	 */
	public function getUpdateTime();
}
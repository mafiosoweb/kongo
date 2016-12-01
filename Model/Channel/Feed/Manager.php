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
* Class for feed layout updates
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Channel\Feed;

use \Nostress\Koongo\Model\Channel\Feed;

class Manager  extends \Magento\Framework\Model\AbstractModel 
{	
	/**
	 * @var \Nostress\Koongo\Model\Channel\FeedFactory
	 */
	protected $feedFactory;
	
	/**
	 * Construct
	 *
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Nostress\Koongo\Helper\Data\Loader $helper
	 */
	public function __construct(\Nostress\Koongo\Model\Channel\FeedFactory $feedFactory)
	{
		$this->feedFactory = $feedFactory;
	}
	
	public function updateFeeds($data) {
		$data = $this->prepareData($data);
		$this->updateData($data);
	}
	
	protected function prepareData($data) {
		$modifData = array();
		foreach ($data as $key => $item) {
			if (!isset($item[Feed::COL_CODE])) {
				throw new \Exception(__("Missing feed setup attribute '".self::COL_CODE."'"));
			}
			$modifData[$item[Feed::COL_CODE]] = $item;
		}
		return $modifData;
	}
	
	protected function updateData($data) 
	{
		$collection = $this->feedFactory->create()->getCollection()->load();
		foreach ($collection as $item) {
			$code = $item->getCode();
			if (isset($data[$code])) {
				$this->copyData($data[$code],$item);
				unset($data[$code]);
			}
			else {
				$item->delete();
			}
		}
		$this->insertData($data,$collection);
		$collection->save();
	}
	
	protected function insertData($data, $collection) {
		foreach ($data as $itemData) {
			$itemData[Feed::COL_ENABLED] = Feed::DEF_ENABLED;
			$colItem = $collection->getNewEmptyItem();
			$colItem->setData($itemData);
			$collection->addItem($colItem);
		}
	}
	
	protected function copyData($data,$dstItem) {
		foreach($data as $key => $src) {
			$dstItem->setData($key,$src);
		}
	}		
}
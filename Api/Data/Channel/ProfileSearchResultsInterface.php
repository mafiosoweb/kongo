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
* Search Interface for profile main class
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Api\Data\Channel;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for cms page search results.
 * @api
 */
interface ProfileSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return \Magento\Cms\Api\Data\PageInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param \Magento\Cms\Api\Data\PageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

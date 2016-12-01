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

namespace Nostress\Koongo\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Backend system config server id field renderer
 */
class Serverid extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
	 * License helper
	 * @var \Nostress\Koongo\Helper\Version
	 */
	protected $_licenseHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nostress\Koongo\Helper\Version $licenseHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
       $this->_licenseHelper  = $licenseHelper;
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getElementHtml(AbstractElement $element)
    {
    	return $this->_licenseHelper->getServerId();
    }
    
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	// Remove scope label
    	$element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
    	return parent::render($element);
    }
}

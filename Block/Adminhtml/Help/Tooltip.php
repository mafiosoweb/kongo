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
namespace Nostress\Koongo\Block\Adminhtml\Help;

/**
 * CMS block edit form container
 */
class Tooltip extends \Magento\Backend\Block\Template
{
    /**
     * @var \Nostress\Koongo\Helper\Data
     */
    protected $helper = null;
    
    protected $_key = null;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Nostress\Koongo\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Nostress\Koongo\Helper\Data $helper,
            array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    
    public function setKey( $key) {
        
        $this->_key = $key;
    }
    
    public function _toHtml() {
        
        return "<div class='store-switcher'>".$this->helper->renderTooltip( $this->_key)."</div>";
    }
}

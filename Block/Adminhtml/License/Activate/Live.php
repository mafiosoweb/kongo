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
namespace Nostress\Koongo\Block\Adminhtml\License\Activate;

/**
 * CMS block edit form container
 */
class Live extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Nostress\Koongo\Helper\Version
     */
    protected $_helper = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Nostress\Koongo\Helper\Version $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Nostress\Koongo\Helper\Version $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Nostress_Koongo';
        $this->_controller = 'adminhtml_license_activate';
        $this->_mode = 'live';

        parent::_construct();

        $this->addButton( 'help', $this->_getHelpButtonData());
        
        $licenseUrl = $this->_helper->getNewLicenseUrl();
        
        $this->addButton(
            'buy',
            [
                    'label' => __('Buy Live License'),
                    'onclick' => 'window.open(\'' . $licenseUrl . '\')',
                    "formtarget"=>"_blank",
                    'class' => 'primary'
            ],
            -1
        );

        $this->buttonList->remove('save');

        $this->buttonList->remove('delete');
    }
    
    protected function _getHelpButtonData() {
         
        $helpButtonProps = [
                'id' => 'help_dialog',
                'label' => __('Get Support'),
                'class' => 'primary',
        ];
        return $helpButtonProps;
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Koongo - Activate Live');
    }
    
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/koongo_license/');
    }
    
    /**
     * Get form save URL
     *
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/liveactivate');
    }
}

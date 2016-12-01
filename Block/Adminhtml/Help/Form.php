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
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const HELP_MODE_TRIAL = 'trial';
    const HELP_MODE_LIVE = 'live';
    
    protected $_template = 'Nostress_Koongo::koongo/help.phtml';
    
    protected $_help_mode = self::HELP_MODE_TRIAL;
    
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper_backend = null;
    
    /**
     * @var \Nostress\Koongo\Helper\Version
     */
    protected $_version = null;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Helper\Data $helperBackend
     * @param \Nostress\Koongo\Helper\Version $version
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Magento\Backend\Helper\Data $helperBackend,
            \Nostress\Koongo\Helper\Version $version,
            array $data = []
    ) {
        $this->_helper_backend = $helperBackend;
        $this->_version = $version;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    public function setHelpMode( $mode) {
        $this->_help_mode = $mode;
    }
    
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->_help_mode = self::HELP_MODE_TRIAL;
        
        // must be edit_form because of relation with form container
        $this->setId('help_form');
    }
    
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                ['data' => ['id' => 'help_form', 'action' => $this->getUrl( '*/license/help'), 'method' => 'post']]
        );
    
        $fieldset = $form->addFieldset('base_fieldset', ['expanded'  => true]);
    
        $fieldset->addField('subject', 'text', array(
            'label' => __('Subject:'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'subject'
        ));
    
        $fieldset->addField('email', 'text', array(
            'label' => __('Email:'),
            'class' => 'required-entry validate-email',
            'required' => true,
            'name' => 'email'
        ));
        
        $fieldset->addField('message', 'textarea', array(
            'label' => __('Question:'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'message',
            'style' => "height: 300px"
        ));
        
        $serverId = $this->_version->getServerId();
        $url = $this->_helper_backend->getUrl( 'dashboard');
        $message = __('Hi,'.PHP_EOL.
                'I need help with Koongo %1 activation.'.PHP_EOL.PHP_EOL.
                'My backend URL is: %2'.PHP_EOL.
                'Server Id: %3'.PHP_EOL.
                'Backend username:'.PHP_EOL.
                'Password:'.PHP_EOL.PHP_EOL.
        
                'Thanks, '.PHP_EOL, ucfirst( $this->_help_mode), $url, $serverId);
        
        $form->setValues( array(
            'subject' => $this->_help_mode == self::HELP_MODE_TRIAL ? __('Koongo Trial activation request') : __('Koongo Live activation request'),
            'message' => $message,
        ));
    
        $form->setUseContainer(true);
        $this->setForm($form);
    
        return parent::_prepareForm();
    }
}

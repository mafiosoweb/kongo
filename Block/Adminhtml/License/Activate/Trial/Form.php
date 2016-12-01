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
namespace Nostress\Koongo\Block\Adminhtml\License\Activate\Trial;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Nostress\Koongo\Model\Api\Client
     */
    protected $_client;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Nostress\Koongo\Model\Api\Client $client,
        array $data = []
    ) {
        $this->_client = $client;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        // must be edit_form because of relation with form container
        $this->setId('edit_form');
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
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['expanded'  => true]);
        
        $fieldset->addField('trial', 'hidden', array(
            'name' => 'trial',
            'value' => true
        ));
        
        $code = $this->_client->getHelper()->getModuleConfig( \Nostress\Koongo\Helper\Version::TRIAL_CODE);
        
        $fieldset->addField('code', 'hidden', array(
            'name' => 'code',
            'value' => $code
        ));
        
        $fieldset->addField('name', 'text', array(
            'label' => __('Name:'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name'
        ));
        
        $fieldset->addField('email', 'text', array(
                'label' => __('Email:'),
                'class' => 'required-entry validate-email',
                'required' => true,
                'name' => 'email'
        ));
        
        $fieldset->addField('telephone', 'text', array(
                'label' => __('Phone:'),
                'class' => 'validate-phone',
                'required' => false,
                'name' => 'telephone'
        ));
        
        $helpUrl = $this->_client->getHelper()->getModuleConfig(\Nostress\Koongo\Helper\Version::HELP_FEED_COLLECTIONS);
        
        $field = $fieldset->addField('collection', 'select', array(
                'label' => __('Feed Collection:'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'collection[]',
                'onchange' => 'changeCollection(this.value)',
                'values' => $this->_client->getAvailableCollectionsAsOptionArray(),
                'note' => __('What is').' <a target="_blank" href="'.$helpUrl.'">'
                .__('Feed Collection').'</a>?'
        ));
        $field->setAfterElementHtml("<script>
                
            require([
                'jquery',
            ], function ( $)
            {
                var feedsByCountry = ".$this->_client->getAvailableFeedsJson().";
                    
                changeCollection = function(selectItem) {
    
                    if( selectItem == '') {
                        $('#feed_link').html( '').attr('disabled', 'disabled');
                    } else {
                        var feeds = feedsByCountry[selectItem];
        
                        var html = '';
                        for (var i = 0; i < feeds.length; i++) {
                            html += '<option value=\"'+feeds[i]['value']+'\">'+feeds[i]['label']+'</option>';
                        }
                        $('#feed_link').html( html).removeAttr('disabled');
                    }
               }
            });
        </script>");
        
        $feedField = $fieldset->addField('feed_link', 'select', array(
            'label' => __('Feed:'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'feed_link',
            'values' => array(),
            'disabled' => 'disabled'
        ));
        
        $licenseConditionsLink = $this->_client->getHelper()->getModuleConfig(\Nostress\Koongo\Helper\Version::HELP_LICENSE_CONDITIONS);
        $fieldset->addField('accept_license_conditions', 'checkbox', array(
                'label' => __('I accept Koongo License Conditions'),
                'note' =>  __('See').' <a href="'.$licenseConditionsLink.'" target="_blank">'.__('Koongo License Condtions').'</a>',
                'name' => 'accept_license_conditions',
                'value' => 0,
                'onclick' => 'this.value = this.checked ? 1 : 0;',
                'required' => true,
        ));
        
        $fieldset->addField('submit', 'submit', array(
            'value' => __('Activate Trial'),
            'class' => 'action-primary abs-action-l',
            'name' => 'submit',
            'label' => ""
        ));

        // data after error in save
        $data = $this->_coreRegistry->registry('edit_form');
        if( $data) {
            
            if( !empty( $data[ 'collection'])) {
                $feedField->setValues( $this->_client->getAvailableFeedsAsOptionArray( $data[ 'collection'][0]));
                $feedField->setData( 'disabled', '');
            }
            
            $form->setValues( $data);
        }
        
        
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

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
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Categories;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    
    /**
     * @var Nostress\Koongo\Model\Channel
     */
    protected $profile;
    
    /**
     * Current profile taxonomy code
     * @var unknown_type
     */
    protected $taxonomyCode;
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->profile = $this->_coreRegistry->registry('koongo_channel_profile');
        $this->taxonomyCode = $this->profile->getFeed()->getTaxonomyCode();
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Nostress_Koongo';
        $this->_controller = 'adminhtml_channel_profile_categories';
        
        $channelLabel = $this->profile->getFeed()->getChannel()->getLabel();

        parent::_construct();

        if ($this->_isAllowedAction('Nostress_Koongo::save')) {
            $this->buttonList->update('save', 'label', __('Save'));
            
            $this->buttonList->add(
	            'preview_button',
	            [
	                'label' => __('Preview'),
	                'class' => 'default',
	            ],
	            -100
        	);
        } else {
            $this->buttonList->remove('save');
        }
        
        $this->buttonList->add(
                'save_update_taxonomy',
                [
                        'class' => 'default',
                        'label' => __('Update %1 Category Tree', $channelLabel),
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event' => 'save',
                                    'target' => '#edit_form',
                                    'eventData' => ['action' => ['args' => [
                                        'update_taxonomy' => true,
                                        'back' => 'edit'
                                    ]]],
                                ],
                            ],
                        ]
                ]
        );
        
        $this->buttonList->add(
		  'save_execute',
		  [
		      'class' => 'save',
		      'label' => __('Execute'),
		      'data_attribute' => [
    		      'mage-init' => [
        		      'button' => [
	        		     'event' => 'save',
	        		     'target' => '#edit_form',
	        		     'eventData' => ['action' => ['args' => ['back' => 'execute']]],
	        	      ],
    		      ],
		      ]
          ]
        );
        

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
    	$channelLabel = $this->profile->getChannel()->getLabel();
        return __("Edit Profile #%1 - %2 Categories", $this->escapeHtml($this->_coreRegistry->registry('channel_profile')->getId()),$channelLabel);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
    	return $this->getUrl('*/*/savecategories');
    }
    
    /**
     * @return string
     */
    public function getFormHtml()
    {
    	if(!empty($this->taxonomyCode))
    	{
    		//add category mapping table into Form
    		$html = parent::getFormHtml();
    		$mappingTableHtml = $this->getChildHtml('category_mapping_table');
    		return str_replace("</form>", $mappingTableHtml."</form>", $html);
    	}
    	else
    		return "<h2>".__('Category mapping for %1 is not available. Please contact support for more information.', $this->profile->getChannel()->getLabel())."</h2>";
    }
}

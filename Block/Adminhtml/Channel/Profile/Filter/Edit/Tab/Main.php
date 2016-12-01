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
 * Channel profile filter settings edit form main tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Filter\Edit\Tab;

use Nostress\Koongo\Model\Channel\Profile;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Stockdependence
	*/
	protected $stockdependenceSource;
	
	/**
	 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
	 */
	protected $_rendererFieldset;
	
	/**
	 * @var \Magento\Rule\Block\Conditions
	 */
	protected $_conditions;
	
	/**
	 * @var \Nostress\Koongo\Model\Rule
	 */
	protected  $rule;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Nostress\Koongo\Model\Config\Source\Stockdependence $stockdependenceSource
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Nostress\Koongo\Model\Rule $rule
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
    	\Nostress\Koongo\Model\Config\Source\Stockdependence $stockdependenceSource,
        \Magento\Framework\Data\FormFactory $formFactory,
    	\Magento\Rule\Block\Conditions $conditions,
    	\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
    	\Nostress\Koongo\Model\Rule $rule,
        array $data = []
    )
    {
    	$this->rule = $rule;
    	$this->_conditions = $conditions;
    	$this->_rendererFieldset = $rendererFieldset;
    	$this->stockdependenceSource = $stockdependenceSource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Nostress\Koongo\Model\Channel\Profile */
        $model = $this->_coreRegistry->registry('koongo_channel_profile');
        $config = $model->getConfigItem(Profile::CONFIG_FILTER,true);
        //$channelLabel = $model->getChannel()->getLabel();
        
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Nostress_Koongo::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        //$form->setHtmlIdPrefix('channel_profile_filter_');
        
        //Prefix must be rule_ to force conditions filter work properly
        $form->setHtmlIdPrefix('rule_');
        

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Stock').$model->helper->renderTooltip( 'filter_stock') ,'expanded'  => true,
        ]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
        	'export_out_of_stock',
        	'select',
        	[
        		'name' => Profile::CONFIG_FILTER.'[export_out_of_stock]',
        		'label' => __('Export Out of Stock Products'),
        		'title' => __('Export Out of Stock Products'),
        		'required' => false,
        		'options' => ['1' => __('Yes'), '0' => __('No')],
        		'note' => __("According to product Stock Status defined by the option below you can choose whether you want to export Out of Stock products or not.")
        	]
        );
        
        $fieldset->addField(
        	'stock_status_dependence',
        	'select',
        	[
        		'name' => Profile::CONFIG_FILTER.'[stock_status_dependence]',
        		'label' => __('Stock Status Dependence'),
        		'title' => __('Stock Status Dependence'),
        		'required' => false,
        		'options' => $this->stockdependenceSource->toIndexedArray(),
        		'note' => __("This option defines the conditions that given product must meet in order to be considered as In Stock or Out of Stock")
        	]
        );
             
        $conditions = [];
        if(isset($config['conditions']) && is_array($config['conditions']))
        	$this->rule->initConditions( $config['conditions']);
        $this->rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
              
        $renderer = $this->_rendererFieldset->setTemplate(
        		'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
        		$this->getUrl('koongo/channel_profile_editfilter/newConditionHtml/form/rule_conditions_fieldset',['profile_id' => $model->getId()])
        );
        
        $fieldset2 = $form->addFieldset( 'conditions_fieldset', [
                'legend' => __('Conditions (don\'t add conditions if all products should be exported)')
                    .$model->helper->renderTooltip( 'filter_conditions'),
            ]
        )->setRenderer(
        		$renderer
        );
               
        $fieldset2->addField(
        		'conditions',
        		'text',
        		['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'), 'required' => true]
        )->setRule(
        		$this->rule
        )->setRenderer(
        		$this->_conditions
        );
        
        
        
        $this->_eventManager->dispatch('adminhtml_koongo_channel_profile_filter_edit_tab_main_prepare_form', ['form' => $form]);
        
        $data = [];
        $data['entity_id'] = $model->getId();
        $fields = ['export_out_of_stock','stock_status_dependence'];
        
        foreach($fields as $field)
        	if(isset($config[$field]))
        	$data[$field] = $config[$field];

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Stock & Attributes');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Stock & Attributes');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
}

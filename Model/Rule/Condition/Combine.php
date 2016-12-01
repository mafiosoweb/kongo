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
 * Rule Combine Condition data model
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Model\Rule\Condition;

class Combine extends  \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
     */
    protected $_productFactory;

    /*
     * @var \Nostress\Koongo\Model\Config\Source\Attributes
    */
    protected $attributeSource;
    
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Nostress\Koongo\Model\Rule\Condition\ProductFactory $conditionFactory,
        array $data = []
    ) {
        $this->_productFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('Nostress\Koongo\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();  
        
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Nostress\Koongo\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Nostress\Koongo\Model\Rule\Condition\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $attributes]
            ]
        );
        return $conditions;
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
    
    public function asSqlWhere( $columns)
    {
    	$w = array();
    	foreach ($this->getConditions() as $cond) {
    		$w[] = $cond->asSqlWhere( $columns);
    	}
    	if (!$w) {
    		return false;
    	}
    	$a = $this->getAggregator();
    	$v = $this->getValue();
    	return ($v ? '' : 'NOT ').'('.join(') '.($a=='all' ? 'AND' : 'OR').' '.($v ? '' : 'NOT ').'(', $w).')';
    }
}

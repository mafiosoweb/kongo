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

namespace Nostress\Koongo\Block\Component;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class StepsWizard extends \Magento\Ui\Block\Component\StepsWizard
{
    /**
     * Wizard step template
     *
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/stepswizard.phtml';
    
    /**
     * \Nostress\Koongo\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,\Nostress\Koongo\Helper\Data $helper , array $data = [])
    {
    	$this->helper = $helper;
    	parent::__construct($context, $data);    	
    }
    
    /**
	* Is magento version equal or greater than 2.1.0
     */
    public function isVersionEqualorGreater210()
    {
    	return $this->helper->isMagentoVersionEqualOrGreaterThan("2.1.0");
    }
}

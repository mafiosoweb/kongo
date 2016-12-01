<?php /*
Magento Module developed by NoStress Commerce

 NOTICE OF LICENSE

 This source file is subject to the Open Software License (OSL 3.0)
 that is bundled with this package in the file LICENSE.txt.
 It is also available through the world-wide-web at this URL:
 http://opensource.org/licenses/osl-3.0.php
 If you did of the license and are unable to
 obtain it through the world-wide-web, please send an email
 to info@nostresscommerce.cz so we can send you a copy immediately.

 @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)

*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

class Loadcategories extends SaveAbstract
{
    protected $_categoriesModel = null;

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation, \Nostress\Koongo\Model\Channel\Profile\Categories $categoriesModel)
    {
        parent::__construct($context, $resultPageFactory, $helper, $urlBuilder, $manager, $profileFactory, $translation);
        $this->_categoriesModel = $categoriesModel;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        $locale = $this->getRequest()->getParam("taxonomy_locale");
        if ($id) {
            $profile = $this->profileFactory->create()->load($id);
            $this->_categoriesModel->initProfile($profile);
            $result = array("mapping_rules" => $this->_categoriesModel->getMappingRules($locale));
            if ($this->getRequest()->getParam("with_categories")) {
                $result["channel_categories"] = $this->_categoriesModel->getChannelCategories($locale);
            }
            $this->_sendAjaxSuccess($result);
        } else {
            $this->_sendAjaxError("wrong data format");
        }
    }
}

?>
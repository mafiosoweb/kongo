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
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile\Editfilter;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class NewConditionHtml extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    protected $profileFactory;

    public function __construct(Context $context, Registry $coreRegistry, Date $dateFilter, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory)
    {
        $this->profileFactory = $profileFactory;
        parent::__construct($context, $coreRegistry, $dateFilter);
    }

    public function execute()
    {
        $profileId = $this->getRequest()->getParam("profile_id");
        $profile = $this->profileFactory->create()->load($profileId);
        if ($profile->getId()) $this->_coreRegistry->register("koongo_channel_profile", $profile);
        $id = $this->getRequest()->getParam("id");
        $typeArr = explode("|", str_replace("-", "/", $this->getRequest()->getParam("type")));
        $type = $typeArr[0];
        $model = $this->_objectManager->create($type)->setId($id)->setType($type)->setRule($this->_objectManager->create("Magento\\CatalogRule\\Model\Rule"))->setPrefix("conditions");
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }
        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam("form"));
            $html = $model->asHtmlRecursive();
        } else {
            $html = "";
        }
        $this->getResponse()->setBody($html);
    }
}

?>
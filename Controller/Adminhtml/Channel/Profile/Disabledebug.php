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
class Disabledebug extends SaveAbstract
{
    public function execute()
    {
        $this->version->setModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_DEBUG_MODE, "0");
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath("*/*/");
    }
}

?>
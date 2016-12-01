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

class Preview extends SaveAbstract
{
    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        $errorMessage = false;
        if ($id) {
            $model = $this->profileFactory->create()->load($id);
            if (!$model->getId()) {
                $errorMessage = __("This profile no longer exists.");
            } else {
                $result = $model->getPreview();
            }
        } else {
            $errorMessage = __("Wrong format of params.");
        }
        if ($errorMessage) {
            $data = "Error: " . $errorMessage;
        } else {
            $data = $result;
        }
        echo $data;
        exit;
    }
}

?>
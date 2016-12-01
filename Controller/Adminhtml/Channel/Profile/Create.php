<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

use Magento\Framework\Controller\ResultFactory;

class Create extends SaveAbstract
{
    public function execute()
    {
        $feedCode = $this->getRequest()->getParam("feed_code");
        $storeId = $this->getRequest()->getParam("store_id");
        if (!empty($feedCode) && !empty($storeId)) {
            $profile = $this->manager->createProfileFromFeed($storeId, $feedCode);
            $this->messageManager->addSuccess($this->getSuccessCreationMessage($profile->getId(), $profile->getName()));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/*/");
    }

    protected function getSuccessCreationMessage($id, $name)
    {
        return __("Profile #%1 - %2 has been successfully created.", $id, $name);
    }
} ?>
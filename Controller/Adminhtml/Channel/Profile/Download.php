<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

class Download extends SaveAbstract
{
    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        $errorMessage = false;
        if ($id) {
            $profile = $this->profileFactory->create()->load($id);
            if (!$profile->getId()) {
                $errorMessage = __("This profile no longer exists.");
            } else {
                $path = $profile->getUrl();
                $fileName = $profile->getFilename();
                $content = file_get_contents($path);
                $this->getResponse()->setHttpResponseCode(200)
                    ->setHeader("Pragma", "public", true)
                    ->setHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0", true)
                    ->setHeader("Content-type", "application/octet-stream", true)
                    ->setHeader("Content-Length", strlen($content))
                    ->setHeader("Content-Disposition", "attachment; filename=\"".$fileName."\"")
                ->setHeader("Last-Modified", date("r"))
                    ->setBody($content);
                $this->getResponse()->sendResponse();exit;}
        } else {
            $errorMessage = __("Wrong format of params.");
        }
        if ($errorMessage) {
            $this->messageManager->addError($errorMessage);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath("*/channel_profile");
    }
} ?>
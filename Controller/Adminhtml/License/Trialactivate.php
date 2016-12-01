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
namespace Nostress\Koongo\Controller\Adminhtml\License;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Trialactivate extends \Magento\Backend\App\Action
{
    protected $client;
    protected $session;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Nostress\Koongo\Model\Api\Client $apiClient, \Magento\Backend\Model\Session $session)
    {
        $this->client = $apiClient;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $result = $this->client->createLicenseKey($data);
                $this->session->setFormData(false);
                $this->messageManager->addSuccess(__("Koongo Connector has been activated with license key %1 .", $result["key"]));
                if (count($result["collection"])) {
                    $this->messageManager->addSuccess(__("Feed collection %1 has been assigned to the license key.", implode(", ", $result["collection"])));
                }
                $this->client->updateFeeds($this->messageManager);
            } catch (\Exception$e) {
                $message = __("Module activation process failed. Error: ");
                $this->messageManager->addError($message . $e->getMessage());
                $this->session->setFormData($data);
                return $resultRedirect->setPath("*/*/trialform");
            }
        }
        return $resultRedirect->setPath("*/channel_profile/");
    }
}

?>
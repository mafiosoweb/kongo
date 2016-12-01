<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\License;

class Check extends \Magento\Backend\App\Action
{
    protected $client = null;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Nostress\Koongo\Model\Api\Client $apiClient)
    {
        $this->client = $apiClient;
        parent::__construct($context);
    }

    public function execute()
    {
        $isAjax = (bool)$this->getRequest()->getParam("ajax", false);
        try {
            $response = $this->client->updateLicense();
            if (!$isAjax) {
                if ($response["valid"]) {
                    $this->messageManager->addSuccess($response["license_status"]);
                } else {
                    $this->messageManager->addError($response["license_status"]);
                }
            }
        } catch (Exception$e) {
            $message = __("License status check failed: ") . $e->getMessage();
            if (!$isAjax) {
                $this->messageManager->addError($message);
            } else {
                $response = array("error" => $message);
            }
        }
        if ($isAjax) {
            echo json_encode($response);
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath("*/channel_profile");
        }
    }
} ?>
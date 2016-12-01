<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

abstract class Profile extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $version;
    protected $urlBuilder;
    protected $manager;
    protected $profileFactory;
    protected $translation;
    protected $_auth_label = 'Nostress_Koongo::koongo_channel_profile';

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->version = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->manager = $manager;
        $this->profileFactory = $profileFactory;
        $this->translation = $translation;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->_auth_label);
    }

    protected function _sendAjaxError($message)
    {
        $this->getResponse()->representJson($this->_objectManager->get("Magento\Framework\\Json\Helper\Data")->jsonEncode(["error" => true, "message" => $message]));
    }

    protected function _sendAjaxSuccess($message)
    {
        $this->getResponse()->representJson($this->_objectManager->get("Magento\Framework\Json\\Helper\Data")->jsonEncode($message));
    }

    protected function _sendAjaxResponse($message, $error = false)
    {
        if (is_array($message) && isset($message["error"])) {
            $error = $message["error"];
            if ($error) {
                $message = $message["message"];
            }
        }
        if ($error) {
            $this->_sendAjaxError($message);
        } else {
            $this->_sendAjaxSuccess($message);
        }
    }

    protected function _isAjax()
    {
        return $this->getRequest()->getParam("isAjax");
    }
} ?>
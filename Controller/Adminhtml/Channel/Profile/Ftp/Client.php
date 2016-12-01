<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile\Ftp;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;
use Nostress\Koongo\Model\Channel\Profile;

class Client extends \Nostress\Koongo\Controller\Adminhtml\Channel\Profile\SaveAbstract
{
    public function execute()
    {
        $path = $this->getRequest()->getParam("path");
        if (($postData = $this->getRequest()->getParams()) !== false && isset($postData[Profile::CONFIG_FEED][Profile::CONFIG_FTP])) {
            $config = $postData[Profile::CONFIG_FEED][Profile::CONFIG_FTP];
        } elseif (($id = $this->_request->getParam("entity_id")) > 0) {
            $profile = $this->profileFactory->create()->load($id);
            $config = $profile->getFtpParams();
        } else {
            $message = __("Wrong data format!");
            return $this->_sendAjaxError($message);
        }
        $ftp = $this->manager->getFtp();
        if ($this->getRequest()->getParam("test")) {
            $message = $ftp->checkFtpConnection($config);
            if ($message["error"]) {
                return $this->_sendAjaxResponse($message);
            }
        } else {
            $message = array();
        }
        if (!$path) {
            $path = $config["path"];
        }
        try {
            $ftpClient = $ftp->getClient($config);
        } catch (\Exception$e) {
            return $this->_sendAjaxError($e->getMessage());
        }
        $file = $this->getRequest()->getParam("file");
        if ($file) {
            $content = $ftpClient->read($file);
            $ftpClient->close();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader("Pragma", "public", true)
                ->setHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0", true)
                ->setHeader("Content-type", "application/octet-stream", true)
                ->setHeader("Content-Length", strlen($content))
                ->setHeader("Content-Disposition", "attachment; filename=\"" . basename($file) . "\"")
                ->setHeader("Last-Modified", date("r"))->setBody($content);
            $this->getResponse()->sendResponse();
            exit;
        } else {
            if ($path) {
                $ftpClient->cd($path);
            }
            $list = $ftp->getFilesSorted($ftpClient);
            $path = $ftpClient->pwd();
            $ftpClient->close();
            $message["list"] = $list;
            $message["path"] = $path;
            return $this->_sendAjaxResponse($message);
        }
    }
} ?>
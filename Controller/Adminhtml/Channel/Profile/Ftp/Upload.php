<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile\Ftp;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Nostress\Koongo\Controller\Adminhtml\Channel\Profile\SaveAbstract
{
    protected $_auth_label = 'Nostress_Koongo::execute';

    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        if (!empty($id) && is_numeric($id)) {
            $profile = $this->profileFactory->create();
            $profile->load($id);
            $result = $this->manager->uploadFeed($profile);
            if ($result === true) {
                $this->messageManager->addSuccess(__("Feed was successfuly uploaded via FTP!"));
            } else {
                $this->messageManager->addError($result);
            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/channel_profile_ftp/edit", array("entity_id" => $id));
    }
} ?>
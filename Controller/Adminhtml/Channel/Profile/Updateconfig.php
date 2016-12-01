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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Updateconfig extends \Magento\Backend\App\Action
{
    protected $version;

    public function __construct(Context $context, \Nostress\Koongo\Helper\Version $version)
    {
        $this->version = $version;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->version->setModuleConfig(base64_decode($this->_request->getParam("label")), $this->_request->getParam("value"));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath("*/*/");
    }
}

?>
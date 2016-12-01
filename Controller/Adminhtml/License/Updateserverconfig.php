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
${"\x47\x4c\x4fB\x41\x4c\x53"}["l\x6fh\x73d\x71\x6c\x72\x63wmb"] = "\x72e\x73ult\x52ed\x69\x72\x65c\x74";
${"G\x4c\x4f\x42\x41L\x53"}["\x72\x77\x68\x76ood"] = "\x65\x6e\x61\x62\x6ceE\x72ror\x49\x6ecr\x65\x6de\x6et";
${"\x47LO\x42AL\x53"}["\x6cg\x75\x6e\x6e\x69s\x6b\x68\x76d\x66"] = "\x63o\x6e\x74\x65x\x74";
${"\x47\x4cO\x42\x41LS"}["\x6c\x6d\x7a\x6dg\x79i\x61d\x78\x74\x74"] = "api\x43\x6c\x69\x65nt";

class Updateserverconfig extends \Magento\Backend\App\Action
{
    protected $client = null;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Nostress\Koongo\Model\Api\Client $apiClient)
    {
        $this->client = ${${"\x47LOB\x41L\x53"}["\x6c\x6d\x7am\x67y\x69\x61\x64\x78tt"]};
        parent::__construct(${${"\x47\x4c\x4f\x42A\x4c\x53"}["\x6cgu\x6e\x6e\x69\x73\x6b\x68\x76d\x66"]});
    }

    public function execute()
    {
        $mllysnbj = "\x72es\x70o\x6e\x73\x65";
        ${"\x47\x4c\x4fB\x41L\x53"}["\x76j\x73\x71\x6e\x6dqg\x74\x6e\x73\x67"] = "\x65\x6ea\x62l\x65\x45rr\x6f\x72\x49nc\x72\x65\x6d\x65\x6e\x74";
        ${${"\x47\x4c\x4fB\x41L\x53"}["r\x77\x68\x76\x6f\x6fd"]} = $this->_request->getParam("e\x65\x69", false);
        if ((${$mllysnbj} = $this->client->updateServerConfig(${${"G\x4cO\x42\x41\x4c\x53"}["\x76j\x73\x71\x6e\x6d\x71gtn\x73\x67"]})) === true) {
            $this->messageManager->addSuccess("Se\x72v\x65r \x63onfi\x67\x20has\x20\x62een\x20\x75\x70d\x61\x74\x65d\x21");
        } else {
            $khdxfon = "\x72e\x73\x70\x6f\x6e\x73\x65";
            $this->messageManager->addError(${$khdxfon});
        }
        ${${"\x47\x4c\x4f\x42\x41L\x53"}["\x6c\x6fh\x73d\x71\x6c\x72\x63\x77\x6d\x62"]} = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath("*/\x63\x68\x61\x6enel_p\x72of\x69l\x65");
    }
}

?>
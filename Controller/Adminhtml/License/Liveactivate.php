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
namespace Nostress\Koongo\Controller\Adminhtml\License;${"\x47LOB\x41LS"}["\x69q\x6f\x6f\x70k\x72oe\x79\x70"]="m\x65\x73\x73\x61\x67e";${"\x47\x4cO\x42\x41\x4c\x53"}["\x63\x76d\x6ed\x71b\x6e\x72"]="\x72\x65\x73\x75\x6ct";${"\x47L\x4f\x42A\x4c\x53"}["\x72\x76\x62\x76\x70uee\x76\x68"]="\x64\x61\x74\x61";${"\x47L\x4f\x42\x41\x4c\x53"}["\x68o\x67\x7a\x76\x77\x65\x73t\x79"]="\x72\x65\x73\x75\x6ct\x52ed\x69r\x65c\x74";${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x68\x75li\x61\x72\x6bty\x6f\x64\x70"]="\x73\x65s\x73i\x6f\x6e";${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x79p\x74n\x67\x6f\x66\x62"]="\x61\x70\x69\x43l\x69\x65\x6e\x74";use Magento\Backend\App\Action;use Magento\Framework\Controller\ResultFactory;class Liveactivate extends\Magento\Backend\App\Action{protected$client;protected$session;public function __construct(\Magento\Backend\App\Action\Context$context,\Nostress\Koongo\Model\Api\Client$apiClient,\Magento\Backend\Model\Session$session){$this->client=${${"\x47\x4c\x4f\x42\x41L\x53"}["\x79\x70\x74\x6e\x67of\x62"]};$nqwykn="co\x6e\x74e\x78\x74";$this->session=${${"GLO\x42\x41\x4c\x53"}["\x68\x75l\x69\x61r\x6bt\x79odp"]};parent::__construct(${$nqwykn});}public function execute(){${${"\x47\x4cO\x42\x41\x4cS"}["h\x6f\x67\x7a\x76\x77\x65s\x74\x79"]}=$this->resultRedirectFactory->create();$xbpqndukkpoh="\x64a\x74a";${$xbpqndukkpoh}=$this->getRequest()->getPostValue();if(${${"\x47LO\x42\x41\x4c\x53"}["\x72\x76\x62vp\x75ee\x76\x68"]}){try{${${"\x47\x4cO\x42\x41\x4c\x53"}["\x63\x76d\x6e\x64q\x62\x6e\x72"]}=$this->client->createLicenseKey(${${"\x47\x4c\x4f\x42\x41L\x53"}["r\x76\x62\x76pue\x65\x76\x68"]});$this->session->setFormData(false);$this->messageManager->addSuccess(__("\x4b\x6fon\x67\x6f\x20\x43\x6fnnec\x74\x6f\x72 \x68\x61\x73 b\x65\x65n \x61\x63ti\x76a\x74\x65\x64\x20\x77\x69t\x68\x20\x6c\x69c\x65\x6es\x65 k\x65\x79 \x251\x20.",${${"G\x4cOB\x41\x4cS"}["c\x76\x64\x6edq\x62\x6er"]}["\x6bey"]));$this->messageManager->addSuccess(__("\x46\x65\x65\x64\x20coll\x65\x63tio\x6e \x25\x31\x20h\x61\x73 b\x65\x65n\x20a\x73s\x69\x67ne\x64 to \x74h\x65\x20l\x69\x63\x65\x6e\x73e k\x65y.",implode(",\x20",${${"\x47\x4c\x4f\x42\x41L\x53"}["\x63\x76\x64n\x64q\x62\x6er"]}["c\x6f\x6c\x6cec\x74\x69o\x6e"])));$this->client->updateFeeds($this->messageManager);}catch(\Exception$e){${"G\x4cOB\x41\x4c\x53"}["dl\x6e\x71ay\x6d"]="\x6de\x73sa\x67e";$okyeoi="d\x61\x74\x61";${${"\x47\x4c\x4f\x42\x41L\x53"}["dl\x6e\x71\x61y\x6d"]}=__("\x4do\x64\x75le\x20\x61c\x74i\x76at\x69on pr\x6fces\x73 \x66\x61\x69\x6ced.\x20\x45\x72\x72o\x72:\x20");$this->messageManager->addError(${${"\x47\x4c\x4fB\x41L\x53"}["i\x71\x6fo\x70\x6b\x72\x6feyp"]}.$e->getMessage());$this->session->setFormData(${$okyeoi});return$resultRedirect->setPath("*/*/l\x69vefo\x72m");}}return$resultRedirect->setPath("adm\x69nht\x6dl/\x73\x79\x73\x74e\x6d\x5fc\x6f\x6efig/ed\x69t/sec\x74io\x6e/koo\x6eg\x6f\x5f\x6cicens\x65/");}}
?>
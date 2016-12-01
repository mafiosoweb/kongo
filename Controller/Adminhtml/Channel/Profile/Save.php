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
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

class Save extends SaveAbstract
{
    protected $rule;
    protected $session;
    protected $cron;

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation, \Nostress\Koongo\Model\Rule $rule, \Magento\Backend\Model\Session $session, \Nostress\Koongo\Model\Cron $cron)
    {
        $this->rule = $rule;
        $this->session = $session;
        $this->cron = $cron;
        parent::__construct($context, $resultPageFactory, $helper, $urlBuilder, $manager, $profileFactory, $translation);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data = $this->preprocessData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->profileFactory->create();
            $paramId = $this->getRequest()->getParam("entity_id");
            if ($paramId) {
                $model->load($paramId);
            }
            $id = $model->getId();
            if (empty($id)) {
                $this->messageManager->addError(__("Profile with id #%1 doesn't exist.", $paramId));
                return $resultRedirect->setPath("*/*/");
            }
            $model->updateData($data);
            $this->_eventManager->dispatch("koongo_channel_profile_prepare_save", ["post" => $model, "request" => $this->getRequest()]);
            if (isset($data["cron"]["rules"])) {
                $this->cron->applyRules($id, $data["cron"]["rules"]);
            }
            try {
                $model->save();
                $this->messageManager->addSuccess(__("Profile #%1 has been successfully saved.", $paramId));
                $this->session->setFormData(false);
                $backParam = $this->getRequest()->getParam("back");
                if ($backParam) {
                    $pageCode = "";
                    $pagePath = "*/*/";
                    switch ($backParam) {
                        case"edit":
                            $pageCode = "editgeneral";
                            break;
                        case"upload":
                            $pagePath = "*/channel_profile_ftp/";
                            $pageCode = "upload";
                            break;
                        case"execute":
                            $pageCode = "execute";
                            break;
                        case"filter":
                            $pageCode = "editfilter";
                            break;
                        case"cron":
                            $pageCode = "editcron";
                            break;
                        case"duplicate":
                            $newProfile = $this->manager->duplicateProfile($model->getId());
                            $this->cron->applyRules($newProfile->getId(), $newProfile->getConfigItem("cron", true, "rules"));
                            $this->messageManager->addSuccess(__("Profile #%1 has been successfully duplicated.", $paramId));
                            $this->messageManager->addSuccess(__("Profile #%1 has been successfully created.", $newProfile->getId()));
                            break;
                    }
                    return $resultRedirect->setPath($pagePath . $pageCode, ["entity_id" => $model->getId(), "_current" => true]);
                }
                return $resultRedirect->setPath("*/*/");
            } catch (\Magento\Framework\Exception\LocalizedException$e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException$e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception$e) {
                $this->messageManager->addException($e, __("Something went wrong while saving the post."));
            }
            $this->session->setFormData(false);
            return $resultRedirect->setPath("*/*/editgeneral", ["entity_id" => $this->getRequest()->getParam("entity_id")]);
        }
        return $resultRedirect->setPath("*/*/");
    }

    protected function preprocessData($data)
    {
        $data = $this->removeUnwantedInputsFromData($data);
        $data = $this->preprocessFilterRules($data);
        $data = $this->preprocessMultiselectValues($data);
        return $data;
    }

    protected function preprocessFilterRules($data)
    {
        if (!isset($data["rule"])) return $data;
        $data["filter"]["conditions"] = $this->rule->parseConditionsPost($data["rule"]);
        unset($data["rule"]);
        return $data;
    }

    protected function preprocessMultiselectValues($data)
    {
        if (!isset($data["filter"])) return $data;
        if (!isset($data["filter"]["types"])) $data["filter"]["types"] = [];
        if (!isset($data["filter"]["visibility_parent"])) $data["filter"]["visibility_parent"] = [];
        if (!isset($data["filter"]["visibility"])) $data["filter"]["visibility"] = [];
        return $data;
    }

    protected function removeUnwantedInputsFromData($data)
    {
        $unwantedItemIndexes = ["form_key", "option", "dropdown_attribute_validation"];
        foreach ($unwantedItemIndexes as $index) {
            if (isset($data[$index])) unset($data[$index]);
        }
        return $data;
    }
}

?>
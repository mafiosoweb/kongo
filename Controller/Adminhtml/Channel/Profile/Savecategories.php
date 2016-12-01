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

class Savecategories extends SaveAbstract
{
    protected $session;
    protected $mappingModel;
    protected $_categoriesModel = null;
    protected $cacheManager;

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation, \Magento\Backend\Model\Session $session, \Nostress\Koongo\Model\Taxonomy\Category\Mapping $mappingModel, \Nostress\Koongo\Model\Channel\Profile\Categories $categoriesModel, \Nostress\Koongo\Model\Cache\Manager $cacheManager)
    {
        $this->session = $session;
        $this->mappingModel = $mappingModel;
        $this->_categoriesModel = $categoriesModel;
        $this->cacheManager = $cacheManager;
        parent::__construct($context, $resultPageFactory, $helper, $urlBuilder, $manager, $profileFactory, $translation);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data = $this->preprocessData($data);
        $message = "";
        $error = "";
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->profileFactory->create();
            $paramId = $this->getRequest()->getParam("entity_id");
            if ($paramId) {
                $model->load($paramId);
            }
            $id = $model->getId();
            if (empty($id)) {
                $message = __("Profile with id #%1 doesn't exist.", $paramId);
                if ($this->_isAjax()) {
                    $this->_sendAjaxError($message);
                } else {
                    $this->messageManager->addError($message);
                    return $resultRedirect->setPath("*/*/");
                }
            }
            try {
                $data = $this->saveCategoryMappingRules($data, $model);
                $model->updateData($data);
                $this->_eventManager->dispatch("koongo_channel_profile_prepare_save", ["post" => $model, "request" => $this->getRequest()]);
                $model->save();
                $message = __("Profile #%1 categories has been successfully saved.", $paramId);
                if (!$this->_isAjax()) $this->messageManager->addSuccess($message);
                $error = false;
                $this->session->setFormData(false);
                $this->cacheManager->reloadProfileChannelCategoriesCache($model);
                if ($this->getRequest()->getParam("update_taxonomy")) {
                    $this->_categoriesModel->initProfile($model);
                    $messages = $this->_categoriesModel->reloadTaxonomyCategories($data["general"]["taxonomy_locale"]);
                    foreach ($messages[true] as $message) {
                        $this->messageManager->addSuccess($message);
                    }
                    foreach ($messages[false] as $message) {
                        $this->messageManager->addError($message);
                    }
                }
            } catch (\Exception$e) {
                $message = $e->getMessage();
                $error = true;
                if (!$this->_isAjax()) $this->messageManager->addError($message);
            }
            $backParam = $this->getRequest()->getParam("back");
            if ($backParam) {
                $pageCode = "";
                switch ($backParam) {
                    case"edit":
                        $pageCode = "editcategories";
                        break;
                    case"execute":
                        $pageCode = "execute";
                        break;
                }
                if ($this->_isAjax()) {
                    $this->_sendAjaxResponse($message, $error);
                } else {
                    return $resultRedirect->setPath("*/*/" . $pageCode, ["entity_id" => $model->getId(), "_current" => true]);
                }
            }
        }
        if ($this->_isAjax()) {
            $this->_sendAjaxResponse($message, $error);
        } else {
            return $resultRedirect->setPath("*/*/");
        }
    }

    protected function saveCategoryMappingRules($data, $profile)
    {
        $rules = [];
        if (isset($data["rules"])) $rules = $data["rules"];
        unset($data["rules"]);
        $locale = $data["current_channel_categories_locale"];
        unset($data["current_channel_categories_locale"]);
        $storeId = $profile->getStoreId();
        $taxonomyCode = $profile->getFeed()->getTaxonomyCode();
        $mappingItem = $this->mappingModel->getMapping($taxonomyCode, $locale, $storeId);
        if (!isset($mappingItem)) {
            $mappingItem = $this->mappingModel;
            $mappingItem->setStoreId($storeId);
            $mappingItem->setTaxonomyCode($taxonomyCode);
            $mappingItem->setLocale($locale);
        }
        $mappingItem->setConfig(json_encode(["rules" => $rules]));
        $mappingItem->save();
        return $data;
    }

    protected function preprocessData($data)
    {
        $data = $this->removeUnwantedInputsFromData($data);
        $data = $this->preprocessMultiselectValues($data);
        return $data;
    }

    protected function preprocessMultiselectValues($data)
    {
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
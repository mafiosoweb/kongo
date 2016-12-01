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

use Nostress\Koongo\Helper\Version;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

class Index extends \Nostress\Koongo\Controller\Adminhtml\Channel\Profile
{
    const BLOG_NEWS_LIMIT = 3;
    protected $feedSingelton;

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation, \Nostress\Koongo\Model\Channel\Feed $feedSingelton)
    {
        $this->feedSingelton = $feedSingelton;
        parent::__construct($context, $resultPageFactory, $helper, $urlBuilder, $manager, $profileFactory, $translation);
    }

    public function execute()
    {
        $configTest = $this->version->getModuleConfig(\Nostress\Koongo\Model\Api\Client::PARAM_TAXONOMY_SOURCE_URL);
        if (empty($configTest) && $this->version->isServerConfigUpdatable(true)) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath("koongo/license/updateserverconfig", array("eei" => true));
        }
        $this->_checkLicense();
        $this->checkFlatEnabled();
        $this->checkDebugMode();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Nostress_Koongo::koongo");
        $resultPage->addBreadcrumb(__("Koongo"), __("Koongo"));
        $resultPage->addBreadcrumb(__("Manage Export Profiles"), __("Manage Export Profiles"));
        $resultPage->getConfig()->getTitle()->prepend(__("Koongo Export Profiles"));
        return $resultPage;
    }

    protected function _checkLicense()
    {
        if ($this->version->isLicenseEmpty()) {
            $this->messageManager->addNotice($this->version->getActivationMessage());
        } else if (!$this->version->isLicenseKeyValid()) {
            $this->messageManager->addNotice($this->version->getTrialLicenseKeyInvalidMessage());
        } else {
            $feedsLoaded = $this->feedSingelton->feedsLoaded();
            if (!$feedsLoaded) {
                $updateFeedsLink = $this->urlBuilder->getUrl("*/*/updatefeeds");
                $helpUrl = $helpUrl = $this->version->getModuleConfig(Version::HELP_FEED_COLLECTIONS);
                $this->messageManager->addNotice(__("Feeds & Taxonomies have not been downloaded yet. Click <a href=\"%1\">Get Feeds Templates</a> button.", $updateFeedsLink) . " " . __("More information you may find in <a href=\"%1\" target=\"blank\" > documentation</a >.", $helpUrl));
            }
            if ($this->version->isLicenseKeyT()) {
                $newLicenseUrl = $this->version->getNewLicenseUrl();
                $this->messageManager->addNotice(__("You are using the 30 days Trial version of Koongo Connector"));
                if ($this->version->isDateValid()){
                    $this->messageManager->addNotice(__("Your Trial period expires on %1 and we encourage you to buy <a href=\"%2\" target=\"_blank\"> Full version </a >.", $this->version->gLD(), $newLicenseUrl));
                }else{
                    $this->messageManager->addNotice($this->version->getTrialLicenseInvalidMessage());
                }
            } else {
                if (!$this->version->isDateValid()) {
                    $feedsinfoUrl = $this->urlBuilder->getUrl("*/*/feedsinfo");
                    $message = $this->version->getLicenseInvalidMessage();
                    $message .= " " . __("<a href='%1'>Which Feeds I get if I extend my Support & Updates period?</a>", $feedsinfoUrl);
                    $this->messageManager->addError($message);
                }
            }
            $showBlogList = $this->version->getModuleConfig(Version::PARAM_SHOW_BLOG_NEWS);
            if ($showBlogList) $this->renderBlogList();
        }
    }

    protected function checkFlatEnabled()
    {
        $productFlatEnabled = $this->version->getStoreConfig(null, \Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_CATALOG_FRONTEND_FLAT_CATALOG_PRODUCT);
        $categoryFlatEnabled = $this->version->getStoreConfig(null, \Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_CATALOG_FRONTEND_FLAT_CATALOG_CATEGORY);
        if ($productFlatEnabled == 0 || $categoryFlatEnabled == 0) {
            $actionLink = $this->urlBuilder->getUrl(" */*/enableflat");
            $helpLink = $this->version->getModuleConfig(\Nostress\Koongo\Helper\Data::HELP_FLAT_CATALOG);
            $this->messageManager->addNotice(__("Flat Catalog Category and Flat Catalog Product usage is required. <a href=\"%1\">Click here to enable Flat Catalog</a>.", $actionLink));
            $this->messageManager->addNotice(__("More information you may find in <a target=\"_blank\" href = \"%1\" > Koongo Docs </a >.", $helpLink));
        }
    }

    protected function checkDebugMode()
    {
        $debugEnabled = $this->version->isDebugMode();
        if ($debugEnabled == 1) {
            $actionLink = $this->urlBuilder->getUrl("*/*/disabledebug");
            $this->messageManager->addNotice(__("Debug mode is On . <a href = \"%1\" > Click here to disable Debug Mode </a >.", $actionLink));
        }
    }

    protected function renderBlogList()
    {
        $feed = $this->version->getBlogNews();
        if (!empty($feed)) {
            $this->messageManager->addNotice("<strong>" . __("Latest news from Koongo") . "</strong>");
            $counter = 0;
            foreach ($feed as $item) {
                if ($counter >= self::BLOG_NEWS_LIMIT) break;
                $date = date("F d, Y", strtotime($item["date"]));
                $this->messageManager->addNotice(" * <a href='" . $item["link"] . "' target='_blank' title='" . __("Read more.") . "'>" . $item["title"] . "</a> (" . $date . ") - " . $item["desc"]);
                $counter++;
            }
            $hideBlogUrl = $this->urlBuilder->getUrl("*/*/updateconfig", array("label" => base64_encode(Version::PARAM_SHOW_BLOG_NEWS), "value" => 0));
            $this->messageManager->addNotice(__("<a href=\"%1\" title=\"Hide blog news.\" > Hide blog news block .</a > ", $hideBlogUrl));
        }
    }
}

?>
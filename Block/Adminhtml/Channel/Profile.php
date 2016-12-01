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
namespace Nostress\Koongo\Block\Adminhtml\Channel;

class Profile extends \Magento\Backend\Block\Widget\Container
{
    protected $_helper;
    protected $feedSingelton;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Nostress\Koongo\Helper\Version $helper, \Nostress\Koongo\Model\Channel\Feed $feedSingelton, array$data = [])
    {
        $this->_helper = $helper;
        $this->feedSingelton = $feedSingelton;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        if ($this->_helper->isDebugMode()) {
            $this->addButton("update_server_config", array("id" => "update_server_config", "label" => __("Update Server Config"), "class" => "default", "onclick" => "location.href='" . $this->getUrl("koongo/license/updateserverconfig") . "'",));
        }
        if ($this->_helper->isLicenseEmpty()) {
            $this->addButton("activate_trial", $this->_getTrialButtonData());
            $this->addButton("activate_live", $this->_getLiveButtonData());
        } else {
            if (!$this->_helper->isLicenseValid() || $this->_helper->isLicenseKeyT()) {
                $this->addButton("activate_live", $this->_getLiveButtonData());
            }
            $this->addButton("update_feeds", $this->_getUpdateFeedsButtonData());
            $this->addButton("add_new", $this->_getNewProfileButtonData());
            if (!$this->_helper->isLicenseValid()) {
                $this->_disableButton("update_feeds");
                $this->_disableButton("add_new");
            } elseif (!$this->_helper->isLicenseValid(true)) {
                $this->_disableButton("update_feeds");
                $this->addButton("check_license", $this->_getCheckButtonData());
            }
            if (!$this->feedSingelton->feedsLoaded()) {
                $this->removeButton("update_feeds");
                $this->removeButton("add_new");
                $this->addButton("get_feeds", $this->_getGetFeedsButtonData());
            }
        }
        return parent::_prepareLayout();
    }

    protected function _disableButton($name)
    {
        $this->updateButton($name, "disabled", "disabled");
    }

    protected function _getUpdateFeedsButtonData()
    {
        return ["id" => "update_feed_templates", "label" => __("Update Feed Templates"), "class" => "update", "onclick" => "location.href='" . $this->getUrl("*/*/updatefeeds") . "'",];
    }

    protected function _getGetFeedsButtonData()
    {
        return ["id" => "get_feed_templates", "label" => __("Get Feed Templates"), "class" => "primary", "onclick" => "location.href='" . $this->getUrl("*/*/updatefeeds") . "'",];
    }

    protected function _getNewProfileButtonData()
    {
        $okdigbezki = "addButtonProps";
        $asxgwecp = "addButtonProps";
        $addButtonProps = [
            "id" => "add_new_product",
            "label" => __("Add New Export Profile"),
            "class" => "primary",
            "before_html" => "<div data-bind=\"scope: 'new-profile-steps-wizard'\" >",
            "after_html" => "</div>",
            "data_attribute" => ["action" => "open-steps-wizard", "bind" => "click: open"],];
        return $addButtonProps;
    }

    protected function _getCheckButtonData()
    {
        return array("id" => "koongo_license_check_button", "label" => __("Check License Status"), "onclick" => "location.href='" . $this->getUrl("koongo/license/check") . "'");
    }

    protected function _getLiveButtonData()
    {
        return array("id" => "activate_live", "label" => __("Activate Live"), "class" => "primary", "onclick" => "location.href='" . $this->getUrl("koongo/license/liveform") . "'",);
    }

    protected function _getTrialButtonData()
    {
        return array("id" => "activate_trial", "label" => __("Activate Trial"), "class" => "default", "onclick" => "location.href='" . $this->getUrl("koongo/license/trialform") . "'",);
    }

    public function getNewProfileWizard($initData)
    {
        $wizardBlock = $this->getChildBlock("new-profile-steps-wizard");
        if ($wizardBlock) {
            $wizardBlock->setInitData($initData);
            return $wizardBlock->toHtml();
        }
        return "";
    }

    public function getTooltip($key = null)
    {
        return $this->_helper->renderTooltip($key);
    }

    protected function _toHtml()
    {
        $wizardHtml = "";
        if ($this->feedSingelton->feedsLoaded()) {
            $newProfilesWizardTitle = __("Add New Export Profile");
            $wizardHtml .= "<div data-role=\"step-wizard-dialog\" data-mage-init='{\"Magento_Ui/js/modal/modal\":{\"type\":\"slide\",\"title\":\"{$newProfilesWizardTitle}\",\"buttons\":[]}}' class=\"no-display\"> {$this->getNewProfileWizard([])}</div>";
        }
        $wizardHtml .= "<script type='text/javascript'>sp = " . $this->getRequest()->getParam("sp", 0) . "; 
        // show preview id\n\t\t        sf = " . $this->getRequest()->getParam("sf", 0) . "; // show ftp submission id\n\t\t    </script>\n        ";
        return $wizardHtml . parent::_toHtml();
    }
}

?>
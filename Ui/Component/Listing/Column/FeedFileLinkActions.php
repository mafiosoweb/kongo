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
namespace Nostress\Koongo\Ui\Component\Listing\Column;

class FeedFileLinkActions extends Actions\ColumnAbstract
{
    public function prepareDataSource(array$dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as &$item) {
                $profile = new\Magento\Framework\DataObject($item);
                $this->channel->setChannelCode($profile->getChannelCode());
                $name = $this->getData("name");
                $item[$name]["title"] = __("Feed Preview for Profile #%1", $profile->getEntityId());
                $item[$name]["manual_url"] = $this->channel->getManualUrl();
                $item[$name]["download_url"] = $this->urlBuilder->getUrl("koongo/channel_profile/download", ["entity_id" => $item["entity_id"]]);
                $item[$name]["preview_url"] = $this->urlBuilder->getUrl("koongo/channel_profile/preview", ["entity_id" => $item["entity_id"]]);
                $item[$name]["edit_general"] = $this->urlBuilder->getUrl("koongo/channel_profile/editgeneral", ["entity_id" => $item["entity_id"]]);
                $item[$name]["edit_url"] = "location.href = '" . $this->urlBuilder->getUrl("koongo/channel_profile_ftp/edit", ["entity_id" => $item["entity_id"]]) . "';";
                $config = json_decode($item["config"], true);
                if ($this->ftp->isFilled($config)) {
                    $item[$name]["upload_enabled"] = true;
                    $item[$name]["upload_url"] = $this->urlBuilder->getUrl("koongo/channel_profile_ftp/upload", ["entity_id" => $item["entity_id"]]);
                } else {
                    $item[$name]["upload_enabled"] = false;
                }
                $item[$name]["channel"] = $item["link"];
                $item[$name]["feed_url"] = $item["url"];
                $item[$name]["feed_file_type"] = ucfirst($item["file_type"]);
                $item[$name]["preview_help_url"] = $this->ftp->helper->getHelp("feed_preview");
            }
        }
        return parent::prepareDataSource($dataSource);
    }
}

?>
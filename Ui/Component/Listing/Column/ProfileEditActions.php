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

class ProfileEditActions extends Actions\ColumnAbstract
{
    const KOONGO_PROFILE_URL_PATH_EDIT_GENERAL = "koongo/channel_profile/editgeneral";
    const KOONGO_PROFILE_URL_PATH_EDIT_FILTER = "koongo/channel_profile/editfilter";
    const KOONGO_PROFILE_URL_PATH_EDIT_CATEGORIES = "koongo/channel_profile/editcategories";

    public function prepareDataSource(array$dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as &$item) {
                $name = $this->getData("name");
                if (isset($item["entity_id"])) {
                    $item[$name]["edit_feed"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EDIT_GENERAL, ["entity_id" => $item["entity_id"]]), "label" => __("Attributes")];
                    $item[$name]["edit_filter"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EDIT_FILTER, ["entity_id" => $item["entity_id"]]), "label" => __("Filter")];
                    $this->channel->setChannelCode($item["channel_code"]);
                    $categoriesLinkLabel = $this->channel->getLabel() . " " . __("Categories");
                    if (empty($item["taxonomy_code"])) $categoriesLinkLabel .= " (N/A)";
                    $item[$name]["edit_taxonomy"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EDIT_CATEGORIES, ["entity_id" => $item["entity_id"]]), "label" => $categoriesLinkLabel];
                }
            }
        }
        return parent::prepareDataSource($dataSource);
    }
}

?>
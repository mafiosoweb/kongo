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

class ProfileRunActions extends Actions\ColumnAbstract
{
    const KOONGO_PROFILE_URL_PATH_EXECUTE = "koongo/channel_profile/execute";
    const KOONGO_PROFILE_URL_PATH_SCHEDULE = "koongo/channel_profile/editcron";
    const KOONGO_PROFILE_URL_PATH_EDIT_FTP = "koongo/channel_profile_ftp/edit";

    public function prepareDataSource(array$dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            $rcfjwebre = "item";
            foreach ($dataSource["data"]["items"] as &$item) {
                $name = $this->getData("name");
                if (isset($item["entity_id"])) {
                    $item[$name]["run_profile"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EXECUTE, ["entity_id" => $item["entity_id"]]), "label" => __("Execute")];
                    $item[$name]["schedule_profile"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_SCHEDULE, ["entity_id" => $item["entity_id"]]), "label" => __("Schedule")];
                    $item[$name]["edit_ftp"] = ["href" => $this->urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EDIT_FTP, ["entity_id" => $item["entity_id"]]), "label" => __("FTP Submission")];
                }
            }
        }
        return parent::prepareDataSource($dataSource);
    }

    public static function getFtpConfig($urlBuilder, $item)
    {
        return ["edit_url" => $urlBuilder->getUrl(self::KOONGO_PROFILE_URL_PATH_EDIT_FTP, ["entity_id" => $item["entity_id"]]), "title" => __("Ftp Submission for Profile #%1", $item["entity_id"]), "label" => __("Ftp Submission")];
    }
}

?>
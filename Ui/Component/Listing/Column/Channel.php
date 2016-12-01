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

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Channel extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = "channel_code";
    const ALT_FIELD = "link";

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, \Nostress\Koongo\Model\Channel $channel, \Magento\Framework\UrlInterface $urlBuilder, array$components = [], array$data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->channel = $channel;
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array$dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            $fieldName = $this->getData("name");
            foreach ($dataSource["data"]["items"] as &$item) {
                $profile = new\Magento\Framework\DataObject($item);
                $channelCode = "empty_channel_code";
                if (isset($item["channel_code"])) $channelCode = $item["channel_code"];
                $this->channel->setChannelCode($channelCode);
                $item[$fieldName . "_src"] = $this->channel->getLogoUrl();
                $item[$fieldName . "_alt"] = $this->getAlt($item);
                $item[$fieldName . "_link"] = $this->urlBuilder->getUrl("koongo/channel_prodie/edit_attributes", ["id" => $profile->getEntityId()]);
                $item[$fieldName . "_orig_src"] = $item[$fieldName . "_src"];
            }
        }
        return $dataSource;
    }

    protected function getAlt($row)
    {
        $altField = $this->getData("config/altField") ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}

?>
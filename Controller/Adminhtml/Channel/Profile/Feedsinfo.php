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

class Feedsinfo extends \Magento\Backend\App\Action
{
    protected $client;
    protected $version;

    public function __construct(Context $context, \Nostress\Koongo\Model\Api\Client $client, \Nostress\Koongo\Helper\Version $version)
    {
        $this->client = $client;
        $this->version = $version;
        parent::__construct($context);
    }

    public function execute()
    {
        $info = $this->client->getFeedsInfo();
        $this->_renderFeedsList($info["feeds_new"], __("<strong>New feeds and channels</strong>"));
        $this->_renderFeedsList($info["feeds_update"], __("<strong>Updated feeds and channels</strong>"));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath("*/*/");
    }

    protected function _renderFeedsList($list, $label)
    {
        if (!empty($list)) {
            $this->messageManager->addNotice(__($label));
            foreach ($list as $item) {
                $channelLink = $this->version->getKoongoWebsiteUrl() . str_replace("_", "-", $item["channel_code"]) . ".html";
                $this->messageManager->addNotice(" - " . $item["link"] . " " . __("(<a href='%1' target='_blank'>more information</a>)", $channelLink));
            }
        }
    }
}

?>
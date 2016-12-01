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
namespace Nostress\Koongo\Ui\Component\Listing\Column\Actions;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class ColumnAbstract extends Column
{
    protected $channel;
    protected $ftp;
    protected $urlBuilder;
    protected $_helper;
    protected $request;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel $channel, \Nostress\Koongo\Helper\Version $helper, \Nostress\Koongo\Model\Channel\Profile\Ftp $ftp, RequestInterface $request, array$components = [], array$data = [])
    {
        $this->channel = $channel;
        $this->ftp = $ftp;
        $this->urlBuilder = $urlBuilder;
        $this->_helper = $helper;
        $this->request = $request;
        $data["is_license_valid"] = $this->_helper->isLicenseValid();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array$dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as &$item) {
                if (!isset($item["is_license_valid"])) {
                    $item["is_license_valid"] = $this->getData("is_license_valid");
                }
            }
        }
        return parent::prepareDataSource($dataSource);
    }
}

?>
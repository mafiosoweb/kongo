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

class Status extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $statusOptions;
    protected $translation;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, \Magento\Framework\UrlInterface $urlBuilder, \Nostress\Koongo\Ui\Component\Listing\Column\Status\Options $statusOptions, \Nostress\Koongo\Model\Translation $translation, array$components = [], array$data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->statusOptions = $statusOptions;
        $this->translation = $translation;
    }

    public function prepareDataSource(array$dataSource)
    {
        $statuses = $this->statusOptions->toIndexedArray();
        $cssMap = $this->statusOptions->getCssClassMap();
        if (isset($dataSource["data"]["items"])) {
            $fieldName = $this->getData("name");
            foreach ($dataSource["data"]["items"] as &$item) {
                $profile = new\Magento\Framework\DataObject($item);
                $status = __("Unknown");
                $statusIndex = $profile->getStatus();
                if (isset($statuses[$statusIndex])) $status = $statuses[$statusIndex];
                $cellLabel = strtoupper($status);
                $class = $this->statusOptions->getDefaultCssClass();
                if (isset($cssMap[$statusIndex])) $class = $cssMap[$statusIndex];
                $item[$fieldName] = "<span class=\"grid-severity-" . $class . "\"><span>" . $cellLabel . "</span></span>";
            }
        }
        return $dataSource;
    }
}

?>
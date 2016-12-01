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

class Message extends \Magento\Ui\Component\Listing\Columns\Column
{
    const MAX_CELL_LABEL_LEN = 40;
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
        if (isset($dataSource["data"]["items"])) {
            $fieldName = $this->getData("name");
            foreach ($dataSource["data"]["items"] as &$item) {
                $profile = new\Magento\Framework\DataObject($item);
                $message = $this->translation->replaceActionLinks($profile->getMessage());
                $status = ucfirst(strtolower(strip_tags($profile->getStatus())));
                $cellLabel = $message;
                if ($status == __("Error")) $cellLabel = trim(substr($cellLabel, 0, self::MAX_CELL_LABEL_LEN)) . "... <a href='#' title='" . __("Click to view full message") . "'>" . __("Read More") . "</a>";
                $item[$fieldName . "_html"] = $cellLabel;
                $item[$fieldName . "_message"] = $message;
                $item[$fieldName . "_status"] = $status;
                $item[$fieldName . "_title"] = __("Profile #%1 - Status & Message", $profile->getEntityId());
            }
        }
        return $dataSource;
    }
}

?>
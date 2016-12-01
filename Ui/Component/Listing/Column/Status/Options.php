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
namespace Nostress\Koongo\Ui\Component\Listing\Column\Status;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    protected $options;

    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->options = [];
        $this->options[] = ["value" => 0, "label" => __("New")];
        $this->options[] = ["value" => 1, "label" => __("Running")];
        $this->options[] = ["value" => 2, "label" => __("Interrupted")];
        $this->options[] = ["value" => 3, "label" => __("Error")];
        $this->options[] = ["value" => 4, "label" => __("Finished")];
        $this->options[] = ["value" => 5, "label" => __("Enabled")];
        $this->options[] = ["value" => 6, "label" => __("Disabled")];
        return $this->options;
    }

    public function getCssClassMap()
    {
        $map = [];
        $map[3] = "critical";
        $map[2] = "critical";
        $map[4] = "notice";
        $map[0] = "minor";
        $map[1] = "minor";
        $map[5] = "minor";
        $map[6] = "minor";
        return $map;
    }

    public function getDefaultCssClass()
    {
        return "minor";
    }

    public function toIndexedArray()
    {
        $options = $this->toOptionArray();
        $indexedArray = [];
        foreach ($options as $option) {
            $indexedArray[$option["value"]] = $option["label"];
        }
        return $indexedArray;
    }
}

?>
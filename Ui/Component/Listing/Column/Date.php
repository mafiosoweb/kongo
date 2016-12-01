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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Date extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $timezone;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, TimezoneInterface $timezone, array$components = [], array$data = [])
    {
        $this->timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array$dataSource)
    {
        return $dataSource;
    }
}

?>
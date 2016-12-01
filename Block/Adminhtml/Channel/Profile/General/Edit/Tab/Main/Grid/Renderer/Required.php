<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */

/**
 * Channel profile feed queue grid block required column item renderer
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab\Main\Grid\Renderer;

/**
 * 
 */
class Required extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var array
     */
    protected static $_options;

    /**
     * Constructor for Grid Renderer Status
     *
     * @return void
     */
    protected function _construct()
    {
        self::$_options = [
            1 => __('Required'),
            0 => __('Optional'),

        ];
        parent::_construct();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return __($this->getRequiredValue($row->getRequired()));
    }

    /**
     * @param string $required
     * @return \Magento\Framework\Phrase
     */
    public static function getRequiredValue($status)
    {
        if (isset(self::$_options[$status])) {
            return self::$_options[$status];
        }

        return __('Optional');
    }
}

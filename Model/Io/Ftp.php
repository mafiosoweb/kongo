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
* Export profile main class
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Io;

class Ftp  extends \Magento\Framework\Filesystem\Io\Ftp implements Listable
{
    public function getItems() {
    
        if (is_array($children = @ftp_rawlist( $this->_conn, '.'))) {
            
            $items = array(
                '..' => array(
                    'type' => self::DIR,
                    'path' => dirname( $this->pwd())
                 )
            );
            
            foreach ($children as $child) {
                $chunks = preg_split("/\s+/", $child);
                list($item['permissions'], $item['number'], $item['uid'], $item['gid'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks;
                $item['type'] = $chunks[0]{0} === 'd' ? self::DIR : self::FILE;
                array_splice($chunks, 0, 8);
                
                if( strpos( $item['time'], ':') !== false) {
                    $item['year'] = date( 'Y');
                } else {
                    $item['year'] = $item['time'];
                    $item['time'] = null;
                }
                $item['mtime'] = $item['day'].". ".$item['month']." ".$item['year'];
                if( $item['time']) {
                    $item['mtime'] .= " ".$item['time'];
                }
                
                $items[implode(" ", $chunks)] = $item;
            }
    
            return $items;
        }
    
        // Throw exception or return false < up to you
        return false;
    }
}
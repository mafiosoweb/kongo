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

class Sftp  extends \Magento\Framework\Filesystem\Io\Sftp implements Listable
{
    /**
     * Write a file
     *
     * @param string $filename
     * @param string $source string data or local file name
     * @param int $mode ignored parameter
     * @return bool
     */
    public function write($filename, $source, $mode = null)
    {
        $mode = is_readable($source) ? 1 : 2;
        return $this->_connection->put($filename, $source, $mode);
    }
    
    public function getItems() {
        
        $list = $this->rawls();
        if( !$list) {
            return false;
        }
        foreach( $list as $name => &$row) {
            
            if( $name == '.') {
                unset( $list[$name]);
            }
            
            $row['type'] = $row['type'] == 1 ?  self::FILE : self::DIR;
            $row['mtime'] = date( 'Y-m-d', $row['mtime']);
            $row['atime'] = date( 'Y-m-d', $row['atime']);
        }
    
        return $list;
    }
}
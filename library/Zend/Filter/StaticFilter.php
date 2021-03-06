<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticFilter
{
    /**
     * @var Broker
     */
    protected static $broker;

    /**
     * Set broker for resolving filter classes
     * 
     * @param  Broker $broker 
     * @return void
     */
    public static function setBroker(Broker $broker = null)
    {
        self::$broker = $broker;
    }

    /**
     * Get broker for loading filter classes
     * 
     * @return Broker
     */
    public static function getBroker()
    {
        if (null === self::$broker) {
            static::setBroker(new FilterBroker());
        }
        return self::$broker;
    }

    /**
     * Returns a value filtered through a specified filter class, without requiring separate
     * instantiation of the filter object.
     *
     * The first argument of this method is a data input value, that you would have filtered.
     * The second argument is a string, which corresponds to the basename of the filter class,
     * relative to the Zend_Filter namespace. This method automatically loads the class,
     * creates an instance, and applies the filter() method to the data input. You can also pass
     * an array of constructor arguments, if they are needed for the filter class.
     *
     * @param  mixed        $value
     * @param  string       $classBaseName
     * @param  array        $args          OPTIONAL
     * @return mixed
     * @throws Exception\ExceptionInterface
     */
    public static function execute($value, $classBaseName, array $args = array())
    {
        $broker = static::getBroker();

        $filter = $broker->load($classBaseName, $args);
        $filteredValue = $filter->filter($value);

        // Unregistering filter to allow different arguments on subsequent calls
        $broker->unregister($classBaseName);

        return $filteredValue;
    }
}

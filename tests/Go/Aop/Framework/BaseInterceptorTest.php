<?php
/**
 * Go! OOP&AOP PHP framework
 *
 * @copyright     Copyright 2013, Lissachenko Alexander <lisachenko.it@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

namespace Go\Aop\Framework;

use Go\Aop\Intercept\Invocation;

class BaseInterceptorTest extends AbstractInterceptorTest
{
    /**
     * Concrete class name for mock, should be redefined with LSB
     */
    const INVOCATION_CLASS = 'Go\Aop\Intercept\Invocation';

    public static function setUpBeforeClass()
    {
        if (!defined('Go\Aop\Framework\IS_MODERN_PHP')) {
            define('Go\Aop\Framework\IS_MODERN_PHP', PHP_VERSION_ID > 50400);
        }
    }

    public function testReturnsRawAdvice()
    {
        $sequence = array();
        $advice   = $this->getAdvice($sequence);

        $interceptor = new BaseInterceptor($advice);
        $this->assertEquals($advice, $interceptor->getRawAdvice());
    }

    public function testCanSerializeInterceptor()
    {
        $sequence = array();
        $advice   = $this->getAdvice($sequence);

        $interceptor = new BaseInterceptor($advice);
        $result      = serialize($interceptor);
        $expected = 'C:32:"Go\Aop\Framework\BaseInterceptor":178:{a:2:{s:12:"adviceMethod";a:3:{s:5:"scope";s:6:"aspect";s:6:"method";s:26:"Go\Aop\Framework\{closure}";s:6:"aspect";s:36:"Go\Aop\Framework\BaseInterceptorTest";}s:5:"order";i:-1;}}';
        $this->assertEquals($expected, $result);
    }

    public function testCanUnserializeInterceptor()
    {
        $advice = $this->getAdvice($sequence);
        $mock   = $this->getMock('Go\Aop\Framework\BaseInterceptor', array('unserializeAdvice'), array($advice));
        $mock
            ->staticExpects($this->any())
            ->method('unserializeAdvice')
            ->will(
                $this->returnCallback(
                    function () use ($advice) {
                        return $advice;
                    }
                )
            );
        $mockClass      = get_class($mock);
        $mockNameLength = strlen($mockClass);
        // Trick to mock unserialization of advice
        $serialized = 'C:' . $mockNameLength .':"' . $mockClass . '":178:{a:2:{s:12:"adviceMethod";a:3:{s:5:"scope";s:6:"aspect";s:6:"method";s:26:"Go\Aop\Framework\{closure}";s:6:"aspect";s:36:"Go\Aop\Framework\BaseInterceptorTest";}s:5:"order";i:-1;}}';
        $result     = unserialize($serialized);
        $this->assertEquals($advice, $result->getRawAdvice());
    }
}
 
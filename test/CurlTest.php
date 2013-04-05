<?php
namespace F3\CurlWrapper\Test;

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Curl.php');

use F3\CurlWrapper\Curl;

/**
 * Only some indirect tests are available due to use of system functions.
 *
 * @package CurlWrapper
 * @version $id$
 * @copyright Alexey Karapetov
 * @author Alexey Karapetov <karapetov@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 */
class CurlTest
    extends \PHPUnit_Framework_TestCase
{
    public function testHandlesShouldBeDifferent()
    {
        $c1 = new Curl();
        $c2 = new Curl();
        $this->assertNotEquals($c1->getHandle(), $c2->getHandle());

        $c3 = clone($c1);
        $this->assertNotEquals($c1->getHandle(), $c3->getHandle());
    }

    public function testGetInfoSetOpt()
    {
        $c = new Curl('http://localhost');
        $this->assertEquals('http://localhost', $c->getInfo(CURLINFO_EFFECTIVE_URL));

        $c = new Curl();
        $this->assertEquals('', $c->getInfo(CURLINFO_EFFECTIVE_URL));
        $c->setOpt(CURLOPT_URL, 'http://foo');
        $this->assertEquals('http://foo', $c->getInfo(CURLINFO_EFFECTIVE_URL));
        $c->setOptArray(array(CURLOPT_URL => 'http://bar'));
        $this->assertEquals('http://bar', $c->getInfo(CURLINFO_EFFECTIVE_URL));
    }

    public function testGetVersion()
    {
        $this->assertEquals(curl_version(), Curl::version());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Attempts count is not positive: -42
     *
     */
    public function testExecInvalidAttemptsCount()
    {
        $c = new Curl();
        $c->exec(-42);
    }

    public function testExecAndErrors()
    {
        $h = curl_init();
        curl_exec($h);
        $expectedError = curl_error($h);

        $c = new Curl();
        $c->exec();
        $this->assertEquals(CURLE_URL_MALFORMAT, $c->errno());
        $this->assertEquals($expectedError, $c->error());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExecException()
    {
        $c = new Curl();
        $c->exec(1, true);
    }
}

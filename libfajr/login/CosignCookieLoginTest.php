<?php
/**
 * This file contains tests for Validator class
 *
 * @package Fajr
 * @subpackage Tests
 * @author Peter Peresini <ppershing+fajr@gmail.com>
 */
namespace fajr\libfajr\login;
use PHPUnit_Framework_TestCase;
use fajr\libfajr\pub\exceptions\LoginException;
use fajr\libfajr\login\CosignCookieLogin;
use fajr\libfajr\pub\connection\HttpConnection;
/**
 * @ignore
 */
require_once 'test_include.php';

/**
 * @ignore
 */
class CosignCookieLoginTest extends PHPUnit_Framework_TestCase
{
  private $responseAlreadyLogged;
  private $responseNotLogged;
  private $connection;

  public function setUp() {
    $this->responseAlreadyLogged = file_get_contents(__DIR__.'/testdata/cosignAlreadyLogged.dat');
    $this->responseNotLogged = file_get_contents(__DIR__.'/testdata/cosignNotLogged.dat');
    $this->connection = $this->getMock('\fajr\libfajr\pub\connection\HttpConnection');
  }

  public function testLoginOk() {
    $this->connection->expects($this->once())
                     ->method('addCookie')
                     ->will($this->returnValue(null));
    $this->connection->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($this->responseAlreadyLogged));
    $login = new CosignCookieLogin('cookie');
    $login->login($this->connection);
  }

  public function testLoginFailed() {
    $this->connection->expects($this->once())
                     ->method('addCookie')
                     ->will($this->returnValue(null));
    $this->connection->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($this->responseNotLogged));
    $login = new CosignCookieLogin('cookie');
    $this->setExpectedException("fajr\libfajr\pub\exceptions\LoginException");
    $login->login($this->connection);
  }


}

?>
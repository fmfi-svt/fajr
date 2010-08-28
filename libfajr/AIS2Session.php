<?php
/* {{{
Copyright (c) 2010 Martin Sucha

 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:

 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.
 }}} */

use \fajr\libfajr\connection\HttpConnection;
use \fajr\libfajr\pub\login\Login;
/**
 * Trieda reprezentujúca session systému (stav prihlásenia, ...)
 *
 * @author majak, ms
 */

class AIS2Session
{
  private $aisLogin = null;
  private $cosignLogin = null;

  public function  __construct(Login $aisLogin, Login $cosignLogin) {
    $this->aisLogin = $aisLogin;
    $this->cosignLogin = $cosignLogin;
  }

  public function login(HttpConnection $connection) {
    $this->cosignLogin->login($connection);
    $this->aisLogin->login($connection);
    return true;
  }

  public function logout(HttpConnection $connection) {
    return $this->aisLogin->logout($connection);
    return $this->cosignLogin->logout($connection);
  }

  public function isLoggedIn(HttpConnection $connection) {
    return $this->aisLogin->isLoggedIn($connection) && $this->cosignLogin->isLoggedIn($connection);
  }
    

}

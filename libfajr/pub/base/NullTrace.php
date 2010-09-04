<?php
/* {{{
Copyright (c) 2010 Peter Perešíni

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

/**
 * Dummy Trace object doing nothing
 *
 * PHP version 5.3.0
 *
 * @package    Fajr
 * @subpackage Libfajr
 * @author     Peter Peresini <ppershing@fks.sk>
 * @filesource
 */

namespace fajr\libfajr\pub\base;
use fajr\libfajr\pub\base\Trace;
/**
 * Represents tracer object which does nothing. It shoud be convenient to
 * pass around as default value of trace parameter.
 *
 * @package    Fajr
 * @subpackage Libfajr
 * @author     Peter Peresini <ppershing@fks.sk>
 */
class NullTrace implements Trace {
  public function setHeader($header)
  {
  }

  public function tlog($text)
  {
  }

  public function tlogData($text)
  {
  }

  public function tlogVariable($name, $variable)
  {
  }

  public function addChild($header = "")
  {
    return $this;
    // TODO(ppershing): is returning $this really safe?
    // Someone may try to compare instances of childs for
    // example.
    // return new NullTrace();
  }
}
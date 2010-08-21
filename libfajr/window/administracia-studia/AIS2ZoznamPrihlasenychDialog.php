<?php
/* {{{
Copyright (c) 2010 Martin Králik

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
 * TODO
 *
 * PHP version 5.3.0
 *
 * @package    Fajr
 * @subpackage Libfajr__Window__Administracia-studia
 * @author     Martin Kralik <majak47@gmail.com>
 * @filesource
 */
use fajr\libfajr\base\Trace;
/**
 * Trieda pre dialóg so zoznamom prihlásených študentov na termín.
 *
 * @package    Fajr
 * @subpackage Libfajr__Window__Administracia-studia
 * @author     Martin Kralik <majak47@gmail.com>
 */
class AIS2ZoznamPrihlasenychDialog extends AIS2AbstractDialog
{
  public function getZoznamPrihlasenych(Trace $trace)
  {
  $this->openIfNotAlready($trace);
    $response = $this->executor->requestContent($trace);
    $constructor = new AIS2TableConstructor();
    return $constructor->createTableFromHtml($trace->addChild("Parsing table"), $response,
        'prihlaseniTable_dataView');
  }
}
?>

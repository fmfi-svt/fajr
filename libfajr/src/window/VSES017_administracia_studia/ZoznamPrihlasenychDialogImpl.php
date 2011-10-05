<?php
// Copyright (c) 2010 The Fajr authors (see AUTHORS).
// Use of this source code is governed by a MIT license that can be
// found in the LICENSE file in the project root directory.

/**
 * TODO
 *
 * PHP version 5.3.0
 *
 * @package    Libfajr
 * @subpackage Window__VSES017_administracia_studia
 * @author     Martin Králik <majak47@gmail.com>
 * @filesource
 */
namespace libfajr\window\VSES017_administracia_studia;

use libfajr\window\VSES017_administracia_studia\ZoznamPrihlasenychDialog;

use libfajr\base\Trace;
use libfajr\window\AIS2AbstractDialog;
use libfajr\data_manipulation\AIS2TableParser;
use libfajr\window\DialogParent;
use libfajr\window\DialogData;

/**
 * Trieda pre dialóg so zoznamom prihlásených študentov na termín.
 *
 * @package    Libfajr
 * @subpackage Window__VSES017_administracia_studia
 * @author     Martin Králik <majak47@gmail.com>
 */
class ZoznamPrihlasenychDialogImpl extends AIS2AbstractDialog
    implements ZoznamPrihlasenychDialog
{
  /**
   * @var AIS2TableParser
   */
  private $parser;

  public function __construct(Trace $trace, DialogParent $parent,
      DialogData $data, AIS2TableParser $parser = null)
  {
    parent::__construct($trace, $parent, $data);
    $this->parser = ($parser !== null) ? $parser :  new AIS2TableParser;
  }

  public function getZoznamPrihlasenych(Trace $trace)
  {
  $this->openIfNotAlready($trace);
    $response = $this->executor->requestContent($trace);
    return $this->parser->createTableFromHtml($trace->addChild("Parsing table"), $response,
        'prihlaseniTable_dataView');
  }
}

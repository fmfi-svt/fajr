<?php
// Copyright (c) 2010 The Fajr authors.
// Use of this source code is governed by a MIT license that can be
// found in the LICENSE file in the project root directory.

// TODO(??): missing author

namespace fajr\libfajr\pub\window\VSES017_administracia_studia;

use fajr\libfajr\pub\window\LazyDialog;
use fajr\libfajr\pub\base\Trace;

interface HodnoteniaPriemeryScreen extends LazyDialog
{
  /**
   * @returns SimpleDataTable tabulka hodnoteni
   */
  public function getHodnotenia(Trace $trace);

  /**
   * @returns SimpleDataTable tabulka priemerov
   */
  public function getPriemery(Trace $trace);

}
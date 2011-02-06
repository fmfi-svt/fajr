<?php
/**
 * Provides cryptographically secure random bytes generation.
 *
 * Warning: Use SecureRandom instead of directly using this class!
 *
 * @copyright  Based on frostschutz's post at
 *             http://forums.thedailywtf.com/forums/t/16453.aspx
 * @copyright  Copyright (c) 2011 The Fajr authors (see AUTHORS).
 *             Use of this source code is governed by a MIT license that can be
 *             found in the LICENSE file in the project root directory.
 * @package    Fajr
 * @subpackage Security
 * @author     frostschutz
 * @author     Peter Perešíni <ppershing+fajr@gmail.com>
 * @filesource
 */
namespace fajr\security;

use fajr\libfajr\base\Preconditions;

/**
 * Generates random bytes using openssl.
 *
 * Warning: Use SecureRandom instead of directly using this class!
 *
 * @package    Fajr
 * @subpackage Security
 * @author     Peter Perešíni <ppershing+fajr@gmail.com>
 */
class SecureRandomOpensslProvider implements SecureRandomProvider {
  /**
   * Check whether we can generate bytes using open SSL.
   *
   * @returns bool true if openssl is available
   */
  public static function isAvailable() {
    return function_exists('openssl_random_pseudo_bytes');
  }

  /**
   * Returns a securely generated random bytes from open SSL.
   *
   * Warning: use SecureRandom::random() instead
   *
   * @param int $count number of random bytes to be generated
   *
   * @returns bytes|false generated bytes or false on error
   */
  public function randomBytes($count) {
    Preconditions::check(is_int($count));
    Preconditions::check($count > 0);

    if (!self::isAvailable()) {
      return false;
    }

    // TODO(ppershing): check this on windows: from user comments in manual page:
    // FYI, openssl_random_pseudo_bytes() can be incredibly slow under Windows, to the point of being
    // unusable.

    $output = openssl_random_pseudo_bytes($count, $strong);

    if ($strong === true) { // openssl claim the random is strong
      return $output;
    }
    return false;
  }
}

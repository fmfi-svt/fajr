<?php
// Copyright (c) 2010 The Fajr authors (see AUTHORS).
// Use of this source code is governed by a MIT license that can be
// found in the LICENSE file in the project root directory.

/**
 * The main logic of fajr application.
 *
 * @package    Fajr
 * @subpackage Fajr
 * @author     Martin Králik <majak47@gmail.com>
 */
namespace fajr;
use Exception;
use fajr\ArrayTrace;
use fajr\libfajr\pub\base\Trace;
use fajr\injection\Injector;
use fajr\libfajr\AIS2Session;
use fajr\libfajr\base\SystemTimer;
use fajr\libfajr\connection;
use fajr\libfajr\pub\connection\HttpConnection;
use fajr\libfajr\pub\login\CosignServiceCookie;
use fajr\libfajr\pub\base\NullTrace;
use fajr\libfajr\pub\login\AIS2Login;
use fajr\libfajr\pub\login\LoginFactoryImpl;
use fajr\libfajr\pub\connection\AIS2ServerConnection;
use fajr\libfajr\pub\connection\AIS2ServerUrlMap;
use fajr\Request;
use fajr\Response;

/**
 * This is "main()" of the fajr. It instantiates all neccessary
 * objects, query ais and renders results.
 *
 * @package    Fajr
 * @subpackage Fajr
 * @author     Martin Králik <majak47@gmail.com>
 */
class Fajr {
  /**
   * @var Injector $injector dependency injector.
   */
  private $injector;

  /**
   * Constructor.
   *
   * @param Injector $injector dependency injector.
   */
  public function __construct(Injector $injector)
  {
    $this->injector = $injector;
  }

  /**
   * WARNING: Must be called before provideConnection().
   */
  private function regenerateSessionOnLogin()
  {
    $login = Input::get('login');
    $krbpwd = Input::get('krbpwd');
    $cosignCookie = Input::get('cosignCookie');

    // FIXME this should be refactored
    if (($login !== null && $krbpwd !== null) || ($cosignCookie !== null)) {
      // we are going to log in, so we get a clean session
      // this needs to be done before a connection
      // is created, because we pass cookie file name
      // that contains session_id into AIS2CurlConnection
      // If we regenerated the session id afterwards,
      // we could not find the cookie file after a redirect
      FajrUtils::dropSession();
    }
  }

  /**
   * Provides login object created from POST-data.
   *
   * @returns AIS2Login
   */
  private function provideLogin()
  {
    // TODO(ppershing): use injector here
    $factory = new LoginFactoryImpl();

    $login = Input::get('login'); Input::set('login', null);
    $krbpwd = Input::get('krbpwd'); Input::set('krbpwd', null);
    $cosignCookie = Input::get('cosignCookie'); Input::set('cosignCookie', null);

    //TODO(ppershing): create hidden field "loginType" in the form
    if ($login !== null && $krbpwd !== null) {
      return $factory->newLoginUsingCosign($login, $krbpwd);
    } else if ($cosignCookie !== null) {
      $cosignCookie = CosignServiceCookie::fixCookieValue($cosignCookie);
      // TODO(anty): change to use correct domain and cookie name
      return $factory->newLoginUsingCookie(new CosignServiceCookie('cosign-filter-ais2.uniba.sk', $cosignCookie, 'ais2.uniba.sk'));
    } else {
      return null;
    }
  }

  // TODO(ppershing): We need to do something about these connections.
  // Currently, this is really ugly solution and should be refactored.
  private $rawStatsConnection;
  private $statsConnection;

  private function provideConnection()
  {
    $connection = new connection\CurlConnection(FajrUtils::getCookieFile());

    $this->rawStatsConnection = new connection\StatsConnection($connection, new SystemTimer());

    $connection = new connection\GzipDecompressingConnection($this->rawStatsConnection, FajrConfig::getDirectory('Path.Temporary'));
    $connection = new connection\AIS2ErrorCheckingConnection($connection);

    $this->statsConnection = new connection\StatsConnection($connection, new SystemTimer());
    return $this->statsConnection;
  }

  /**
   * Set an exception to be displayed in DisplayManager
   * @param Exception $ex
   */
  private function setException(Exception $ex) {
    $this->response->set('exception', $ex);
    $this->response->set('showStackTrace', FajrConfig::get('Debug.Exception.ShowStacktrace'));
  }

  /**
   * Runs the whole logic. It is fajr's main()
   *
   * @returns void
   */
  public function run()
  {
    $this->injector->getInstance('SessionInitializer.class')->startSession();

    $timer = new SystemTimer();

    // TODO(ppershing): use injector here!
    $trace = new NullTrace();

    if (FajrConfig::get('Debug.Trace') === true) {
      $trace = new ArrayTrace($timer, "--Trace--");
    }

    // TODO(anty): do we want DisplayManager? If so, use injector here
    $this->displayManager = new DisplayManager();

    $this->request = new Request();
    $this->response = new Response();

    try {
      Input::prepare();

      $this->regenerateSessionOnLogin();
      $connection = $this->provideConnection();
      $this->runLogic($trace, $connection);
    } catch (LoginException $e) {
      if ($connection) {
        FajrUtils::logout($connection);
      }

      $this->setException($e);
      $this->response->setTemplate('exception');
    } catch (Exception $e) {
      $this->setException($e);
      $this->response->setTemplate('exception');
    }

    $this->displayManager->setBase(FajrUtils::basePath());

    $trace->tlog("everything done, generating html");

    if (FajrConfig::get('Debug.Trace')===true) {
      $this->response->set('trace', $trace);
    }
    echo $this->displayManager->display($this->response);
  }

  public function runLogic(Trace $trace, HttpConnection $connection)
  {
      $serverConnection = new AIS2ServerConnection($connection,
          new AIS2ServerUrlMap(FajrConfig::get('AIS2.ServerName')));
      $timer = new SystemTimer();

      $this->request->setAisConnection($serverConnection);

      if (Input::get('logout') !== null) {
        FajrUtils::logout($serverConnection);
        FajrUtils::redirect();
      }

      $loggedIn = FajrUtils::isLoggedIn($serverConnection);

      // TODO(anty): use injector or some other configuration method
      $actionMap = array(
        'moje.hodnotenie' => '\\fajr\\controller\\studium\\MojeHodnotenieController',
        'moje.terminyHodnotenia' => '\\fajr\\controller\\studium\\MojeTerminyHodnoteniaController',
        'moje.predmety' => '\\fajr\\controller\\studium\\MojePredmetyController',
        'studium.zoznamTerminovHodnotenia' => '\\fajr\\controller\\studium\\ZoznamTerminovHodnoteniaController',
      );

      $cosignLogin = $this->provideLogin();
      if (!$loggedIn && $cosignLogin != null) {
          FajrUtils::login($trace->addChild("logging in"), $cosignLogin, $serverConnection);
          $loggedIn = true;
      }

      if ($loggedIn) {
        $this->response->set('logoutUrl', FajrUtils::linkUrl(array('logout'=>true)));

        $controller = new \fajr\controller\studium\LegacyStudiumController();

        $controller->invokeAction($trace, 'Legacy', $this->request, $this->response);

        $this->response->set("stats_connections",
            $this->statsConnection->getTotalCount());
        $this->response->set("stats_rawBytes",
            $this->rawStatsConnection->getTotalSize());
        $this->response->set("stats_bytes",
            $this->statsConnection->getTotalSize());
        $this->response->set("stats_connectionTime",
            sprintf("%.3f", $this->statsConnection->getTotalTime()));
        $this->response->set("stats_totalTime",
            sprintf("%.3f", $timer->getElapsedTime()));
      }
      else
      {
        $this->response->setTemplate('welcome');
      }
  }
}

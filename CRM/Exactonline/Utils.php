<?php

// load the Exact php lib
require __DIR__ . '/../../exact-php-client/vendor/autoload.php';

class CRM_Exactonline_Utils {
  public $exactConnection;

  private $exactOnlineURL;
  private $exactOnlineClientID;
  private $exactOnlineClientSecret;
  private $exactOnlineAuthorizationCode;
  private $exactOnlineAccessToken;
  private $exactOnlineRefreshToken;
  private $exactOnlineExpiresIn;
  private $exactOnlineDivision;


  public function __construct() {
    $this->exactConnection = new \Picqer\Financials\Exact\Connection();

    // set authorize callback url
    global $base_url;
    $this->exactConnection->setRedirectUrl($base_url . CRM_Utils_System::url('/civicrm/exactonline/callback', 'reset=1'));

    // set refresh token callback
    $this->exactConnection->setTokenUpdateCallback('CRM_Exactonline_Utils::exactTokenUpdateCallback');

    // load the settings
    $this->exactOnlineURL = $this->loadParam('ExactOnlineURL', 'https://start.exactonline.be');
    $this->exactConnection->setBaseUrl($this->exactOnlineURL);

    $this->exactOnlineClientID = $this->loadParam('ExactOnlineClientID', '');
    $this->exactConnection->setExactClientId($this->exactOnlineClientID);

    $this->exactOnlineClientSecret = $this->loadParam('ExactOnlineClientSecret', '');
    $this->exactConnection->setExactClientSecret($this->exactOnlineClientSecret);

    $this->exactOnlineAuthorizationCode = $this->loadParam('ExactOnlineAuthorizationCode', '');
    $this->exactConnection->setAuthorizationCode($this->exactOnlineAuthorizationCode);

    $this->exactOnlineAccessToken = $this->loadParam('ExactOnlineAccessToken', '');
    $this->exactConnection->setAccessToken($this->exactOnlineAccessToken);

    $this->exactOnlineRefreshToken = $this->loadParam('ExactOnlineRefreshToken', '');
    $this->exactConnection->setRefreshToken($this->exactOnlineRefreshToken);

    $this->exactOnlineExpiresIn = $this->loadParam('ExactOnlineExpiresIn', '');
    $this->exactConnection->setTokenExpires($this->exactOnlineExpiresIn);

    $this->exactOnlineDivision = $this->loadParam('ExactOnlineDivision', '');
    $this->exactConnection->setDivision($this->exactOnlineDivision);
  }

  /************************************************
   *
   * getters and setters
   *
   ************************************************/

  public function getExactOnlineURL() {
    return $this->exactOnlineURL;
  }

  public function getClientID() {
    return $this->exactOnlineClientID;
  }

  public function getClientSecret() {
    return $this->exactOnlineClientSecret;
  }

  public function getAuthorizationCode() {
    return $this->exactOnlineAuthorizationCode;
  }

  public function getAccessToken() {
    return $this->exactOnlineAccessToken;
  }

  public function getRefreshToken() {
    return $this->exactOnlineRefreshToken;
  }

  public function getExpiresIn() {
    return $this->exactOnlineExpiresIn;
  }

  public function getDivision() {
    return $this->exactOnlineDivision;
  }

  public function setExactOnlineURL($v) {
    $this->exactOnlineURL = $v;
    $this->exactConnection->setBaseUrl($v);
    $this->saveParam('ExactOnlineURL', $v);
  }

  public function setClientID($v) {
    $this->exactOnlineClientID = $v;
    $this->exactConnection->setExactClientId($this->exactOnlineClientID);
    $this->saveParam('ExactOnlineClientID', $v);
  }

  public function setClientSecret($v) {
    $this->exactOnlineClientSecret = $v;
    $this->exactConnection->setExactClientSecret($this->exactOnlineClientSecret);
    $this->saveParam('ExactOnlineClientSecret', $v);
  }

  public function setAuthorizationCode($v) {
    $this->exactOnlineAuthorizationCode = $v;
    $this->exactConnection->setAuthorizationCode($this->exactOnlineAuthorizationCode);
    $this->saveParam('ExactOnlineAuthorizationCode', $v);
  }

  public function setAccessToken($v) {
    $this->exactOnlineAccessToken = $v;
    $this->exactConnection->setAccessToken($this->exactOnlineAccessToken);
    $this->saveParam('ExactOnlineAccessToken', $v);
  }

  public function setRefreshToken($v) {
    $this->exactOnlineRefreshToken = $v;
    $this->exactConnection->setRefreshToken($this->exactOnlineRefreshToken);
    $this->saveParam('ExactOnlineRefreshToken', $v);
  }

  public function setExpiresIn($v) {
    $this->exactOnlineExpiresIn = $v;
    $this->exactConnection->setTokenExpires($this->exactOnlineExpiresIn);
    $this->saveParam('ExactOnlineExpiresIn', $v);
  }

  public function setDivision($v) {
    $this->exactOnlineDivision = $v;
    $this->exactConnection->setDivision($v);
    $this->saveParam('ExactOnlineDivision', $v);
  }

  /***********************************************
   *
   * Exact Online Stuff
   *
   ***********************************************/

  public function forceLogin() {
    $authUrl = $this->exactConnection->getAuthUrl() . '&force_login=1';
    header('Location: ' . $authUrl, TRUE, 303);
    exit;
  }

  public static function exactTokenUpdateCallback(\Picqer\Financials\Exact\Connection $connection) {
    // callback is called when tokens and expire time needs to be saved
    $eol = new CRM_Exactonline_Utils();
    $eol->setAccessToken($connection->getAccessToken());
    $eol->setRefreshToken($connection->getRefreshToken());
    $eol->setExpiresIn($connection->getTokenExpires());
  }

  private function loadParam($name, $defaultValue = '') {
    $v = Civi::settings()->get($name);
    if ($v) {
      return $v;
    }
    else {
      return $defaultValue;
    }
  }

  private function saveParam($name, $value) {
    Civi::settings()->set($name, $value);
  }
}

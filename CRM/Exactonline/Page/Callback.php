<?php
use CRM_Exactonline_ExtensionUtil as E;

class CRM_Exactonline_Page_Callback extends CRM_Core_Page {

  public function run() {
    $exactOL = new CRM_Exactonline_Utils();

    // get the authorization code (if available)
    $authorizationCode = CRM_Utils_Array::value('code', $_GET);
    if ($authorizationCode) {
      $exactOL->setAuthorizationCode($authorizationCode);
      CRM_Core_Session::setStatus('Nieuwe authorisatiecode verkregen', 'Authorisatiecode', 'success');
    }
    else {
      CRM_Core_Session::setStatus('Geen authorisatiecode verkregen!', 'Authorisatiecode', 'error');
    }

    // return to the settings page
    CRM_Utils_System::redirect('/civicrm/exactonline/settings');

    parent::run();
  }
}

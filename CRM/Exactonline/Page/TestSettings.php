<?php
use CRM_Exactonline_ExtensionUtil as E;

class CRM_Exactonline_Page_TestSettings extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle('Exact Online Configuratietest');

    try {
      $exactOL = new CRM_Exactonline_Utils();

      // store the settings in the template variables
      $this->assign('exact_url', $exactOL->getExactOnlineURL());
      $this->assign('client_id', $exactOL->getClientID());
      $this->assign('client_secret', $exactOL->getClientSecret());
      $this->assign('division', $exactOL->getDivision());
      $this->assign('authorization_code', $exactOL->getAuthorizationCode());
      $this->assign('access_token', $exactOL->getAccessToken());
      $this->assign('refresh_token', $exactOL->getRefreshToken());
      $this->assign('expires_in', $exactOL->getExpiresIn());

      // connect to exact and show journals
      $exactOL->exactConnection->connect();
      $journals = new \Picqer\Financials\Exact\Journal($exactOL->exactConnection);
      $result = $journals->get();
      $journalNames = [];
      foreach ($result as $journal) {
        $journalNames[] = $journal->Description;
      }

      $this->assign('journal_names', $journalNames);

      CRM_Core_Session::setStatus('Gelukt!', 'Exact configuratietest', 'success');
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), 'Exact configuratietest', 'error');
    }

    $date = new DateTime();
    $date->modify('-30 days');
    $sqlParams[1] = [$date->getTimestamp(), 'Integer'];
    $sql = "SELECT `response_status_code`, COUNT(*) as `total` FROM `civicrm_exactonline_log` WHERE `tstamp` >= %1 GROUP BY `response_status_code``";
    $dao = \CRM_Core_DAO::executeQuery($sql, $sqlParams);
    $statistics = [];
    while($dao->fetch()) {
      $statistics[] = array(
        'status_code' => $dao->response_status_code,
        'count' => $dao->total
      );
    }
    $this->assign('statistics', $statistics);

    parent::run();
  }

}

<?php
use CRM_Exactonline_ExtensionUtil as E;

class CRM_Exactonline_Page_ViewLog extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle('Exact Online Logging');

    $logSummary = CRM_Exactonline_Logging::getLoggerSummary(30);
    $this->assign('logsummary', $logSummary);

    $logError429 = CRM_Exactonline_Logging::getError429List();
    $this->assign('log429', $logError429);

    $date = self::getDateFromQueryString();
    $logDetail = CRM_Exactonline_Logging::getLoggerDetail($date);
    $this->assign('logdetail', $logDetail);

    parent::run();
  }

  private function getDateFromQueryString() {
    $date = CRM_Utils_Request::retrieve('day', 'String');

    if (strlen($date) == 10) {
      return $date;
    }
    else {
      return date('Y-m-d');
    }
  }

}

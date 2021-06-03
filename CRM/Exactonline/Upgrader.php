<?php
use CRM_Exactonline_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Exactonline_Upgrader extends CRM_Exactonline_Upgrader_Base {


  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }

  public function uninstall() {
    $this->executeSqlFile('sql/uninstall.sql');
  }

  public function upgrade_1001() {
    $this->executeSqlFile('sql/install.sql');
    return TRUE;
  }

}

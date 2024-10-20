<?php

require_once 'exactonline.civix.php';
use CRM_Exactonline_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function exactonline_civicrm_config(&$config) {
  _exactonline_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function exactonline_civicrm_install() {
  _exactonline_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function exactonline_civicrm_enable() {
  _exactonline_civix_civicrm_enable();

  exactonline_create_cache_table_if_not_exists();
}

function exactonline_create_cache_table_if_not_exists() {
  $sql = "
    CREATE TABLE IF NOT EXISTS `civicrm_exactonline_cache` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `exact_name` varchar(255) NOT NULL,
      `exact_entity` varchar(255) NOT NULL,
      `exact_guid` varchar(255) NOT NULL,
      `created_date` timestamp NULL  DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`exact_name`, `exact_entity`)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
  ";
  CRM_Core_DAO::executeQuery($sql);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function exactonline_civicrm_navigationMenu(&$menu) {
  _exactonline_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _exactonline_civix_navigationMenu($menu);
} // */

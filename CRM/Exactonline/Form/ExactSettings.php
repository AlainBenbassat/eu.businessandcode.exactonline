<?php

use CRM_Exactonline_ExtensionUtil as E;

class CRM_Exactonline_Form_ExactSettings extends CRM_Core_Form {

  public function buildQuickForm() {
    $elements = [];
    $buttons = [];

    // set the title
    CRM_Utils_System::setTitle('Exact Online Configuratie');

    // add the Save button
    $buttons[] = [
      'type' => 'submit',
      'name' => E::ts('Save'),
      'isDefault' => TRUE,
    ];

    // add the fields
    $this->add('text', 'url','Exact Online URL', NULL, TRUE);
    $elements[] = 'url';

    $this->add('text', 'client_id', 'Client ID', NULL, TRUE);
    $elements[] = 'client_id';

    $this->add('text', 'client_secret', 'Client Secret', NULL, TRUE);
    $elements[] = 'client_secret';

    $this->add('text', 'division', 'Division', NULL, TRUE);
    $elements[] = 'division';

    $this->addElement('checkbox', 'clear_settings', 'WIS ALLE GEGEVENS<br>(client id, client secret, authorisatiecode, access token... worden gewist uit CiviCRM)');
    $elements[] = 'clear_settings';

    // get the stored settings and set them as default values of the fields
    $exactOL = new CRM_Exactonline_Utils();

    $defaults = [];
    $defaults['url'] = $exactOL->getExactOnlineURL();
    $defaults['client_id'] = $exactOL->getClientID();
    $defaults['client_secret'] = $exactOL->getClientSecret();
    $defaults['division'] = $exactOL->getDivision();
    $this->setDefaults($defaults);

    // add the Test button if we have an authorization code
    if ($exactOL->getAuthorizationCode()) {
      $buttons[] = [
        'type' => 'done',
        'name' => E::ts('Test'),
        'icon' => 'fa-check-circle',
      ];
    }

    // add the fields and buttons to the template
    $this->addButtons($buttons);
    $this->assign('elementNames', $elements);
    parent::buildQuickForm();
  }

  public function postProcess() {
    $exactOL = new CRM_Exactonline_Utils();

    // get the submitted values
    $values = $this->exportValues();

    if (array_key_exists('clear_settings', $values) && $values['clear_settings'] == 1) {
      // clear all stored values
      $exactOL->setClientID('');
      $exactOL->setClientSecret('');
      $exactOL->setDivision('');
      $exactOL->setAuthorizationCode('');
      $exactOL->setAccessToken('');
      $exactOL->setExpiresIn('');
      $exactOL->setRefreshToken('');

      CRM_Core_Session::setStatus('De Exact Online gegevens zijn gewist.', 'Succes', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/exactonline/settings', 'reset=1'));
    }
    else {
      // save the values
      if (array_key_exists('url', $values)) {
        $exactOL->setExactOnlineURL($values['url']);
      }
      if (array_key_exists('client_id', $values)) {
        $exactOL->setClientID($values['client_id']);
      }
      if (array_key_exists('client_secret', $values)) {
        $exactOL->setClientSecret($values['client_secret']);
      }
      if (array_key_exists('division', $values)) {
        $exactOL->setDivision($values['division']);
      }

      // do we have an authorization code?
      if (array_key_exists('authorization_code', $values) && $values['authorization_code']) {
        // yes, store it
        $exactOL->setAuthorizationCode($values['authorization_code']);
      }
      else {
        // no, redirect to Exact Online
        $exactOL->exactConnection->redirectForAuthorization();
        CRM_Utils_System::civiExit();
      }

      // test the settings?
      if (array_key_exists('_qf_ExactSettings_done', $values)) {
        CRM_Utils_System::redirect('/civicrm/exactonline/settings-test');
      }
    }

    parent::postProcess();
  }
}

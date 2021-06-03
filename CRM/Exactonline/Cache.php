<?php

class CRM_Exactonline_Cache {
  public static function get($name, $entity) {
    $sql = "
      select
        exact_guid
      from
        civicrm_exactonline_cache
      where
        exact_name = %1
      and
        exact_entity = %2
    ";
    $sqlParams = [
      1 => [$name, 'String'],
      2 => [$entity, 'String'],
    ];
    $guid = CRM_Core_DAO::singleValueQuery($sql, $sqlParams);

    if ($guid) {
      return $guid;
    }
    else {
      return FALSE;
    }
  }

  public static function set($name, $entity, $guid) {
    $sql = "
      insert into
        civicrm_exactonline_cache (exact_name, exact_entity, exact_guid)
      values
        (%1, %2, %3)
    ";
    $sqlParams = [
      1 => [$name, 'String'],
      2 => [$entity, 'String'],
      3 => [$guid, 'String'],
    ];
    CRM_Core_DAO::executeQuery($sql, $sqlParams);
  }

  public static function deleteByNameEntity($name, $entity) {
    $sql = "
      delete from
        civicrm_exactonline_cache
      where
        exact_name = %1
      and
        exact_entity = %2
    ";
    $sqlParams = [
      1 => [$name, 'String'],
      2 => [$entity, 'String'],
    ];
    CRM_Core_DAO::executeQuery($sql, $sqlParams);
  }

  public static function deleteByGuid($guid) {
    $sql = "
      delete from
        civicrm_exactonline_cache
      where
        exact_guid = %1
    ";
    $sqlParams = [
      1 => [$guid, 'String'],
    ];
    CRM_Core_DAO::executeQuery($sql, $sqlParams);
  }
}

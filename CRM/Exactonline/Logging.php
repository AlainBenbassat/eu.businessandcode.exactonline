<?php
/**
 * Copyright (C) 2021  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use Psr\Http\Message\ResponseInterface;

class CRM_Exactonline_Logging {

  /**
   * Log the response.
   *
   * @param \Psr\Http\Message\ResponseInterface $response
   * @return \Psr\Http\Message\ResponseInterface
   */
  public static function logResponse(ResponseInterface $response) {
    $headers = '';
    foreach ($response->getHeaders() as $name => $values) {
      $headers .= $name . ": " . implode(", ", $values) . "\r\n";
    }
    \CRM_Core_Error::debug_log_message('Got response ('.$response->getStatusCode().') with headers: '.$headers);
    return $response;
  }

}

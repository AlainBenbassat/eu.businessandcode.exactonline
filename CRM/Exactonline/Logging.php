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

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class CRM_Exactonline_Logging {

  /**
   * Log the response.
   *
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param \Psr\Http\Message\RequestInterface $request
   */
  public static function logResponse(ResponseInterface $response, RequestInterface $request) {
    $headers = '';
    foreach ($response->getHeaders() as $name => $values) {
      $headers .= $name . ": " . implode(", ", $values) . "\r\n";
    }
    \CRM_Core_Error::debug_log_message('Got response ('.$response->getStatusCode().') '. $request->getUri() . ' with headers: '.$headers);
  }

  /**
   * Middleware that logs requests, responses, and errors using a message
   * formatter.
   *
   * @param LoggerInterface  $logger Logs messages.
   * @param MessageFormatter $formatter Formatter used to create message strings.
   * @param string           $logLevel Level at which to log requests.
   *
   * @return callable Returns a function that accepts the next handler.
   */
  public static function loggerMiddleware()
  {
    return function (callable $handler) {
      return function ($request, array $options) use ($handler) {
        return $handler($request, $options)->then(
          function ($response) use ($request) {
            self::logResponse($response, $request);
            return $response;
          },
          function ($reason) use ($request) {
            $response = $reason instanceof RequestException
              ? $reason->getResponse()
              : null;
            if ($response) {
              self::logResponse($response, $request);
            }
            return \GuzzleHttp\Promise\rejection_for($reason);
          }
        );
      };
    };
  }

}

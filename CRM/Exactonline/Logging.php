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
    $request_headers = '';
    foreach ($request->getHeaders() as $name => $values) {
      $request_headers .= $name . ": " . implode(", ", $values) . "\r\n";
    }
    $response_headers = '';
    foreach ($response->getHeaders() as $name => $values) {
      $response_headers .= $name . ": " . implode(", ", $values) . "\r\n";
    }

    $sqlParams[1] = [date('Y-m-d H:i:s'), 'String'];
    $sqlParams[2] = [$request->getUri(), 'String'];
    $sqlParams[3] = [$request_headers, 'String'];
    $sqlParams[4] = [$response->getStatusCode(), 'String'];
    $sqlParams[5] = [$response_headers, 'String'];

    $limit = 0;
    $remaining_limit = 0;
    $minutely_limit = 0;
    $remaining_minutely_limit = 0;
    if ($response->hasHeader('X-RateLimit-Limit')) {
      $limit = $response->getHeaderLine('X-RateLimit-Limit');
    }
    if ($response->hasHeader('X-RateLimit-Remaining')) {
      $remaining_limit = $response->getHeaderLine('X-RateLimit-Remaining');
    }
    if ($response->hasHeader('X-RateLimit-Minutely-Limit')) {
      $minutely_limit = $response->getHeaderLine('X-RateLimit-Minutely-Limit');
    }
    if ($response->hasHeader('X-RateLimit-Minutely-Remaining')) {
      $remaining_minutely_limit = $response->getHeaderLine('X-RateLimit-Minutely-Remaining');
    }

    $sqlParams[6] = [$limit, 'Integer'];
    $sqlParams[7] = [$remaining_limit, 'Integer'];
    $sqlParams[8] = [$minutely_limit, 'Integer'];
    $sqlParams[9] = [$remaining_minutely_limit, 'Integer'];

    \CRM_Core_DAO::executeQuery("
        INSERT INTO `civicrm_exactonline_log` (
         `request_time`,
         `request_uri`,
         `request_headers`,
         `response_status_code`,
         `response_headers`,
         `response_limit`,
         `response_remaning_limit`,
         `response_minutely_limit`,
         `response_remaning_minutely_limit`
       ) VALUES (%1, %2, %3, %4, %5, %6, %7, %8, %9)", $sqlParams);

    // slow down when we reach the limit
    if ($minutely_limit > 0 && $remaining_minutely_limit <= 10) {
      sleep(3);
    }
  }

  private static function dateMinusNumDays(&$day, $numDays) {
    if ($numDays == 0) {
      return;
    }

    $day->add(\DateInterval::createFromDateString('-' . $numDays . ' Day'));
  }

  public static function getLoggerSummary($numDays) {
    $logSummary = [];

    $today = new DateTime('today');

    for ($i = 0; $i < $numDays; $i++) {
      $logSummary[] = self::getLoggerSummaryForDay($today);
      self::dateMinusNumDays($today, 1);
    }

    return $logSummary;
  }

  private static function getLoggerSummaryForDay($day) {
    $from = $day->format('Y-m-d') . ' 00:00:00';
    $to = $day->format('Y-m-d') . ' 23:59:59';

    $sql = "
      select
        count(*)
      from
        civicrm_exactonline_log
      where
        request_time between '$from' and '$to'
    ";

    $sqlOK = $sql . ' and response_status_code < 300 order by id desc';
    $sqlNotOK = $sql . ' and response_status_code >= 300 order by id desc';

    $logSummary = [];
    $logSummary['date'] = $day->format('Y-m-d');
    $logSummary['day_month'] = $day->format('d/m');
    $logSummary['count_ok'] = CRM_Core_DAO::singleValueQuery($sqlOK);
    $logSummary['count_not_ok'] = CRM_Core_DAO::singleValueQuery($sqlNotOK);
    $logSummary['count_total'] = $logSummary['count_ok'] + $logSummary['count_not_ok'];

    return $logSummary;
  }

  public static function getLoggerDetail($date) {
    $sql = "select * from civicrm_exactonline_log where date_format(request_time, '%Y-%m-%d') = '" . $date . "' order by id";
    return CRM_Core_DAO::executeQuery($sql)->fetchAll();
  }

  public static function getError429List() {
    $sql = "
      select
        date_format(request_time, '%Y-%m-%d') date,
        count(*) num_errors
      from
        civicrm_exactonline_log
      where
        response_status_code = 429
      group by
        date_format(request_time, '%Y-%m-%d')
      having
        count(*) > 0
    ";
    return CRM_Core_DAO::executeQuery($sql)->fetchAll();
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

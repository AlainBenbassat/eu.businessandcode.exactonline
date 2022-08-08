<p><a href="https://support.exactonline.com/community/s/knowledge-base#All-All-DNO-Content-respcodeserrorhandling">Exact Online REST API Response codes</a></p>

<h3>Overzicht afgelopen 30 dagen</h3>

<table>
  <tr>
      {foreach from=$logsummary item=logday}
          {if $logday.count_not_ok eq 0}
              {assign var="style" value="text-align: center; color: white; background-color: green; border: 1px solid white;"}
          {elseif $logday.count_not_ok lt 10}
              {assign var="style" value="text-align: center; color: white; background-color: orange; border: 1px solid white;"}
          {else}
              {assign var="style" value="text-align: center; color: white; background-color: red; border: 1px solid white;"}
          {/if}

          <td style="{$style}">{$logday.count_total}</td>
      {/foreach}
  </tr>

  <tr>
      {foreach from=$logsummary item=logday}
        <td>
          <a href="viewlog?reset=1&day={$logday.date}">{$logday.day_month}</a>
        </td>
      {/foreach}
  </tr>
</table>

<h3>Dagen met foutcode 429 - Too Many Requests</h3>

<ul>
    {foreach from=$log429 item=log429day}
      <li>
        <a href="viewlog?reset=1&day={$log429day.date}">{$log429day.date}</a>: {$log429day.num_errors}
      </li>
    {/foreach}
</ul>


<h3>Detail {$logdetail[0].request_time}</h3>

<table>
    <tr>
      <th>ID</th>
      <th>Datum</th>
      <th>Code</th>
      <th>Limiet</th>
      <th>Nog over</th>
      <th>Limiet per minuut</th>
      <th>Nog over</th>
    </tr>
    {foreach from=$logdetail item=logentry}
        {if $logentry.response_status_code lt 300}
            {assign var="style" value=""}
        {else}
            {assign var="style" value="color: white; background-color: red; border: 1px solid white;"}
        {/if}

        <tr>
          <td>{$logentry.id}</td>
          <td>{$logentry.request_time}</td>
          <td style="{$style}">{$logentry.response_status_code}</td>
          <td>{$logentry.response_limit}</td>
          <td>{$logentry.response_remaning_limit}</td>
          <td>{$logentry.response_minutely_limit}</td>
          <td>{$logentry.response_remaning_minutely_limit}</td>
        </tr>
    {/foreach}
</table>

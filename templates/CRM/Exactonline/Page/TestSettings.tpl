<a href="/civicrm/exactonline/settings">&lt; Terug naar Exact instellingen</a>
<br>
<br>

<h3>Dagboeken</h3>

<ul>
{foreach from=$journal_names item=journal}
    <li>{$journal}</li>
{/foreach}
</ul>

<br>

<h3>Exact instellingen</h3>

<div>
    <p><strong>Exact URL:</strong> {$exact_url}</p>
</div>

<div>
    <p><strong>Client ID:</strong> {$client_id}</p>
</div>

<div>
    <p><strong>Division:</strong> {$division}</p>
</div>

<div>
    <p><strong>Authorization code:</strong> {$authorization_code}</p>
</div>

<div>
    <p><strong>Access token:</strong> {$access_token}</p>
</div>

<div>
    <p><strong>Refresh token:</strong> {$refresh_token}</p>
</div>

<div>
    <p><strong>Expires in:</strong> {$expires_in}</p>
</div>

<h3>Statistieken</h3>

<p>Afgelopen 30 dagen</p>

{foreach from=$statistics item=item}
  <p><strong>Statuscode {$item.status_code}</strong>: {$item.count} keer</p>
{/foreach}

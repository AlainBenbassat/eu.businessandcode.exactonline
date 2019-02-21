<div id="help" class="description">
    <p>Vul onderstaande velden in, en gebruik de juiste Exact Online URL: https://start.exactonline.nl voor Nederland of https://start.exactonline.be voor België.</p>
    <p>Druk dan op "Opslaan".</p>
    <p>Na succesvolle login met Exact Online, krijg je een authorisatiecode terug. Via de knop "Test", kan je de verbinding testen.</p>
    <p><a target="_blank" href="https://github.com/AlainBenbassat/eu.businessandcode.exactonline/blob/master/README.md">Raadpleeg de documentatie</a> voor meer informatie.</p>
    <p>Je kan ook alle opgeslagen Exact Online authorisatie- en authenticatiegegevens wissen, door "WIS ALLE GEGEVENS" aan te vinken.</p>
</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

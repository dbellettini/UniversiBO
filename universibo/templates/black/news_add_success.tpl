{if $common_pageType == "index"}
{include file=header_index.tpl}
{elseif $common_pageType == "popup"}
{include file=header_popup.tpl}
{/if}
{include file=avviso_notice.tpl}
<table width="95%" border="0" cellspacing="0" cellpadding="4" summary="">
<tr><td align="center"><p class="Titolo">&nbsp;<br />Aggiungi una nuova notizia<br />&nbsp;</p></td></tr>
<tr><td align="center" class="Normal">La notizia &egrave; stata inserita con successo.</td></tr>
<tr><td align="center" class="Normal"><a href="{$NewsAdd_back|escape:"htmlall"}">Torna indietro.</a></td></tr>
</table>

{if $common_pageType == "index"}
{include file=footer_index.tpl}
{elseif $common_pageType == "popup"}
{include file=footer_popup.tpl}
{/if}
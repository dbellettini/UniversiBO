{if $common_pageType == "index"}
{include file=header_index.tpl}
{elseif $common_pageType == "popup"}
{include file=header_popup.tpl}
{/if}

<table width="90%" border="0" cellspacing="0" cellpadding="0" summary="">
<tr><td class="Normal" align="center">
	&nbsp;<br />
	Titolo<br />&nbsp;
</td></tr>
<tr>
    <td class="Normal" align="left">
    </td>
</tr>
</table>



{if $common_pageType == "index"}
{include file=footer_index.tpl}
{elseif $common_pageType == "popup"}
{include file=footer_popup.tpl}
{/if}
{* dato un array di link in formato bbcode lo dispone in una tabella di due colonne*}

{*parametro: array $arrayToShow*}

<div class="tbl2col">
<table width="100%" border="1" cellspacing="0" cellpadding="0" summary="">
{section loop=$arrayToShow name=allitem} 
{if $smarty.section.allitem.last && $smarty.section.allitem.index is even}
	<tr align="center"><td width="50%"><p class="lastRow">{$arrayToShow[allitem]|escape:"htmlall"|bbcode2html}</p></td><td>&nbsp;</td></tr>
{elseif $smarty.section.allitem.last && $smarty.section.allitem.index is odd}
	<td width="50%"><p class="lastColRow">{$arrayToShow[allitem]|escape:"htmlall"|bbcode2html}</p></td></tr>
{else}
	{if $smarty.section.allitem.index is odd}
	 	<td width="50%"><p class="lastCol">{$arrayToShow[allitem]|escape:"htmlall"|bbcode2html}</p></td></tr>
	{elseif $smarty.section.allitem.index is even}
		<tr align="center"><td width="50%"><p class="leftCol">{$arrayToShow[allitem]|escape:"htmlall"|bbcode2html}</p></td>
	{/if}	
{/if}
{/section}
</table>
</div>
{if $showFileStudentiCommenti_langCommentiAvailableFlag == "true"}
	<div class="boxCommenti">
	<h2>Commenti:</h2>
	{foreach from=$showFileStudentiCommenti_commentiList item=temp_commenti}
	<div class="boxCommento">
	    <p>Voto proposto: {$temp_commenti.voto}</p>
		<p>Commento:<div class="commento"> {$temp_commenti.commento|escape:"htmlall"|bbcode2html|ereg_replace:"[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]":"<a href=\"\\0\" target=\"_blank\">\\0</a>"|ereg_replace:"[^<>[:space:]]+[[:alnum:]/]@[^<>[:space:]]+[[:alnum:]/]":"<a href=\"mailto:\\0\" target=\"_blank\">\\0</a>"|nl2br}</div></p>
		<p>Autore: <a href="{$temp_commenti.userLink|escape:"htmlall"}">{$temp_commenti.userNick}</a></p>
		{if $temp_commenti.dirittiCommento=="true"}
		<p><span>
			<a href="{$temp_commenti.editCommentoLink|escape:"htmlall"}">Modifica il commento</a>&nbsp;
			<a href="{$temp_commenti.deleteCommentoLink|escape:"htmlall"}">Cancella il commento</a>
		</span></p>
		{/if}
	</div>
	{/foreach}
	</div>
{else}
<p> Non esistono commenti per questo file.</p>
{/if}
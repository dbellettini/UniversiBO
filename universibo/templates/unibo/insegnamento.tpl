{include file=header_index.tpl}
<div class="titoloPagina">
<h2>{$ins_title|escape:"htmlall"}</h2>
{if $common_langCanaleMyUniversiBO != '' }
	<p>
	{if $common_canaleMyUniversiBO == "remove"}
		<img src="tpl/black/esame_myuniversibo_del.gif" width="15" height="15" alt="" />&nbsp;
	{else}<img src="tpl/black/esame_myuniversibo_add.gif" width="15" height="15" alt="" />&nbsp;
	{/if}<a href="{$common_canaleMyUniversiBOUri|escape:"htmlall"}">{$common_langCanaleMyUniversiBO|escape:"htmlall"}</a></p>
{/if}
</div>
{include file=tabellina_due_colonne.tpl arrayToShow=$ins_tabella}
{if $ins_infoDidEdit != ""}<h4><a href="{$ins_infoDidEdit|escape:"htmlall"|nl2br}">Modifica le informazioni dell'esame</a></h4>{/if}
<hr />
{include file=News/latest_news.tpl}
<hr/>
{include file=Files/show_file_titoli.tpl}

{include file=footer_index.tpl}
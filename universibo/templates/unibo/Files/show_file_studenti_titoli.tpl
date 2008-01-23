<h2>Files degli studenti</h2>
{if $showFileStudentiTitoli_addFileFlag == "true"}
<div class="comandi">
	<img src="tpl/unibo/file_new.gif" width="15" height="15" alt="" />
	<a href="{$showFileStudentiTitoli_addFileUri|escape:"htmlall"}">{$showFileStudentiTitoli_addFile|escape:"htmlall"|bbcode2html|nl2br}</a>
</div>
{/if}
{if $showFileStudentiTitoli_langFileAvailableFlag=="true"}
{foreach name=listacategorie from=$showFileStudentiTitoli_fileList item=temp_categoria}
	<div class="elencoFile">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
			<tr><th colspan="8">{$temp_categoria.desc|escape:"htmlall"}</th></tr>
			{foreach name=listafile from=$temp_categoria.file item=temp_file}
				<tr class="{cycle values="even,odd"}">
				<td>&nbsp;&nbsp;{$temp_file.data|escape:"htmlall"}&nbsp;&nbsp;</td>
				<td><a href="{$temp_file.show_info_uri|escape:"htmlall"}">{$temp_file.titolo|escape:"htmlall"|nl2br|truncate}</a>&nbsp;{if $temp_file.nuova=="true"}&nbsp;&nbsp;<img src="tpl/unibo/icona_new.gif" width="21" height="9" alt="!NEW!" />{/if}</td>
				<td><a href="{$temp_file.autore_link|escape:"htmlall"}">{$temp_file.autore|escape:"htmlall"}</a></td>
				<td>&nbsp;&nbsp;{$temp_file.dimensione|escape:"htmlall"}&nbsp;kB&nbsp;&nbsp;</td>
				<td>{if $temp_file.modifica!=""}<a href="{$temp_file.modifica_link|escape:"htmlall"}"><img src="tpl/unibo/news_edt.gif" border="0" width="15" height="15" alt="modifica" hspace="1"/></a>{/if}</td>
				<td>{if $temp_file.elimina!=""}<a href="{$temp_file.elimina_link|escape:"htmlall"}"><img src="tpl/unibo/file_del.gif" border="0" width="15" height="15" alt="elimina" hspace="1"/></a>{/if}</td>
				<td><a href="{$temp_file.download_uri|escape:"htmlall"}"><img src="tpl/unibo/file_download.gif" border="0" width="15" height="15" alt="scarica il file" vspace="2" hspace="1"/></a></td></tr>
			{/foreach}
		</table>
</div>
{/foreach}
{else}
<p>{$showFileStudentiTitoli_langFileAvailable|escape:htmlall}</p>
{/if}

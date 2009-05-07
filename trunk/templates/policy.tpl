{include file='header.inc.tpl'}

<h1>Security policies</h1>

<form method="post" action="policy.php">
	{foreach from=$options.tables key=table item=chains} 
	<fieldset>
	<legend>{$table|capitalize}</legend>
		<table>
		{foreach from=$chains item=chain} 
		<tr>
			<th style="width:90px;"><label for="{"$table$chain"}">{$chain}</label></th>
			<td>: {html_options name="$table[$chain]" id=$table$chain options=$options.policies selected=$config.$table.$chain.policy}</td>
			<td>
				<a href="{$table}.php?chain={$chain}&amp;new=1" title="Add rule to {$chain}"><img src="img/icons/add.png" width="16" height="16" alt="Add" /></a>
			</td>
		</tr>
		{/foreach} 
		</table>
	</fieldset>
	{/foreach} 

	<p class="centered">
		<input type="submit" name="submit" value="Submit" />
		<input type="reset" value="Cancel" />
	</p>
</form>

{include file='footer.inc.tpl'}

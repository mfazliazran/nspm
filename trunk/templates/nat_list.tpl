{include file='header.inc.tpl'}

<script type="text/javascript" src="js/filter.js"></script>

<h1>NAT rules</h1>

<table class="list" style="text-align:center;">
<tbody>
	<tr>
		<td class="disable" style="width:20%;">Disabled</td>
		<td class="dnat" style="width:20%;">DNAT</td>
		<td class="redirect" style="width:20%;">REDIRECT</td>
		<td class="snat" style="width:20%;">SNAT</td>
		<td class="masquerade" style="width:20%;">MASQUERADE</td>
	</tr>
</tbody>
</table><br />

{foreach from=$options.tables.$table item=chain}
<form method="post" action="{$table}.php?chain={$chain}" id="{$chain}form">
	<fieldset>
	<legend>{$chain}</legend>
		<img src="img/icons/add.png" width="16" height="16" alt="Add" />
		<a href="{$table}.php?chain={$chain}&amp;new=1">New rule</a>
		<br /><br />
		<table class="list">
		<thead>
			<tr>
				<th style="width:20px;"></th>
				<th style="width:60px;">Protocol</th>
				<th>Source</th>
				<th>Destination</th>
				<th>Translation</th>
				<th style="width:150px;">Comment</th>
				<th style="width:95px;"></th>
			</tr>
		</thead>

		<tbody>
			{foreach from=$config.$table.$chain.rules key=key item=rule} 
			<tr class="{$rule->get('target')|lower}">
				<td>
					<input type="checkbox" name="rules[]" value="{$key+1}" id="{$chain}check{$key+1}" />
				</td>
				<td>
					{$rule->get('protocol')|default:'all'|upper}
				</td>
				<td>
					{if $rule->get('source_net')}{$rule->get('source_net')|MaskToCIDR}
					{else}{$rule->get('source_address')|default:'all'|upper}{/if}
					{if $rule->get('source_type')}({$rule->get('source_type')}){/if} 
					{if $rule->get('source_ports')}: {$rule->get('source_ports')}{/if} 
					{if $rule->get('source_interface')}via {$rule->get('source_interface')}{/if} 
					{if $rule->get('source_mac')}<br />MAC {$rule->get('source_mac')}{/if} 
				</td>
				<td>
					{if $rule->get('destination_net')}{$rule->get('destination_net')|MaskToCIDR}
					{else}{$rule->get('destination_address')|default:'all'|upper}{/if}
					{if $rule->get('destination_type')}({$rule->get('destination_type')}){/if} 
					{if $rule->get('destination_ports')}: {$rule->get('destination_ports')}{/if} 
					{if $rule->get('destination_interface')}via {$rule->get('destination_interface')}{/if} 
				</td>
				<td>
					{$rule->get('nat')|capitalize} 
				</td>
				<td>
					{$rule->get('comment')|regex_replace:"/\@.*\@/":""|stripslashes} 
				</td>
				<td class="icons" style="width:116px;">
					{if $key != 0}<a href="{$table}.php?chain={$chain}&amp;up={$key+1}" title="Move this rule up"><img src="img/icons/up.png" width="16" height="16" alt="Up" /></a>
					{else}<img src="img/icons/spacer.png" width="16" height="16" alt="Spacer" />{/if}
					{if $key != $config.$table.$chain.rules|@count -1}<a href="{$table}.php?chain={$chain}&amp;down={$key+1}" title="Move this rule down"><img src="img/icons/down.png" width="16" height="16" alt="Down" /></a>
					{else}<img src="img/icons/spacer.png" width="16" height="16" alt="Spacer" />{/if}
					{if $rule->get('target') == 'disable'}<a href="{$table}.php?chain={$chain}&amp;enable={$key+1}" title="Enable this rule"><img src="img/icons/enable.png" width="16" height="16" alt="Disabled" /></a>
					{else}<a href="{$table}.php?chain={$chain}&amp;disable={$key+1}" title="Disable this rule"><img src="img/icons/disable.png" width="16" height="16" alt="Enabled" /></a>{/if} 
					<a href="{$table}.php?chain={$chain}&amp;edit={$key+1}" title="Edit this rule"><img src="img/icons/edit.png" width="16" height="16" alt="Edit" /></a>
					<a href="{$table}.php?chain={$chain}&amp;copy={$key+1}" title="Duplicate this rule"><img src="img/icons/copy.png" width="16" height="16" alt="Copy" /></a>
					<a href="{$table}.php?chain={$chain}&amp;delete={$key+1}" title="Delete this rule"><img src="img/icons/delete.png" width="16" height="16" alt="Delete" /></a>
				</td>
			</tr>
			{/foreach}

			<tr class="{$config.$table.$chain.policy|lower}">
				<td></td>
				<td>ALL</td>
				<td>ALL</td>
				<td>ALL</td>
				<td></td>
				<td>Default Policy ({$config.$table.$chain.policy})</td>
				<td class="icons"><a href="{$table}.php?chain={$chain}&amp;reverse=1" title="Reverse policy"><img src="img/icons/change.png" width="16" height="16" alt="Change" /></a></td>
			</tr>
		</tbody>
		</table>

		<label><input type="checkbox" name="checkall" value="all" id="{$chain}checkall" onclick="checkAll('{$chain}');" />(Un)check all</label>
		<br /><br />
		Apply to selection:
		<input type="image" name="up" src="img/icons/up.png" alt="Up" title="Move these rules up" />
		<input type="image" name="down" src="img/icons/down.png" alt="Down" title="Move these rules down" />
		<input type="image" name="enable" src="img/icons/enable.png" alt="Disabled" title="Enable these rules" />
		<input type="image" name="disable" src="img/icons/disable.png" alt="Enabled" title="Disable these rules" />
		<input type="image" name="delete" src="img/icons/delete.png" alt="Delete" title="Delete these rules" />
	</fieldset>
</form>
{/foreach}

{include file='footer.inc.tpl'}

{include file="header.inc.tpl"}

<script type="text/javascript" src="js/nat.js"></script>

{assign var="warn_icon" value='<img src="img/icons/warning.png" width="16" height="16" alt="Warning" title="Error on this field" />&nbsp;'}

{if isset($edit)}
<h1>Edit {$chain} rule</h1>
{else}
<h1>New {$chain} rule</h1>
{/if}

<p>
	<img src="img/icons/previous.png" width="16" height="16" alt="Back" />
	<a href="{$table}.php">Back to NAT rules</a>
</p>

{if isset($errors) && $errors|@count != 0 }
<p class="warning">
	Errors in rule parameters.
</p>
{elseif isset($edit) && isset($values.target) && ($values.target == 'disable')}
<p class="warning">
	This rule is currently disabled.
</p>
{/if}

<form method="post" action="{$table}.php?chain={$chain}">
	<fieldset>
	<legend>Context</legend>
		<table>
		<tr id="row_chain">
			<th style="width:130px;">Chain</th>
			<td>: {$chain}</td>
		</tr>
		<tr id="row_policy">
			<th>Default politcy</th>
			<td>: {$config.$table.$chain.policy}</td>
		</tr>
		<tr id="row_protocol">
			<th>{if isset($errors.protocol)}{$warn_icon}{/if}<label for="protocol">Protocol</label></th>
			<td>: {html_options name="protocol" id="protocol" options=$options.protocols selected=$values.protocol|default:'' onchange="updateProtocol(this.value);"}</td>
		</tr>
		<tr id="row_target">
			<th>{if isset($errors.target)}{$warn_icon}{/if}<label for="target">Target</label></th>
			<td>: {if $chain == 'POSTROUTING'}{html_options name="target" id="target" options=$options.snat selected=$values.target|default:'' onchange="updateTarget(this.value);"}{else}{html_options name="target" id="target" options=$options.dnat selected=$values.target|default:'' onchange="updateTarget(this.value);"}{/if}</td>
		</tr>
		<tr id="row_comment">
			<th>{if isset($errors.comment)}{$warn_icon}{/if}<label for="comment">Comment</label></th>
			<td>: <input type="text" name="comment" id="comment" size="36" maxlength="200" value="{$values.comment|default:''|regex_replace:"/\@.*\@/":""}" /></td>
		</tr>
		</table>
	</fieldset>

	<fieldset>
	<legend>Connection</legend>
		<table>
		{if $chain == 'PREROUTING'} 
		<!-- Input interface -->
		<tr id="row_source_interface">
			<th>{if isset($errors.source_interface)}{$warn_icon}{/if}<label for="source_interface">Input interface</label></th>
			<td>: {html_options name="source_interface" id="source_interface" options=$system.interfaces selected=$values.source_interface|default:''}</td>
		</tr>
		{elseif $chain == 'OUTPUT' || $chain == 'POSTROUTING'} 
		<!-- Output interface -->
		<tr id="row_destination_interface">
			<th>{if isset($errors.destination_interface)}{$warn_icon}{/if}<label for="destination_interface">Output interface</label></th>
			<td>: {html_options name="destination_interface" id="destination_interface" options=$system.interfaces selected=$values.destination_interface|default:'' onchange="updateInterface(this.options[this.selectedIndex].text);"}</td>
		</tr>
		{/if} 
		<!-- IP addresses -->
		<tr id="row_source_address">
			<th style="width:130px;">{if isset($errors.source_address)}{$warn_icon}{/if}<label for="source_address">Source address</label></th>
			<td style="width:290px;">:
				<input type="text" name="source_address" id="source_address" size="15" maxlength="15" value="{$values.source_address|default:''}" />
				<span id="source_address_separator">{if isset($values.source_address_range) && $values.source_address_range != ''}-{else}/{/if}</span>
				<input type="text" name="source_address_net" id="source_address_net" size="2" maxlength="2" value="{$values.source_address_net|default:''}" {if isset($values.source_address_range) && $values.source_address_range != ''}style="display:none;"{/if} />
				<input type="text" name="source_address_range" id="source_address_range" size="15" maxlength="15" value="{$values.source_address_range|default:''}" {if !isset($values.source_address_range) || $values.source_address_range == ''}style="display:none;"{/if} />
				<img src="img/icons/change.png" width="16" height="16" alt="Change" title="Change formatting" style="cursor:pointer;" onclick="changeAddress('source');" />
			</td>
		</tr>
		<tr id="row_destination_address">
			<th>{if isset($errors.destination_address)}{$warn_icon}{/if}<label for="destination_address">Destination address</label></th>
			<td>:
				<input type="text" name="destination_address" id="destination_address" size="15" maxlength="15" value="{$values.destination_address|default:''}" />
				<span id="destination_address_separator">{if isset($values.destination_address_range) && $values.destination_address_range != ''}-{else}/{/if}</span>
				<input type="text" name="destination_address_net" id="destination_address_net" size="2" maxlength="2" value="{$values.destination_address_net|default:''}" {if isset($values.destination_address_range) && $values.destination_address_range != ''}style="display:none;"{/if} />
				<input type="text" name="destination_address_range" id="destination_address_range" size="15" maxlength="15" value="{$values.destination_address_range|default:''}" {if !isset($values.destination_address_range) || $values.destination_address_range == ''}style="display:none;"{/if} />
				<img src="img/icons/change.png" width="16" height="16" alt="Change" title="Change formatting" style="cursor:pointer;" onclick="changeAddress('destination');" />
			</td>
		</tr>
		<!-- Ports -->
		<tr id="row_destination_ports" {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>
			<th>{if isset($errors.destination_ports) || isset($errors.destination_ports_range)}{$warn_icon}{/if}<label for="destination_ports1">Destination ports</label></th>
			<td>:
				<input type="text" name="destination_ports[]" id="destination_ports1" size="5" maxlength="5" value="{$values.destination_ports.0|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports2" size="5" maxlength="5" value="{$values.destination_ports.1|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports3" size="5" maxlength="5" value="{$values.destination_ports.2|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports4" size="5" maxlength="5" value="{$values.destination_ports.3|default:''}" />
			</td>
			<td>&nbsp;</td>
			<th style="width:105px;">{if isset($errors.destination_ports_range)}{$warn_icon}{/if}<label for="destination_ports10">Ports ranges</label></th>
			<td>:
				<input type="text" name="destination_ports_range[0][]" id="destination_ports10" size="5" maxlength="5" value="{$values.destination_ports_range.0.0|default:''}" /> -
				<input type="text" name="destination_ports_range[0][]" id="destination_ports11" size="5" maxlength="5" value="{$values.destination_ports_range.0.1|default:''}" /> &nbsp;
				<input type="text" name="destination_ports_range[1][]" id="destination_ports12" size="5" maxlength="5" value="{$values.destination_ports_range.1.0|default:''}" /> -
				<input type="text" name="destination_ports_range[1][]" id="destination_ports13" size="5" maxlength="5" value="{$values.destination_ports_range.1.1|default:''}" />
			</td>
		</tr>
		</table>
	</fieldset>

	<fieldset>
	<legend>Translation</legend>
		<table>
		<!-- Address -->
		<tr id="row_nat_address" {if isset($values.target) && ($values.target == 'MASQUERADE' || $values.target == 'REDIRECT')}style="display:none;"{/if}>
			<th style="width:130px;">{if isset($errors.nat_address)}{$warn_icon}{/if}<label for="nat_address">New&nbsp;addresses</label></th>
			<td>:
				<input type="text" name="nat_address" id="nat_address" size="15" maxlength="15" value="{$values.nat_address|default:''}" /> -
				<input type="text" name="nat_address_range" id="nat_address_range" size="15" maxlength="15" value="{$values.nat_address_range|default:''}" />
			</td>
		</tr>
		<tr id="row_nat_ports" {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>
			<th style="width:130px;">{if isset($errors.nat_port)}{$warn_icon}{/if}<label for="nat_port">New&nbsp;ports</label></th>
			<td>:
				<input type="text" name="nat_port" id="nat_port" size="5" maxlength="5" value="{$values.nat_port|default:''}" /> -
				<input type="text" name="nat_port_range" id="nat_port_range" size="5" maxlength="5" value="{$values.nat_port_range|default:''}" />
			</td>
		</tr>
		</table>
	</fieldset>

	<p class="centered">
		{if isset($edit)} 
		<input type="hidden" name="edit" value="{$edit}" />
		<input type="submit" name="submit" value="Modify in {$chain}" />
		{else} 
		<input type="submit" name="submit" value="Add to {$chain}" />
		{/if} 
		<input type="reset" value="Reset fields" />
	</p>
</form>

{include file="footer.inc.tpl"}

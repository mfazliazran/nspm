{include file="header.inc.tpl"}

<script type="text/javascript" src="js/filter.js"></script>

{assign var="warn_icon" value='<img src="img/icons/warning.png" width="16" height="16" alt="Warning" title="Error on this field" />&nbsp;'}

{if isset($edit)}
<h1>Edit {$chain} rule</h1>
{else}
<h1>New {$chain} rule</h1>
{/if}

<p>
	<img src="img/icons/previous.png" width="16" height="16" alt="Back" />
	<a href="{$table}.php">Back to filter rules</a>
</p>

{if isset($errors) && $errors|@count != 0 }
<p class="warning">
	<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
	&nbsp;Errors in rule parameters&nbsp;
	<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
</p>
{elseif isset($edit) && isset($values.target) && ($values.target == 'disable')}
<p class="warning">
	<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
	&nbsp;This rule is currently disabled&nbsp;
	<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
</p>
{/if}

<form method="post" action="{$page}.php?chain={$chain}">
	<fieldset>
	<legend>Context</legend>
		<table>
		<tr id="row_chain">
			<th>Chain</th>
			<td>: {$chain}</td>
		</tr>
		<tr id="row_policy">
			<th>Default policy</th>
			<td>: {$config.$table.$chain.policy}</td>
		</tr>
		<tr id="row_protocol">
			<th style="width:130px;">{if isset($errors.protocol)}{$warn_icon}{/if}<label for="protocol">Protocol</label></th>
			<td style="width:290px;">: {html_options name="protocol" id="protocol" options=$options.protocols selected=$values.protocol|default:'' onchange="updateProtocol(this.value);"}</td>
			<td>&nbsp;</td>
			<th {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}><label for="connections_ports">Used ports</label></th>
			<td {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>: {html_options name="connections_ports" id="connections_ports" options=$system.connections_ports}</td>
			<td {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>
				<img src="img/icons/source.png" width="16" height="16" alt="Source" title="Use as source port" style="cursor:pointer;" onclick="usePort('source', document.getElementById('connections_ports').value);" />
				<img src="img/icons/destination.png" width="16" height="16" alt="Destination" title="Use as destination port" style="cursor:pointer;" onclick="usePort('destination', document.getElementById('connections_ports').value);" />
			</td>
		</tr>
		<tr id="row_target">
			<th>{if isset($errors.target)}{$warn_icon}{/if}<label for="target">Target</label></th>
			<td>: {html_options name="target" id="target" options=$options.filter selected=$values.target|default:''}</td>
			<td>&nbsp;</td>
			<th style="width:105px;"><label for="connections_addresses">Known IPs</label></th>
			<td style="width:170px;">: {html_options name="connections_addresses" id="connections_addresses" options=$system.connections_addresses}</td>
			<td>
				<img src="img/icons/source.png" width="16" height="16" alt="Source" title="Use as source address" style="cursor:pointer;" onclick="useAddress('source', document.getElementById('connections_addresses').value);" />
				<img src="img/icons/destination.png" width="16" height="16" alt="Destination" title="Use as destination address" style="cursor:pointer;" onclick="useAddress('destination', document.getElementById('connections_addresses').value);" />
			</td>
		</tr>
		<tr id="row_comment">
			<th>{if isset($errors.comment)}{$warn_icon}{/if}<label for="comment">Comment</label></th>
			<td>: <input type="text" name="comment" id="comment" size="32" maxlength="200" value="{$values.comment|default:''|regex_replace:"/\@.*\@/":""}" /></td>
			<td>&nbsp;</td>
			<th><label for="arp_cache">Known MACs</label></th>
			<td>: {html_options name="arp_cache" id="arp_cache" options=$system.arp_cache}</td>
			<td>
				<img src="img/icons/source.png" width="16" height="16" alt="Source" title="Use as source MAC address" style="cursor:pointer;" onclick="useMAC('source', document.getElementById('arp_cache').value);" />
			</td>
		</tr>
		</table>
	</fieldset>

	<fieldset>
	<legend>Source</legend>
		<table>
		{if $chain == 'INPUT' || $chain == 'FORWARD' || $chain == 'PREROUTING'} 
		<!-- Interfaces -->
		<tr id="row_source_interface">
			<th>{if isset($errors.source_interface)}{$warn_icon}{/if}<label for="source_interface">Input interface</label></th>
			<td>: {html_options name="source_interface" id="source_interface" options=$system.interfaces selected=$values.source_interface|default:''}</td>
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
			<td>&nbsp;</td>
			<th style="width:105px;">{if isset($errors.source_type)}{$warn_icon}{/if}<label for="source_type">Address type</label></th>
			<td>: {html_options name="source_type" id="source_type" options=$options.addresses selected=$values.source_type|default:''}</td>
		</tr>
		<!-- Ports -->
		<tr id="row_source_ports" {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>
			<th>{if isset($errors.source_ports)}{$warn_icon}{/if}<label for="source_ports1">Source ports</label></th>
			<td>:
				<input type="text" name="source_ports[]" id="source_ports1" size="5" maxlength="5" value="{$values.source_ports.0|default:''}" />&nbsp;
				<input type="text" name="source_ports[]" id="source_ports2" size="5" maxlength="5" value="{$values.source_ports.1|default:''}" />&nbsp;
				<input type="text" name="source_ports[]" id="source_ports3" size="5" maxlength="5" value="{$values.source_ports.2|default:''}" />&nbsp;
				<input type="text" name="source_ports[]" id="source_ports4" size="5" maxlength="5" value="{$values.source_ports.3|default:''}" />
			</td>
			<td>&nbsp;</td>
			<th>{if isset($errors.source_ports_range)}{$warn_icon}{/if}<label for="source_ports10">Ports ranges</label></th>
			<td>:
				<input type="text" name="source_ports_range[0][]" id="source_ports10" size="5" maxlength="5" value="{$values.source_ports_range.0.0|default:''}" /> -
				<input type="text" name="source_ports_range[0][]" id="source_ports11" size="5" maxlength="5" value="{$values.source_ports_range.0.1|default:''}" />&nbsp;
				<input type="text" name="source_ports_range[1][]" id="source_ports12" size="5" maxlength="5" value="{$values.source_ports_range.1.0|default:''}" /> -
				<input type="text" name="source_ports_range[1][]" id="source_ports13" size="5" maxlength="5" value="{$values.source_ports_range.1.1|default:''}" />
			</td>
		</tr>
		{if $chain == 'INPUT' || $chain == 'FORWARD' || $chain == 'PREROUTING'} 
		<!-- MAC address -->
		<tr id="row_source_mac">
			<th>{if isset($errors.source_mac)}{$warn_icon}{/if}<label for="source_mac1">Source MAC</label></th>
			<td>:
				<input type="text" name="source_mac[]" id="source_mac1" size="2" maxlength="2" value="{$values.source_mac.0|default:''}" /> :
				<input type="text" name="source_mac[]" id="source_mac2" size="2" maxlength="2" value="{$values.source_mac.1|default:''}" /> :
				<input type="text" name="source_mac[]" id="source_mac3" size="2" maxlength="2" value="{$values.source_mac.2|default:''}" /> :
				<input type="text" name="source_mac[]" id="source_mac4" size="2" maxlength="2" value="{$values.source_mac.3|default:''}" /> :
				<input type="text" name="source_mac[]" id="source_mac5" size="2" maxlength="2" value="{$values.source_mac.4|default:''}" /> :
				<input type="text" name="source_mac[]" id="source_mac6" size="2" maxlength="2" value="{$values.source_mac.5|default:''}" />
			</td>
		</tr>
		{/if} 
		</table>
	</fieldset>

	<fieldset>
	<legend>Destination</legend>
		<table>
		{if $chain == 'FORWARD' || $chain == 'OUTPUT' || $chain == 'POSTROUTING'} 
		<!-- Interfaces -->
		<tr id="row_destination_interface">
			<th>{if isset($errors.destination_interface)}{$warn_icon}{/if}<label for="destination_interface">Output interface</label></th>
			<td>: {html_options name="destination_interface" id="destination_interface" options=$system.interfaces selected=$values.destination_interface|default:''}</td>
		</tr>
		{/if} 
		<!-- IP addresses -->
		<tr id="row_destination_address">
			<th style="width:130px;">{if isset($errors.destination_address)}{$warn_icon}{/if}<label for="destination_address">Destination address</label></th>
			<td style="width:290px;">:
				<input type="text" name="destination_address" id="destination_address" size="15" maxlength="15" value="{$values.destination_address|default:''}" />
				<span id="destination_address_separator">{if isset($values.destination_address_range) && $values.destination_address_range != ''}-{else}/{/if}</span>
				<input type="text" name="destination_address_net" id="destination_address_net" size="2" maxlength="2" value="{$values.destination_address_net|default:''}" {if isset($values.destination_address_range) && $values.destination_address_range != ''}style="display:none;"{/if} />
				<input type="text" name="destination_address_range" id="destination_address_range" size="15" maxlength="15" value="{$values.destination_address_range|default:''}" {if !isset($values.destination_address_range) || $values.destination_address_range == ''}style="display:none;"{/if} />
				<img src="img/icons/change.png" width="16" height="16" alt="Change" title="Change formatting" style="cursor:pointer;" onclick="changeAddress('destination');" />
			</td>
			<td>&nbsp;</td>
			<th style="width:105px;">{if isset($errors.destination_type)}{$warn_icon}{/if}<label for="destination_type">Address type</label></th>
			<td>: {html_options name="destination_type" id="destination_type" options=$options.addresses selected=$values.destination_type|default:''}</td>
		</tr>
		<!-- Ports -->
		<tr id="row_destination_ports" {if !isset($values.protocol) || ($values.protocol != 'tcp' && $values.protocol != 'udp')}style="display:none;"{/if}>
			<th>{if isset($errors.destination_ports)}{$warn_icon}{/if}<label for="destination_ports1">Destination ports</label></th>
			<td>:
				<input type="text" name="destination_ports[]" id="destination_ports1" size="5" maxlength="5" value="{$values.destination_ports.0|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports2" size="5" maxlength="5" value="{$values.destination_ports.1|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports3" size="5" maxlength="5" value="{$values.destination_ports.2|default:''}" />&nbsp;
				<input type="text" name="destination_ports[]" id="destination_ports4" size="5" maxlength="5" value="{$values.destination_ports.3|default:''}" />
			</td>
			<td>&nbsp;</td>
			<th>{if isset($errors.destination_ports_range)}{$warn_icon}{/if}<label for="destination_ports10">Ports ranges</label></th>
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
	<legend>Advanced options</legend>
		<table>
		<!-- ICMP type -->
		<tr id="row_icmp" {if !isset($values.protocol) || $values.protocol != 'icmp'}style="display:none;"{/if}>
			<th style="width:130px;">{if isset($errors.icmp)}{$warn_icon}{/if}<label for="icmp">ICMP type</label></th>
			<td>: {html_options name="icmp" id="icmp" options=$options.icmp selected=$values.icmp|default:''}</td>
		</tr>
		<!-- TCP flags -->
		<tr id="row_flags" {if !isset($values.protocol) || $values.protocol != 'tcp'}style="display:none;"{/if}>
			<th style="width:130px;">{if isset($errors.flags)}{$warn_icon}{/if}<label>TCP flags</label></th>
			<td>:{html_checkboxes name="flags" options=$options.flags selected=$values.flags|default:''}
			</td>
		</tr>
		<!-- Connection trace -->
		<tr id="row_states">
			<th style="width:130px;">{if isset($errors.states)}{$warn_icon}{/if}<label>Connection state</label></th>
			<td>:{html_checkboxes name="states" options=$options.states selected=$values.states|default:''}</td>
		</tr>
		<!-- Limit -->
		<tr id="row_limit">
			<th style="width:130px;">{if isset($errors.limit_value)}{$warn_icon}{/if}<label for="limit_value">Limit</label></th>
			<td>:
				<input type="text" name="limit_value" id="limit_value" size="3" maxlength="3" value="{$values.limit_value|default:''}" /> /
				{html_options name="limit_suffix" id="limit_suffix" options=$options.limits selected=$values.limit_suffix|default:''}
			</td>
		</tr>
		</table>

		<div id="more_link">
			<img src="img/icons/more.png" width="16" height="16" alt="More" />
			<a href="javascript:moreInfo();">More options</a>
		</div>
		<div id="less_link" style="display:none;">
			<img src="img/icons/less.png" width="16" height="16" alt="Less" />
			<a href="javascript:lessInfo();">Less options</a>
		</div>

		<table id="more_data" style="display:none;">
		<!-- TTL -->
		<tr id="row_ttl">
			<th style="width:130px;">{if isset($errors.ttl_value)}{$warn_icon}{/if}<label for="ttl_operator">TTL</label></th>
			<td>:
				{html_options name="ttl_operator" id="ttl_operator" options=$options.operators selected=$values.ttl_operator|default:''}
				<input type="text" name="ttl_value" id="ttl_value" size="3" maxlength="3" title="Enter TTL value between 1 and 255" value="{$values.ttl_value|default:''}" />
			</td>
		</tr>
		<!-- Packet length -->
		<tr id="row_length">
			<th style="width:130px;">{if isset($errors.length)}{$warn_icon}{/if}<label for="length">Packet length</label></th>
			<td>:
				<input type="text" name="length" id="length" size="5" maxlength="5" value="{$values.length|default:''}" /> -
				<input type="text" name="length_range" id="length_range" size="5" maxlength="5" value="{$values.length_range|default:''}" /> bytes
			</td>
		</tr>
		<!-- Service type -->
		<tr id="row_tos">
			<th style="width:130px;">{if isset($errors.destination_type)}{$warn_icon}{/if}<label for="tos">Service type</label></th>
			<td>: {html_options name="tos" id="tos" options=$options.tos selected=$values.tos|default:''}</td>
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

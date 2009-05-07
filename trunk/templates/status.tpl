{include file='header.inc.tpl'}

{assign var="warn_icon" value='<img src="img/icons/warning.png" width="16" height="16" alt="Warning" title="Interface is not connected" />'}
{assign var="error_icon" value='<img src="img/icons/error.png" width="16" height="16" alt="Error" title="Interface is down" />'}

<h1>System and network status</h1>
<form method="post" action="./{$page}.php">
	<fieldset>
	<legend>Interfaces</legend>
		<table class="list">
		<thead>
			<tr>
				<th>Name</th>
				<th>Link type</th>
				<th>IPv4 addresses</th>
				<th>IPv6 addresses</th>
				<th>MAC address</th>
				<th>MTU</th>
				<th>Received</th>
				<th>Transmitted</th>
			</tr>
		</thead>

		<tbody>
			{foreach from=$system.interfaces key=name item=data} 
			<tr class="{$data.link|lower}">
				<td>{$name}{if !$data.up} {$error_icon}{elseif !$data.running} {$warn_icon}{/if}</td>
				<td>{$data.link}</td>
				<td>{$data.ipv4|@implode:'<br />'}</td>
				<td>{$data.ipv6|@implode:'<br />'}</td>
				<td>{$data.mac}</td>
				<td class="numeric">{$data.mtu}</td>
				<td class="numeric">{math equation="x / 10485760" x=$data.rx format="%.2f"} MB</td>
				<td class="numeric">{math equation="x / 10485760" x=$data.tx format="%.2f"} MB</td>
			</tr>
			{/foreach} 
		</tbody>
		</table>
	</fieldset>

	<fieldset>
	<legend>Connections</legend>
		<table class="list">
		<thead>
			<tr>
				<th>Remote address : port</th>
				<th>Local address : port</th>
				<th>Protocol</th>
				<th>State</th>
				<th style="width:37px;">
					<img src="img/icons/source.png" width="16" height="16" alt="Source" />
					<img src="img/icons/destination.png" width="16" height="16" alt="Destination" />
				</th>
			</tr>
		</thead>

		<tbody>
			{foreach from=$system.connections item=data} 
			<tr class="{$data.state|lower}">
				<td>{$data.remote_address} : {$data.remote_port}</td>
				<td>{$data.local_address} : {$data.local_port}</td>
				<td>{$data.protocol|upper}</td>
				<td>{$data.state}</td>
				<td>
					<a href="filter.php?chain=INPUT&amp;req=1&amp;protocol={$data.protocol}&amp;source_address={$data.remote_address}&amp;destination_address={$data.local_address}&amp;destination_ports[]={$data.local_port}&amp;target=DROP" title="Block incoming connection"><img src="img/icons/block.png" width="16" height="16" alt="Block" /></a>
					<a href="filter.php?chain=OUTPUT&amp;req=1&amp;protocol={$data.protocol}&amp;source_address={$data.local_address}&amp;destination_address={$data.remote_address}&amp;destination_ports[]={$data.remote_port}&amp;target=DROP" title="Block outgoing connection"><img src="img/icons/block.png" width="16" height="16" alt="Block" /></a>
				</td>
			</tr>
			{/foreach} 
		</tbody>
		</table>
	</fieldset>

	<fieldset>
	<legend>ARP cache</legend>
		<table class="list">
		<thead>
			<tr>
				<th>IP address</th>
				<th>MAC address</th>
				<th>Interface</th>
				<th style="width:37px;">
					<img src="img/icons/remote.png" width="16" height="16" alt="Remote" />
					<img src="img/icons/local.png" width="16" height="16" alt="Local" />
				</th>
			</tr>
		</thead>

		<tbody>
			{foreach from=$system.arp key=ip item=data} 
			<tr class="{cycle name=arp values='odd,even'}">
				<td>{$ip}</td>
				<td>{$data.mac|default:'Unknown'}</td>
				<td>{$data.interface}{if $data.link} ({$data.link}){/if}</td>
				<td>
					<a href="filter.php?chain=INPUT&amp;req=1&amp;source_address={$ip}&amp;target=DROP" title="Block IP address"><img src="img/icons/block.png" width="16" height="16" alt="Block" /></a>
					{if $data.mac}<a href="filter.php?chain=INPUT&amp;req=1&amp;source_mac={$data.mac}&amp;target=DROP" title="Block MAC address"><img src="img/icons/block.png" width="16" height="16" alt="Block" /></a>{else}<img src="img/icons/spacer.png" width="16" height="16" alt="Spacer" />{/if}
				</td>
			</tr>
			{/foreach} 
		</tbody>
		</table>
	</fieldset>
	<fieldset>
	<legend>System</legend>
		<table class="list">
		<tr class="{cycle name=system values='odd,even'}">
			<th style="width:135px;">System</th>
			<td>{$system.name}</td>
		</tr>
		<tr class="{cycle name=system values='odd,even'}">
			<th>Uptime</th>
			<td>{$system.uptime}</td>
		</tr>
		<tr class="{cycle name=system values='odd,even'}">
			<th>CPU</th>
			<td>{$system.cpu.model_name} @ {$system.cpu.cpu_mhz|string_format:"%d"} MHz ({$system.cpu.cache_size} cache)</td>
		</tr>
		<tr class="{cycle name=system values='odd,even'}">
			<th>Memory</th>
			<td>
				Physical :
				{math equation="x / 1024" x=$system.memory.memtotal-$system.memory.memfree format="%.2f"} MB /
				{math equation="x / 1024" x=$system.memory.memtotal format="%.2f"} MB
				({math equation="x/y*100" x=$system.memory.memtotal-$system.memory.memfree y=$system.memory.memtotal format="%.2f"}% used)<br />
				Swap :
				{math equation="x / 1024" x=$system.memory.swaptotal-$system.memory.swapfree format="%.2f"} MB /
				{math equation="x / 1024" x=$system.memory.swaptotal format="%.2f"} MB
				({math equation="x/y*100" x=$system.memory.swaptotal-$system.memory.swapfree y=$system.memory.swaptotal format="%.2f"}% used)
			</td>
		</tr>
		</table>
		<p class="centered">
			<img src="img/icons/ok.png" width="16" height="16" alt="Validate" />
			<a href="./index.php?check=1">Test configuration</a>
		</p>
	</fieldset>
</form>

{include file='footer.inc.tpl'}

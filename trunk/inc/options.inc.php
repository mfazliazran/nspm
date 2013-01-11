<?php
// Define tables, chains ans associated targets
$OPTIONS['tables'] = array(
	'filter'	=> array('INPUT', 'FORWARD', 'OUTPUT'),
	'nat'		=> array('PREROUTING', 'POSTROUTING', 'OUTPUT'),
	'mangle'	=> array('PREROUTING', 'INPUT', 'FORWARD', 'OUTPUT', 'POSTROUTING')
);
$OPTIONS['policies'] = array(
	'ACCEPT'	=> 'ACCEPT',
	'DROP'		=> 'DROP'
);
$OPTIONS['filter'] = array(
	'ACCEPT'	=> 'ACCEPT',
	'DROP'		=> 'DROP',
	'REJECT'	=> 'REJECT',
	'LOG'		=> 'LOG',
	'ULOG'		=> 'ULOG'
);
$OPTIONS['dnat'] = array(
	'DNAT'		=> 'DNAT',
	'REDIRECT'	=> 'REDIRECT',
	'NETMAP'	=> 'NETMAP'
);
$OPTIONS['snat'] = array(
	'SNAT'		=> 'SNAT',
	'MASQUERADE'	=> 'MASQUERADE',
	'NETMAP'	=> 'NETMAP'
);
$OPTIONS['nat'] = array_merge($OPTIONS['dnat'], $OPTIONS['snat']);

// Define known protocols
$OPTIONS['protocols'] = array(
	''		=> 'All',
	'tcp'		=> 'TCP',
	'udp'		=> 'UDP',
	'icmp'		=> 'ICMP',
	'Routing'		=> array(
		'ospf'		=> 'OSPF',
		'isis'		=> 'IS-IS',
		'eigrp'		=> 'EIGRP',
		'rsvp'		=> 'RSVP'
	),
	'Tunneling'	=> array(
		'gre'		=> 'GRE',
		'ipip'		=> 'IP-IP',
		'ah'		=> 'IPsec AH',
		'esp'		=> 'IPsec ESP'
	),
	'Multicast'	=> array(
		'igmp'		=> 'IGMP',
		'pim'		=> 'PIM'
	),
);

// Define specific header options
$OPTIONS['addresses'] = array(
	''		=> 'All',
	'UNICAST'	=> 'Unicast',
	'MULTICAST'	=> 'Multicast',
	'BROADCAST'	=> 'Broadcast',
	'ANYCAST'	=> 'Anycast',
	'LOCAL'		=> 'Local'
);
$OPTIONS['icmp'] = array(
	''		=> 'All',
	'0'		=> '(0) Echo reply',
	'3'		=> '(3) Destination unreachable',
	'4'		=> '(4) Source quench',
	'5'		=> '(5) Redirect',
	'8'		=> '(8) Echo request',
	'9'		=> '(9) Router advertisement',
	'10'		=> '(10) Router solicitation',
	'11'		=> '(11) Time exceeded',
	'12'		=> '(12) Parameter problem',
	'13'		=> '(13) Timestamp request',
	'14'		=> '(14) Timestamp reply',
	'17'		=> '(17) Address mask request',
	'18'		=> '(18) Address mask reply'
);
$OPTIONS['flags'] = array(
	'SYN'		=> 'SYN',
	'ACK'		=> 'ACK',
	'FIN'		=> 'FIN',
	'RST'		=> 'RST',
	'URG'		=> 'URG',
	'PSH'		=> 'PSH',
	'NONE'		=> 'None'
);
$OPTIONS['states'] = array(
	'NEW'		=> 'NEW',
	'ESTABLISHED'	=> 'ESTABLISHED',
	'RELATED'	=> 'RELATED',
	'INVALID'	=> 'INVALID'
);
$OPTIONS['severities'] = array(
	''		=> 'Default',
	'7'		=> 'debug',
	'6'		=> 'info',
	'5'		=> 'notice',
	'4'		=> 'warning',
	'3'		=> 'err',
	'2'		=> 'crit',
	'1'		=> 'alert',
	'0'		=> 'emerg'
);
$OPTIONS['limits'] = array(
	'second'	=> 'second',
	'minute'	=> 'minute',
	'hour'		=> 'hour',
	'day'		=> 'day'
);
$OPTIONS['tos'] = array(
	''			=> 'None',
	'Normal-Service'	=> '(0) Normal-Service',
	'Minimize-Cost'		=> '(2) Minimize-Cost',
	'Maximize-Reliability'	=> '(4) Maximize-Reliability',
	'Maximize-Throughput'	=> '(8) Maximize-Throughput',
	'Minimize-Delay'	=> '(16) Minimize-Delay'
);
$OPTIONS['ipv4options'] = array(
	''		=> 'None',
	'ssrr'		=> 'Strict source routing',
	'lsrr'		=> 'Loose source routing',
	'no-srr'	=> 'No source routing',
	'rr'		=> 'Record route',
	'ts'		=> 'Timestamp',
	'ra'		=> 'Router-alert'
);
$OPTIONS['operators'] = array(
	'lt'		=> 'less than',
	'eq'		=> 'equal to',
	'gt'		=> 'more than'
);
$OPTIONS['nlgroups'][''] = 'Default';
for ($i=1; $i<=32; $i++)
{
	$OPTIONS['nlgroups'][$i] = $i;
}
?>

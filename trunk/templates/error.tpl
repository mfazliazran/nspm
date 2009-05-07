{include file="header.inc.tpl" title="System error"}

<h1>{$error|capitalize} error</h1>

<p class="warning">
{if ($error == 'session')}
	A potential security breach was detected on your connection. A conflict
	occured between the requested session and the user who initiated it. A new
	session has just been created to allow the resuming of normal operations.
	If you reached this page using an external link or a browser bookmark,
	please update them not to include any kind of session ID.
{elseif ($error == 'system')}
	A fatal error occured while executing an external system call. Either your
	system was not ready to perform the requested action or this action was not
	possible. Please check the error message below and your current
	configuration before resuming normal operations.
{else}
	An uncaught exception occured. If you just did some specific or non
	standard actions, your system may not have been able to handle them. Please
	check the error message below and your current configuration before
	resuming normal operations.
{/if}
</p>

<p>
<em>
{if ($message)}
	<br />
	Context: {$message}
{/if}
{if ($command)}
	<br />
	Command: {$command}
{/if}
{if ($output)}
	<br />
	Output: {$output}
{/if}
</em>
</p>

{include file="footer.inc.tpl"}

<?php

define('INSIDE', true);
define('IN_ADMIN', true);

$_SetAccessLogPreFilename = 'admin/';
$_SetAccessLogPath = '../';
$_EnginePath = '../';

include($_EnginePath.'common.php');

	includeLang('admin');
	includeLang('admin/errorslist');

	$parse = $_Lang;
	if(!CheckAuth('programmer'))
	{
		message($_Lang['sys_noalloaw'], $_Lang['sys_noaccess']);
	}

	$TPL_Row = gettemplate('admin/errors_row');

	$DeleteID = round(floatval($_GET['delete']));
	$DoDeleteAll = ($_GET['deleteall'] == 'yes' ? true : false);

	if($DeleteID > 0)
	{
		doquery("DELETE FROM {{table}} WHERE `error_id` = {$DeleteID} LIMIT 1;", 'errors');
	}
	else if($DoDeleteAll)
	{
		doquery("TRUNCATE TABLE {{table}}", 'errors');
	}

	$Query_GetErrors = doquery("SELECT * FROM {{table}} LIMIT 100;", 'errors');
	$i = 0;

	while($ErrorData = mysql_fetch_assoc($Query_GetErrors))
	{
		++$i;
		$parse['errors_list'] .= parsetemplate($TPL_Row, array
		(
			'ID' => $ErrorData['error_id'],
			'Date' => date('d.m.Y H:i:s', $ErrorData['error_time']),
			'Text' => nl2br($ErrorData['error_text'])
		));
	}

	$Query_GetCount = doquery("SELECT COUNT(`error_id`) AS `count` FROM {{table}};", 'errors', true);
	$i = $Query_GetCount['count'];
	if($i >= 100)
	{
		$parse['errors_list'] .= "<tr><th class=b colspan=4>{$_Lang['ErrorsList_TooManyErrors']}</th></tr>";
		break;
	}

	$parse['errors_list'] .= "<tr><th class=b colspan=4>{$_Lang['ErrorsList_Count']}: {$i}</th></tr>";

	display(parsetemplate(gettemplate('admin/errors_body'), $parse), $_Lang['ErrorsList_Title'], false, true);
?>
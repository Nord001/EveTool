<table style="min-width: 650px;">
<tr>
    <?php if ($chars_standing !== False): ?>
    <td style="background-color: <?php echo ($agent->required_standing <= $chars_standing) ? 'green' : 'red'; ?>">
        &nbsp;
    </td>
    <?php endif; ?>
    <td width="50" valign="top" align="center">
        <div style="font-size: 300%;"><?php echo $agent->level; ?></div>
	    <div style="padding-left:3px"><b>Q <?echo $agent->quality; ?></b></div>
	</td>
	<td width="68"><?php echo get_character_portrait($agent->itemID, 64); ?></td>
	<td valign="top" style="padding-left:5px" id="left">
	    <b>Corporation</b>: <?php echo $agent->corpName; ?> / <?php echo $agent->division; ?> <br>
	    <b>Faction</b>: <?php echo $agent->faction; ?> -<b> Region</b>: <?php echo $agent->region; ?> - <b>System</b>: <?php echo $agent->systemName; ?> (<font color="<?php ($agent->security < 0.5) ? 'red' : 'black'; ?>"><?php echo $agent->security;?></font>)<br>

	    <b>Station</b>: <a id="fb_location" href="<?php echo site_url('/fancybox/location/'.$agent->stationID); ?>"><?php echo $agent->station; ?></a><br>
	    <b>Required Standing</b>: <?php echo $agent->required_standing; ?> - <b>Type</b>: <?php print_r(preg_replace("|(\p{Lu})|", ' $1', $agent->agentType));?><br>
	</td>
</tr>
</table>

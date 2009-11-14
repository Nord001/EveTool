<table style="min-width: 650px;">
<tr>
    <?php if ($chars_standing !== False): ?>
    <td bgcolor="<?php echo ($agent->required_standing <= $chars_standing) ? 'green' : 'red'; ?>">
        &nbsp;
    </td>
    <?php endif; ?>
    <td width="50" valign="top" align="center">
        <div style="font-size: 300%;"><?php echo $agent->level; ?></div>
	    <div style="padding-left:3px"><b>Q <?echo $agent->quality; ?></b></div>
	</td>
	<td valign="top" style="padding-left:5px" id="left">
	    <b>Corporation</b>: <?php echo $agent->corpName; ?> / <?php echo $agent->division; ?> <br>
	    <b>Faction</b>: <?php echo $agent->faction; ?> -<b> Region</b>: <?php echo $agent->region; ?> - <b>System</b>: <?php echo $agent->systemName; ?> (<font color="<?php echo $agent->security_color; ?>"><?php echo $agent->security;?></font>)<br>

	    <b>Station</b>: <a id="fb_location" style="color: black;" href="<?php echo site_url('/fancybox/location/'.$agent->itemID); ?>"><?php echo $agent->station; ?></a><br>
	    <b>Required Standing</b>: <?php echo $agent->required_standing; ?> - <b>Type</b>: <?php print_r(preg_replace("|(\p{Lu})|", ' $1', $agent->agentType));?><br>
	</td>
	<td>
        <a id="fb_character" style="color: black;" href="<?php echo site_url('/fancybox/character/'.$agent->itemID); ?>">
    	    <img src="<?php echo "/files/cache/char/{$agent->itemID}/64/char.jpg"; ?>">
	    </a>
	</td>
</tr>
</table>

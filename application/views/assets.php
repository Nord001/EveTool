<!--
<?php print_r($assets); ?>
-->
<div id="content">
<table width="100%">
<caption><?php echo isset($caption) ? $caption : 'Assets for all Characters'; ?></caption>
<tr>
    <th colspan="3">Item</th>
    <th>Amount</th>
    <th>Volume</th>
    <th colspan="2">Location</th>
</tr>
<?php foreach ($assets as $k => $v): ?>
    <?php if ($v['containerID'] != Null && !$show_contents) continue; ?>
    <tr>
    <td width="32">
        <a id="fb_character" href="<?php echo site_url('/fancybox/character/'.$v['owner']->characterID); ?>">
            <?php echo get_character_portrait($v['owner'], 32, 'entry'); ?>
        </a>
    </td>
	<td style="text-align: left;" width="32">
        <a id="fb_item" href="<?php echo site_url('/fancybox/item/'.$v['typeID']); ?>">       
    	   <?php echo icon_url($v,32);?>
	    </a>
    </td>
    <td><?php echo $v['typeName']; ?></td>
    <td><?php echo number_format($v['quantity']); ?></td>
    <td><?php echo number_format($v['quantity'] * $v['volume'], 1); ?> m&sup3;</td>
	<td>
        <a id="fb_location" href="<?php echo site_url('/fancybox/location/'.$v['locationID']); ?>"><?php echo locationid_to_name($v['locationID']);?></a>
    </td>
    <td width="32">
        <?php if (isset($v['contents']) && count($v['contents']) > 0): ?>
        <a id="fb_assets_content" href="<?php echo site_url('/assets/ajax_contents/'.$v['itemID']); ?>"><img src="/files/itemdb/icons/32_32/icon03_13.png" title="Contents"></a>
        <?php elseif (isset($v['containerID'])): ?>
        <a id="fb_assets_content" href="<?php echo site_url('/assets/ajax_contents/'.$v['containerID']); ?>"><?php echo icon_url($v['container'], 32);?></a>
        <?php else: ?>
        &nbsp;
        <?php endif; ?>
    </td>
    </tr>
<?php endforeach; ?>
    <tr>
        <td colspan="6" style="text-align: center;">
            <?php echo $this->pagination->create_links(); ?>
        </td>
    </tr>

</table>
</div>

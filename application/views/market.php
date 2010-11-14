<?php foreach(array('sell','buy') as $type): ?>
<table width="100%">
<caption>
    <div><?php echo ucfirst($type);?> Orders</div>
</caption>
<tr>
    <th width="32">By</th>
	<th colspan="2">Type</th>
    <th>Price</th>
    <!--th colspan="2" title="Current Market Price">Market Price</th-->
    <th colspan="2">Remaining</th>
    <th>Ends</th>
    <th>Station</th>
</tr>
<?php foreach ($$type as $row): ?>
<tr>
    <td>
        <a id="fb_character" href="<?php echo site_url('/fancybox/character/'.$row['charID']); ?>">       
            <?php echo get_character_portrait($row['owner'], 32, 'entry'); ?>
        </a>
    </td>
	<td style="text-align: left;">
        <a id="fb_item" href="<?php echo site_url('/fancybox/item/'.$row['typeID']); ?>">       
    	    <?php echo icon_url($row,32);?>
	    </a>
    </td>
    <td style="text-align: left;"><?php echo $row['typeName'];?></td>
	<td><?php echo number_format($row['price'], 2);?> ISK</td>
	<!--td>
	    <?php //echo number_format($prices[$row->typeID][$type]['median'], 2);?> ISK
    </td>
    <td>
	    <?php //echo number_format(($prices[$row->typeID][$type]['median'] - $row->price) / $row->price * 100, 1); ?> %
    </td-->
    <td width="5"><?php echo $row['remaining'].'/'.$row['total']; ?></td>
	<td><?php echo number_format($row['remaining']*$row['price']);?> ISK</td>
	<td><?php echo $row['ends'];?></td>
	<td>
        <a id="fb_location" href="<?php echo site_url('/fancybox/location/'.$row['locationid']); ?>"><?php echo $row['location'];?></a>
    </td>
</tr>
<?php endforeach;?>
<tr>
    <th colspan="4">Sum:</td>
    <td><?php echo $remaining[$type].'/'.$total[$type]; ?></td>
    <td><?php echo number_format($remainingPrice[$type]); ?> ISK</td>
    <td colspan="2">&nbsp;</td>
</tr>
</table>
<?php endforeach; ?>

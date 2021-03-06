<div id="content">

    <?php foreach(array('sell','buy') as $type): ?>
    <?php if (!isset($$type)) continue; ?>
    <table width="100%">
    <caption>
        <div><?php echo ucfirst($type);?> Orders</div>
    </caption>
    <tr>
        <th width="32">By</th>
	    <th colspan="2">Item</th>
        <th>Price per Unit</th>
        <th>Left</th>
        <th>Total</th>
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
        <td width="5"><?php echo number_format($row['remaining']); ?></td>
        <td width="5"><?php echo number_format($row['total']); ?></td>
	    <!-- td><?php #echo number_format($row['remaining']*$row['price']);?> ISK</td -->
	    <td><?php echo $row['ends'];?></td>
	    <td>
            <a id="fb_location" href="<?php echo site_url('/fancybox/location/'.$row['stationID']); ?>"><?php echo locationid_to_name($row['stationID']);?></a>
        </td>
    </tr>
    <?php endforeach;?>
    <?php if (!empty($remaining[$type])): ?>
    <tr>
        <th colspan="4">Sum:</td>
        <td><?php echo $remaining[$type]; ?></td>
        <td><?php echo $total[$type]; ?></td>
        <td align="right" colspan="2"><b><?php echo number_format($remainingPrice[$type]); ?> ISK</b></td>
    </tr>
    <?php endif; ?>
    </table>
    <br />
    <?php endforeach; ?>

</div>

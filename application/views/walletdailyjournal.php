<br />
<a id="buttan" href="<? echo site_url('/wallet/journal/'.$character);?>">Wallet Journal</a>
<a id="buttan" href="<? echo site_url('/wallet/dailyjournal/'.$character);?>">Group by Day</a>
<br />
<table width="100%">
    <caption>Wallet Journal, grouped by Day</caption>
    <tr>
        <th>Type</th>
        <th>Expense</th>
        <th>Income</th>
    </tr>
    <?php foreach($daily as $k => $v):?>
    <tr>
        <td class="light" style="text-align: left;"><?php echo $total[$k]['prettydate']; ?></td>
        <td class="light"><font color="red"><?php echo number_format($total[$k]['expense'], 2); ?></font></td>
        <td class="light"><font color="green"><?php echo number_format($total[$k]['income'], 2); ?></font></td>
    </tr>
    <?php foreach($v as $type): ?>
    <tr>
        <td><?php echo $type['refTypeName']; ?></td>
        <td><font color="red"><?php echo number_format($type['expense'], 2); ?></font></td>
        <td><font color="green"><?php echo number_format($type['income'], 2); ?></font></td>
    </tr>
    <?php endforeach;?>
    <tr><td colspan="3">&nbsp;</td></tr>
    <?php endforeach;?>
</table>

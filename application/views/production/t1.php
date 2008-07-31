<script type="text/javascript">
function numberFormat(nStr) {
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1))
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  return x1 + x2;
}
$(document).ready(function(){
    $("#bpForm").ajaxComplete(function(request, settings){
      $("#bpSpinner").hide();
    });
    $("#bpForm").ajaxStart(function(request, settings){
      $("#bpSpinner").show();
    });
    $.getJSON("<?php echo site_url('production/t1Update/'.$character.'/'.$blueprintID); ?>", loadResults);
    $("#bpForm").submit(formProcess);
    
    function loadResults(data) {
        $.each(data.req, function(i, item){
            $(".req" + i).text(numberFormat(item));
            $(".have" + i).text(numberFormat(data.have[i]))
            if ( item > data.have[i]) {
                $(".have" + i).css({color:"red"});
            } else {
                $(".have" + i).css({color: $("td").css("color")});
            }
            
        });
    }
    
    function formProcess(event){
      event.preventDefault();
      me = $("#me").val();
      amount = $("#amount").val();
      $.post("<?php echo site_url('production/t1Update/'.$character.'/'.$blueprintID);?>", {me: me, amount: amount},loadResults, "json");
    }
});
</script>
<table width="100%">
    <tr>
        <th colspan="5"><?php echo $product->typeName; ?></th>
    </tr>
    <tr>
        <td colspan="5" style="text-align: left;">
            <img src="<?php echo getIconUrl($product->typeID, 128); ?>" align="left">
            <p style="padding-left: 140px;"><?php echo nl2br($product->description); ?></p>
        </td>
    </tr>
    <tr>
        <form action="<?php echo site_url('production/t1Update/'.$blueprintID); ?>" method="post" id="bpForm">
        <th colspan="2">ME: <input type="text" name="me" id="me" value="0" size="2" /></th>
        <th>Amount: <input type="text" name="amount" id="amount" value="1" size="2"></th>
        <th colspan="2"><img style="padding-left: 20px;" id="bpSpinner" align="left" src="<?php echo site_url('/files/spinner-light.gif'); ?>"><?php echo form_submit('Submit', 'Submit'); ?></th>
        </form>
    </tr>
    <tr>
        <th colspan="2">Type</th>
        <th>Perfect</th> 
        <th>Requires</th>
        <th>Available</th>
    </tr>
<?php foreach($data as $r): ?>
    <tr>
        <td width="32"><img src="<?php echo getIconUrl($r['typeID'], 32); ?>"></td>
        <td style="text-align: left"><?php echo $r['typeName']; ?></td>
        <td><?php echo number_format($r['requiresPerfect']); ?></td>
        <td><p class="req<?php echo $r['typeID'];?>"></p></td>
        <td><p class="have<?php echo $r['typeID'];?>"></p></td>
    </tr>
<?php endforeach; ?>
</table>
<?php if (isset($skillreq)): ?>
<br />
<p><b>Skill Requirements:</b></p>
<ul>
<?php foreach ($skillreq as $skill): ?>
    <li><?php echo "{$skill['typeName']} level {$skill['level']}"; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />

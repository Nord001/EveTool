<!-- 
<?php #print_r($data); ?>
-->
<div id="content">

	<div class="post">
		<h2 class="title">Totals</h2>
		<p>
		<ul>
		<li>You currently have <b><?php echo number_format($global['totalisk']);?></b> ISK total on all Characters.
		<li>All your Characters Combined have <b><?php echo number_format($global['totalsp']);?></b> Skillpoints.
		</ul>
		</p>
	</div>
<?php if(!empty($alerts)): ?>
	<div class="post">
		<h2>Alerts</h2>
		<p>
		<ul>
        <?php foreach ($alerts as $alert): ?>
            <li><?php echo $alert;?></li>
	    <?php endforeach; ?>
		</ul>
		</p>
	</div>
<?php endif; ?>

<?php foreach ($data as $i):?>
			<div class="post">
				<h2 class="title"><?php echo $i['name']; ?><?php echo get_character_portrait($i, 64, 'left'); ?></h2>
				<div class="entry">
					<p>
					<!-- there is WAY to much php code right here. Not pretty -->
					<?php echo $i['name']; ?> (<i><?php echo $i['corporationName']; ?><?php if (!empty($i['allianceName'])): echo ' / '.$i['allianceName']; endif;?></i>)
					<?php if ($i['isTraining']):?>
					is currently Training <b><?php echo $i['trainingTypeName']; ?></b> to Level <b><?php echo $i['trainingToLevel']; ?></b>. 
					<?php echo $i['sex']; ?> started Training <?php echo api_time_print($i['trainingStartTime']);?> and will finish <?php echo api_time_print($i['trainingEndTime']);?> (<b><?php echo api_time_to_complete($i['trainingEndTime']);?></b>).
					<?php else: ?>
					is currently <b>not</b> Training a Skill.
					<?php endif; ?>
					At <b><?php echo number_format($i['skillpoints_total']);?></b> Skillpoints <?php echo $i['sex']; ?> has a total of <b><?php echo $i['skills_total'];?></b> Skills Trained, <b><?php echo $i['skills_at_level'][5];?></b> of them at Level <b>5</b>.
					<?php echo $i['sex2']; ?> Wallet currently sits at <b><?php echo number_format($i['balance']); ?></b> ISK.
					</p>
					<!--ul style="clear: left;">
					    <li><a href="<?php echo site_url("characters/sheet/{$i['name']}");?>">Character Sheet</a></li>
					    <li><a href="<?php echo site_url("characters/ships/{$i['name']}");?>">Ships <?php echo $i['name']; ?> can fly</a></li>
					</ul-->
				</div>
			</div>
<?php endforeach; ?>
</div>

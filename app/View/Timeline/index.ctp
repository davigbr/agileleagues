<h2>Timeline</h2>
<br />
<?if (empty($timelines)): ?>
	<p>No timeline data! :(</p>
<?else:?>
	<div class="timeline-centered">
		<?$i = 0; ?>
		<?foreach ($timelines as $timeline): ?>
			<?
				$datetime = new DateTime($timeline['Timeline']['when']);
			?>
			<article class="timeline-entry <? echo $i%2? 'left-aligned' : ''?>">
				<? if ($timeline['Timeline']['what'] == 'Activity'): ?>
					<div class="timeline-entry-inner">
						<time class="timeline-time" 
							datetime="<? echo $this->Format->date($timeline['Timeline']['when']) . 'T' . $datetime->format('H:i:s'); ?>">
							<span><? echo $this->Format->time($timeline['Timeline']['when']) ?></span> 
							<span><? echo $this->Format->date($timeline['Timeline']['when']) ?></span>
						</time>
						<div class="timeline-icon bg-success" style="background-color: <? echo $timeline['Domain']['color']?>">
							<i class="entypo-flag"></i>
						</div>
						<div class="timeline-label">
							<h2>
								<a href="#"><? echo $timeline['Player']['name'] ?></a> 
								<span> reported a <strong><? echo $timeline['Domain']['name']?></strong> activity</span></h2>
							<p>
								<strong><? echo $timeline['Activity']['name']?></strong>: 
								<? echo $timeline['Activity']['description']?>
							</p>
						</div>
					</div>
				<?endif;?>
			</article>
			<?$i++?>
		<?endforeach;?>
	</div>
<?endif;?>
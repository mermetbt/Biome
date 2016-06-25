<?php

$value = $this->getValue();

$value = round($value);

?><div class="databox">
	<div class="databox-left databox-<?php echo $this->getType(); ?>">
		<div class="databox-piechart">
			<div data-trackcolor="rgba(255,255,255,0.1)" data-size="47" data-linewidth="3" data-animate="1000" data-percent="<?php echo $value; ?>" data-linecap="butt" data-barcolor="#fff" data-toggle="easypiechart" style="width: 47px; height: 47px; line-height: 47px;"><span><?php echo $value; ?>%</span></div>
		</div>
	</div>
	<div class="databox-right">
		<span class="databox-number"><?php echo $this->getQuantity(); ?></span>
		<div class="databox-text"><?php echo $this->getName(); ?></div>
		<div class="databox-stat">
			<i class="<?php echo $this->getIcon(); ?>"></i>
		</div>
	</div>
</div>

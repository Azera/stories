<div class="view">
	<b><?php echo CHtml::link(CHtml::encode($data->title), array('view', 'id'=>$data->id)); ?></b> by <b><?php echo CHtml::link(CHtml::encode($data->author_id), array('user/view', 'id'=>$data->author_id)); ?></b><br />
	Tags: <?php echo CHtml::encode($data->tags); ?><br />
	Rating: <?php echo CHtml::encode($data->totalRating); ?><br />
	<?php echo CHtml::encode($data->description); ?><br />
</div>
<?php
$this->breadcrumbs=array(
	'Stories'=>array('index'),
	$model->title,
);
?>

<h1><?php echo CHtml::encode($model->title); ?></h1>
Tags: <?php echo CHtml::encode($model->tags); ?><br />
Rating: <?php echo CHtml::encode($model->totalRating); ?><br />
Posted on <?php echo Yii::app()->dateFormatter->formatDateTime($model->create_time,'medium',null); ?> by <b><?php echo CHtml::link(CHtml::encode($model->author->username), array('user/view', 'id'=>$model->author->id)); ?></b><br />
<?php echo CHtml::encode($model->description); ?>
<hr>
<?php
	// No need for encoding here. String as halready been encoded before parsed
	echo $model->content_html;
?>
<hr>
Rating gadget, fixed if user already rated this story<br/>

<?php
	echo CHtml::ajaxLink('Favorite story', array('story/favorite')) .', '.
		 CHtml::ajaxLink('Favorite author', array('user/favorite', 'id'=>$model->author->id));
	if(NULL === $model->reviewed_user) echo ', '. CHtml::link('Report this story.', array('story/report', 'id'=>$model->id));
?><br />

<h2>Comments</h2>
<?php if(count($model->comments) == 0): ?>
	<p>No comments left yet.</p>
<?php else: ?>
	<?php foreach($model->comments as $comment): ?>
		<div class="comment">
			<div class="commentheader">
				<?php echo Yii::app()->dateFormatter->formatDateTime($comment->create_time,'medium',null); ?> by <?php echo CHtml::link(CHtml::encode($comment->author->username), array('user/view', 'id'=>$comment->author->id)); ?>
			</div>
			<div class="commentbody">
				<?php echo CHtml::encode($comment->content); ?>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<h3>Leave a comment</h3>
<?php if(Yii::app()->user->isGuest): ?>
	<p>Please log in to leave a comment.</p>
<?php elseif(Yii::app()->user->hasFlash('commentSubmitted')): ?>
	<div class="flash-success">
		<?php echo Yii::app()->user->getFlash('commentSubmitted'); ?>
	</div>
<?php else: ?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'comment-form',
	'enableAjaxValidation'=>true,
	// 'action'=>Yii::app()->createUrl('story/newcomment', array('id'=>$model->id)),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($comment,'content'); ?>
		<?php echo $form->textArea($comment,'content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($comment,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php endif; ?>

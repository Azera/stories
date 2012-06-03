<?php
$this->pageTitle=Yii::app()->name . ' - Change password';
$this->breadcrumbs=array(
        'Change Password',
);
?>

<h1>Change Password</h1>

<p>Please fill out the following form to change your password:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'password-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
                'validateOnSubmit'=>true,
        ),
)); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <div class="row">
                <?php echo $form->labelEx($model,'oldpass'); ?>
                <?php echo $form->passwordField($model,'oldpass'); ?>
                <?php echo $form->error($model,'oldpass'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'newpass1'); ?>
                <?php echo $form->passwordField($model,'newpass1'); ?>
                <?php echo $form->error($model,'newpass1'); ?>
        </div>

		<div class="row">
                <?php echo $form->labelEx($model,'newpass2'); ?>
                <?php echo $form->passwordField($model,'newpass2'); ?>
                <?php echo $form->error($model,'newpass2'); ?>
        </div>

        <div class="row buttons">
                <?php echo CHtml::submitButton('Submit'); ?>
        </div>

<?php $this->endWidget(); ?>
</div><!-- form -->

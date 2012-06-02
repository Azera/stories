<?php
$this->pageTitle=Yii::app()->name . ' - Recover';

$this->breadcrumbs = array(
        'Recover',
);
?>

<h1>Recover</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'recover-form',
        // 'enableAjaxValidation'=>true,
        // 'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
        'clientOptions'=>array(
                'validateOnSubmit'=>true,
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary(array($model)); ?>

        <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
        </div>

        <?php if(CCaptcha::checkRequirements()): ?>
        <div class="row">
                <?php echo $form->labelEx($model,'verifyCode'); ?>
                
                <?php $this->widget('CCaptcha'); ?>
                <?php echo $form->textField($model,'verifyCode'); ?>
                <?php echo $form->error($model,'verifyCode'); ?>
                
                <p class="hint">Please enter the letters as they are shown in the image above.<br/>Letters are not case-sensitive.</p>
        </div>
        <?php endif; ?>
        
        <div class="row submit">
                <?php echo CHtml::submitButton('Recover'); ?>
        </div>

<?php $this->endWidget(); ?>
</div><!-- form -->

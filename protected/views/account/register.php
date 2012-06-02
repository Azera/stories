<?php
$this->pageTitle=Yii::app()->name . ' - Registration';

$this->breadcrumbs = array(
        'Registration',
);
?>

<h1>Registration</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'registration-form',
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
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username'); ?>
        <?php echo $form->error($model,'username'); ?>
        </div>

        <div class="row">
        <?php echo $form->labelEx($model,'password1'); ?>
        <?php echo $form->passwordField($model,'password1'); ?>
        <?php echo $form->error($model,'password1'); ?>
        <p class="hint">Minimal password length 6 symbols.</p>
        </div>

        <div class="row">
        <?php echo $form->labelEx($model,'password2'); ?>
        <?php echo $form->passwordField($model,'password2'); ?>
        <?php echo $form->error($model,'password2'); ?>
        </div>

        <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
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
                <?php echo CHtml::submitButton('Register'); ?>
        </div>

<?php $this->endWidget(); ?>
</div><!-- form -->

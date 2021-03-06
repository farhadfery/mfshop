<div class="form">
<?php $this->widget('ext.EChosen.EChosen'); ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'comment-news-form',
        'enableAjaxValidation' => false,
            ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>


    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'email'); ?>
        <?php echo $form->textField($model, 'email', array('size' => 50, 'maxlength' => 50)); ?>
        <?php echo $form->error($model, 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->hiddenField($model, 'news_id'); ?>
        <?php //echo $form->textField($model,'news_id');  ?>
        <?php //echo $form->error($model,'news_id');  ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'comment_text'); ?>
        <?php echo $form->textArea($model, 'comment_text', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'comment_text'); ?>
    </div>

    <?if(Yii::app()->user->checkAccess('admin')){ ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo $form->dropDownList($model, 'status', Lookup::items('commentStatus'),array('class'=>'chzn-select')); ?>
        <?php echo $form->error($model, 'status'); ?>
    </div>
    <?php } ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class' => 'button grey')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
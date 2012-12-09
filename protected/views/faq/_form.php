<div class="form">
<?php $this->widget('ext.EChosen.EChosen'); ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'faq-form',
        'enableAjaxValidation' => false,
            ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'question'); ?>
        <?php echo $form->textArea($model, 'question', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'question'); ?>
    </div>

    <div class="row">
        <?php
        echo $form->labelEx($model, 'answer');
        $this->widget('ext.editMe.ExtEditMe', array(
            'model' => $model,
            'attribute' => 'answer',
        ));
        echo $form->error($model, 'answer');
        ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'grey button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
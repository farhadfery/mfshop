<div class="form">
    <?php $this->widget('ext.EChosen.EChosen'); ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'product-form',
        'enableAjaxValidation' => false,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
            ));
    ?>

    <?php if (Yii::app()->user->hasFlash('tagexist')) { ?>
        <div class="flash-error">
            <?php
            echo Yii::app()->user->getFlash('tagexist');
            Yii::app()->clientScript->registerScript('fadeAndHideEffect2', '$(".flash-error").animate({opacity: 1.0}, 5000).fadeOut("slow");');
            ?>
        </div>
    <?php } ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <div class="row">
        <div style="float:left;width: 10%;">
            <?php echo $form->labelEx($model, 'type'); ?>
        </div>
        <?php echo CHtml::encode(Lookup::item('productType', $model->type)); ?> 
    </div>
    <hr/>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'price'); ?>
        <?php echo $form->textField($model, 'price'); ?>
        <?php echo $form->error($model, 'price'); ?>
    </div>

    <hr/>
    <?php if ($model->getTags()!==null) { ?>
        <div class="row">
            <?php echo CHtml::label('Previous Tags', 'prevTags'); ?>
            <div class="tagslist">
                <?php
                echo CHtml::image(Yii::app()->theme->baseUrl . '/images/big_icons/icon-tag-blank-invert.png', '', array(
                    'style' => 'float:left',
                ))
                ?>
                <?php echo $model->getTags();?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <table>
            <tr>
                <td>
                    <?php echo $form->labelEx($model, 'tags'); ?>
                    <?php
                    echo $form->dropDownList($model, 'tags', CHtml::listData(Tag::model()->findAll(), 'id', 'name'), array(
                        'multiple' => 'multiple',                        
                        'class' => 'chzn-select',
                        'style' => 'width:400px',
                    ));
                    ?>
                    <p class="hint">Select as many tags as you want</p>
                    <?php echo $form->error($model, 'tags'); ?>
                </td>
                <td style="padding-right: 2em">- OR -</td>
                <td>
                    <?php
                    echo CHtml::TextField('addTag', '', array(
                        'class' => 'hinttext',
                        'placeholder'=>'Create a new one',
                    )) . ' ' . CHtml::ajaxLink('Create', array('tag/UpdateTagsList'), array(
                        'type' => 'POST',
                        'replace' => '#' . CHtml::activeId($model, 'tags') . '_chzn',
                            )
                    );
                    ?>
                </td>
            </tr>
        </table>
    </div>    

    <hr/>
    <?php
    echo $form->labelEx($model, 'description');
    $this->widget('ext.editMe.ExtEditMe', array(
        'model' => $model,
        'attribute' => 'description',
    ));
    echo $form->error($model, 'description');
    ?>
    <br/>
    <hr/>

    <!-- -------------------------------- bookorvideoform ------------------------------------------------- -->

    <?php if (isset($bookModel)) { ?>

        <div class="row">
            <?php echo $form->labelEx($bookModel, 'isbn'); ?>
            <?php echo $form->textField($bookModel, 'isbn'); ?>
            <?php echo $form->error($bookModel, 'isbn'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($bookModel, 'publisher_id'); ?>
            <?php
            echo $form->dropDownList($bookModel, 'publisher_id', CHtml::listData(Publisher::model()->findAll(), 'id', 'name'), array('class' => 'chzn-rtl chzn-select',
                'style' => 'width: 200px'
            ));
            ?>
            <?php echo $form->error($bookModel, 'publisher_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($bookModel, 'pages_count'); ?>
            <?php echo $form->textField($bookModel, 'pages_count'); ?>
            <?php echo $form->error($bookModel, 'pages_count'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($bookModel, 'edition'); ?>
            <?php echo $form->textField($bookModel, 'edition'); ?>
            <?php echo $form->error($bookModel, 'edition'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($bookModel, 'press_number'); ?>
            <?php echo $form->textField($bookModel, 'press_number'); ?>
            <?php echo $form->error($bookModel, 'press_number'); ?>
        </div>

    <?php } elseif (isset($videoModel)) { ?>
        <div class="row">
            <?php echo $form->LabelEx($videoModel, 'duration'); ?>
            <?php echo $form->TextField($videoModel, 'duration'); ?>
            <p class="hint">Duration in minutes</p>
            <?php echo $form->error($videoModel, 'duration'); ?>
        </div>

        <div class="row">
            <?php echo $form->LabelEx($videoModel, 'format'); ?>
            <?php echo $form->TextField($videoModel, 'format'); ?>
            <?php echo $form->error($videoModel, 'format'); ?>
        </div>

    <?php } elseif (isset($videoModel)) { ?>
        <div class="row">
            <?php echo $form->LabelEx($videoModel, 'duration'); ?>
            <?php echo $form->TextField($videoModel, 'duration'); ?>
            <p class="hint">Duration in minutes</p>
            <?php echo $form->error($videoModel, 'duration'); ?>
        </div>

        <div class="row">
            <?php echo $form->LabelEx($videoModel, 'format'); ?>
            <?php echo $form->TextField($videoModel, 'format'); ?>
            <?php echo $form->error($videoModel, 'format'); ?>
        </div>
    <?php } ?>
    <!-- --------------------------------- end of bookorvideoform ----------------------------------------- -->

    <div class="row buttons"> 
        <?php
        echo CHtml::submitButton(($model->isNewRecord || (isset($bookModel) && $bookModel->isNewRecord)
                || (isset($videoModel) && $videoModel->isNewRecord)) ? 'Create' : 'Save', array('class' => 'button grey'));
        ?>
    </div>

    <?php $this->endWidget();
    ?>

</div><!-- form -->

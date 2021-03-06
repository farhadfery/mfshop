<?php

class ProductController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column3';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        $adminUsers = AuthAssignment::model()->findAll('itemname=:admin', array(':admin' => 'admin'));
        $usernames = array();
        foreach ($adminUsers as $item) {
            $usernames[] = $item->user->username;
        }
        return array(
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('searchAll', 'index', 'view', 'indexBook', 'indexVideo'),
                'users' => array('*'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'create', 'update', 'chooseType'),
                'users' => $usernames,
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        //----------------------------------------------------------------------------

        $model = $this->loadModel($id);
        $commentProductDataProvider = new CActiveDataProvider('CommentProduct', array(
                    'criteria' => array(
                        'condition' => 'product_id=:productID',
                        'params' => array(':productID' => $id),
                    ),
                    'pagination' => array('pageSize' => 5),
                ));

        $images = $model->productImages;
        $imageList = array();
        $imageProperties = array();
        foreach ($images as $image) {
            $imageProperties["image_url"] = $image->product_image_url;
            $title = array();
            $title = explode('/', $image->product_image_url);
            $imageProperties["title"] = end($title);
            $imageList[] = $imageProperties;
        }

        //----------------------------------------------------------------------------
        if (Yii::app()->user->checkAccess('admin')) {
            $this->render('view', array(
                'model' => $model,
                'commentProductDataProvider' => $commentProductDataProvider,
                'bookModel' => $model->book,
                'videoModel' => $model->video,
                'imageList' => $imageList,
            ));
        } else {
            $this->render('view2', array(
                'model' => $model,
                'commentProductDataProvider' => $commentProductDataProvider,
                'bookModel' => $model->book,
                'videoModel' => $model->video,
                'imageList' => $imageList,
            ));
        }
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Product;
        $bookModel = new Book;
        $videoModel = new Video;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        //$this->performAjaxValidationForBook($bookModel);
        //$this->performAjaxValidationForVideo($videoModel);

        if (isset($_POST['Product']) && (isset($_POST['Book']) || isset($_POST['Video']))) {
            $transaction = Book::model()->dbConnection->beginTransaction();
            try {
                $model->attributes = $_POST['Product'];

                if ($model->save()) {
                    if (isset($_POST['Product']['tags'])) {
                        foreach ($_POST['Product']['tags'] as $tagId) {
                            $tagProductModel = new TagProductAssign;
                            $tagProductModel->product_id = $model->id;
                            $tagProductModel->tag_id = $tagId;
                            if (!$tagProductModel->save()) {
                                throw new CDbException('error in saving in tag-product');
                            } else {
                                $newFrequency = ++Tag::model()->findByPk($tagProductModel->tag_id)->frequency;
                                Tag::model()->updateByPk($tagProductModel->tag_id, array('frequency' => $newFrequency));
                            }
                        }
                    }

                    if (isset($_POST['Book'])) {
                        $bookModel->attributes = $_POST['Book'];
                        $bookModel->id = $model->id;

                        if ($bookModel->save()) {
                            $transaction->commit();
                            Yii::app()->user->setFlash('productSubmitted', 'You\'ve successfully submitted a new product to your shop.');
                            $this->redirect(array('productImage/create', 'pid' => $model->id));
                        }
                    } elseif (isset($_POST['Video'])) {
                        $videoModel->attributes = $_POST['Video'];
                        $videoModel->id = $model->id;
                        if ($videoModel->save()) {
                            $transaction->commit();
                            Yii::app()->user->setFlash('productSubmitted', 'You\'ve successfully submitted a new product to your shop.');
                            $this->redirect(array('productImage/create', 'pid' => $model->id));
                        }
                        //$this->redirect(array('view', 'id' => $model->id));
                    }
                    else
                        throw new CDbException('Error in saving in database. New product did NOT save.');;
                }
                else {
                    throw new CDbException('Error in saving in database. New product did NOT save.');
                }
            } catch (CException $e) {
                $transaction->rollBack();
            }
        } else {
            ProductImage::clearUploadedFile();
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $bookModel = $model->book;
        $videoModel = $model->video;

        $tagWrong = false;

        if (isset($_POST['Product']) && (isset($_POST['Book']) || isset($_POST['Video']))) {
            $transaction = Book::model()->dbConnection->beginTransaction();
            try {
                $model->attributes = $_POST['Product'];

                if ($model->save()) {
                    if (isset($_POST['Product']['tags'])) {
                        foreach ($_POST['Product']['tags'] as $tagId) {
                            $tagProductModel = new TagProductAssign;
                            $tagProductModel->product_id = $model->id;
                            $tagProductModel->tag_id = $tagId;
                            try {
                                $tagProductModel->save();
                            } catch (Exception $e) {
                                Yii::app()->user->setFlash('tagexist', 'Tag already used! Make sure you don\'t repeat tags.');
                                $tagWrong = true;
                            }
                        }
                    }

                    if (!$tagWrong) {
                        if (isset($_POST['Book'])) {
                            $bookModel->attributes = $_POST['Book'];
                            $bookModel->id = $model->id;

                            if ($bookModel->save()) {
                                $transaction->commit();
                                Yii::app()->user->setFlash('productSubmitted', 'You\'ve successfully submitted a new product to your shop.');
                                $this->redirect(array('product/view', 'id' => $model->id));
                            }
                        } elseif (isset($_POST['Video'])) {
                            $videoModel->attributes = $_POST['Video'];
                            $videoModel->id = $model->id;
                            if ($videoModel->save()) {
                                $transaction->commit();
                                Yii::app()->user->setFlash('productSubmitted', 'You\'ve successfully submitted a new product to your shop.');
                                $this->redirect(array('product/view', 'id' => $model->id));
                            }
                            //$this->redirect(array('view', 'id' => $model->id));
                        }
                    }
                    else
                        throw new CDbException('Error in saving in database. New product did NOT save.');;
                }
                else {
                    throw new CDbException('Error in saving in database. New product did NOT save.');
                }
            } catch (CException $e) {
                $transaction->rollBack();
            }
        } else {
            ProductImage::clearUploadedFile();
        }       

        $this->render('update', array(
            'model' => $model,
            'bookModel' => $bookModel,
            'videoModel' => $videoModel,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $p = $this->loadModel($id);
            $pImages = $p->productImages;
            $pImagesPath = array();
            foreach ($pImages as $pImage) {
                $pImagesPath[] = $pImage->product_image_url;
            }

            $p->delete();

            foreach ($pImagesPath as $path) {
                unlink(__DIR__ . '/../../../' . $path);
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex($tag = '') {
        if ($tag != '') {
            $criteria = new CDbCriteria;
            $temp = array();
            $temp2 = Tag::model()->find('name=:tag', array(':tag' => $tag))->tagProductAssigns;
            foreach ($temp2 as $item) {
                $temp[] = $item->product_id;
            }
            $criteria->addInCondition('id', $temp);

            $dataProvider = new CActiveDataProvider('Product', array(
                        'criteria' => $criteria,
                        'pagination' => array('pageSize' => 10),
                    ));
        }
        else
            $dataProvider = new CActiveDataProvider('Product');

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Product('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Product']))
            $model->attributes = $_GET['Product'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Product::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    //-----------------------------------------------------------------   

    public function actionChooseType() {
        if (isset($_POST['Product']) && isset($_POST['Product']['type'])) {
            $proType = (int) $_POST['Product']['type'];
            switch ($proType) {
                case Product::TYPE_BOOK:
                    $bookModel = new Book;
                    $this->renderPartial('//book/_form', array('bookModel' => $bookModel, 'this' => $this),false,true);
                    break;
                case Product::TYPE_VIDEO:
                    $videoModel = new Video;
                    $this->renderPartial('//video/_form', array('videoModel' => $videoModel),false,true);
                    break;
                default:
                    break;
            }
        }
    }

    public function actionSearchAll() {
        if (isset($_GET['searchquery'])) {

            $keyword = $_GET['searchquery'];
            // escape % and characters
            $keyword = strtr($keyword, array('%' => '\%', '_' => '\_'));

            $criteria = new CDbCriteria;

            $criteria->compare('name', $keyword, true);
            $criteria->compare('description', $keyword, true, 'OR');
            $criteria->compare('price', $keyword, FALSE, 'OR');

            $dataProvider = new CActiveDataProvider('Product', array(
                        'criteria' => $criteria,
                        'pagination' => array('pageSize' => 10),
                    ));

            $this->render('index', array(
                'dataProvider' => $dataProvider,
            ));
        }
    }

    public function actionIndexBook() {
        $criteria = new CDbCriteria;
        $criteria->compare('type', Product::TYPE_BOOK);

        $dataProvider = new CActiveDataProvider('Product', array(
                    'criteria' => $criteria,
                    'pagination' => array('pageSize' => 10),
                ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionIndexVideo() {
        $criteria = new CDbCriteria;
        $criteria->compare('type', Product::TYPE_VIDEO);

        $dataProvider = new CActiveDataProvider('Product', array(
                    'criteria' => $criteria,
                    'pagination' => array('pageSize' => 10),
                ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

}

<?php

namespace app\modules\master\controllers;

use Yii;
use app\modules\master\models\Suppliers;
use app\modules\master\models\SuppliersSearch;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SuppliersController implements the CRUD actions for Suppliers model.
 */
class SuppliersController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SuppliersSearch();
        $model = new Suppliers();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        Url::remember();
        $model = new Suppliers();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();

            if ($valid) {
                try {
                    $model->save();
                    $transaction->commit();
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Supplier is successfully added.'));
                    return $this->redirect('index');
                } catch (Exception $e) {

                    $transaction->rollback();
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', $e->getMessage()));
                     return $this->redirect('index');
                }
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Please change a few things up and try submitting again. '));
            }
            return $this->redirect('index');
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Agama is successfully added.'));
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Delete Supplier is successfully.'));
            return $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Delete Supplier is failed.'));
            return $this->redirect(['index']);
        }
    }

    protected function findModel($id)
    {
        if (($model = Suppliers::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

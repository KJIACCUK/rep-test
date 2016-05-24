<?php

class PromoController extends ApiController
{

    public function filters()
    {
        return array(
            array('ApiAccessControlFilter'),
            array('ApiProfileIsFilledFilter'),
            array('ApiProfileIsVerifiedFilter')
        );
    }

    public function actionActivateCode()
    {
        $currentUser = $this->getUser();
        $model = new PromoForm();
        /* @var $model PromoForm */
        $model->code = Api::getParam('code');

        if (!$model->validate()) {
            Api::respondValidationError($model);
        }

        $model->activateCode($currentUser);
        Api::respondSuccess(array('pointsAdded' => $model->getPromoCode()->pointsActivated));
    }
}
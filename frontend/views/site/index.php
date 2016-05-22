<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 21/05/16
 * Time: 15:57
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<div id="divLogin" class="row hidden">
    <div class="well col-md-6 col-lg-8 col-md-offset-3 col-lg-offset-2">

        <?= Html::beginForm(['/site/login'], 'POST', ['id' => 'login-form']) ?>

        <div class="form-group">
            <?= Html::textInput('name', 'bot', ['class' => 'form-control', 'placeholder' => 'Name']) ?>
        </div>

        <div class="form-group">
            <?= Html::textInput('email', 'bot@bot.io', ['class' => 'form-control', 'placeholder' => 'Email']) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Send', ['class' => 'btn btn-block btn-success']) ?>
        </div>

        <?= Html::endForm() ?>

    </div>
</div>


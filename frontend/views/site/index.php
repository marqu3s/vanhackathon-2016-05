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

<div id="divNewGameJoinGame" class="row hidden">
    <div class="col-xs-6 col-md-3 col-md-offset-3">
        <button id="btnHostGame" class="btn btn-primary btn-large btn-block">Host new game</button>
    </div>
    <div class="col-xs-6 col-md-3">
        <button id="btnJoinGame" class="btn btn-primary btn-large btn-block">Join a game</button>
    </div>
</div>
<div id="divGameSettings" class="row hidden">
    <div class="col-xs-6 col-md-3 col-md-offset-3">
        <div class="form-group">
            <p>Choose the code size:</p>
            <?= Html::dropDownList('secret_size', null, ['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8'], ['id' => 'secret_size', 'class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="form-group">
            <p>&nbsp;</p>
            <?= Html::button('OK', ['id' => 'btnCreateGame', 'class' => 'btn btn-block btn-success']) ?>
        </div>
    </div>
</div>
<div id="divGamesList"></div>
<div id="divGameRoom"></div>

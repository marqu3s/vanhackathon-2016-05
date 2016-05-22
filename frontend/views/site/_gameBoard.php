<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 22/05/16
 * Time: 14:06
 */

/** @var $this \yii\web\View */
/** @var $game array */
/** @var $player array */
/** @var $colorMap array */

//\yii\helpers\VarDumper::dump($player,10,true);
//\yii\helpers\VarDumper::dump($game,10,true);


$maxChancesToGuess = 8; // This can become a parameter to the game model.
$availableColorsFromGame = explode(',', $game['available_colors']);
$code = explode(',', $game['code']);
$codeSize = count($code);
$thisPlayerId = $player['id'];
$colorPickerScript = '';

foreach ($game['matches'] as $idInArray => $data) {
    if ($data['id_player'] == $thisPlayerId) {
        $numGuesses = (int) $data['num_guesses'];
        $availableGuesses = $maxChancesToGuess - $numGuesses;
        $playerStatus = $data['player_status'];
        break;
    }
}
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <h4 class="text-center">You are playing game #<?= $game['id'] ?> - Started at <?= Yii::$app->formatter->asDatetime($game['started_at']) ?></h4>

        <table class="table table-bordered">
            <?php if (isset($game['matches'][$idInArray]['history'])): ?>
                <?php for ($i = 0; $i < count($game['matches'][$idInArray]['history']); $i++): ?>
                    <tr>
                        <td>
                            Guess #<?= $i+1 ?>
                        </td>
                        <?php
                        $history = $game['matches'][$idInArray]['history'][$i];
                        $guess = explode(',', $history['guess']);
                        $nearMatches = $history['near_matches'];
                        $exactMatches = $history['exact_matches'];
                        $guessedAt = $history['guessed_at'];
                        ?>
                        <?php foreach ($guess as $colorCode):?>
                            <td><div style="width: 30px; height: 30px; background-color: <?= $colorMap[$colorCode] ?>; border-radius: 15px"></div></td>
                        <?php endforeach ?>
                        <td>
                            <span class="label label-info">Near: <?= $nearMatches ?></span>
                            <span class="label label-success">Exact: <?= $exactMatches ?></span>
                            <?= Yii::$app->formatter->asDuration($guessedAt - $game['started_at']) ?> ago.
                        </td>
                    </tr>
                <?php endfor ?>
            <?php endif ?>
            <?php if ($availableGuesses > 0): ?>
                <tr>
                    <td></td>
                    <?php for ($c = 0; $c < $codeSize; $c++): ?>
                        <td class="td-color-picker">
                            <input type="text" class="color-picker" name="color_<?= $c ?>">
                            <?php
                            $colorPickerScript .= "\$('[name=\"color_{$c}\"]').paletteColorPicker({
                                colors: [\"#E05656\",\"#8CDC92\",\"#547CF3\",\"#F7FB6A\",\"#F9BC5C\",\"#A460DA\",\"#93F5F1\",\"#F67EF7\"]
                            });\n";
                            ?>
                        </td>
                    <?php endfor ?>
                    <td>
                        <button id="btnSubmitGuess" class="btn btn-success btn-xs" data-idgame="<?= $game['id'] ?>" data-idplayer="<?= $thisPlayerId ?>" style="display: <?php if ($playerStatus != 'ready') echo 'none' ?>;">Submit Guess</button>
                        <p id="btnSubmitGuessMsg" style="display: <?php if ($playerStatus != 'waitting others') echo 'none' ?>;">Waitting for other players.</p>
                    </td>
                </tr>
            <?php endif ?>
        </table>
    </div>
</div>

<?php
$colorPickerScript = "\$(document).ready(function() {\n" . $colorPickerScript . "});";
$this->registerJs($colorPickerScript, \yii\web\View::POS_READY);
?>


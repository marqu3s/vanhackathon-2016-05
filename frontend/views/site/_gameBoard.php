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

//\yii\helpers\VarDumper::dump($player,10,true);
\yii\helpers\VarDumper::dump($game,10,true);
?>
<h4 class="text-center">You are playing game #<?= $game['id'] ?></h4>

<?php
$maxChancesToGuess = 8; // This can become a parameter to the game model.
 
$availableColors = explode(',', $game['available_colors']);
$code = explode(',', $game['code']);
$codeSize = count($code);
$thisPlayerId = $player['id'];
$colorPickerScript = '';

foreach ($game['matches'] as $idInArray => $data) {
    if ($data['id_player'] == $thisPlayerId) {
        $numGuesses = (int) $data['num_guesses'];
        $availableGuesses = $maxChancesToGuess - $numGuesses;
        break;
    }
}
?>

<table class="table table-bordered">
    <?php if (isset($game['matches'][$idInArray]['history'])): ?>
        <?php for ($i = 0; $i < count($game['matches'][$idInArray]['history']); $i++): ?>
            <tr>
                <?php for ($c = 0; $c < $codeSize; $c++): ?>
                    <td>Guessed color <?= $c ?></td>
                <?php endfor ?>
                <td></td>
            </tr>
        <?php endfor ?>
    <?php endif ?>
    <?php if ($availableGuesses > 0): ?>
        <tr>
            <?php for ($c = 0; $c < $codeSize; $c++): ?>
                <td class="td-color-picker">
                    <input type="text" class="color-picker" name="color_<?= $c ?>">
                    <?php
                    $colorPickerScript .= "\$('[name=\"color_{$c}\"]').paletteColorPicker({
                        colors: [\"#0F8DFC\",\"rgba(135,1,101)\",\"#F00285\",\"hsla(190,41%,95%,1)\"]
                    });";
                    ?>
                </td>
            <?php endfor ?>
            <td>
                <button class="btn btn-success btn-xs">Submit</button>
            </td>
        </tr>
    <?php endif ?>
</table>


<?php
$colorPickerScript = "\$(document).ready(function() {\n" . $colorPickerScript . "});";
$this->registerJs($colorPickerScript, \yii\web\View::POS_READY);
?>


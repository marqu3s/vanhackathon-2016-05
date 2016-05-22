<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 21/05/16
 * Time: 23:38
 */

/** @var $response array */

?>
<?php if (count($response) > 0): ?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">Select a game to join</h2>
            <table class="table table-bordered">
                <tr>
                    <th class="text-center">Game ID</th>
                    <th class="text-center">Created at</th>
                    <th class="text-center">Num. Players</th>
                    <th class="text-center">Join</th>
                </tr>
            <?php foreach ($response as $game): ?>
                <tr>
                    <td class="text-center" style="vertical-align: middle"><?= $game['id'] ?></td>
                    <td class="text-center" style="vertical-align: middle"><?= Yii::$app->formatter->asDatetime($game['created_at']) ?></td>
                    <td class="text-center" style="vertical-align: middle"><?= count($game['players']) ?></td>
                    <td class="text-center">
                        <button class="btn btn-success btn-join-game" data-id="<?= $game['id'] ?>">Join</button>
                    </td>
                </tr>
            <?php endforeach ?>
            </table>
        </div>
    </div>
<?php else: ?>
    <h2 class="text-center">No games available.</h2>
<?php endif ?>

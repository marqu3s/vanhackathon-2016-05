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
//\yii\helpers\VarDumper::dump($response,10); die;
?>
<?php if (isset($response['message'])): ?>
    <h3 class="text-center"><?= $response['message'][0] ?></h3>
<?php else: ?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">Game #<?= $response['id'] ?></h2>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th class="text-center">PLayer Status</th>
                    <th class="text-center">Ready?</th>
                </tr>
            <?php foreach ($response['players'] as $i => $player): ?>
                <tr>
                    <td class="" style="vertical-align: middle"><?= $player['name'] ?></td>
                    <td class="text-center" style="vertical-align: middle"><?= $response['matches'][$i]['player_status'] ?></td>
                    <td class="text-center">
                        <?php if ($player['access_token'] == $_SESSION['token']): ?>
                            <button class="btn btn-success btn-player-ready" data-idGame="<?= $response['id'] ?>" data-idPlayer="<?= $player['id'] ?>">Ready!</button>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </table>
        </div>
    </div>
<?php endif ?>
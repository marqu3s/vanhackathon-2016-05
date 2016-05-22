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
//\yii\helpers\VarDumper::dump($response,10,true); die;
?>
<?php if (isset($response['message'])): ?>
    <h3 class="text-center"><?= $response['message'][0] ?></h3>
<?php else: ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center">Game #<?= $response['id'] ?></h2>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th class="text-center">Player Status</th>
                    <th class="text-center">Action</th>
                </tr>
            <?php foreach ($response['players'] as $i => $player): ?>
                <tr>
                    <td class="" style="vertical-align: middle"><?= $player['name'] ?></td>
                    <td class="text-center" style="vertical-align: middle"><?= $response['matches'][$i]['player_status'] ?></td>
                    <td class="text-center">
                        <?php if ($player['access_token'] == $_SESSION['token']): ?>
                            <?php// if ($response['matches'][$i]['player_status'] != 'ready'): ?>
                                <button class="btn btn-success btn-xs btn-player-ready" data-idgame="<?= $response['id'] ?>" data-idplayer="<?= $player['id'] ?>">Ready!</button>
                            <?php //else: ?>
                                <!--<button class="btn btn-success btn-xs btn-player-leave" data-idgame="--><?php //= $response['id'] ?><!--" data-idplayer="--><?php//= $player['id'] ?><!--">Leave</button>-->
                            <?php //endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </table>
        </div>
    </div>
<?php endif ?>

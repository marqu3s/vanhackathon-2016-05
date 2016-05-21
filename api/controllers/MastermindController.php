<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 20/05/16
 * Time: 22:50
 */

namespace api\controllers;

use yii;
use yii\rest\Controller;

class MastermindController extends Controller
{
    /**
     * http://tools.ietf.org/html/rfc7231
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    public static function returnError($message, $statusCode = 400)
    {
        Yii::$app->response->statusCode = $statusCode;

        return [
            'message' => $message
        ];
    }
}

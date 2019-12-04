<?php
/**
 * Created by PhpStorm.
 * User: igomis
 * Date: 2019-12-04
 * Time: 06:50
 */

namespace Intranet\Entities\Poll;


interface ModelPoll
{
    public static function loadPoll();
    public static function interviewed();
    public static function keyInterviewed();

}
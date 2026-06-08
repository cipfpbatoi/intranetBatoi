<?php
/**
 * Created by PhpStorm.
 * Profesor: igomis
 * Date: 2019-12-04
 * Time: 06:50
 */

namespace Intranet\Entities\Poll;

use Illuminate\Support\Collection;

/**
 * Contracte base per als diferents tipus d'enquesta.
 */
abstract class ModelPoll
{
    public static function loadPoll($votes, ?Poll $poll = null)
    {}
    public static function loadVotes($id)
    {}
    public static function loadGroupVotes($id)
    {}
    public static function interviewed()
    {
        return 'Intranet\\Entities\\Alumno';
    }
    public static function keyInterviewed()
    {
        return 'nia';
    }
    public static function vista()
    {
        return class_basename(static::class);
    }
    public static function aggregate(&$votes, $option1, $option2, ?Poll $poll = null)
    {}

    /**
     * Permet al model descartar respostes que no corresponen al context de la poll.
     */
    public static function filterVotesForPoll(Collection $votes, Poll $poll): Collection
    {
        return $votes;
    }

    public static function has(?Poll $poll = null)
    {
        return true;
    }
}

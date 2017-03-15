<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 14:51
 */

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TeamSynonym
 *
 * @package LVA\Models
 */
class TeamSynonym extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams_synonyms';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['synonym', 'team_id'];

    /**
     * @param string $synonym
     *
     * @return Team|null
     */
    public static function findBySynonym($synonym)
    {
        /** @var TeamSynonym $synonym */
        $synonym = self::where('synonym', $synonym)->first();
        if ($synonym) {
            return $synonym->team;
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSynonym()
    {
        return $this->synonym;
    }

    /**
     * @param string $synonym
     *
     * @return TeamSynonym
     */
    public function setSynonym($synonym)
    {
        $this->synonym = $synonym;

        return $this;
    }

    /**
     * @param string $team
     *
     * @return TeamSynonym
     */
    public function setTeam($team)
    {
        $this->team()->associate(Team::findByName($team));

        return $this;
    }
}
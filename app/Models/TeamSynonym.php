<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 14:51
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TeamSynonym
 *
 * @package App\Models
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
        $synonym = self::where('synonym', $synonym)->first();
        if ($synonym) {
            return $synonym->name;
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
<?php

namespace Lara;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The database columns used by the model.
     * This attributes are mass assignable.
     *
     * @var array
     */
    protected $fillable = array();

    /**
     * Get the corresponding Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getPerson()
    {
        return $this->belongsTo('Lara\Person', 'creator_id', 'id');
    }
    
    /**
     * Get the corresponding Revision_SurveyAnswer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getRevision_SurveyAnswer()
    {
        return $this->hasOne('Lara\Revision_SurveyAnswer');
    }

    /**
     * Get the corresponding RevisionEntries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getRevisionEntries()
    {
        return $this->hasMany('Lara\RevisionEntry');
    }
}

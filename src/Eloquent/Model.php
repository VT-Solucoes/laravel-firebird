<?php

namespace HarryGulliford\Firebird\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{

    /**
     * The sequence for the model.
     *
     * @var string
     */
    protected $sequence = null;

    /**
     * Get sequence name
     *
     * @return string
     */
    protected function getSequence()
    {
        $autoSequence = mb_substr('SEQ' . $this->getTable(), 0, 32);
        return $this->sequence ?: $autoSequence;
    }

    /**
     * Get next sequence value
     *
     * @param string $sequence
     *
     * @return int
     */
    protected function nextSequenceValue($sequence = null)
    {
        return $this->getConnection()->query()->nextSequenceValue($sequence ? $sequence : $this->getSequence());
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $this->nextSequenceValue();
        $keyName = $this->getKeyName();
        $attributes[$keyName] = $id;
        $query->insert($attributes);
        $this->setAttribute($keyName, $id);
    }
}

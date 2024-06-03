<?php

namespace HarryGulliford\Firebird\Query\Processors;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;

class FirebirdProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return array_map(function ($result) {
            return ((object) $result)->column_name;
        }, $results);
    }

    /**
     * Process an "insert get ID" query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $sql
     * @param  array  $values
     * @param  string|null  $sequence
     * @return int
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $result = $query->getConnection()->selectFromWriteConnection($sql, $values)[0];

        $sequence = $sequence ?: 'ID';

        $id = is_object($result) ? $result->{$sequence} : $result[$sequence];

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process an "next sequence value" query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $sql
     * @return int
     */
    public function processNextSequenceValue(Builder $query, $sql)
    {
        $results = $query->getConnection()->selectFromWriteConnection($sql);

        $result = (array)$results[0];
        $id = $result['ID'];

        return is_numeric($id) ? (int)$id : $id;
    }

    /**
     * Process an "get context variable value" query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $sql
     * @return int
     */
    public function processGetContextValue(Builder $query, $sql)
    {
        $result = $query->getConnection()->selectOne($sql);

        return $result['VAL'];
    }

    /**
     * Process an "execute function" query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $sql
     * @param array $values
     *
     * @return mixed
     */
    public function processExecuteFunction(Builder $query, $sql, $values)
    {
        $result = $query->getConnection()->selectOne($sql, $values);

        return $result['VAL'];
    }
}

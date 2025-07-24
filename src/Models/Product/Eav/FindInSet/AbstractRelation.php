<?php

namespace Rapidez\Core\Models\Product\Eav\FindInSet;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

abstract class AbstractRelation extends HasOneOrMany
{
    private $index;
    private $ownerKey;

    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, $index = null)
    {
        $this->index = $index;
        $this->ownerKey = $localKey;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $parentKey = $this->getParentKey();
            if (is_array($parentKey)) {
                $parentKey = implode(',', $parentKey);
            }
            $this->query->whereRaw('FIND_IN_SET(' . $this->foreignKey . ',"' . $parentKey . '")');
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $localKeys = $this->getKeys($models, $this->localKey);
        foreach ($localKeys as $key => $value) {
            if (is_array($value)) {
                $localKeys[$key] = implode(',', $value);
            } else {
                preg_match('/([0-9]+,?)+/', $value ?? '', $matches);
                if (! isset($matches[0]) || $value !== $matches[0]) {
                    unset($localKeys[$key]);
                }
            }
        }

        $this->query->whereRaw('FIND_IN_SET(' . $this->foreignKey . ', "' . implode(',', $localKeys) . '" )');
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @return array
     */
    protected function buildDictionary(Collection $results, $localKey = null)
    {
        $foreign = $this->getForeignKeyName();

        if (is_array($localKey)) {
            $localKeyArr = $localKey;
            $localKey = implode(',', $localKey);
        } else {

            $localKeyArr = explode(',', $localKey ?? '');
        }

        return $results->mapToDictionary(function ($result) use ($foreign, $localKeyArr, $localKey) {

            if (is_null($this->index)) {
                if (in_array($result->{$foreign}, $localKeyArr)) {
                    return [$localKey => $result];
                }
            } else {
                $i = $this->index - 1;
                if ($i > 0 && ! empty($localKeyArr[$i]) && $result->{$foreign} == $localKeyArr[$i]) {
                    return [$localKey => $result];
                }
            }

            return [$this->getDictionaryKey($result->{$foreign}) => $result];
        })->all();
    }

    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $localKey = $model->getAttribute($this->localKey);
            if (is_array($localKey)) {
                $localKey = implode(',', $localKey);
            }
            $dictionary = $this->buildDictionary($results, $localKey);
            if (isset($dictionary[$key = $this->getDictionaryKey($localKey)])) {
                $model->setRelation(
                    $relation,
                    $this->getRelationValue($dictionary, $key, $type)
                );
            }
        }

        return $models;
    }

    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        return $query->select($columns)->whereRaw(
            'FIND_IN_SET(' . $this->getQualifiedForeignKeyName() . ', ' . $this->parent->qualifyColumn($this->ownerKey) . ')'
        );
    }
}

<?php

namespace Application\Repository;


use Application\QueryBuilder\KeywordQueryBuilder;

class KeywordRepository extends AbstractRepository
{
    protected $queryBuilderClass = KeywordQueryBuilder::class;

}
<?php
namespace LosBase\ORM\Tools\Pagination;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;

class Paginator extends DoctrinePaginator
{
    private $countQuery;

    private $count;

    public function __construct(QueryBuilder $query, $fetchJoinCollection = true)
    {
        $countQuery = clone ($query);
        $countQuery = $countQuery->select('count(e) as c')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery();

        $this->countQuery = $countQuery;
        parent::__construct($query, $fetchJoinCollection);
    }

    public function count()
    {
        if ($this->count === null) {
            try {
                $res = $this->countQuery->execute();
                $this->count = $res[0]['c'];
            } catch (NoResultException $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }
}

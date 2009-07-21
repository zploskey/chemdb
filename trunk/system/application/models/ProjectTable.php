<?php

class ProjectTable extends Doctrine_Table
{
    public function getList()
    {
        return $this->createQuery('p')
            ->select('p.id, p.name')
            ->orderBy('p.name DESC')
            ->execute();
    }
}
<?php

/**
 * Contains queries to the Project database table.
 */
class ProjectTable extends Doctrine_Table
{
    /**
     * Gets the ids and names of all project in alphabetical order.
     * @return Doctrine_Collection of Project
     */
    public function getList()
    {
        return $this->createQuery('p')
            ->select('p.id, p.name')
            ->orderBy('p.name ASC')
            ->execute();
    }
}

<?php

class SampleTable extends Doctrine_Table
{
    public function countLike($name)
    {
        return Doctrine_Query::create()
               ->from('Sample s')
               ->select('COUNT(s.id) num_samples')
               ->where('s.name LIKE ?', "%$name%")
               ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    }
}
<?php

/**
 * Represents a row in the sample table in the database.
 */
class Sample extends BaseSample
{
    /**
     * Sets up our join table with Projects.
     * @param void
     */
    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Project', array(
                'refClass' => 'ProjectSample',
                'local' => 'sample_id',
                'foreign' => 'project_id',
            )
        );
    }
}
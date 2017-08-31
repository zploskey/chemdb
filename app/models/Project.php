<?php

/**
 * An object representing a row in the project database table.
 */
class Project extends BaseProject
{
    /**
     * Sets up our join table with Samples.
     * @param void
     */
    public function setUp()
    {
        parent::setUp();
        $this->hasMany(
            'Sample',
            array(
                'refClass' => 'ProjectSample',
                'local'    => 'project_id',
                'foreign'  => 'sample_id',
            )
        );
    }
}

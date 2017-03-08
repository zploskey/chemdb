<?php

class Version31 extends Doctrine_Migration_Base
{
    public function up()
    {
        // Drop all foreign keys
        $this->dropForeignKey('al_ams', 'al_ams_al_ams_std_id_al_ams_std_id');
        $this->dropForeignKey('al_ams', 'al_ams_ams_lab_id_ams_lab_id');
        $this->dropForeignKey('al_ams', 'al_ams_analysis_id_analysis_id');
        $this->dropForeignKey('al_ams_std', 'al_ams_std_al_std_series_id_al_std_series_id');
        $this->dropForeignKey('alcheck_analysis', 'alcheck_analysis_alcheck_batch_id_alcheck_batch_id');
        $this->dropForeignKey('alcheck_analysis', 'alcheck_analysis_sample_id_sample_id');
        $this->dropForeignKey('ams_current', 'ams_current_al_ams_id_al_ams_id');
        $this->dropForeignKey('ams_current', 'ams_current_be_ams_id_be_ams_id');
        $this->dropForeignKey('analysis', 'analysis_batch_id_batch_id');
        $this->dropForeignKey('analysis', 'analysis_sample_id_sample_id');
        $this->dropForeignKey('batch', 'batch_al_carrier_id_al_carrier_id');
        $this->dropForeignKey('be_ams', 'be_ams_ams_lab_id_ams_lab_id');
        $this->dropForeignKey('be_ams', 'be_ams_analysis_id_analysis_id');
        $this->dropForeignKey('be_ams', 'be_ams_be_ams_std_id_be_ams_std_id');
        $this->dropForeignKey('be_ams_std', 'be_ams_std_be_std_series_id_be_std_series_id');
        $this->dropForeignKey('icp_run', 'icp_run_split_id_split_id');
        $this->dropForeignKey('project_sample', 'project_sample_project_id_project_id');
        $this->dropForeignKey('project_sample', 'project_sample_sample_id_sample_id');
        $this->dropForeignKey('sil_cl36_analysis', 'sil_cl36_analysis_sample_id_sample_id');
        $this->dropForeignKey('split', 'split_analysis_id_analysis_id');
        $this->dropForeignKey('split', 'split_split_bkr_id_split_bkr_id');

        // Drop foreign keys not previous in migrations
        $this->dropForeignKey('analysis', 'analysis_diss_bottle_id_diss_bottle_id');
        $this->dropForeignKey('batch', 'batch_be_carrier_id_be_carrier_id');

        $this->changeColumn('al_ams', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('al_ams', 'analysis_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('al_ams', 'al_ams_std_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('al_ams', 'ams_lab_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('al_ams_std', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('al_ams_std', 'al_std_series_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('al_carrier', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('al_std_series', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('alcheck_analysis', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('alcheck_analysis', 'sample_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('alcheck_analysis', 'alcheck_batch_id', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('alcheck_analysis', 'number_within_batch', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('alcheck_analysis', 'flag_bkr_tare_avg', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('alcheck_batch', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('ams_current', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('ams_current', 'be_ams_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('ams_current', 'al_ams_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('ams_lab', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('analysis', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('analysis', 'batch_id', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('analysis', 'sample_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('analysis', 'number_within_batch', 'integer', '8', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('analysis', 'diss_bottle_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('batch', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('batch', 'al_carrier_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('batch', 'be_carrier_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('be_ams', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('be_ams', 'analysis_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('be_ams', 'be_ams_std_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('be_ams', 'ams_lab_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('be_ams_std', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('be_ams_std', 'be_std_series_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('be_carrier', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('be_std_series', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('diss_bottle', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('icp_run', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('icp_run', 'split_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('icp_run', 'run_num', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '1',
             'notnull' => '1',
             ));
        $this->changeColumn('project', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('project_sample', 'project_id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             ));
        $this->changeColumn('project_sample', 'sample_id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             ));
        $this->changeColumn('sample', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('sample', 'antarctic', 'integer', '8', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('sil_cl36_analysis', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('sil_cl36_analysis', 'sample_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('sil_cl36_analysis', 'cl36_batch_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('sil_cl36_analysis', 'calib_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('sil_cl36_analysis', 'cl37_spike_id', 'integer', '8', array(
             'unsigned' => '1',
             ));
        $this->changeColumn('sil_cl36_batch', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('sil_cl36_batch', 'cl_carrier_id', 'integer', '8', array(
             ));
        $this->changeColumn('split', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
             ));
        $this->changeColumn('split', 'analysis_id', 'integer', '8', array(
             'unsigned' => '1',
             'notnull' => '1',
             ));
        $this->changeColumn('split', 'split_bkr_id', 'integer', '8', array(
             'unsigned' => '1',
             'notnull' => '1',
             ));
        $this->changeColumn('split', 'split_num', 'integer', '8', array(
             'unsigned' => '1',
             'default' => '1',
             'notnull' => '1',
             ));
        $this->changeColumn('split_bkr', 'id', 'integer', '8', array(
             'unsigned' => '1',
             'primary' => '1',
             'autoincrement' => '1',
            ));

        // Recreate foreign keys
        $this->createForeignKey('al_ams', 'al_ams_al_ams_std_id_al_ams_std_id', array(
             'name' => 'al_ams_al_ams_std_id_al_ams_std_id',
             'local' => 'al_ams_std_id',
             'foreign' => 'id',
             'foreignTable' => 'al_ams_std',
             ));
        $this->createForeignKey('al_ams', 'al_ams_ams_lab_id_ams_lab_id', array(
             'name' => 'al_ams_ams_lab_id_ams_lab_id',
             'local' => 'ams_lab_id',
             'foreign' => 'id',
             'foreignTable' => 'ams_lab',
             ));
        $this->createForeignKey('al_ams', 'al_ams_analysis_id_analysis_id', array(
             'name' => 'al_ams_analysis_id_analysis_id',
             'local' => 'analysis_id',
             'foreign' => 'id',
             'foreignTable' => 'analysis',
             ));
        $this->createForeignKey('al_ams_std', 'al_ams_std_al_std_series_id_al_std_series_id', array(
             'name' => 'al_ams_std_al_std_series_id_al_std_series_id',
             'local' => 'al_std_series_id',
             'foreign' => 'id',
             'foreignTable' => 'al_std_series',
             ));
        $this->createForeignKey('alcheck_analysis', 'alcheck_analysis_alcheck_batch_id_alcheck_batch_id', array(
             'name' => 'alcheck_analysis_alcheck_batch_id_alcheck_batch_id',
             'local' => 'alcheck_batch_id',
             'foreign' => 'id',
             'foreignTable' => 'alcheck_batch',
             ));
        $this->createForeignKey('alcheck_analysis', 'alcheck_analysis_sample_id_sample_id', array(
             'name' => 'alcheck_analysis_sample_id_sample_id',
             'local' => 'sample_id',
             'foreign' => 'id',
             'foreignTable' => 'sample',
             ));
        $this->createForeignKey('ams_current', 'ams_current_al_ams_id_al_ams_id', array(
             'name' => 'ams_current_al_ams_id_al_ams_id',
             'local' => 'al_ams_id',
             'foreign' => 'id',
             'foreignTable' => 'al_ams',
             ));
        $this->createForeignKey('ams_current', 'ams_current_be_ams_id_be_ams_id', array(
             'name' => 'ams_current_be_ams_id_be_ams_id',
             'local' => 'be_ams_id',
             'foreign' => 'id',
             'foreignTable' => 'be_ams',
             ));
        $this->createForeignKey('analysis', 'analysis_batch_id_batch_id', array(
             'name' => 'analysis_batch_id_batch_id',
             'local' => 'batch_id',
             'foreign' => 'id',
             'foreignTable' => 'batch',
             ));
        $this->createForeignKey('analysis', 'analysis_sample_id_sample_id', array(
             'name' => 'analysis_sample_id_sample_id',
             'local' => 'sample_id',
             'foreign' => 'id',
             'foreignTable' => 'sample',
             ));
        $this->createForeignKey('batch', 'batch_al_carrier_id_al_carrier_id', array(
             'name' => 'batch_al_carrier_id_al_carrier_id',
             'local' => 'al_carrier_id',
             'foreign' => 'id',
             'foreignTable' => 'al_carrier',
             ));
        $this->createForeignKey('be_ams', 'be_ams_ams_lab_id_ams_lab_id', array(
             'name' => 'be_ams_ams_lab_id_ams_lab_id',
             'local' => 'ams_lab_id',
             'foreign' => 'id',
             'foreignTable' => 'ams_lab',
             ));
        $this->createForeignKey('be_ams', 'be_ams_analysis_id_analysis_id', array(
             'name' => 'be_ams_analysis_id_analysis_id',
             'local' => 'analysis_id',
             'foreign' => 'id',
             'foreignTable' => 'analysis',
             ));
        $this->createForeignKey('be_ams', 'be_ams_be_ams_std_id_be_ams_std_id', array(
             'name' => 'be_ams_be_ams_std_id_be_ams_std_id',
             'local' => 'be_ams_std_id',
             'foreign' => 'id',
             'foreignTable' => 'be_ams_std',
             ));
        $this->createForeignKey('be_ams_std', 'be_ams_std_be_std_series_id_be_std_series_id', array(
             'name' => 'be_ams_std_be_std_series_id_be_std_series_id',
             'local' => 'be_std_series_id',
             'foreign' => 'id',
             'foreignTable' => 'be_std_series',
             ));
        $this->createForeignKey('icp_run', 'icp_run_split_id_split_id', array(
             'name' => 'icp_run_split_id_split_id',
             'local' => 'split_id',
             'foreign' => 'id',
             'foreignTable' => 'split',
             ));
        $this->createForeignKey('project_sample', 'project_sample_project_id_project_id', array(
             'name' => 'project_sample_project_id_project_id',
             'local' => 'project_id',
             'foreign' => 'id',
             'foreignTable' => 'project',
             ));
        $this->createForeignKey('project_sample', 'project_sample_sample_id_sample_id', array(
             'name' => 'project_sample_sample_id_sample_id',
             'local' => 'sample_id',
             'foreign' => 'id',
             'foreignTable' => 'sample',
             ));
        $this->createForeignKey('sil_cl36_analysis', 'sil_cl36_analysis_sample_id_sample_id', array(
             'name' => 'sil_cl36_analysis_sample_id_sample_id',
             'local' => 'sample_id',
             'foreign' => 'id',
             'foreignTable' => 'sample',
             ));
        $this->createForeignKey('split', 'split_analysis_id_analysis_id', array(
             'name' => 'split_analysis_id_analysis_id',
             'local' => 'analysis_id',
             'foreign' => 'id',
             'foreignTable' => 'analysis',
             ));
        $this->createForeignKey('split', 'split_split_bkr_id_split_bkr_id', array(
             'name' => 'split_split_bkr_id_split_bkr_id',
             'local' => 'split_bkr_id',
             'foreign' => 'id',
             'foreignTable' => 'split_bkr',
             ));

        //ALTER TABLE analysis ADD CONSTRAINT analysis_diss_bottle_id_diss_bottle_id FOREIGN KEY (diss_bottle_id) REFERENCES diss_bottle(id);
        $this->createForeignKey('analysis', 'analysis_diss_bottle_id_diss_bottle_id', array(
             'name' => 'analysis_diss_bottle_id_diss_bottle_id',
             'local' => 'diss_bottle_id',
             'foreign' => 'id',
             'foreignTable' => 'diss_bottle',
             ));
        //ALTER TABLE batch ADD CONSTRAINT batch_be_carrier_id_be_carrier_id FOREIGN KEY (be_carrier_id) REFERENCES be_carrier(id);
        $this->createForeignKey('batch', 'batch_be_carrier_id_be_carrier_id', array(
             'name' => 'batch_be_carrier_id_be_carrier_id',
             'local' => 'be_carrier_id',
             'foreign' => 'id',
             'foreignTable' => 'be_carrier',
             ));
    }

    public function down()
    {

    }
}

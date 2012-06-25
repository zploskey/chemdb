CREATE TABLE `alcheck_analysis` (
  `analysis_ID` mediumint(8) unsigned zerofill NOT NULL auto_increment,
  `batch_ID` smallint(5) unsigned zerofill NOT NULL default '00000',
  `number_within_batch` tinyint(3) unsigned NOT NULL default '0',
  `sample_name` text NOT NULL,
  `bkr_number` tinytext NOT NULL,
  `wt_bkr_tare` double NOT NULL default '0',
  `flag_bkr_tare_avg` tinyint(3) unsigned NOT NULL default '0',
  `wt_bkr_sample` double NOT NULL default '0',
  `wt_bkr_soln` double default '0',
  `ICP_Al` double default '0',
  `ICP_Ba` double default '0',
  `ICP_Be` double default '0',
  `ICP_Ca` double default '0',
  `ICP_Fe` double default '0',
  `ICP_K` double default '0',
  `ICP_Mg` double default '0',
  `ICP_Mn` double default '0',
  `ICP_Na` double default '0',
  `ICP_Ti` double default '0',
  `addl_dil_factor` double NOT NULL default '1',
  `notes` text,
  PRIMARY KEY  (`analysis_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `alcheck_batch` (
  `batch_ID` smallint(5) unsigned zerofill NOT NULL auto_increment,
  `owner` tinytext,
  `prep_date` date default NULL,
  `ICP_date` date default NULL,
  `description` text,
  `notes` text,
  PRIMARY KEY  (`batch_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO alcheck_analysis SELECT * FROM alchecks.analyses;
INSERT INTO alcheck_batch SELECT * FROM alchecks.batches;

ALTER TABLE `alcheck_analysis` CHANGE `analysis_ID` `id` mediumint(8) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `alcheck_batch` CHANGE `batch_ID` `id` smallint(5) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `alcheck_analysis` CHANGE `batch_id` `alcheck_batch_id` smallint(5) UNSIGNED ZEROFILL NOT NULL DEFAULT '00000' ;

/* Relate to regular analysis */
ALTER TABLE `alcheck_analysis` ADD `analysis_id` int(11) UNSIGNED default NULL;
UPDATE alcheck_analysis,analysis SET alcheck_analysis.analysis_id = analysis.id WHERE analysis.sample_name = alcheck_analysis.sample_name;

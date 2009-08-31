/* First copy over the old database using mysqldump, then do the following commands to modify it to the new format */

CREATE TABLE `splits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `analysis_id` smallint(5) unsigned  NOT NULL,
  `split_bkr_id` int(10) unsigned  NOT NULL,
  `split_num` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `split_bkr_name` tinytext,
  `wt_split_bkr_tare` double NOT NULL default '0',
  `wt_split_bkr_split` double NOT NULL default '0',
  `wt_split_bkr_icp` double NOT NULL default '0',
  `ICP_Al_split1_run1` double default NULL,
  `ICP_Al_split1_run2` double NOT NULL default '0',
  `ICP_Al_split2_run1` double default NULL,
  `ICP_Al_split2_run2` double NOT NULL default '0',
  `ICP_Be_split1_run1` double NOT NULL default '0',
  `ICP_Be_split1_run2` double NOT NULL default '0',
  `ICP_Be_split2_run1` double NOT NULL default '0',
  `ICP_Be_split2_run2` double NOT NULL default '0',
  `use_Be_b1_r1` enum('y','n') NOT NULL default 'y',
  `use_Be_b1_r2` enum('y','n') NOT NULL default 'y',
  `use_Be_b2_r1` enum('y','n') NOT NULL default 'y',
  `use_Be_b2_r2` enum('y','n') NOT NULL default 'y',
  `use_Al_b1_r1` enum('y','n') NOT NULL default 'y',
  `use_Al_b1_r2` enum('y','n') NOT NULL default 'y',
  `use_Al_b2_r1` enum('y','n') NOT NULL default 'y',
  `use_Al_b2_r2` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `icp_runs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `split_id` int(10) unsigned NOT NULL,
  `run_num` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `al_result` double default NULL,
  `be_result` double default NULL,
  `use_al` enum('y','n') NOT NULL default 'y',
  `use_be` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/* transfer beaker 1 data to splits  */
insert into splits 
    (analysis_id, split_bkr_name, wt_split_bkr_tare, wt_split_bkr_split, wt_split_bkr_icp, use_Be_b1_r1, use_Be_b1_r2, use_Al_b1_r1, use_Al_b1_r2, ICP_Al_split1_run1, ICP_Al_split1_run2, ICP_Be_split1_run1, ICP_Be_split1_run2, split_num) 
    select analysis_ID, split_bkr_1_ID, wt_split_bkr_1_tare, wt_split_bkr_1_split, wt_split_bkr_1_ICP, use_Be_b1_r1, use_Be_b1_r2, use_Al_b1_r1, use_Al_b1_r2, ICP_Al_split1_run1, ICP_Al_split1_run2, ICP_Be_split1_run1, ICP_Be_split1_run2, 1 
    from analyses;

/* transfer beaker 2 data to splits */
insert into splits 
    (analysis_id, split_bkr_name, wt_split_bkr_tare, wt_split_bkr_split, wt_split_bkr_icp, use_Be_b2_r1, use_Be_b2_r2, use_Al_b2_r1, use_Al_b2_r2, ICP_Al_split2_run1, ICP_Al_split2_run2, ICP_Be_split2_run1, ICP_Be_split2_run2, split_num) 
    select analysis_ID, split_bkr_2_ID, wt_split_bkr_2_tare, wt_split_bkr_2_split, wt_split_bkr_2_ICP, use_Be_b2_r1, use_Be_b2_r2, use_Al_b2_r1, use_Al_b2_r2, ICP_Al_split2_run1, ICP_Al_split2_run2, ICP_Be_split2_run1, ICP_Be_split2_run2, 2 
    from analyses;

/*set the correct split_bkr_id*/
UPDATE splits,split_bkrs SET splits.split_bkr_id = split_bkrs.arbitrary_ID WHERE splits.split_bkr_name = split_bkrs.bkr_number;

/* transfer beaker 1 run 1 icp_run data */
insert into icp_runs (split_id, run_num, al_result, be_result, use_al, use_be) 
    select id, 1, ICP_Al_split1_run1, ICP_Be_split1_run1, use_Al_b1_r1, use_Be_b1_r1 
    from splits 
    where ICP_Al_split2_run1 IS NULL;

/* transfer beaker 1 run 2 icp_run data */
insert into icp_runs (split_id, run_num, al_result, be_result, use_al, use_be) 
    select id, 2, ICP_Al_split1_run2, ICP_Be_split1_run2, use_Al_b1_r2, use_Be_b1_r2 
    from splits 
    where ICP_Al_split2_run1 IS NULL;

/* b2r1 */
insert into icp_runs (split_id, run_num, al_result, be_result, use_al, use_be) 
    select id, 1, ICP_Al_split2_run1, ICP_Be_split2_run1, use_Al_b2_r1, use_Be_b2_r1 
    from splits 
    where ICP_Al_split1_run1 IS NULL;

/* b2r2 */
insert into icp_runs (split_id, run_num, al_result, be_result, use_al, use_be) 
    select id, 2, ICP_Al_split2_run2, ICP_Be_split2_run2, use_Al_b2_r2, use_Be_b2_r2 
    from splits 
    where ICP_Al_split1_run1 IS NULL;

/* remove temporary and moved fields, also splits.split_bkr_name or make it keyed to split_bkrs */
ALTER TABLE splits DROP use_Be_b1_r1, DROP use_Be_b1_r2, DROP use_Al_b1_r1, DROP use_Al_b1_r2, DROP ICP_Al_split1_run1, DROP ICP_Al_split1_run2, DROP ICP_Be_split1_run1, DROP ICP_Be_split1_run2, DROP use_Be_b2_r1, DROP use_Be_b2_r2, DROP use_Al_b2_r1, DROP use_Al_b2_r2, DROP ICP_Al_split2_run1, DROP ICP_Al_split2_run2, DROP ICP_Be_split2_run1, DROP ICP_Be_split2_run2;

ALTER TABLE analyses DROP use_Be_b1_r1, DROP use_Be_b1_r2, DROP use_Al_b1_r1, DROP use_Al_b1_r2, DROP ICP_Al_split1_run1, DROP ICP_Al_split1_run2, DROP ICP_Be_split1_run1, DROP ICP_Be_split1_run2, DROP use_Be_b2_r1, DROP use_Be_b2_r2, DROP use_Al_b2_r1, DROP use_Al_b2_r2, DROP ICP_Al_split2_run1, DROP ICP_Al_split2_run2, DROP ICP_Be_split2_run1, DROP ICP_Be_split2_run2, DROP split_bkr_1_ID, DROP split_bkr_2_ID, DROP wt_split_bkr_1_tare, DROP wt_split_bkr_2_tare, DROP wt_split_bkr_1_split, DROP wt_split_bkr_2_split, DROP wt_split_bkr_1_ICP, DROP wt_split_bkr_2_ICP;

/* further modifications to the database follow:
 * first some alterations
 */
ALTER TABLE `batches` CHANGE `batch_ID` `id` smallint(5) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `batches` CHANGE `batch_owner` `owner` tinytext DEFAULT NULL ;
ALTER TABLE `batches` CHANGE `batch_desc` `description` text DEFAULT NULL ;
ALTER TABLE `be_carriers` CHANGE `arbitrary_ID` `id` tinyint(4) NOT NULL auto_increment;
ALTER TABLE `be_carriers` CHANGE `Be_carrier_ID` `name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `analyses` CHANGE `analysis_notes` `notes` mediumtext NOT NULL DEFAULT '' ;
ALTER TABLE `diss_bottles` CHANGE `arbitrary_ID` `id` int(11) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `split_bkrs` CHANGE `arbitrary_ID` `id` int(11) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `analyses` CHANGE `analysis_ID` `id` smallint(5) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `analyses` CHANGE `batch_ID` `batch_id` smallint(6) NOT NULL DEFAULT '0' ;
ALTER TABLE `batches` CHANGE `Be_carrier_ID` `Be_carrier_name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `batches` CHANGE `Al_carrier_ID` `Al_carrier_name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `al_carriers` CHANGE `arbitrary_ID` `id` tinyint(4) NOT NULL auto_increment;
ALTER TABLE `al_carriers` CHANGE `Al_carrier_ID` `Al_carrier_name` tinytext DEFAULT NULL ;
ALTER TABLE `batches` CHANGE `batch_notes` `notes` text DEFAULT NULL ;

/* singular table names */
RENAME TABLE `analyses` TO `analysis`;
RENAME TABLE `al_carriers` TO `al_carrier`;
RENAME TABLE `batches` TO `batch`;
RENAME TABLE `be_carriers` TO `be_carrier`;
RENAME TABLE `diss_bottles` TO `diss_bottle`;
RENAME TABLE `icp_runs` TO `icp_run`;
RENAME TABLE `split_bkrs` TO `split_bkr`;
RENAME TABLE `splits` TO `split`;

/* add some things */
ALTER TABLE `batch` ADD `Al_carrier_id` int DEFAULT NULL ;
ALTER TABLE `batch` ADD `Be_carrier_id` int DEFAULT NULL ;

ALTER TABLE `al_carrier` CHANGE `Al_carrier_name` `name` tinytext DEFAULT NULL ;

/* switch from storing names to storing ids of the carriers in batch */
UPDATE batch b, be_carrier bec SET b.Be_carrier_id = bec.id WHERE b.Be_carrier_name = bec.name;
UPDATE batch b, al_carrier alc SET b.Al_carrier_id = alc.id WHERE b.Al_carrier_name = alc.name;

ALTER TABLE `analysis` CHANGE `diss_bottle_ID` `diss_bottle_number` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `analysis` ADD `diss_bottle_id` int(11) UNSIGNED  DEFAULT NULL;
ALTER TABLE `analysis` MODIFY COLUMN `diss_bottle_id` int(11) AFTER `sample_type`;

/* foreign key to sample table */
ALTER TABLE `analysis` ADD `sample_id` int unsigned DEFAULT NULL  ;

UPDATE analysis a, diss_bottle SET a.diss_bottle_id = diss_bottle.id WHERE a.diss_bottle_number = diss_bottle.bottle_number;

ALTER TABLE `batch` CHANGE `Al_carrier_id` `al_carrier_id` int(11) DEFAULT NULL;
ALTER TABLE `batch` CHANGE `Be_carrier_id` `be_carrier_id` int(11) DEFAULT NULL;

ALTER TABLE `batch` CHANGE `wt_Al_carrier_init` `wt_al_carrier_init` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Al_carrier_final` `wt_al_carrier_final` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Be_carrier_init` `wt_be_carrier_init` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Be_carrier_final` `wt_be_carrier_final` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` MODIFY COLUMN `al_carrier_id` int(11) DEFAULT NULL AFTER `id`;
ALTER TABLE `batch` MODIFY COLUMN `be_carrier_id` int(11) DEFAULT NULL AFTER `al_carrier_id`;

ALTER TABLE `icp_run` MODIFY COLUMN `al_result` double NOT NULL DEFAULT '0';
ALTER TABLE `icp_run` MODIFY COLUMN `be_result` double NOT NULL DEFAULT '0';
ALTER TABLE `icp_run` MODIFY COLUMN `split_id` int(10) UNSIGNED DEFAULT NULL;

/* needs some sample and projects tables */
CREATE TABLE `sample` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) UNIQUE NOT NULL,
  `latitude` double default NULL,
  `longitude` double default NULL,
  `altitude` double default NULL,
  `shield_factor` double default NULL,
  `depth_top` double default NULL,
  `depth_bottom` double default NULL,
  `density` double default NULL,
  `erosion_rate` float default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) UNIQUE NOT NULL,
  `date_added` datetime default NULL,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

/* also a linking table */
CREATE TABLE `project_sample` (
  `project_id` int(11) unsigned NOT NULL default '0',
  `sample_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`project_id`,`sample_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

/* Copy over samples that aren't blanks and have names into the samples table */
INSERT INTO sample (name) 
    SELECT DISTINCT sample_name
    FROM analysis a
    WHERE UPPER(a.sample_type) != 'BLANK' AND a.sample_name != '';
/* Now key those analyses to the sample */
UPDATE sample s, analysis a SET a.sample_id = s.id WHERE a.sample_name = s.name;

/**
 * Now we work on Alchecks
 */
 CREATE TABLE `alcheck_batch` (
     `batch_ID` smallint(5) unsigned NOT NULL auto_increment,
     `owner` tinytext,
     `prep_date` date default NULL,
     `ICP_date` date default NULL,
     `description` text,
     `notes` text,   
     PRIMARY KEY  (`batch_ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 
CREATE TABLE `alcheck_analysis` (
    `analysis_ID` mediumint(8) unsigned NOT NULL auto_increment,
    `batch_ID` smallint(5) unsigned NOT NULL default '0',
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

INSERT INTO alcheck_batch SELECT * FROM alchecks.batches;
INSERT INTO alcheck_analysis SELECT * FROM alchecks.analyses;

ALTER TABLE `alcheck_analysis` CHANGE `analysis_ID` `id` mediumint(8) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `alcheck_batch` CHANGE `batch_ID` `id` smallint(5) UNSIGNED  NOT NULL auto_increment;
ALTER TABLE `alcheck_analysis` CHANGE `batch_id` `alcheck_batch_id` smallint(5) UNSIGNED  NOT NULL DEFAULT '00000';
ALTER TABLE `alcheck_batch` CHANGE `ICP_date` `icp_date` date DEFAULT NULL;

/* Relate to samples */
ALTER TABLE `alcheck_analysis` ADD `sample_id` int(11) UNSIGNED default NULL AFTER `id`;
UPDATE alcheck_analysis a, sample s SET a.sample_id = s.id WHERE a.sample_name = s.name;
/* sample names should be unique */
ALTER TABLE `sample` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT '' UNIQUE;
ALTER TABLE `sample` ADD `antarctic` bool NOT NULL DEFAULT '0' AFTER `altitude`;

/* create ams measurements */
CREATE TABLE `be_ams` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `analysis_id` int(10) unsigned default NULL,
  `be_ams_std_id` int(10) unsigned default NULL,
  `date` date default NULL,
  `ams_sample_name` varchar(160) default NULL,
  `caams_num` varchar(60) default NULL,
  `r_to_rstd` double default NULL,
  `interror` double default NULL,
  `exterror` double default NULL,
  `truefrac` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `be_calc_code` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(60) default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `be_ams_std` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `be_calc_code_id` int(10) unsigned default NULL,
  `r10to9` double default NULL,
  `error` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `ams_current` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `current` double default NULL,
  `be_ams_id` int(11) unsigned default NULL,
  `al_ams_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

/* initial sketch of Cl36 analysis and batch tables */
CREATE TABLE `sil_cl36_analysis` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sample_id` int(10) unsigned default NULL,
  `sample_name` varchar(255) NOT NULL default '',
  `cl36_batch_id` int(10) unsigned default NULL,
  `calib_id` int(10) unsigned default NULL,
  `cl37_spike_id` int(10) unsigned default NULL,
  `sample_type` enum('SAMPLE','CALIB','BLANK') NOT NULL default 'SAMPLE',
  `wt_spike` double default NULL,
  `wt_bkr_tare` double default NULL,
  `wt_bkr_sample` double default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `sil_cl36_batch` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `owner` varchar(60) default NULL,
  `cl_carrier_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

/* add indexes for speed! */
ALTER TABLE `analysis` ADD INDEX `sample_idx` (`sample_id`);
ALTER TABLE `analysis` ADD INDEX `batch_idx` (`batch_id`);
ALTER TABLE `analysis` ADD INDEX `diss_bottle_idx` (`diss_bottle_id`);

ALTER TABLE `batch` ADD INDEX `al_carrier_idx` (`al_carrier_id`);
ALTER TABLE `batch` ADD INDEX `be_carrier_idx` (`be_carrier_id`);

ALTER TABLE `split` ADD INDEX `analysis_idx` (`analysis_id`);
ALTER TABLE `split` ADD INDEX `split_bkr_idx` (`split_bkr_id`);

ALTER TABLE `alcheck_analysis` ADD INDEX `sample_idx` (`sample_id`);
ALTER TABLE `alcheck_analysis` ADD INDEX `alcheck_batch_idx` (`alcheck_batch_id`);
ALTER TABLE `icp_run` ADD INDEX `split_idx` (`split_id`);

CREATE TABLE `al_ams` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `analysis_id` int(10) unsigned default NULL,
  `al_ams_std_id` int(10) unsigned default NULL,
  `date` date default NULL,
  `ams_sample_name` varchar(160) default NULL,
  `caams_num` varchar(60) default NULL,
  `r_to_rstd` double default NULL,
  `interror` double default NULL,
  `exterror` double default NULL,
  `truefrac` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

CREATE TABLE `al_ams_std` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `al_std_series_id` int(10) unsigned default NULL,
  `r26to27` double default NULL,
  `error` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1

CREATE TABLE `al_std_series` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(60) default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1

CREATE TABLE `be_ams` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `analysis_id` int(10) unsigned default NULL,
  `be_ams_std_id` int(10) unsigned default NULL,
  `date` date default NULL,
  `ams_sample_name` varchar(160) default NULL,
  `caams_num` varchar(60) default NULL,
  `r_to_rstd` double default NULL,
  `interror` double default NULL,
  `exterror` double default NULL,
  `truefrac` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1

CREATE TABLE `be_ams_std` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `be_std_series_id` int(10) unsigned default NULL,
  `r10to9` double default NULL,
  `error` double default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1

CREATE TABLE `be_std_series` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(60) default NULL,
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1

ALTER TABLE `analysis` CHANGE `batch_id` smallint(6) unsigned NOT NULL default '0';

/* Fix the ordering of split beaker IDs so that AB1 - AB80 are continuous. */
UPDATE split_bkr sb SET sb.id = sb.id - 50 WHERE 70 < sb.id AND sb.id <= 80;
UPDATE split s SET s.split_bkr_id = s.split_bkr_id - 50 WHERE 70 < s.split_bkr_id AND s.split_bkr_id <= 80;

/* TODO: Finally, remove fields that are now redundant */
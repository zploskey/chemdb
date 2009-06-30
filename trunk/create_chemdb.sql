/* First copy over the old database using mysqldump, then do the following commands to modify it to the new format */

CREATE TABLE `splits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `analysis_id` smallint(5) unsigned zerofill NOT NULL,
  `split_bkr_id` int(10) unsigned zerofill NOT NULL,
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
  `al_result` double default NULL,
  `be_result` double default NULL,
  `use_al` enum('y','n') NOT NULL default 'y',
  `use_be` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/* transfer beaker 1 data to splits  */
insert into splits (analysis_id, split_bkr_name, wt_split_bkr_tare, wt_split_bkr_split, wt_split_bkr_icp, use_Be_b1_r1, use_Be_b1_r2, use_Al_b1_r1, use_Al_b1_r2, ICP_Al_split1_run1, ICP_Al_split1_run2, ICP_Be_split1_run1, ICP_Be_split1_run2) select analysis_ID, split_bkr_1_ID, wt_split_bkr_1_tare, wt_split_bkr_1_split, wt_split_bkr_1_ICP, use_Be_b1_r1, use_Be_b1_r2, use_Al_b1_r1, use_Al_b1_r2, ICP_Al_split1_run1, ICP_Al_split1_run2, ICP_Be_split1_run1, ICP_Be_split1_run2 from analyses;

/* transfer beaker 2 data to splits */
insert into splits (analysis_id, split_bkr_name, wt_split_bkr_tare, wt_split_bkr_split, wt_split_bkr_icp, use_Be_b2_r1, use_Be_b2_r2, use_Al_b2_r1, use_Al_b2_r2, ICP_Al_split2_run1, ICP_Al_split2_run2, ICP_Be_split2_run1, ICP_Be_split2_run2) select analysis_ID, split_bkr_2_ID, wt_split_bkr_2_tare, wt_split_bkr_2_split, wt_split_bkr_2_ICP, use_Be_b2_r1, use_Be_b2_r2, use_Al_b2_r1, use_Al_b2_r2, ICP_Al_split2_run1, ICP_Al_split2_run2, ICP_Be_split2_run1, ICP_Be_split2_run2 from analyses;

/*set the correct split_bkr_id*/
UPDATE splits,split_bkrs SET splits.split_bkr_id = split_bkrs.arbitrary_ID WHERE splits.split_bkr_name = split_bkrs.bkr_number;

/* transfer beaker 1 run 1 icp_run data */
insert into icp_runs (split_id, al_result, be_result, use_al, use_be) select id, ICP_Al_split1_run1, ICP_Be_split1_run1, use_Al_b1_r1, use_Be_b1_r1 from splits where ICP_Al_split2_run1 IS NULL;

/* transfer beaker 1 run 2 icp_run data */
insert into icp_runs (split_id, al_result, be_result, use_al, use_be) select id, ICP_Al_split1_run2, ICP_Be_split1_run2, use_Al_b1_r2, use_Be_b1_r2 from splits where ICP_Al_split2_run1 IS NULL;

/* b2r1 */
insert into icp_runs (split_id, al_result, be_result, use_al, use_be) select id, ICP_Al_split2_run1, ICP_Be_split2_run1, use_Al_b2_r1, use_Be_b2_r1 from splits where ICP_Al_split1_run1 IS NULL;

/* b2r2 */
insert into icp_runs (split_id, al_result, be_result, use_al, use_be) select id, ICP_Al_split2_run2, ICP_Be_split2_run2, use_Al_b2_r2, use_Be_b2_r2 from splits where ICP_Al_split1_run1 IS NULL;

/* remove temporary and moved fields, also splits.split_bkr_name or make it keyed to split_bkrs */
ALTER TABLE splits DROP use_Be_b1_r1, DROP use_Be_b1_r2, DROP use_Al_b1_r1, DROP use_Al_b1_r2, DROP ICP_Al_split1_run1, DROP ICP_Al_split1_run2, DROP ICP_Be_split1_run1, DROP ICP_Be_split1_run2, DROP use_Be_b2_r1, DROP use_Be_b2_r2, DROP use_Al_b2_r1, DROP use_Al_b2_r2, DROP ICP_Al_split2_run1, DROP ICP_Al_split2_run2, DROP ICP_Be_split2_run1, DROP ICP_Be_split2_run2;

ALTER TABLE analyses DROP use_Be_b1_r1, DROP use_Be_b1_r2, DROP use_Al_b1_r1, DROP use_Al_b1_r2, DROP ICP_Al_split1_run1, DROP ICP_Al_split1_run2, DROP ICP_Be_split1_run1, DROP ICP_Be_split1_run2, DROP use_Be_b2_r1, DROP use_Be_b2_r2, DROP use_Al_b2_r1, DROP use_Al_b2_r2, DROP ICP_Al_split2_run1, DROP ICP_Al_split2_run2, DROP ICP_Be_split2_run1, DROP ICP_Be_split2_run2, DROP split_bkr_1_ID, DROP split_bkr_2_ID, DROP wt_split_bkr_1_tare, DROP wt_split_bkr_2_tare, DROP wt_split_bkr_1_split, DROP wt_split_bkr_2_split, DROP wt_split_bkr_1_ICP, DROP wt_split_bkr_2_ICP;

/* further modifications to the database follow:
 * first some alterations
 */
ALTER TABLE `batches` CHANGE `batch_ID` `id` smallint(5) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `batches` CHANGE `batch_owner` `owner` tinytext DEFAULT NULL ;
ALTER TABLE `batches` CHANGE `batch_desc` `description` text DEFAULT NULL ;
ALTER TABLE `be_carriers` CHANGE `arbitrary_ID` `id` tinyint(4) NOT NULL auto_increment;
ALTER TABLE `be_carriers` CHANGE `Be_carrier_ID` `name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `analyses` CHANGE `analysis_notes` `notes` mediumtext NOT NULL DEFAULT '' ;
ALTER TABLE `diss_bottles` CHANGE `arbitrary_ID` `id` int(11) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `split_bkrs` CHANGE `arbitrary_ID` `id` int(11) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `analyses` CHANGE `analysis_ID` `id` smallint(5) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `analyses` CHANGE `batch_ID` `batch_id` smallint(6) NOT NULL DEFAULT '0' ;
ALTER TABLE `batches` CHANGE `Be_carrier_ID` `Be_carrier_name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `batches` CHANGE `Al_carrier_ID` `Al_carrier_name` tinytext NOT NULL DEFAULT '' ;
ALTER TABLE `al_carriers` CHANGE `arbitrary_ID` `id` tinyint(4) NOT NULL auto_increment;
ALTER TABLE `al_carriers` CHANGE `Al_carrier_ID` `Al_carrier_name` tinytext DEFAULT NULL ;
ALTER TABLE `batch` CHANGE `batch_notes` `notes` text DEFAULT NULL ;

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
ALTER TABLE `analysis` ADD `diss_bottle_id` int(11) UNSIGNED ZEROFILL DEFAULT NULL;
ALTER TABLE `analysis` MODIFY COLUMN `diss_bottle_id` int(11) AFTER `sample_type`;

/* foreign key to sample table */
ALTER TABLE `analysis` ADD `sample_id` int unsigned DEFAULT NULL;

UPDATE analysis a, diss_bottle SET a.diss_bottle_id = diss_bottle.id WHERE a.diss_bottle_number = diss_bottle.bottle_number;

ALTER TABLE `batch` CHANGE `Al_carrier_id` `al_carrier_id` int(11) DEFAULT NULL;
ALTER TABLE `batch` CHANGE `Be_carrier_id` `be_carrier_id` int(11) DEFAULT NULL;

ALTER TABLE `batch` CHANGE `wt_Al_carrier_init` `wt_al_carrier_init` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Al_carrier_final` `wt_al_carrier_final` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Be_carrier_init` `wt_be_carrier_init` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` CHANGE `wt_Be_carrier_final` `wt_be_carrier_final` double NOT NULL DEFAULT '0';
ALTER TABLE `batch` MODIFY COLUMN `al_carrier_id` int(11) DEFAULT NULL AFTER `id`;
ALTER TABLE `batch` MODIFY COLUMN `be_carrier_id` int(11) DEFAULT NULL AFTER `al_carrier_id`;

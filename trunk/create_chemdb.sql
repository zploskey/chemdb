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

/*turn off foreign key checks so we can copy in data without MySQL complaining*/
/* set FOREIGN_KEY_CHECKS = 0; */

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

/* further modifications to the database follow */
ALTER TABLE `batches` CHANGE `batch_ID` `id` smallint(5) UNSIGNED ZEROFILL NOT NULL auto_increment;
ALTER TABLE `batches` CHANGE `batch_owner` `owner` tinytext DEFAULT NULL ;
ALTER TABLE `batches` CHANGE `batch_desc` `description` text DEFAULT NULL ;
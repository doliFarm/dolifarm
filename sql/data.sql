
TRUNCATE TABLE `llx_dolifarm_dictionary`;
INSERT INTO `llx_dolifarm_dictionary` ( `label`, `code`,  `active`, `module`, `position`) VALUES
('Farm','DF_FARM',1,'dolifarm',10);

-- Plot organic status definitions
INSERT INTO `llx_dolifarm_dictionary` ( `label`, `code`,  `active`, `module`, `position`) VALUES
('Certified','DF_PLSTAT',1,'dolifarm',10),
('Conversion','DF_PLSTAT',1,'dolifarm',10),
('Conventional','DF_PLSTAT',1,'dolifarm',10),
('Uknown','DF_PLSTAT',1,'dolifarm',10);



-- Eliminazione impostazione non utilizzata
DELETE FROM `zz_settings` WHERE `zz_settings`.`nome` = 'Modifica Viste di default';

UPDATE `zz_settings` SET `sezione` = 'Backup' WHERE `zz_settings`.`nome` = 'Numero di backup da mantenere';
UPDATE `zz_settings` SET `sezione` = 'Backup' WHERE `zz_settings`.`nome` = 'Backup automatico';

UPDATE `zz_settings` SET `sezione` = 'Aggiornamenti' WHERE `zz_settings`.`nome` = 'Attiva aggiornamenti';
UPDATE `zz_settings` SET `sezione` = 'API' WHERE `zz_settings`.`nome` = 'apilayer API key for VAT number';
UPDATE `zz_settings` SET `sezione` = 'API' WHERE `zz_settings`.`nome` = 'Google Maps API key';

-- Abilita la possibilità di ripristinare backup da archivi esterni al gestionale
INSERT INTO `zz_settings` (`id`, `nome`, `valore`, `tipo`, `editable`, `sezione`, `created_at`, `updated_at`, `order`, `help`) VALUES (NULL, 'Permetti il ripristino di backup da file esterni', '1', 'boolean', '0', 'Backup', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 'Abilita la possibilità di ripristinare backup da archivi esterni al gestionale.');


UPDATE `zz_settings` SET `help` = 'Esegue automaticamente un backup completo del gestionale al primo accesso della giornata.' WHERE `zz_settings`.`nome` = 'Backup automatico';

-- Aggiungo come completatato lo stato "Accettato" e "Parzialmente evaso" dell'ordine
UPDATE `or_statiordine` SET `completato` = '1' WHERE `or_statiordine`.`descrizione` = 'Accettato' OR `or_statiordine`.`descrizione` = 'Parzialmente evaso';

-- Aumento dimensione campo qta
ALTER TABLE `co_righe_preventivi` CHANGE `qta` `qta` DECIMAL(14,6) NOT NULL;
ALTER TABLE `co_righe_contratti` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `co_righe_documenti` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `dt_righe_ddt` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `mg_articoli` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `mg_movimenti` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `or_righe_ordini` CHANGE `qta` `qta` decimal(14, 6) NOT NULL;
ALTER TABLE `or_righe_ordini` CHANGE `qta_evasa` `qta_evasa` decimal(14, 6) NOT NULL;
ALTER TABLE `dt_righe_ddt` CHANGE `qta_evasa` `qta_evasa` decimal(14, 6) NOT NULL;
ALTER TABLE `co_righe_preventivi` CHANGE `qta_evasa` `qta_evasa` decimal(14, 6) NOT NULL;
ALTER TABLE `co_righe_documenti` CHANGE `qta_evasa` `qta_evasa` decimal(14, 6) NOT NULL;
ALTER TABLE `co_righe_contratti` CHANGE `qta_evasa` `qta_evasa` decimal(14, 6) NOT NULL;
ALTER TABLE `mg_articoli` CHANGE `threshold_qta` `threshold_qta` decimal(14, 6) NOT NULL;

INSERT INTO `zz_prints` (`id`, `id_module`, `is_record`, `name`, `title`, `filename`, `directory`, `previous`, `options`, `icon`, `version`, `compatibility`, `order`, `predefined`, `default`, `enabled`) VALUES
(NULL, (SELECT id FROM zz_modules WHERE `name`='Preventivi'), 1, 'Preventivo (solo totale)', 'Preventivo (solo totale)', 'Preventivo num. {numero} del {data}', 'preventivi', 'idpreventivo', '{\"pricing\":false, \"show_only_total\":true}', 'fa fa-print', '', '', 0, 0, 1, 1);

-- Unificazione righe e articoli interventi
ALTER TABLE `in_righe_interventi` ADD `abilita_serial` boolean NOT NULL DEFAULT '0' AFTER `um`;
ALTER TABLE `in_righe_interventi` ADD `idimpianto` int(11);
ALTER TABLE `in_righe_interventi` ADD `old_id` int(11);

INSERT INTO `in_righe_interventi` (`old_id`, `idarticolo`, `idintervento`, `is_descrizione`, `is_sconto`, `descrizione`, `prezzo_acquisto`, `prezzo_vendita`, `sconto`, `sconto_unitario`, `tipo_sconto`, `idiva`, `desc_iva`, `iva`, `qta`, `um`, `abilita_serial`, `created_at`, `updated_at`, `idimpianto`) SELECT `id`, `idarticolo`, `idintervento`, `is_descrizione`, `is_sconto`, `descrizione`, `prezzo_acquisto`, `prezzo_vendita`, `sconto`, `sconto_unitario`, `tipo_sconto`, `idiva`, `desc_iva`, `iva`, `qta`, `um`, `abilita_serial`, `created_at`, `updated_at`, `idimpianto` FROM `mg_articoli_interventi`;

UPDATE `mg_prodotti` SET `id_riga_intervento` = (SELECT `id` FROM `in_righe_interventi` WHERE `in_righe_interventi`.`old_id` = `id_riga_intervento`) WHERE `id_riga_intervento` IS NOT NULL;
ALTER TABLE `in_righe_interventi` DROP `old_id`;

-- ALTER TABLE `in_righe_interventi` CHANGE `prezzo_acquisto` `prezzo_unitario_acquisto` decimal(12,6),
--    CHANGE `prezzo_vendita` `prezzo_unitario_vendita` decimal(12,6);
-- TODO su tutte le tabelle

<?php

include_once __DIR__.'/../../core.php';

$id_azienda = $dbo->fetchArray("SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='Azienda'")[0]['idtipoanagrafica'];
$id_cliente = $dbo->fetchArray("SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='Cliente'")[0]['idtipoanagrafica'];
$id_fornitore = $dbo->fetchArray("SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='Fornitore'")[0]['idtipoanagrafica'];
$id_tecnico = $dbo->fetchArray("SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='Tecnico'")[0]['idtipoanagrafica'];

if (isset($id_record)) {
    $records = $dbo->fetchArray('SELECT *, (SELECT GROUP_CONCAT(an_tipianagrafiche.idtipoanagrafica) FROM an_tipianagrafiche INNER JOIN an_tipianagrafiche_anagrafiche ON an_tipianagrafiche.idtipoanagrafica=an_tipianagrafiche_anagrafiche.idtipoanagrafica WHERE idanagrafica=an_anagrafiche.idanagrafica) AS idtipianagrafica, (SELECT GROUP_CONCAT(idagente) FROM an_anagrafiche_agenti WHERE idanagrafica=an_anagrafiche.idanagrafica) AS idagenti, (SELECT GROUP_CONCAT(descrizione) FROM an_tipianagrafiche INNER JOIN an_tipianagrafiche_anagrafiche ON an_tipianagrafiche.idtipoanagrafica=an_tipianagrafiche_anagrafiche.idtipoanagrafica WHERE idanagrafica=an_anagrafiche.idanagrafica) AS tipianagrafica FROM an_anagrafiche GROUP BY idanagrafica HAVING idanagrafica='.prepare($id_record).' '.Modules::getAdditionalsQuery($id_module));

    // Cast per latitudine e longitudine
    if (!empty($records)) {
        $records[0]['lat'] = floatval($records[0]['lat']);
        $records[0]['lng'] = floatval($records[0]['lng']);
    }

    $tipi_anagrafica = $dbo->fetchArray('SELECT an_tipianagrafiche.idtipoanagrafica FROM an_tipianagrafiche INNER JOIN an_tipianagrafiche_anagrafiche ON an_tipianagrafiche.idtipoanagrafica=an_tipianagrafiche_anagrafiche.idtipoanagrafica WHERE idanagrafica='.prepare($id_record));
    $tipi_anagrafica = array_column($tipi_anagrafica, 'idtipoanagrafica');
}

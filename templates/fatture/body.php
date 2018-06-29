<?php

include_once __DIR__.'/../../core.php';

include_once DOCROOT.'/modules/fatture/modutil.php';

$report_name = 'fattura_'.$numero.'.pdf';

$autofill = [
    'count' => 0, // Conteggio delle righe
    'words' => 70, // Numero di parolo dopo cui contare una riga nuova
    'rows' => $fattura_accompagnatoria ? 15 : 20, // Numero di righe massimo presente nella pagina
    'additional' => $fattura_accompagnatoria ? 10 : 15, // Numero di righe massimo da aggiungere
    'columns' => 5, // Numero di colonne della tabella
];

$v_iva = [];
$v_totale = [];

$sconto = [];
$imponibile = [];
$iva = [];

// Intestazione tabella per righe
echo "
<table class='table table-striped table-bordered' id='contents'>
    <thead>
        <tr>
            <th class='text-center' style='width:50%'>".tr('Descrizione', [], ['upper' => true])."</th>
            <th class='text-center' style='width:14%'>".tr('Q.tà', [], ['upper' => true])."</th>
            <th class='text-center' style='width:16%'>".tr('Prezzo unitario', [], ['upper' => true])."</th>
            <th class='text-center' style='width:20%'>".tr('Importo', [], ['upper' => true])."</th>
            <th class='text-center' style='width:10%'>".tr('IVA', [], ['upper' => true]).' (%)</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <td style="border-top:none; border-bottom:1px solid #aaa;"></td>
            <td style="border-top:none; border-bottom:1px solid #aaa;"></td>
            <td style="border-top:none; border-bottom:1px solid #aaa;"></td>
            <td style="border-top:none; border-bottom:1px solid #aaa;"></td>
            <td style="border-top:none; border-bottom:1px solid #aaa;"></td>
        </tr>
    </tfoot>

    <tbody>';

// RIGHE FATTURA CON ORDINAMENTO UNICO
$righe = $dbo->fetchArray("SELECT *,
    IFNULL((SELECT `codice` FROM `mg_articoli` WHERE `id` = `co_righe_documenti`.`idarticolo`), '') AS codice_articolo,
    (SELECT GROUP_CONCAT(`serial` SEPARATOR ', ') FROM `mg_prodotti` WHERE `id_riga_documento` = `co_righe_documenti`.`id`) AS seriali,
    (SELECT `percentuale` FROM `co_iva` WHERE `id` = `co_righe_documenti`.`idiva`) AS perc_iva
FROM `co_righe_documenti` WHERE `iddocumento` = ".prepare($id_record).' ORDER BY `order`');
foreach ($righe as $r) {
    $count = 0;
    $count += ceil(strlen($r['descrizione']) / $autofill['words']);
    $count += substr_count($r['descrizione'], PHP_EOL);

    echo '
        <tr>
            <td>
                '.nl2br($r['descrizione']);

    // Codice articolo
    if (!empty($r['codice_articolo'])) {
        echo '
                <br><small>'.tr('COD. _COD_', [
                    '_COD_' => $r['codice_articolo'],
                ]).'</small>';

        if ($count <= 1) {
            $count += 0.4;
        }
    }

    // Seriali
    if (!empty($r['seriali'])) {
        echo '
                <br><small>'.tr('SN').': '.$r['seriali'].'</small>';

        if ($count <= 1) {
            $count += 0.4;
        }
    }

    // Aggiunta dei riferimenti ai documenti
    $ref = doc_references($r, $records[0]['dir'], ['iddocumento']);

    if (!empty($ref)) {
        echo '
                <br><small>'.$ref['description'].'</small>';
        if ($count <= 1) {
            $count += 0.4;
        }
    }

    echo '
            </td>';

    echo '
            <td class="text-center">';
    if (empty($r['is_descrizione'])) {
        echo '
                '.Translator::numberToLocale($r['qta'], 'qta').' '.$r['um'];
    }
    echo '
            </td>';

    // Prezzo unitario
    echo "
            <td class='text-right'>";
    if (empty($r['is_descrizione'])) {
        echo '
                '.(empty($r['qta']) || empty($r['subtotale']) ? '' : Translator::numberToLocale($r['subtotale'] / $r['qta'])).' &euro;';

        if ($r['sconto'] > 0) {
            echo "
                <br><small class='text-muted'>- ".tr('sconto _TOT_ _TYPE_', [
                    '_TOT_' => Translator::numberToLocale($r['sconto_unitario']),
                    '_TYPE_' => ($r['tipo_sconto'] == 'PRC' ? '%' : '&euro;'),
                ]).'</small>';

            if ($count <= 1) {
                $count += 0.4;
            }
        }
    }

    echo '
            </td>';

    // Imponibile
    echo "
            <td class='text-right'>";
    if (empty($r['is_descrizione'])) {
        echo '
                '.(empty($r['subtotale']) ? '' : Translator::numberToLocale($r['subtotale'] - $r['sconto'])).' &euro;';

        if ($r['sconto'] > 0) {
            echo "
                <br><small class='text-muted'>".tr('sconto di _TOT_ _TYPE_', [
                    '_TOT_' => Translator::numberToLocale($r['sconto']),
                    '_TYPE_' => '&euro;',
                ]).'</small>';

            if ($count <= 1) {
                $count += 0.4;
            }
        }
    }
    echo '
            </td>';

    // Iva
    echo '
            <td class="text-center">';
    if (empty($r['is_descrizione'])) {
        echo '
                '.Translator::numberToLocale($r['perc_iva']);
    }
    echo '
            </td>
        </tr>';

    $autofill['count'] += $count;

    $imponibile[] = $r['subtotale'];
    $iva[] = $r['iva'];
    $sconto[] = $r['sconto'];

    $v_iva[$r['desc_iva']] = sum($v_iva[$r['desc_iva']], $r['iva']);
    $v_totale[$r['desc_iva']] = sum($v_totale[$r['desc_iva']], [
        $r['subtotale'], -$r['sconto'],
    ]);
}

echo '
        |autofill|
    </tbody>
</table>';

// Aggiungo diciture particolari per l'anagrafica cliente
$dicitura = $dbo->fetchArray('SELECT diciturafissafattura FROM an_anagrafiche WHERE idanagrafica = '.prepare($id_cliente));

if (!empty($dicitura[0]['diciturafissafattura'])) {
    $testo = $dicitura[0]['diciturafissafattura'];

    echo "
<p class='text-center'>
<b>".nl2br($testo).'</b>
</p>';
}

// Aggiungo diciture per condizioni iva particolari
foreach ($v_iva as $key => $value) {
    $dicitura = $dbo->fetchArray('SELECT dicitura FROM co_iva WHERE descrizione = '.prepare($key));

    if (!empty($dicitura[0]['dicitura'])) {
        $testo = $dicitura[0]['dicitura'];

        echo "
<p class='text-center'>
    <b>".nl2br($testo).'</b>
</p>';
    }
}
echo '
<table class="table">';
echo '
    <tr>';
if (abs($records[0]['bollo']) > 0) {
    echo '
        <td width="85%">';
} else {
    echo '
        <td width="100%">';
}
    if (!empty($records[0]['note'])) {
        echo '
            <p class="small-bold">'.tr('Note', [], ['upper' => true]).':</p>
            <p>'.nl2br($records[0]['note']).'</p>';
    }
    echo '
        </td>';
if (abs($records[0]['bollo']) > 0) {
    echo '
        <td width="15%" align="right">';
}
if (abs($records[0]['bollo']) > 0) {
    echo '
            <table style="width: 20mm; font-size: 50%; text-align: center" class="table-bordered">
                <tr>
                    <td style="height: 20mm;">
                        <br><br>
                        '.tr('Spazio per applicazione marca da bollo', [], ['upper' => true]).'
                    </td>
                </tr>
            </table>';
}
if (abs($records[0]['bollo']) > 0) {
    echo '
        </td>';
}

echo '
    </tr>';
echo '
</table>';

// Info per il footer
$imponibile = sum($imponibile);
$iva = sum($iva, 0) + $records[0]['iva_rivalsainps'];
$sconto = sum($sconto);

$totale = $imponibile + $iva - $sconto + $records[0]['rivalsainps'];

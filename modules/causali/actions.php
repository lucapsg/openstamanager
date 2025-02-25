<?php
/*
 * OpenSTAManager: il software gestionale open source per l'assistenza tecnica e la fatturazione
 * Copyright (C) DevCode s.r.l.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

include_once __DIR__.'/../../core.php';

switch (filter('op')) {
    case 'update':
        $descrizione = filter('descrizione');

        if (isset($descrizione)) {
            if ($dbo->fetchNum('SELECT * FROM `dt_causalet` WHERE `descrizione`='.prepare($descrizione).' AND `id`!='.prepare($id_record)) == 0) {
                $predefined = post('predefined');
                if (!empty($predefined)) {
                    $dbo->query('UPDATE dt_causalet SET predefined = 0');
                }

                $dbo->update('dt_causalet', [
                    'descrizione' => $descrizione,
                    'is_importabile' => filter('is_importabile'),
                    'reversed' => filter('reversed'),
                    'predefined' => $predefined,
                ], ['id' => $id_record]);

                flash()->info(tr('Salvataggio completato!'));
            } else {
                flash()->error(tr("E' già presente una tipologia di _TYPE_ con la stessa descrizione", [
                    '_TYPE_' => 'causale',
                ]));
            }
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio'));
        }

        break;

    case 'add':
        $descrizione = filter('descrizione');

        if (isset($descrizione)) {
            if ($dbo->fetchNum('SELECT * FROM `dt_causalet` WHERE `descrizione`='.prepare($descrizione)) == 0) {
                $dbo->insert('dt_causalet', [
                    'descrizione' => $descrizione,
                    'is_importabile' => 1,
                ]);
                $id_record = $dbo->lastInsertedID();

                if (isAjaxRequest()) {
                    echo json_encode(['id' => $id_record, 'text' => $descrizione]);
                }

                flash()->info(tr('Aggiunta nuova tipologia di _TYPE_', [
                    '_TYPE_' => 'causale',
                ]));
            } else {
                flash()->error(tr("E' già presente una tipologia di _TYPE_ con la stessa descrizione", [
                    '_TYPE_' => 'causale',
                ]));
            }
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio'));
        }

        break;

    case 'delete':
        $documenti = $dbo->fetchNum('SELECT id FROM dt_ddt WHERE idcausalet='.prepare($id_record).'
                     UNION SELECT id FROM co_documenti WHERE idcausalet='.prepare($id_record));

        if (isset($id_record) && empty($documenti)) {
            $dbo->query('DELETE FROM `dt_causalet` WHERE `id`='.prepare($id_record));
        } else {
            $dbo->update('dt_causalet', [
                'deleted_at' => date('Y-m-d H:i:s'),
            ], ['id' => $id_record]);
        }

        flash()->info(tr('Tipologia di _TYPE_ eliminata con successo.', [
            '_TYPE_' => 'causale',
        ]));

        break;
}

<?php

?><form action="" method="post">
	<input type="hidden" name="op" value="update">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="id_record" value="<?php echo $id_record; ?>">

	<div class="row">
		<div class="col-md-4">
			{[ "type": "span", "label": "<?php echo tr('Codice'); ?>", "name": "codice", "value": "$codice$" ]}
		</div>

		<div class="col-md-6">
			{[ "type": "text", "label": "<?php echo tr('Descrizione'); ?>", "name": "descrizione", "required": 1, "value": "$descrizione$" ]}
		</div>

		<div class="col-md-2">
			{[ "type": "number", "label": "<?php echo tr('Tempo standard'); ?>", "name": "tempo_standard", "help": "<?php echo tr('Valore compreso tra 0,25 - 24 ore. <br><small>Esempi: <em><ul><li>60 minuti = 1 ora</li><li>30 minuti = 0,5 ore</li><li>15 minuti = 0,25 ore</li></ul></em></small> Suggerisce il tempo solitamente impiegato per questa tipologia di attivita'); ?>.", "maxlength": 5, "min-value": "0", "max-value": "24", "class": "text-center", "value": "$tempo_standard$", "icon-after": "ore"  ]}
		</div>

	</div>

	<div class="card card-primary">
		<div class="card-heading">
			<h3 class="card-title"><?php echo tr('Addebiti unitari al cliente'); ?></h3>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Addebito orario'); ?>", "name": "costo_orario", "required": 1, "value": "$costo_orario$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Addebito km'); ?>", "name": "costo_km", "required": 1, "value": "$costo_km$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Addebito diritto ch.'); ?>", "name": "costo_diritto_chiamata", "required": 1, "value": "$costo_diritto_chiamata$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>
			</div>
		</div>
	</div>


	<div class="card card-primary">
		<div class="card-heading">
			<h3 class="card-title"><?php echo tr('Costi unitari del tecnico'); ?></h3>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Costo orario'); ?>", "name": "costo_orario_tecnico", "required": 1, "value": "$costo_orario_tecnico$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Costo km'); ?>", "name": "costo_km_tecnico", "required_tecnico": 1, "value": "$costo_km_tecnico$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Costo diritto ch.'); ?>", "name": "costo_diritto_chiamata_tecnico", "required": 1, "value": "$costo_diritto_chiamata_tecnico$", "icon-after": "<i class='fa fa-euro'></i>" ]}
				</div>
			</div>
		</div>
	</div>
</form>

<?php

$interventi = $dbo->fetchArray('SELECT COUNT(*) AS tot_interventi FROM in_interventi WHERE id_tipo_intervento='.prepare($id_record));

$tot_interventi = $interventi[0]['tot_interventi'];
if ($tot_interventi > 0) {
    echo '
    <div class="alert alert-danger">
        '.tr('Ci sono _NUM_ interventi collegati', [
            '_NUM_' => $tot_interventi,
        ]).'.
    </div>';
} else {
    echo '
<a href="#" class="btn btn-danger ask" data-backto="record-list">
    <i class="fa fa-trash"></i> '.tr('Elimina').'
</a>';
}

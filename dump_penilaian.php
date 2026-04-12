<?php
$data = \App\Models\PenilaianSikap::all();
foreach ($data as $item) {
    echo $item->id . " - " . $item->sikap . " - " . $item->keterangan . "\n";
}

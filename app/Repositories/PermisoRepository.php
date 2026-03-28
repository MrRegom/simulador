<?php
namespace App\Repositories;

class PermisoRepository extends BaseRepository {
    protected $table = 'permisos';

    public function getAllGroupedByModulo() {
        $todos = $this->all();
        $agrupados = [];
        foreach ($todos as $p) {
            $agrupados[$p['modulo']][] = $p;
        }
        return $agrupados;
    }
}

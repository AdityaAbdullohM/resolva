<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class KelasMataPelajaran extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelas_mata_pelajaran';

    /**
     * Get the guru for this pivot record.
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the mata pelajaran for this pivot record.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}

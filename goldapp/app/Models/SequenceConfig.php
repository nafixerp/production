<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SequenceConfig extends Model
{
    protected $fillable = [
        'module','prefix','suffix','current_seq','reset_cycle','format','branch_specific',
    ];

    public function previewNext(): string
    {
        $seq = $this->current_seq + 1;
        $now = Carbon::now();
        $result = $this->format;
        $result = str_replace('{PREFIX}', $this->prefix, $result);
        $result = str_replace('{SUFFIX}', $this->suffix ?? '', $result);
        $result = str_replace('{YY}', $now->format('y'), $result);
        $result = str_replace('{YYYY}', $now->format('Y'), $result);
        $result = str_replace('{MM}', $now->format('m'), $result);
        $result = str_replace('{DD}', $now->format('d'), $result);
        $result = str_replace('{SEQ4}', str_pad($seq, 4, '0', STR_PAD_LEFT), $result);
        $result = str_replace('{SEQ6}', str_pad($seq, 6, '0', STR_PAD_LEFT), $result);
        $result = str_replace('{SEQ}', $seq, $result);
        return $result;
    }
}

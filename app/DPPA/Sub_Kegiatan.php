<?php

namespace App\Dppa;

use App\PerjalananDinas;
use App\RekeningPengajuan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sub_Kegiatan extends Model
{
    protected $table = 'dppa_sub_kegiatan';
    
    protected $fillable = ['nama','jumlah_anggaran','kegiatan_id','kode_rekening','uraian'];

    protected $appends = ['dana_tersedia','editable'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::addGlobalScope('tahun_anggaran_scope',function (Builder $builder){
            $builder->whereHas('kegiatan');
        });
    }

    public function getEditableAttribute()
    {
        return $this->jumlah_anggaran == $this->dana_tersedia;
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class,'kegiatan_id');
    }

    public function uraian()
    {
        return $this->belongsTo(Uraian_Sub_Kegiatan::class, 'uraian_id');
    }

    public function perjalanan_dinas()
    {
        return $this->hasMany(PerjalananDinas::class, 'sub_kegiatan_id');
    }

    public function getDanaTersediaAttribute()
    {
        $dana_tersedia = $this->jumlah_anggaran;
        foreach(RekeningPengajuan::where('sub_kegiatan_id', $this->id)->get() as $rp){
            $dana_tersedia = $dana_tersedia - $rp->jumlah;
        }
        return $dana_tersedia;
    }
}

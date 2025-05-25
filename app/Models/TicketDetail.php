<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'certificate_id',
        'movie_title',
        'movie_date',
        'movie_time',
        'movie_format',
        'hall_number',
        'row_number',
        'seat_number',
        'cinema_name',
        'cinema_address',
        'cinema_notes',
        'ticket_price',
        'booking_number',
        'is_refundable',
        'age_restriction',
        'requires_id',
    ];

    /**
     * Атрибуты, которые нужно приводить к определённому типу.
     *
     * @var array
     */
    protected $casts = [
        'movie_date' => 'date',
        'is_refundable' => 'boolean',
        'requires_id' => 'boolean',
        'ticket_price' => 'decimal:2',
    ];

    /**
     * Получить сертификат/документ, к которому относится этот билет.
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Форматирует дату фильма для отображения
     *
     * @return string
     */
    public function getFormattedMovieDateAttribute()
    {
        return $this->movie_date->format('d.m.Y');
    }

    /**
     * Получить полное название кинотеатра с адресом
     *
     * @return string
     */
    public function getFullCinemaInfoAttribute()
    {
        $cinemaInfo = $this->cinema_name;
        
        if (!empty($this->cinema_address)) {
            $cinemaInfo .= ', ' . $this->cinema_address;
        }
        
        return $cinemaInfo;
    }

    /**
     * Преобразовать билет в данные для шаблона
     *
     * @return array
     */
    public function toTemplateData()
    {
        return [
            'movie_title' => $this->movie_title,
            'movie_date' => $this->formatted_movie_date,
            'movie_time' => $this->movie_time,
            'movie_format' => $this->movie_format,
            'hall_number' => $this->hall_number,
            'row_number' => $this->row_number,
            'seat_number' => $this->seat_number,
            'cinema_address' => $this->full_cinema_info,
            'cinema_notes' => $this->cinema_notes,
            'ticket_price' => $this->ticket_price,
        ];
    }
}

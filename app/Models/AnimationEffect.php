<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimationEffect extends Model
{
    use HasFactory;
    
    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'particles',
        'direction',
        'speed',
        'color',
        'size_min',
        'size_max',
        'quantity',
        'sort_order',
        'is_active',
    ];

    /**
     * Атрибуты, которые должны быть приведены к нативным типам.
     *
     * @var array
     */
    protected $casts = [
        'particles' => 'array',
        'is_active' => 'boolean',
        'size_min' => 'integer',
        'size_max' => 'integer',
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Значения по умолчанию для атрибутов модели.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];
    
    /**
     * Получить default-эффекты, если в базе нет никаких эффектов
     */
    public static function getDefaultEffects()
    {
        return [
            [
                'id' => 1,
                'name' => 'Конфетти',
                'slug' => 'confetti',
                'description' => 'Красочные частицы конфетти',
                'type' => 'confetti',
                'particles' => ['🎉', '🎊', '✨', '🎁', '💫'],
                'direction' => 'center',
                'speed' => 'normal',
            ],
            [
                'id' => 2,
                'name' => 'Фейерверк',
                'slug' => 'fireworks',
                'description' => 'Яркие вспышки фейерверка',
                'type' => 'fireworks',
                'particles' => ['💥', '🎆', '✨'],
                'direction' => 'center',
                'speed' => 'fast',
            ],
            [
                'id' => 3,
                'name' => 'Снег',
                'slug' => 'snow',
                'description' => 'Падающие снежинки',
                'type' => 'snow',
                'particles' => ['❄️', '❄', '❅', '❆'],
                'direction' => 'bottom',
                'speed' => 'slow',
            ]
        ];
    }
}

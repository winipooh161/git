<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Классический',
                'description' => 'Элегантный дизайн с золотой рамкой и классическим шрифтом',
                'image' => 'templates/classic.jpg',
                'html_template' => '<div class="certificate classic-template">
                    <div class="certificate-header">
                        <h1>{{ $company_name }}</h1>
                    </div>
                    <div class="certificate-content">
                        <h2>Подарочный документ</h2>
                        <p>Выдан: {{ $recipient_name }}</p>
                        <p>Сумма: {{ $amount }} руб.</p>
                        <p>{!! $message !!}</p>
                    </div>
                    <div class="certificate-footer">
                        <p>Действителен до: {{ $valid_until }}</p>
                        <p>документ №: {{ $certificate_number }}</p>
                    </div>
                </div>',
                'fields' => json_encode([
                    'company_name' => ['label' => 'Название компании', 'required' => true],
                    'recipient_name' => ['label' => 'Имя получателя', 'required' => true],
                    'amount' => ['label' => 'Сумма', 'required' => true],
                    'message' => ['label' => 'Сообщение', 'required' => false],
                    'valid_until' => ['label' => 'Действителен до', 'required' => true],
                ]),
                'is_premium' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Современный',
                'description' => 'Минималистичный дизайн с чистыми линиями и современной типографикой',
                'image' => 'templates/modern.jpg',
                'html_template' => '<div class="certificate modern-template">
                    <div class="certificate-header">
                        <h1>{{ $company_name }}</h1>
                    </div>
                    <div class="certificate-content">
                        <h2>Подарочный документ</h2>
                        <p>Выдан: {{ $recipient_name }}</p>
                        <p>Сумма: {{ $amount }} руб.</p>
                        <p>{!! $message !!}</p>
                    </div>
                    <div class="certificate-footer">
                        <p>Действителен до: {{ $valid_until }}</p>
                        <p>документ №: {{ $certificate_number }}</p>
                    </div>
                </div>',
                'fields' => json_encode([
                    'company_name' => ['label' => 'Название компании', 'required' => true],
                    'recipient_name' => ['label' => 'Имя получателя', 'required' => true],
                    'amount' => ['label' => 'Сумма', 'required' => true],
                    'message' => ['label' => 'Сообщение', 'required' => false],
                    'valid_until' => ['label' => 'Действителен до', 'required' => true],
                ]),
                'is_premium' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Праздничный',
                'description' => 'Яркий дизайн для особых случаев и праздничных предложений',
                'image' => 'templates/holiday.jpg',
                'html_template' => '<div class="certificate holiday-template">
                    <div class="certificate-header">
                        <h1>{{ $company_name }}</h1>
                    </div>
                    <div class="certificate-content">
                        <h2>Праздничный документ</h2>
                        <p>Выдан: {{ $recipient_name }}</p>
                        <p>Сумма: {{ $amount }} руб.</p>
                        <p>{!! $message !!}</p>
                    </div>
                    <div class="certificate-footer">
                        <p>Действителен до: {{ $valid_until }}</p>
                        <p>документ №: {{ $certificate_number }}</p>
                    </div>
                </div>',
                'fields' => json_encode([
                    'company_name' => ['label' => 'Название компании', 'required' => true],
                    'recipient_name' => ['label' => 'Имя получателя', 'required' => true],
                    'amount' => ['label' => 'Сумма', 'required' => true],
                    'message' => ['label' => 'Сообщение', 'required' => false],
                    'valid_until' => ['label' => 'Действителен до', 'required' => true],
                ]),
                'is_premium' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            CertificateTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}

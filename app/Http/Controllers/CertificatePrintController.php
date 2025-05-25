<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CertificatePrintController extends Controller
{
    /**
     * Генерирует печатную версию документа для публичного доступа
     */
    public function printPublic(Request $request, Certificate $certificate)
    {
        // Получаем формат и ориентацию из параметров запроса или используем значения по умолчанию
        $format = $request->input('format', 'a4');
        $orientation = $request->input('orientation', 'landscape');
        
        // Валидация входных данных
        if (!in_array($format, ['a4', 'a5', 'a6', 'letter', 'legal'])) {
            $format = 'a4';
        }
        
        if (!in_array($orientation, ['portrait', 'landscape'])) {
            $orientation = 'landscape';
        }
        
        try {
            // Создание HTML-содержимого для печатной версии
            $printHtml = $this->preparePrintContent($certificate, $format, $orientation);
            
            // Генерация имени файла
            $fileName = "certificate-{$certificate->certificate_number}-{$format}.pdf";
            
            // Настройка параметров PDF
            $pdf = PDF::loadHTML($printHtml)
                ->setPaper($format, $orientation)
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif'
                ]);
            
            // Отправка файла на скачивание
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Ошибка при создании PDF: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при создании PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Подготовка HTML-содержимого для печати
     */
    private function preparePrintContent(Certificate $certificate, $format, $orientation)
    {
        // Получаем шаблон документа
        $template = $certificate->template;
        $templatePath = public_path($template->template_path);
        
        if (!file_exists($templatePath)) {
            abort(404, 'Файл шаблона не найден');
        }

        // Подготавливаем данные для передачи в Blade-шаблон
        $viewData = [
            'recipient_name' => $certificate->recipient_name,
            'amount' => number_format($certificate->amount, 0, '.', ' ') . ' ₽',
            'valid_from' => $certificate->valid_from->format('d.m.Y'),
            'valid_until' => $certificate->valid_until->format('d.m.Y'),
            'message' => $certificate->message ?? '',
            'certificate_number' => $certificate->certificate_number,
            'company_name' => $certificate->user->company ?? config('app.name'),
        ];
        
        // Обработка логотипа компании
        if ($certificate->company_logo === 'none') {
            // Если логотип не нужен
            $viewData['company_logo'] = '';
            $viewData['show_logo'] = false;
        } elseif ($certificate->company_logo) {
            $viewData['company_logo'] = asset('storage/' . $certificate->company_logo);
            $viewData['show_logo'] = true;
        } elseif ($certificate->user && $certificate->user->company_logo) {
            $viewData['company_logo'] = asset('storage/' . $certificate->user->company_logo);
            $viewData['show_logo'] = true;
        } else {
            $viewData['company_logo'] = asset('images/default-logo.png');
            $viewData['show_logo'] = true;
        }
        
        // Используем Blade для рендеринга шаблона
        $html = view()->file($templatePath, $viewData)->render();

        // Добавляем стили для печати с адаптивным масштабированием
        $html .= $this->getPrintStyles($format, $orientation);
        
        // Оборачиваем содержимое в контейнер для масштабирования
        $html = $this->wrapContentInScalingContainer($html);
        
        return $html;
    }

    /**
     * Оборачивает содержимое в масштабируемый контейнер
     */
    private function wrapContentInScalingContainer($html)
    {
        // Ищем тег body и его содержимое
        if (preg_match('/<body[^>]*>(.*)<\/body>/si', $html, $matches)) {
            $bodyContent = $matches[1];
            
            // Оборачиваем содержимое в масштабируемый контейнер
            $wrappedContent = '<div class="certificate-scaling-container">' . $bodyContent . '</div>';
            
            // Заменяем оригинальное содержимое body
            $html = str_replace($bodyContent, $wrappedContent, $html);
        }
        
        return $html;
    }
    
    /**
     * Получение CSS стилей для печати с адаптацией под разные форматы
     */
    private function getPrintStyles($format, $orientation)
    {
        // Основные стили для всех форматов
        $commonStyles = <<<HTML
        <style>
            @page {
                size: {$format} {$orientation};
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                width: 100%;
                height: 100%;
                position: relative;
                overflow: hidden;
            }
            .no-print, .print-button, .admin-qr-code, .admin-qr-toggle {
                display: none !important;
            }
            .certificate-scaling-container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
                transform-origin: center center;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
            }
            /* Для всех внутренних элементов, чтобы избежать обрезки */
            .certificate-scaling-container * {
                max-width: 100%;
                box-sizing: border-box;
            }
        </style>
        HTML;
        
        // Добавляем специфические стили в зависимости от формата и ориентации
        $formatSpecificStyles = '';
        
        // Для формата A4
        if ($format === 'a4') {
            if ($orientation === 'landscape') {
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A4 Landscape: 297mm × 210mm */
                    .certificate-scaling-container {
                        transform: scale(0.98);
                    }
                </style>
                HTML;
            } else { // portrait
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A4 Portrait: 210mm × 297mm */
                    .certificate-scaling-container {
                        transform: scale(0.85);
                    }
                </style>
                HTML;
            }
        }
        
        // Для формата A5 - УЛУЧШЕННЫЕ СТИЛИ
        else if ($format === 'a5') {
            if ($orientation === 'landscape') {
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A5 Landscape: 210mm × 148mm */
                    html, body {
                        width: 100%;
                        height: 100%;
                        overflow: hidden;
                    }
                    .certificate-scaling-container {
                        transform: scale(0.65);
                        transform-origin: center center;
                    }
                    /* Гарантируем, что контент документа помещается целиком */
                    .certificate-content, .certificate-inner, .certificate-body {
                        width: 100%;
                        max-width: 100%;
                        max-height: 100%;
                    }
                </style>
                HTML;
            } else { // portrait
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A5 Portrait: 148mm × 210mm */
                    html, body {
                        width: 100%;
                        height: 100%;
                        overflow: hidden;
                    }
                    .certificate-scaling-container {
                        transform: scale(0.55);
                        transform-origin: center center;
                    }
                    /* Гарантируем, что контент документа помещается целиком */
                    .certificate-content, .certificate-inner, .certificate-body {
                        width: 100%;
                        max-width: 100%;
                        max-height: 100%;
                    }
                </style>
                HTML;
            }
        }
        
        // Для формата A6 - УЛУЧШЕННЫЕ СТИЛИ
        else if ($format === 'a6') {
            if ($orientation === 'landscape') {
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A6 Landscape: 148mm × 105mm */
                    html, body {
                        width: 100%;
                        height: 100%;
                        overflow: hidden;
                    }
                    .certificate-scaling-container {
                        transform: scale(0.45);
                        transform-origin: center center;
                    }
                    /* Гарантируем, что контент документа помещается целиком */
                    .certificate-content, .certificate-inner, .certificate-body {
                        width: 100%;
                        max-width: 100%;
                        max-height: 100%;
                    }
                </style>
                HTML;
            } else { // portrait
                $formatSpecificStyles = <<<HTML
                <style>
                    /* A6 Portrait: 105mm × 148mm */
                    html, body {
                        width: 100%;
                        height: 100%;
                        overflow: hidden;
                    }
                    .certificate-scaling-container {
                        transform: scale(0.38);
                        transform-origin: center center;
                    }
                    /* Гарантируем, что контент документа помещается целиком */
                    .certificate-content, .certificate-inner, .certificate-body {
                        width: 100%;
                        max-width: 100%;
                        max-height: 100%;
                    }
                </style>
                HTML;
            }
        }
        
        // Для других стандартных форматов
        else if ($format === 'letter') {
            if ($orientation === 'landscape') {
                $formatSpecificStyles = <<<HTML
                <style>
                    /* Letter Landscape: 279mm × 216mm */
                    .certificate-scaling-container {
                        transform: scale(0.95);
                    }
                </style>
                HTML;
            } else {
                $formatSpecificStyles = <<<HTML
                <style>
                    /* Letter Portrait: 216mm × 279mm */
                    .certificate-scaling-container {
                        transform: scale(0.80);
                        transform-origin: top center;
                    }
                </style>
                HTML;
            }
        }
        
        // Добавляем настройки для печати
        $printMediaStyles = <<<HTML
        <style>
            @media print {
                html, body {
                    width: 100%;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    overflow: hidden;
                }
                
                /* Предотвращаем разрыв страницы внутри документа */
                .certificate-scaling-container {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
                
                /* Дополнительные стили для исправления масштабирования при печати */
                @page {
                    size: {$format} {$orientation};
                    margin: 0;
                }
            }
        </style>
        HTML;
        
        return $commonStyles . $formatSpecificStyles . $printMediaStyles;
    }
}

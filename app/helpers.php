<?php

/**
 * Безопасно форматирует дату
 * 
 * @param mixed $date Дата для форматирования (строка, Carbon или null)
 * @param string $format Формат даты
 * @return string Отформатированная дата или пустая строка
 */
if (!function_exists('format_date_safe')) {
    function format_date_safe($date, $format = 'd.m.Y') {
        if (is_null($date)) {
            return '';
        }
        
        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }
        
        return (string) $date;
    }
}

/**
 * Безопасно форматирует дату, учитывая различные форматы входных данных
 * 
 * @param mixed $date Дата для форматирования (Carbon, string, null)
 * @param string $format Формат даты (по умолчанию d.m.Y)
 * @return string Отформатированная дата или пустая строка для null
 */
if (!function_exists('format_date')) {
    function format_date($date, $format = 'd.m.Y') {
        // Для null возвращаем пустую строку
        if (is_null($date)) {
            return '';
        }
        
        // Если это уже строка, пытаемся преобразовать в Carbon
        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return $date; // Возвращаем исходную строку, если преобразование не удалось
            }
        }
        
        // Если успешно получили объект Carbon, форматируем его
        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }
        
        // В крайнем случае просто преобразуем в строку
        return (string) $date;
    }
}

if (!function_exists('safe_carbon_date')) {
    /**
     * Безопасно преобразует значение в Carbon объект
     *
     * @param mixed $date Дата (строка или Carbon)
     * @return \Carbon\Carbon|null Объект Carbon или null
     */
    function safe_carbon_date($date)
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date;
        }
        
        if (is_string($date) && !empty($date)) {
            try {
                return \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
}

if (!function_exists('diff_days_format')) {
    /**
     * Возвращает разницу в днях между двумя датами с правильным склонением
     *
     * @param mixed $date Дата (строка или Carbon)
     * @param string $format Формат возврата: 'days' - только число, 'word' - только слово, 'full' - и число и слово
     * @return mixed Разница в днях в выбранном формате
     */
    function diff_days_format($date, $format = 'days')
    {
        $carbon = safe_carbon_date($date);
        if (!$carbon) {
            return $format === 'word' ? 'дней' : ($format === 'full' ? '0 дней' : 0);
        }
        
        $days = $carbon->diffInDays(now());
        
        // Определяем правильное склонение
        $word = 'дней';
        if ($days % 10 === 1 && $days % 100 !== 11) {
            $word = 'день';
        } elseif (in_array($days % 10, [2, 3, 4]) && !in_array($days % 100, [12, 13, 14])) {
            $word = 'дня';
        }
        
        // Возвращаем результат в нужном формате
        if ($format === 'days') {
            return $days;
        } elseif ($format === 'word') {
            return $word;
        } else {
            return "{$days} {$word}";
        }
    }
}

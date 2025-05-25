<?php

namespace App\Http\Controllers;

use App\Models\TelegramChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\TelegramBotService;
use App\Mail\TemplateOrderMail;

class TelegramBotController extends Controller
{
    protected $telegramService;
    protected $token = "7861225545:AAGMlMVmNh-QkrtLVj8H3qpUsiDLp3XAj-I";

    // FAQ вопросы и ответы
    protected $faqItems = [
        'faq_1' => [
            'question' => 'Что такое документы?',
            'answer' => 'документы - это документы, подтверждающие ваши достижения, квалификацию или участие в определенных мероприятиях. Наш сервис позволяет быстро создавать и управлять электронными документами.'
        ],
        'faq_2' => [
            'question' => 'Как создать документ?',
            'answer' => 'Чтобы создать документ, зарегистрируйтесь на нашем сайте, выберите подходящий шаблон, введите необходимые данные и сгенерируйте готовый документ. Это займет всего несколько минут!'
        ],
        'faq_3' => [
            'question' => 'Сколько стоят услуги?',
            'answer' => 'У нас есть как бесплатные, так и премиум варианты шаблонов. Базовая функциональность доступна бесплатно, а расширенные возможности доступны по подписке. Подробнее о ценах можно узнать на нашем сайте в разделе "Тарифы".'
        ],
        'faq_4' => [
            'question' => 'Как проверить подлинность документа?',
            'answer' => 'Каждый документ имеет уникальный QR-код или ID, который можно проверить на нашем сайте в разделе "Проверка документов". Просто отсканируйте QR-код или введите ID документа.'
        ],
        'faq_5' => [
            'question' => 'Можно ли заказать индивидуальный шаблон?',
            'answer' => 'Да, мы создаем индивидуальные шаблоны под ваши потребности. Для заказа нажмите "Заказать свой шаблон" и заполните форму заказа.'
        ],
    ];

    // Шаги заказа шаблона
    protected $orderSteps = [
        1 => ['field' => 'name', 'question' => 'Как вас зовут? (ФИО или название организации)'],
        2 => ['field' => 'email', 'question' => 'Укажите ваш email для связи:'],
        3 => ['field' => 'phone', 'question' => 'Укажите ваш номер телефона для связи:'],
        4 => ['field' => 'description', 'question' => 'Опишите, как должен выглядеть ваш шаблон документа:'],
        5 => ['field' => 'purpose', 'question' => 'Для какой цели вам нужен документ? (например, обучение, мероприятие, награждение)'],
        6 => ['field' => 'deadline', 'question' => 'Укажите желаемые сроки выполнения заказа:']
    ];

    // ID группового чата для уведомлений
    protected $groupChatId = -4816418916;

    public function __construct()
    {
        $this->telegramService = new TelegramBotService($this->token);
    }

    /**
     * Обработка входящего вебхука от Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = json_decode($request->getContent(), true);
            Log::info('Telegram webhook received', ['update' => $update]);

            // Определяем тип обновления (сообщение или callback_query)
            if (isset($update['message'])) {
                return $this->handleMessage($update['message']);
            } elseif (isset($update['callback_query'])) {
                return $this->handleCallbackQuery($update['callback_query']);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Обработка входящих сообщений
     */
    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        // Получаем или создаем запись о чате
        $chat = TelegramChat::firstOrCreate(['chat_id' => $chatId]);

        // Обработка команд
        if (strpos($text, '/') === 0) {
            return $this->handleCommand($text, $chatId, $chat);
        }

        // Обработка шагов заказа шаблона, если пользователь находится в состоянии заказа
        if ($chat->state == 'ordering') {
            return $this->handleOrderStep($chatId, $chat, $text);
        }

        // Если это обычное сообщение "привет"
        if (mb_strtolower($text) === 'привет') {
            Log::info('Отправляем ответ на сообщение "Привет"', ['chat_id' => $chatId]);
            $this->telegramService->sendMessage($chatId, 'Привет! Я бот для управления документами. Используйте меню для навигации по функциям.');
            $this->sendMainMenu($chatId);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Обработка команд
     */
    private function handleCommand($command, $chatId, $chat)
    {
        switch (strtolower($command)) {
            case '/start':
                $this->telegramService->sendMessage($chatId, 
                    "Добро пожаловать! Я бот для управления документами.\n" .
                    "Используйте команды:\n" .
                    "- /faq - Часто задаваемые вопросы\n" .
                    "- /order - Заказать индивидуальный шаблон\n" .
                    "- /menu - Показать главное меню"
                );
                $this->sendMainMenu($chatId);
                break;

            case '/faq':
                $this->sendFaqMenu($chatId);
                break;

            case '/order':
                $this->startOrderProcess($chatId, $chat);
                break;

            case '/menu':
                $this->sendMainMenu($chatId);
                break;

            default:
                $this->telegramService->sendMessage($chatId, "Неизвестная команда. Используйте /menu для просмотра доступных функций.");
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Обработка нажатий на инлайн-кнопки
     */
    private function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];

        // Получаем запись о чате
        $chat = TelegramChat::firstOrCreate(['chat_id' => $chatId]);

        // Обработка нажатий на кнопки FAQ
        if (strpos($data, 'faq_') === 0) {
            $this->showFaqAnswer($chatId, $data);
        }
        // Обработка кнопки "Заказать шаблон"
        elseif ($data == 'order_template') {
            $this->startOrderProcess($chatId, $chat);
        }
        // Обработка кнопки "FAQ"
        elseif ($data == 'show_faq') {
            $this->sendFaqMenu($chatId);
        }
        // Обработка кнопки "Главное меню"
        elseif ($data == 'main_menu') {
            $this->sendMainMenu($chatId);
        }
        // Обработка кнопки "Отмена" в процессе заказа
        elseif ($data == 'cancel_order') {
            $chat->state = null;
            $chat->step = 0;
            $chat->data = null;
            $chat->save();
            
            $this->telegramService->sendMessage($chatId, 'Заказ отменен. Вы можете использовать меню для выбора других функций.');
            $this->sendMainMenu($chatId);
        }

        // Отправляем ответ на callback query, чтобы убрать часики на кнопке
        $this->telegramService->answerCallbackQuery($callbackQuery['id']);

        return response()->json(['status' => 'success']);
    }

    /**
     * Отправка главного меню
     */
    private function sendMainMenu($chatId)
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'FAQ - Частые вопросы', 'callback_data' => 'show_faq']
                ],
                [
                    ['text' => 'Заказать свой шаблон', 'callback_data' => 'order_template']
                ]
            ]
        ];

        $this->telegramService->sendMessage(
            $chatId,
            "Выберите действие из меню:",
            $keyboard
        );
    }

    /**
     * Отправка меню FAQ
     */
    private function sendFaqMenu($chatId)
    {
        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($this->faqItems as $id => $item) {
            $keyboard['inline_keyboard'][] = [
                ['text' => $item['question'], 'callback_data' => $id]
            ];
        }

        // Добавляем кнопку возврата в главное меню
        $keyboard['inline_keyboard'][] = [
            ['text' => '« Назад в главное меню', 'callback_data' => 'main_menu']
        ];

        $this->telegramService->sendMessage(
            $chatId,
            "Выберите вопрос, на который хотите получить ответ:",
            $keyboard
        );
    }

    /**
     * Показ ответа на выбранный вопрос FAQ
     */
    private function showFaqAnswer($chatId, $faqId)
    {
        if (isset($this->faqItems[$faqId])) {
            $item = $this->faqItems[$faqId];
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '« Назад к FAQ', 'callback_data' => 'show_faq']
                    ],
                    [
                        ['text' => '« Главное меню', 'callback_data' => 'main_menu']
                    ]
                ]
            ];
            
            $this->telegramService->sendMessage(
                $chatId,
                "<b>{$item['question']}</b>\n\n{$item['answer']}",
                $keyboard
            );
        }
    }

    /**
     * Начало процесса заказа шаблона
     */
    private function startOrderProcess($chatId, $chat)
    {
        // Сбрасываем предыдущие данные и устанавливаем состояние "ordering"
        $chat->state = 'ordering';
        $chat->step = 1;
        $chat->data = [];
        $chat->save();
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Отмена', 'callback_data' => 'cancel_order']
                ]
            ]
        ];
        
        $this->telegramService->sendMessage(
            $chatId,
            "Вы начали процесс заказа индивидуального шаблона документа. " .
            "Я задам несколько вопросов, чтобы уточнить детали заказа.\n\n" .
            "Вы можете отменить процесс заказа в любой момент нажатием кнопки 'Отмена'.",
            $keyboard
        );
        
        // Отправляем первый вопрос
        $this->telegramService->sendMessage(
            $chatId,
            $this->orderSteps[1]['question']
        );
    }

    /**
     * Обработка ответов на шаги заказа шаблона
     */
    private function handleOrderStep($chatId, $chat, $text)
    {
        // Получаем текущий шаг
        $step = $chat->step;
        
        // Если такой шаг существует
        if (isset($this->orderSteps[$step])) {
            // Сохраняем ответ в данные
            $field = $this->orderSteps[$step]['field'];
            $data = $chat->data;
            $data[$field] = $text;
            $chat->data = $data;
            
            // Увеличиваем номер шага
            $chat->step += 1;
            $chat->save();
            
            // Если есть следующий шаг, задаем вопрос
            if (isset($this->orderSteps[$chat->step])) {
                $this->telegramService->sendMessage(
                    $chatId,
                    $this->orderSteps[$chat->step]['question']
                );
            } else {
                // Если шаги закончились, завершаем процесс заказа
                $this->completeOrderProcess($chatId, $chat);
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Завершение процесса заказа шаблона
     */
    private function completeOrderProcess($chatId, $chat)
    {
        $orderData = $chat->data;
        
        // Очищаем состояние
        $chat->state = null;
        $chat->step = 0;
        $chat->save();
        
        // Формируем сообщение с данными заказа
        $summaryMessage = "Спасибо! Ваш заказ принят.\n\n";
        $summaryMessage .= "<b>Детали вашего заказа:</b>\n";
        $summaryMessage .= "Имя: " . ($orderData['name'] ?? 'Не указано') . "\n";
        $summaryMessage .= "Email: " . ($orderData['email'] ?? 'Не указан') . "\n";
        $summaryMessage .= "Телефон: " . ($orderData['phone'] ?? 'Не указан') . "\n";
        $summaryMessage .= "Описание шаблона: " . ($orderData['description'] ?? 'Не указано') . "\n";
        $summaryMessage .= "Цель: " . ($orderData['purpose'] ?? 'Не указана') . "\n";
        $summaryMessage .= "Желаемые сроки: " . ($orderData['deadline'] ?? 'Не указаны') . "\n\n";
        $summaryMessage .= "Наш менеджер свяжется с вами в ближайшее время для уточнения деталей.";
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'В главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
        
        // Отправляем сообщение пользователю
        $this->telegramService->sendMessage(
            $chatId,
            $summaryMessage,
            $keyboard
        );
        
        // Формируем текст уведомления для группы
        $groupMessage = "🔔 <b>НОВЫЙ ЗАКАЗ ШАБЛОНА</b>\n\n";
        $groupMessage .= "👤 Имя: " . ($orderData['name'] ?? 'Не указано') . "\n";
        $groupMessage .= "📧 Email: " . ($orderData['email'] ?? 'Не указан') . "\n";
        $groupMessage .= "📱 Телефон: " . ($orderData['phone'] ?? 'Не указан') . "\n";
        $groupMessage .= "📝 Описание: " . ($orderData['description'] ?? 'Не указано') . "\n";
        $groupMessage .= "🎯 Цель: " . ($orderData['purpose'] ?? 'Не указана') . "\n";
        $groupMessage .= "⏰ Сроки: " . ($orderData['deadline'] ?? 'Не указаны') . "\n";
        
        // Отправляем уведомление в групповой чат
        try {
            $this->telegramService->sendMessage($this->groupChatId, $groupMessage);
            Log::info('Notification about new order sent to group chat', ['chat_id' => $this->groupChatId]);
        } catch (\Exception $e) {
            Log::error('Failed to send order notification to group chat', ['error' => $e->getMessage()]);
        }
        
        // Отправляем данные на почту
        try {
            Mail::to('w1nishko@yandex.ru')->send(new TemplateOrderMail($orderData));
            Log::info('Order email sent', ['to' => 'w1nishko@yandex.ru', 'data' => $orderData]);
        } catch (\Exception $e) {
            Log::error('Failed to send order email', ['error' => $e->getMessage()]);
        }
    }
}

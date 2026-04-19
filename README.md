# JsonObject

Минимальная библиотека для динамической обертки над JSON-подобными данными.

Идея:
- вход: массив или JSON-строка;
- доступ к полям через `__get`;
- доступ через Telegram-style геттеры вида `getMessageThreadId()`;
- поддержка `isBot()` и аналогичных булевых методов;
- автоматическое преобразование `PascalCase`/`camelCase` в `snake_case`;
- вложенные объекты автоматически заворачиваются в `JsonObject`.

Пример:

```php
use losthost\JsonObject\JsonObject;

$update = new JsonObject([
    'message' => [
        'message_id' => 10,
        'chat' => [
            'id' => 9,
            'is_forum' => true,
        ],
    ],
]);

$update->getMessage()->getMessageId(); // 10
$update->getMessage()->getChat()->getId(); // 9
$update->getMessage()->getChat()->getIsForum(); // true
```

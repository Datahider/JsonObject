# JsonObject

Минимальная библиотека для динамической обертки над JSON-данными.

Идея:
- вход: JSON-строка или `stdClass`;
- доступ через Telegram-style геттеры вида `getMessageThreadId()`;
- поддержка `isBot()` и аналогичных булевых методов;
- автоматическое преобразование `PascalCase`/`camelCase` в `snake_case`;
- вложенные JSON-объекты автоматически заворачиваются в `JsonObject`;
- JSON-массивы остаются массивами, но их элементы-объекты тоже заворачиваются в `JsonObject`.

Текущее поведение:
- `getMessage()` ищет поле `message`;
- `getMessageThreadId()` ищет `message_thread_id`;
- `isBot()` ищет `is_bot`;
- если поля нет, возвращается `null`;
- scalar значения возвращаются как есть.

Пример:

```php
use losthost\JsonObject\JsonObject;

$update = new JsonObject(<<<'JSON'
{
  "message": {
    "message_id": 10,
    "message_thread_id": 20,
    "chat": {
      "id": 9,
      "is_forum": true
    },
    "from": {
      "id": 29,
      "is_bot": true
    }
  }
}
JSON);

$update->getMessage()->getMessageId(); // 10
$update->getMessage()->getMessageThreadId(); // 20
$update->getMessage()->getChat()->getId(); // 9
$update->getMessage()->getChat()->getIsForum(); // true
$update->getMessage()->getFrom()->isBot(); // true
$update->getMessage()->getNoSuchField(); // null
```

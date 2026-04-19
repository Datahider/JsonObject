<?php

declare(strict_types=1);

require_once __DIR__ . '/src/JsonObject.php';

use losthost\JsonObject\JsonObject;

$root = new JsonObject(<<<'JSON'
{
  "message": {
    "message_id": 182,
    "message_thread_id": 20,
    "chat": {
      "id": 9,
      "type": "supergroup",
      "is_forum": true
    },
    "from": {
      "id": 29,
      "first_name": "Oberbot",
      "is_bot": true
    },
    "entities": [
      {
        "type": "mention",
        "offset": 0,
        "length": 8
      }
    ]
  }
}
JSON);

$checks = [
    'message_id' => $root->getMessage()?->getMessageId(),
    'thread_id' => $root->getMessage()?->getMessageThreadId(),
    'chat_id' => $root->getMessage()?->getChat()?->getId(),
    'chat_type' => $root->getMessage()?->getChat()?->getType(),
    'is_forum' => $root->getMessage()?->getChat()?->getIsForum(),
    'from_name' => $root->getMessage()?->getFrom()?->getFirstName(),
    'is_bot' => $root->getMessage()?->getFrom()?->isBot(),
    'missing_field' => $root->getMessage()?->getNoSuchField(),
    'entity_type' => $root->getMessage()?->getEntities()[0]?->getType() ?? null,
    'entity_offset' => $root->getMessage()?->getEntities()[0]?->getOffset() ?? null,
];

foreach ($checks as $name => $value) {
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    } elseif ($value === null) {
        $value = 'null';
    }

    echo $name . '=' . $value . PHP_EOL;
}

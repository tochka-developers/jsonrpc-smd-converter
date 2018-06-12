# SDM ковертер
Конвертер SMD схему в рзаные форматы

#### Поддерживаемы форматы
- postman

#### Использование

```
php artisan jsonrpc:convert {source} {format}
```
- source и format можно заменить на **all**
- если не указать будет выбор из доступных вариантов
- если source или format всего один то можно не их можно не указывать

##Установка
###lumen 
bootstrap
```php
$app->register(Tochka\JsonRpcSmdConverter\ServiceProvider::class);
```
###Laravel >=5.5
Провайдер регистрируется атомаматически
###laravel <=5.4
Зарегистировать пройвадера в configs/app.php

### Конфигурация
```
php artisan vendor:publish --provider="Tochka\JsonRpcSmdConverter\JsonRpcSmdConverterServiceProvider"
```

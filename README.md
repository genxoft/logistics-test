# Тестовое задание

## Описание

Разработать сервис расчета доставки (подробности: [Техническое задание](docs/ts.docx))

## Установка
```shell
git clone https://github.com/genxoft/logistics-test
cd logistics-test
composer install
```

## Запуск
```shell
docker-compose up -d
```

## Интерфейс

[http://localhost:8080](http://localhost:8080)

На глваной странице откроется SwaggerUI

## Документация API

[api.yaml](docs/api.yaml)

## Описание
Главный метод ```[POST] /api/delivery``` принимает на вход массив грузов и возвращает массив отправлений сгруппированный по службам доставки
Алгоритм также учитывает, что служба доставки может иметь ограниченную массу одного отправления, в данном случае он компанует грузы в несколько доставок
### Request
```
[
    {
        "from": "<Код кладр>",
        "to": "<Код кладр>",
        "weight": "<float>"
    },
    ...
]
```
### Response
```
{
    "fast": [
        {
            "price": "<float>",
            "date": "<string>",
            "error": "<string>|null",
            "cargo": {
                // Подробности груза
            }
        },
        ...
    ],
    "slow": ...,
    ...
}
```

## TODO

- [ ] Сделать более умный алгоритм компоновщика (добавить Knapsack Problem Algorithm)
- [ ] Покрыть тестами весь код (на данном этапе реализованны только пара тестов для демонстрации)

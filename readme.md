# Памятка по запуску
## Redis

Обязательно нужно установить Redis и прописать его параметры в .env.
## Task Scheduler
1. Если это компьютер Mac, то cron предварительно нужно дать права на диск компьютера как описано по [этой](https://osxdaily.com/2020/04/27/fix-cron-permissions-macos-full-disk-access/https://osxdaily.com/2020/04/27/fix-cron-permissions-macos-full-disk-access/) ссылке.
2. Необходимо добавить строку в `cron` через `crontab -e`:

`* * * * * cd <полный путь к корню проекта> && php artisan schedule:run >> /dev/null 2>&1`

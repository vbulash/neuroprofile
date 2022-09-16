# Памятка по запуску
## Redis

Обязательно нужно установить Redis `brew install redis` и прописать его параметры в .env.

В файле конфигурации `/opt/homebrew/etc/redis.conf` после строки `bind 127.0.0.1 ::1` вставить строку `bind 0.0.0.0` - это позволит присоединяться к серверу redis извне.

После изменений в конфигурации сервис redis необходимо перезапустить через `brew service restart redis`.

## Task Scheduler
1. Если это компьютер Mac, то cron предварительно нужно дать права на диск компьютера как описано по [этой](https://osxdaily.com/2020/04/27/fix-cron-permissions-macos-full-disk-access/https://osxdaily.com/2020/04/27/fix-cron-permissions-macos-full-disk-access/) ссылке.
2. Необходимо добавить строку в `cron` через `crontab -e`:

`* * * * * cd <полный путь к корню проекта> && php artisan schedule:run >> /dev/null 2>&1`

3. Необходимо добавить обновление сертификатов Let's Encrypt через `sudo crontab -e`:

`0 0 * * * /opt/homebrew/bin/certbot renew --quiet`

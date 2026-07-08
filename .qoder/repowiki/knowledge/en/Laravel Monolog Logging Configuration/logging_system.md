The application uses Laravel's built-in logging system, which is powered by the **Monolog** library. Logging is configured centrally in `config/logging.php`.

### System Approach
- **Framework**: Laravel Logging (Monolog adapter).
- **Configuration**: Defined in `config/logging.php`, utilizing environment variables for flexibility (`LOG_CHANNEL`, `LOG_LEVEL`, etc.).
- **Default Channel**: The default channel is `stack`, which aggregates other channels (defaulting to `single` via `LOG_STACK`).

### Key Channels
1. **single**: Writes to a single file `storage/logs/laravel.log`. Default level is `debug`.
2. **daily**: Rotates log files daily in `storage/logs/`. Retention is configurable via `LOG_DAILY_DAYS` (default 14).
3. **slack**: Sends critical errors to a Slack webhook. Configured via `LOG_SLACK_WEBHOOK_URL`. Default level is `critical`.
4. **stderr**: Outputs logs to standard error, useful for containerized environments. Uses `PsrLogMessageProcessor`.
5. **null**: Discards all logs (used for deprecations by default).

### Conventions & Rules
- **Environment Driven**: Log levels and channels are controlled via `.env` variables (`LOG_CHANNEL`, `LOG_LEVEL`).
- **Structured Placeholders**: Most channels enable `replace_placeholders => true`, allowing for structured log messages using `{key}` syntax in Monolog.
- **Deprecations**: PHP/Library deprecations are logged to the `null` channel by default but can be routed elsewhere via `LOG_DEPRECATIONS_CHANNEL`.
- **Usage**: Developers should use the `Log` facade (`Illuminate\Support\Facades\Log`) or the `logger()` helper. No custom logging wrappers or middleware-based logging were found in the application code.
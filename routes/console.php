<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduler (container `scheduler` chay `php artisan schedule:work`)
|--------------------------------------------------------------------------
|
| Tac vu theo lich cua tung module dang ky tai day (hoac trong boot() cua
| ServiceProvider module). Xem docs/04-team/progress.md cho cac muc con thieu:
| sinh buoi hoc, nhac han, chuyen trang thai lop (muc 9.1).
|
*/

Schedule::command('auth:clear-resets')->daily();
Schedule::command('queue:prune-batches --hours=48')->daily();
Schedule::command('queue:prune-failed --hours=168')->weekly();

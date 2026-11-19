<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteAdminCommand extends Command
{
    protected $signature = 'arissa:make-admin {email : Email address of an existing guest account} {--force : Skip the confirmation prompt}';

    protected $description = 'Promote an existing account to hotel administrator from the command line';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No account was found with that email address.');

            return self::FAILURE;
        }

        if ($user->role === 'admin') {
            $this->info('This account is already an administrator.');

            return self::SUCCESS;
        }

        if (! $this->option('force')
            && ! $this->confirm("Promote {$user->email} to administrator? This grants full system access.")) {
            $this->warn('No changes were made.');

            return self::SUCCESS;
        }

        $user->role = 'admin';
        $user->save();

        $this->info("{$user->email} is now an administrator.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Mail;
use App\User;
use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send drip e-mails to a user';

    protected $drip;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
//        $this->drip = $drip;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->drip->send(User::find($this->argument('user')));
        Mail::raw('Hello world', function ($m) {
            $m->from('wuhulinyi@126.com', 'FormReview');

            $m->to('wuhulinyi@sina.com', 'SINA')->subject('Your Reminder!');
        });
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailMessageFlag extends Model
{
    protected $table = 'mail_message_flags';
    protected $primaryKey = 'message_id';
}

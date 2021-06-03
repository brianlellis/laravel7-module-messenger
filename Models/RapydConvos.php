<?php

namespace Rapyd\Model;

use Illuminate\Database\Eloquent\Model;

class RapydConvos extends Model
{
  protected $table   = 'message_conversations';
  protected $colKey  = 'id';
  protected $guarded = [];
}

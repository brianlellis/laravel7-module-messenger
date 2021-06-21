<?php

namespace Rapyd\Messenger;

use Illuminate\Http\Request;
use Rapyd\Model\RapydMessages;
use Rapyd\Model\RapydConvos;

class Convos
{
  public static function retrieve($scope = 'all', $convo_id = false)
  {
    // IF SCOPE IS ALL THEN IT WILL RETURN THE LIST MESSAGES
    if ($scope === 'all') {
      $messages = RapydMessages::where('user_one_id', \Auth::user()->id)
                    ->orWhere('user_two_id', \Auth::user()->id)
                    ->paginate(25);
    }
    // GET ALL MESSAGES WITH AN UNREAD STATUS
    if ($scope === 'unread') {
      $messages = RapydConvos::select(
                      'messages.title',
                      'message_conversations.message_id',
                      'message_conversations.sender_id'
                    )
                    ->distinct('message_id')
                    ->where('recipient_read', 0)
                    ->where('recipient_id', \Auth::user()->id)
                    ->where('message_id', '!=', $convo_id) // Check to see if current page is a conversation which needs to be excluded
                    ->leftJoin('messages', 'messages.id', '=', 'message_conversations.message_id')
                    ->paginate(25);
    }
    // IF SCOPE IS CONVO THEN IT RETURNS THE MESSAGE'S CONVERSATION
    elseif($scope === 'convo') {
      self::read($convo_id);

      $messages = RapydConvos::select(
                      'messages.title',
                      'message_conversations.*'
                    )
                    ->where('message_id', $convo_id)
                    ->orderBy('created_at')
                    ->leftJoin('messages', 'messages.id', '=', 'message_conversations.message_id')
                    ->paginate(25);
    }

    return $messages;
  }

  public function create(REQUEST $request)
  {
    $validated = $request->validate([
        'sender_id'    => 'required',
        'recipient_id' => 'required',
        'title'        => 'required',
        'content'      => 'required'
      ]);

    $message = RapydMessages::create([
      'user_one_id' => $validated['sender_id'],
      'user_two_id' => $validated['recipient_id'],
      'title'       => $validated['title']
    ]);

    $convo = RapydConvos::create([
      'message_id'   => $message->id,
      'sender_id'    => $validated['sender_id'],
      'recipient_id' => $validated['recipient_id'],
      'content'      => $validated['content']
    ]);
  }

  public static function send(REQUEST $request)
  {
      $validated = $request->validate([
        'message_id'   => 'required',
        'sender_id'    => 'required',
        'recipient_id' => 'required',
        'content'      => 'required'
      ]);

      $convo = RapydConvos::create([
        'message_id'   => $validated['message_id'],
        'sender_id'    => $validated['sender_id'],
        'recipient_id' => $validated['recipient_id'],
        'content'      => $validated['content']
      ]);

      return redirect(config('app.url').'/admin/messages/convo?convo_id='.$validated['message_id']);
  }

  protected static function read($convo_msg_id)
  {
    $convo = RapydConvos::where('message_id', $convo_msg_id)
                  ->where('recipient_id', \Auth::user()->id)
                  ->update(array('recipient_read' => 1));

    return true;
  }

  public function message_visibility(REQUEST $request)
  {
    $message = RapydMessages::find($request->id);

    if ($message->user_one_id === $request->user_id) {
      $message->user_one_is_visible = $request->visiblity;
    } else {
      $message->user_two_is_visible = $request->visiblity;
    }

    $message->save();
  }
}

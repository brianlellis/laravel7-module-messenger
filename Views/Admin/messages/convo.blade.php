@php
  use Rapyd\Messenger\Convos;
  $conversations = Convos::retrieve('convo', Request::get('convo_id'));

  // THERE WILL ONLY EVER BE 2 USERS IN THE CONVERSATION
  // SO THE RECIPIENT IS THE NON MATCHING USER ID
  $recipient_id = $conversations->first()->sender_id === Auth::user()->id ? $conversations->first()->recipient_id : $conversations->first()->sender_id;
@endphp


<h3>{{$conversations->first()->title}}</h3>

<div class="row chatbox">
  <div class="chat col-sm-12">
    <div class="card">
      <div class="card-body msg_card_body">
        @foreach ($conversations as $conversation)
          @php
            $user_sent = $conversation->sender_id !== Auth::user()->id ? true : false;
          @endphp

          <div class="d-flex @if($user_sent) justify-content-start @else justify-content-end @endif">
            <div class="msg_cotainer">
              {{$conversation->content}}

              <span class="@if($user_sent) msg_time @else msg_time_send @endif">
                @if(!$conversation->recipient_read) (Unread) @endif
                {{$conversation->created_at->format('g:i A')}},
                @if($conversation->created_at->isToday()) Today @else {{$conversation->created_at->format('m/d/Y')}} @endif
              </span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="card-footer">
        <form method="POST" action="{{ route('rapyd.convos.add') }}">
            @csrf

            <input type="hidden" name="message_id" value={{Request::get('convo_id')}} />
            <input type="hidden" name="sender_id" value={{Auth::user()->id}} />
            <input type="hidden" name="recipient_id" value={{$recipient_id}} />

    				<div class="msb-reply d-flex">
    					<span class="input-group-text attach_btn">
    						<i class="fe fe-paperclip mr-2"></i>
    					</span>
    					<textarea name="content" placeholder="Enter message here..."></textarea>
    					<button><i class="fa fa-paper-plane-o"></i></button>
    				</div>
        </form>
			</div>
    </div>
  </div>
</div>

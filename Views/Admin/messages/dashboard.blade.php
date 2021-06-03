@php
  use Rapyd\Messenger\Convos;
  $data = Convos::retrieve();
@endphp

@dashboard_table_header('Messages')
@dashboard_table('ID #, Title , Recipient,Action,'{!! $data->render() !!})
  @foreach ($data as $conversation)
    <tr>
      @php
        $other_user = $conversation->user_one_id === Auth::user()->id ? $conversation->user_two_id : $conversation->user_one_id;
      @endphp
      <td>{{$conversation->id}}</td>
      <td>{{$conversation->title}}</td>
      <td>From: @if(!$other_user) System @endif</td>
      <td>
        <a class="btn btn-primary" href="/admin/messages/convo?convo_id={{$conversation->id}}">View</a>
      </td>
    </tr>
  @endforeach
@end_dashboard_table
